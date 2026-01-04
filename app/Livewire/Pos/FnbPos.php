<?php

namespace App\Livewire\Pos;

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Enums\UserRole;
use App\Models\FnbOrder;
use App\Models\FnbOrderDetail;
use App\Models\MenuItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.pos')]
#[Title('FnB POS - Cinema XXL')]
class FnbPos extends Component
{
    /**
     * Authorization check - only Admin, Manager, FnbStaff can access FnB POS.
     */
    public function mount(): void
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, [
            UserRole::Admin,
            UserRole::Manager,
            UserRole::FnbStaff,
        ])) {
            abort(403, 'Unauthorized access to FnB POS.');
        }
    }

    // Search & Filters
    public string $search = '';
    public string $activeCategory = 'all';

    // Cart
    public array $cart = [];

    // Payment
    public string $paymentMethod = 'cash';

    // UI State
    public bool $showSuccessModal = false;
    public ?string $lastOrderCode = null;
    public float $lastOrderTotal = 0;

    /**
     * Get filtered menu items with max stock calculation.
     */
    #[Computed]
    public function menuItems(): Collection
    {
        $query = MenuItem::with(['recipes.inventoryItem'])
            ->where('is_available', true);

        // Category filter
        if ($this->activeCategory !== 'all') {
            $query->where('category', $this->activeCategory);
        }

        // Search filter
        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $items = $query->get();

        // Calculate max stock for each item
        return $items->map(function ($item) {
            $item->max_stock = $this->calculateMaxStock($item);
            $item->cart_qty = $this->getCartQty($item->id);
            return $item;
        });
    }

    /**
     * Calculate maximum sellable quantity based on inventory.
     */
    private function calculateMaxStock(MenuItem $item): int
    {
        if ($item->recipes->isEmpty()) {
            // No recipe = unlimited (or a high number)
            return 999;
        }

        $maxQuantities = [];

        foreach ($item->recipes as $recipe) {
            $inventoryStock = $recipe->inventoryItem->stock_quantity;
            $neededPerItem = $recipe->quantity_needed;

            if ($neededPerItem > 0) {
                $maxQuantities[] = floor($inventoryStock / $neededPerItem);
            }
        }

        return empty($maxQuantities) ? 999 : (int) min($maxQuantities);
    }

    /**
     * Get quantity of item in cart.
     */
    private function getCartQty(int $menuId): int
    {
        foreach ($this->cart as $item) {
            if ($item['menu_id'] === $menuId) {
                return $item['qty'];
            }
        }
        return 0;
    }

    /**
     * Get cart total.
     */
    #[Computed]
    public function cartTotal(): float
    {
        return collect($this->cart)->sum(fn($item) => $item['price'] * $item['qty']);
    }

    /**
     * Get cart items count.
     */
    #[Computed]
    public function cartCount(): int
    {
        return collect($this->cart)->sum('qty');
    }

    /**
     * Add item to cart.
     */
    public function addToCart(int $menuId): void
    {
        $menuItem = MenuItem::with(['recipes.inventoryItem'])->find($menuId);

        if (!$menuItem || !$menuItem->is_available) {
            $this->dispatch('show-toast', type: 'error', message: 'Item not available');
            return;
        }

        $maxStock = $this->calculateMaxStock($menuItem);
        $currentQty = $this->getCartQty($menuId);

        if ($currentQty >= $maxStock) {
            $this->dispatch('show-toast', type: 'error', message: 'Maximum stock reached for ' . $menuItem->name);
            return;
        }

        // Check if already in cart
        $found = false;
        foreach ($this->cart as $key => $item) {
            if ($item['menu_id'] === $menuId) {
                $this->cart[$key]['qty']++;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $this->cart[] = [
                'menu_id' => $menuItem->id,
                'name' => $menuItem->name,
                'price' => (float) $menuItem->price,
                'qty' => 1,
            ];
        }

        $this->dispatch('show-toast', type: 'success', message: $menuItem->name . ' added to cart');
    }

    /**
     * Remove item from cart (decrement or remove).
     */
    public function removeFromCart(int $menuId): void
    {
        foreach ($this->cart as $key => $item) {
            if ($item['menu_id'] === $menuId) {
                if ($this->cart[$key]['qty'] > 1) {
                    $this->cart[$key]['qty']--;
                } else {
                    unset($this->cart[$key]);
                    $this->cart = array_values($this->cart);
                }
                break;
            }
        }
    }

    /**
     * Update item quantity directly.
     */
    public function updateQty(int $menuId, int $qty): void
    {
        if ($qty <= 0) {
            $this->removeItemCompletely($menuId);
            return;
        }

        $menuItem = MenuItem::with(['recipes.inventoryItem'])->find($menuId);
        $maxStock = $this->calculateMaxStock($menuItem);

        if ($qty > $maxStock) {
            $this->dispatch('show-toast', type: 'error', message: 'Only ' . $maxStock . ' available for ' . $menuItem->name);
            $qty = $maxStock;
        }

        foreach ($this->cart as $key => $item) {
            if ($item['menu_id'] === $menuId) {
                $this->cart[$key]['qty'] = $qty;
                break;
            }
        }
    }

    /**
     * Remove item completely from cart.
     */
    public function removeItemCompletely(int $menuId): void
    {
        $this->cart = array_values(array_filter($this->cart, fn($item) => $item['menu_id'] !== $menuId));
    }

    /**
     * Clear entire cart.
     */
    public function clearCart(): void
    {
        $this->cart = [];
        $this->dispatch('show-toast', type: 'info', message: 'Cart cleared');
    }

    /**
     * Process checkout transaction.
     */
    public function checkout(): void
    {
        if (empty($this->cart)) {
            $this->dispatch('show-toast', type: 'error', message: 'Cart is empty');
            return;
        }

        try {
            DB::transaction(function () {
                // Generate unique order code
                $orderCode = 'FNB-' . strtoupper(substr(uniqid(), -6)) . '-' . now()->format('dmy');

                // Create FnB Order
                $order = FnbOrder::create([
                    'order_code' => $orderCode,
                    'cashier_id' => Auth::id(),
                    'user_id' => null, // Walk-in customer
                    'booking_id' => null,
                    'total_amount' => $this->cartTotal,
                    'payment_method' => $this->paymentMethod,
                    'status' => BookingStatus::Paid,
                    'transaction_time' => now(),
                ]);

                // Create order details & deduct inventory
                foreach ($this->cart as $item) {
                    // Create order detail
                    FnbOrderDetail::create([
                        'fnb_order_id' => $order->id,
                        'menu_item_id' => $item['menu_id'],
                        'quantity' => $item['qty'],
                        'subtotal' => $item['price'] * $item['qty'],
                    ]);

                    // Deduct inventory
                    $menuItem = MenuItem::with('recipes.inventoryItem')->find($item['menu_id']);
                    foreach ($menuItem->recipes as $recipe) {
                        $deductAmount = $recipe->quantity_needed * $item['qty'];
                        $recipe->inventoryItem->deductStock($deductAmount);
                    }
                }

                // Store for success modal
                $this->lastOrderCode = $orderCode;
                $this->lastOrderTotal = $this->cartTotal;

                // Clear cart
                $this->cart = [];
                $this->paymentMethod = 'cash';

                // Show success modal
                $this->showSuccessModal = true;
            });
        } catch (\Exception $e) {
            $this->dispatch('show-toast', type: 'error', message: 'Transaction failed: ' . $e->getMessage());
        }
    }

    /**
     * Close success modal.
     */
    public function closeSuccessModal(): void
    {
        $this->showSuccessModal = false;
        $this->lastOrderCode = null;
        $this->lastOrderTotal = 0;
    }

    /**
     * Set active category.
     */
    public function setCategory(string $category): void
    {
        $this->activeCategory = $category;
    }

    /**
     * Get payment methods for dropdown.
     */
    public function getPaymentMethods(): array
    {
        return [
            'cash' => 'Cash',
            'qris' => 'QRIS',
            'card' => 'Debit/Credit Card',
            'e-wallet' => 'E-Wallet',
        ];
    }

    public function render()
    {
        return view('livewire.pos.fnb-pos');
    }
}

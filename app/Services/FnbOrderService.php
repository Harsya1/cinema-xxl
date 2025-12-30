<?php

namespace App\Services;

use App\Models\FnbOrder;
use App\Models\FnbOrderDetail;
use App\Models\MenuItem;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class FnbOrderService
{
    /**
     * Create a new FnB order.
     */
    public function createOrder(
        int $cashierId,
        array $items, // [['menu_item_id' => 1, 'quantity' => 2], ...]
        string $paymentMethod,
        ?int $userId = null
    ): FnbOrder {
        // Validate all items can be made
        foreach ($items as $item) {
            $menuItem = MenuItem::findOrFail($item['menu_item_id']);
            if (!$menuItem->canBeMade($item['quantity'])) {
                $missing = $menuItem->getMissingIngredients($item['quantity']);
                throw new \Exception("Insufficient stock for {$menuItem->name}");
            }
        }

        return DB::transaction(function () use ($cashierId, $items, $paymentMethod, $userId) {
            // Calculate total
            $total = 0;
            foreach ($items as $item) {
                $menuItem = MenuItem::find($item['menu_item_id']);
                $total += $menuItem->price * $item['quantity'];
            }

            // Create order
            $order = FnbOrder::create([
                'cashier_id' => $cashierId,
                'user_id' => $userId,
                'total_price' => $total,
                'payment_method' => $paymentMethod,
                'transaction_time' => now(),
            ]);

            // Create order details and deduct inventory
            foreach ($items as $item) {
                $menuItem = MenuItem::find($item['menu_item_id']);

                FnbOrderDetail::create([
                    'fnb_order_id' => $order->id,
                    'menu_item_id' => $item['menu_item_id'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $menuItem->price * $item['quantity'],
                ]);

                // Deduct inventory
                $menuItem->deductInventory($item['quantity']);
            }

            AuditLog::log('FNB_ORDER_CREATED', "FnB Order #{$order->id} created. Total: {$order->getFormattedTotal()}");

            return $order->load('details.menuItem');
        });
    }

    /**
     * Get daily sales summary.
     */
    public function getDailySalesSummary(?string $date = null): array
    {
        $date = $date ? \Carbon\Carbon::parse($date) : today();

        $orders = FnbOrder::whereDate('transaction_time', $date)->get();

        return [
            'date' => $date->toDateString(),
            'total_orders' => $orders->count(),
            'total_revenue' => $orders->sum('total_price'),
            'by_payment_method' => $orders->groupBy('payment_method')->map->sum('total_price'),
        ];
    }

    /**
     * Get top selling items.
     */
    public function getTopSellingItems(int $limit = 10, ?string $startDate = null, ?string $endDate = null): \Illuminate\Support\Collection
    {
        $query = FnbOrderDetail::selectRaw('menu_item_id, SUM(quantity) as total_sold, SUM(subtotal) as total_revenue')
            ->groupBy('menu_item_id')
            ->orderByDesc('total_sold')
            ->limit($limit);

        if ($startDate) {
            $query->whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->whereDate('transaction_time', '>=', $startDate);
                if ($endDate) {
                    $q->whereDate('transaction_time', '<=', $endDate);
                }
            });
        }

        return $query->with('menuItem')->get();
    }

    /**
     * Check item availability.
     */
    public function checkAvailability(int $menuItemId, int $quantity = 1): array
    {
        $menuItem = MenuItem::with('recipes.inventoryItem')->findOrFail($menuItemId);

        return [
            'available' => $menuItem->canBeMade($quantity),
            'missing_ingredients' => $menuItem->getMissingIngredients($quantity),
        ];
    }
}

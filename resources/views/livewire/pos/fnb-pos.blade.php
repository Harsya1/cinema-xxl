<div class="h-screen bg-gray-900 flex overflow-hidden">
    {{-- Toast Notification --}}
    <div 
        x-data="{ 
            show: false, 
            message: '', 
            type: 'success',
            timeout: null
        }"
        x-on:show-toast.window="
            clearTimeout(timeout);
            message = $event.detail.message;
            type = $event.detail.type;
            show = true;
            timeout = setTimeout(() => show = false, 3000);
        "
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed top-4 right-4 z-50"
        style="display: none;"
    >
        <div 
            x-bind:class="{
                'bg-green-600': type === 'success',
                'bg-red-600': type === 'error',
                'bg-blue-600': type === 'info',
                'bg-yellow-600': type === 'warning'
            }"
            class="px-6 py-3 rounded-lg shadow-lg text-white font-medium flex items-center gap-3"
        >
            <span x-text="message"></span>
        </div>
    </div>

    {{-- Success Modal --}}
    @if($showSuccessModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70">
        <div class="bg-gray-800 rounded-2xl p-8 max-w-md w-full mx-4 text-center">
            <div class="w-20 h-20 bg-green-500 bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Payment Successful!</h3>
            <p class="text-gray-400 mb-6">Order has been processed</p>
            
            <div class="bg-gray-900 rounded-xl p-4 mb-6">
                <p class="text-gray-400 text-sm mb-1">Order Code</p>
                <p class="text-xl font-mono font-bold text-yellow-400">{{ $lastOrderCode }}</p>
            </div>
            
            <div class="bg-gray-900 rounded-xl p-4 mb-6">
                <p class="text-gray-400 text-sm mb-1">Total Paid</p>
                <p class="text-3xl font-bold text-green-400">Rp {{ number_format($lastOrderTotal, 0, ',', '.') }}</p>
            </div>
            
            <button 
                wire:click="closeSuccessModal"
                class="w-full py-4 bg-yellow-500 hover:bg-yellow-400 text-gray-900 font-bold rounded-xl transition"
            >
                New Order
            </button>
        </div>
    </div>
    @endif

    {{-- LEFT COLUMN: Menu Grid --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        {{-- Header --}}
        <div class="flex-shrink-0 p-6 pb-4">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-white">FnB Point of Sale</h1>
                    <p class="text-gray-400">{{ now()->format('l, d F Y') }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-gray-400 text-sm">Logged in as</p>
                        <p class="text-white font-medium">{{ Auth::user()->name }}</p>
                    </div>
                    <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center">
                        <span class="text-gray-900 font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                </div>
            </div>

            {{-- Search Bar --}}
            <div class="mb-4">
                <div class="relative">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search menu items..."
                        class="w-full bg-gray-800 border border-gray-700 rounded-xl py-3 px-4 pl-12 text-white placeholder-gray-500 focus:border-yellow-500 focus:outline-none"
                    >
                    <svg class="w-5 h-5 text-gray-500 absolute left-4 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            {{-- Category Tabs --}}
            <div class="flex gap-3 overflow-x-auto pb-2">
                <button 
                    wire:click="setCategory('all')"
                    class="px-6 py-2.5 rounded-full font-medium transition whitespace-nowrap {{ $activeCategory === 'all' ? 'bg-yellow-500 text-gray-900' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}"
                >
                    All Items
                </button>
                <button 
                    wire:click="setCategory('Food')"
                    class="px-6 py-2.5 rounded-full font-medium transition whitespace-nowrap {{ $activeCategory === 'Food' ? 'bg-yellow-500 text-gray-900' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}"
                >
                    🍿 Food
                </button>
                <button 
                    wire:click="setCategory('Beverage')"
                    class="px-6 py-2.5 rounded-full font-medium transition whitespace-nowrap {{ $activeCategory === 'Beverage' ? 'bg-yellow-500 text-gray-900' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}"
                >
                    🥤 Beverage
                </button>
                <button 
                    wire:click="setCategory('Combo')"
                    class="px-6 py-2.5 rounded-full font-medium transition whitespace-nowrap {{ $activeCategory === 'Combo' ? 'bg-yellow-500 text-gray-900' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}"
                >
                    🎁 Combo
                </button>
            </div>
        </div>

        {{-- Menu Grid (Scrollable) --}}
        <div class="flex-1 overflow-y-auto px-6 pb-6">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse($this->menuItems as $item)
                    @php
                        $isSoldOut = $item->max_stock <= 0;
                        $remainingStock = max(0, $item->max_stock - $item->cart_qty);
                        $isDisabled = $isSoldOut || $remainingStock <= 0;
                        $categoryValue = $item->category instanceof \App\Enums\MenuCategory ? $item->category->value : $item->category;
                    @endphp
                    <button
                        wire:click="addToCart({{ $item->id }})"
                        wire:key="menu-{{ $item->id }}"
                        @if($isDisabled) disabled @endif
                        class="bg-gray-800 rounded-2xl overflow-hidden transition-all text-left {{ $isDisabled ? 'opacity-50 cursor-not-allowed' : 'hover:ring-2 hover:ring-yellow-500 cursor-pointer' }}"
                    >
                        {{-- Image --}}
                        <div class="aspect-square bg-gray-700 relative">
                            @if($item->image_path)
                                <img 
                                    src="{{ asset('storage/' . $item->image_path) }}" 
                                    alt="{{ $item->name }}"
                                    class="w-full h-full object-cover"
                                >
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif

                            {{-- Sold Out / Max Overlay --}}
                            @if($isSoldOut)
                                <div class="absolute inset-0 bg-black bg-opacity-70 flex items-center justify-center">
                                    <span class="bg-red-600 text-white px-4 py-2 rounded-lg font-bold text-sm">SOLD OUT</span>
                                </div>
                            @elseif($remainingStock <= 0)
                                <div class="absolute inset-0 bg-black bg-opacity-70 flex items-center justify-center">
                                    <span class="bg-orange-600 text-white px-4 py-2 rounded-lg font-bold text-sm">MAX IN CART</span>
                                </div>
                            @endif

                            {{-- Cart Badge --}}
                            @if($item->cart_qty > 0)
                                <div class="absolute top-2 right-2 w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <span class="text-gray-900 font-bold text-sm">{{ $item->cart_qty }}</span>
                                </div>
                            @endif

                            {{-- Category Badge --}}
                            <div class="absolute top-2 left-2">
                                <span class="px-2 py-1 rounded-md text-xs font-medium text-white {{ $categoryValue === 'Food' ? 'bg-orange-500' : ($categoryValue === 'Beverage' ? 'bg-blue-500' : 'bg-purple-500') }}">
                                    {{ $categoryValue }}
                                </span>
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="p-4">
                            <h3 class="font-semibold text-white text-sm mb-1 truncate">{{ $item->name }}</h3>
                            <div class="flex items-center justify-between">
                                <span class="text-yellow-400 font-bold">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                @if(!$isSoldOut)
                                    <span class="text-xs text-gray-500">Stock: {{ $item->max_stock }}</span>
                                @endif
                            </div>
                        </div>
                    </button>
                @empty
                    <div class="col-span-full py-16 text-center">
                        <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-gray-500 text-lg">No menu items found</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- RIGHT COLUMN: Cart Sidebar --}}
    <div class="w-96 bg-gray-800 border-l border-gray-700 flex flex-col h-screen">
        {{-- Cart Header --}}
        <div class="flex-shrink-0 p-6 border-b border-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-yellow-500 bg-opacity-20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-white">Current Order</h2>
                        <p class="text-gray-400 text-sm">{{ $this->cartCount }} items</p>
                    </div>
                </div>
                @if(count($cart) > 0)
                    <button 
                        wire:click="clearCart"
                        class="text-red-400 hover:text-red-300 text-sm font-medium"
                    >
                        Clear
                    </button>
                @endif
            </div>
        </div>

        {{-- Cart Items (Scrollable) --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-3">
            @forelse($cart as $index => $item)
                <div class="bg-gray-900 rounded-xl p-4" wire:key="cart-{{ $item['menu_id'] }}">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1 pr-2">
                            <h4 class="text-white font-medium text-sm">{{ $item['name'] }}</h4>
                            <p class="text-gray-400 text-xs">Rp {{ number_format($item['price'], 0, ',', '.') }} each</p>
                        </div>
                        <button 
                            wire:click="removeItemCompletely({{ $item['menu_id'] }})"
                            class="text-gray-500 hover:text-red-400 transition"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <button 
                                wire:click="removeFromCart({{ $item['menu_id'] }})"
                                class="w-8 h-8 bg-gray-800 hover:bg-gray-700 rounded-lg flex items-center justify-center text-white transition"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </button>
                            <span class="w-8 text-center text-white font-bold">{{ $item['qty'] }}</span>
                            <button 
                                wire:click="addToCart({{ $item['menu_id'] }})"
                                class="w-8 h-8 bg-yellow-500 hover:bg-yellow-400 rounded-lg flex items-center justify-center text-gray-900 transition"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </button>
                        </div>
                        <span class="text-yellow-400 font-bold text-sm">Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}</span>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center h-full text-center py-12">
                    <div class="w-16 h-16 bg-gray-900 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500 font-medium">Your cart is empty</p>
                    <p class="text-gray-600 text-sm mt-1">Add items from the menu</p>
                </div>
            @endforelse
        </div>

        {{-- Payment Section (Sticky Bottom) --}}
        <div class="flex-shrink-0 p-6 border-t border-gray-700 bg-gray-800">
            {{-- Subtotal --}}
            <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-700">
                <span class="text-gray-400">Subtotal</span>
                <span class="text-white font-medium">Rp {{ number_format($this->cartTotal, 0, ',', '.') }}</span>
            </div>

            {{-- Total --}}
            <div class="flex items-center justify-between mb-6">
                <span class="text-gray-300 text-lg font-medium">Total</span>
                <span class="text-2xl font-bold text-white">Rp {{ number_format($this->cartTotal, 0, ',', '.') }}</span>
            </div>

            {{-- Payment Method --}}
            <div class="mb-4">
                <label class="text-gray-400 text-sm mb-2 block">Payment Method</label>
                <div class="grid grid-cols-2 gap-2">
                    <button 
                        wire:click="$set('paymentMethod', 'cash')"
                        class="py-3 px-3 rounded-xl font-medium transition flex items-center justify-center gap-2 text-sm {{ $paymentMethod === 'cash' ? 'bg-yellow-500 text-gray-900' : 'bg-gray-900 text-gray-300 hover:bg-gray-700' }}"
                    >
                        💵 Cash
                    </button>
                    <button 
                        wire:click="$set('paymentMethod', 'qris')"
                        class="py-3 px-3 rounded-xl font-medium transition flex items-center justify-center gap-2 text-sm {{ $paymentMethod === 'qris' ? 'bg-yellow-500 text-gray-900' : 'bg-gray-900 text-gray-300 hover:bg-gray-700' }}"
                    >
                        📱 QRIS
                    </button>
                    <button 
                        wire:click="$set('paymentMethod', 'card')"
                        class="py-3 px-3 rounded-xl font-medium transition flex items-center justify-center gap-2 text-sm {{ $paymentMethod === 'card' ? 'bg-yellow-500 text-gray-900' : 'bg-gray-900 text-gray-300 hover:bg-gray-700' }}"
                    >
                        💳 Card
                    </button>
                    <button 
                        wire:click="$set('paymentMethod', 'e-wallet')"
                        class="py-3 px-3 rounded-xl font-medium transition flex items-center justify-center gap-2 text-sm {{ $paymentMethod === 'e-wallet' ? 'bg-yellow-500 text-gray-900' : 'bg-gray-900 text-gray-300 hover:bg-gray-700' }}"
                    >
                        📲 E-Wallet
                    </button>
                </div>
            </div>

            {{-- Checkout Button --}}
            <button 
                wire:click="checkout"
                wire:loading.attr="disabled"
                @if(count($cart) === 0) disabled @endif
                class="w-full py-4 bg-green-600 hover:bg-green-500 disabled:bg-gray-700 disabled:cursor-not-allowed text-white font-bold text-lg rounded-xl transition flex items-center justify-center gap-3"
            >
                <span wire:loading.remove wire:target="checkout">
                    ✓ Process Payment
                </span>
                <span wire:loading wire:target="checkout">
                    Processing...
                </span>
            </button>
        </div>
    </div>
</div>

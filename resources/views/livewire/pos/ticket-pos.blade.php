<div class="min-h-screen bg-gray-900" x-data="{ }" 
    @keydown.escape.window="$wire.closeShowtimeModal(); $wire.closeSeatModal(); $wire.closeCheckoutModal();">
    
    {{-- Header --}}
    <header class="bg-gray-800 border-b border-gray-700 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-900" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-lg font-bold text-white">Cinema</span>
                        <span class="text-lg font-bold text-amber-500">XXL</span>
                    </div>
                </a>
                
                {{-- Page Title --}}
                <div class="h-8 w-px bg-gray-700"></div>
                <h1 class="text-xl font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                    Ticket POS
                </h1>
            </div>

            <div class="flex items-center gap-4">
                {{-- Current Time --}}
                <div class="text-gray-400 text-sm" x-data="{ time: '' }" 
                    x-init="setInterval(() => time = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }), 1000)">
                    <span x-text="time"></span>
                </div>
                
                {{-- Cashier Info --}}
                <div class="flex items-center gap-2 text-sm">
                    <div class="w-8 h-8 bg-amber-500/20 rounded-full flex items-center justify-center">
                        <span class="text-amber-500 font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <span class="text-gray-300">{{ Auth::user()->name }}</span>
                </div>

                {{-- Back to Admin --}}
                <a href="{{ url('/admin') }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg text-sm text-gray-300 transition-colors">
                    ← Back to Admin
                </a>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="p-6">
        {{-- Search Bar --}}
        <div class="mb-6">
            <div class="relative max-w-md">
                <input type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search movie..."
                    class="w-full bg-gray-800 border border-gray-700 rounded-xl pl-10 pr-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>

        {{-- Movies Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-8 gap-4">
            @forelse($this->filteredMovies as $movie)
                <button 
                    wire:click="selectMovie({{ $movie['id'] }}, '{{ addslashes($movie['title']) }}', '{{ $movie['poster_path'] ?? '' }}')"
                    class="group relative bg-gray-800 rounded-xl overflow-hidden hover:ring-2 hover:ring-amber-500 transition-all duration-200 transform hover:scale-105"
                >
                    {{-- Poster --}}
                    @if(!empty($movie['poster_path']))
                        <img src="https://image.tmdb.org/t/p/w300{{ $movie['poster_path'] }}" 
                            alt="{{ $movie['title'] }}"
                            class="w-full aspect-[2/3] object-cover"
                            loading="lazy">
                    @else
                        <div class="w-full aspect-[2/3] bg-gray-700 flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                            </svg>
                        </div>
                    @endif
                    
                    {{-- Title Overlay --}}
                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/90 via-black/60 to-transparent p-3">
                        <h3 class="text-white text-sm font-medium line-clamp-2">{{ $movie['title'] }}</h3>
                    </div>

                    {{-- Hover Overlay --}}
                    <div class="absolute inset-0 bg-amber-500/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <span class="bg-amber-500 text-gray-900 px-4 py-2 rounded-lg font-semibold text-sm">
                            Select Showtime
                        </span>
                    </div>
                </button>
            @empty
                <div class="col-span-full text-center py-12">
                    <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                    </svg>
                    <p class="text-gray-500">No movies found</p>
                </div>
            @endforelse
        </div>
    </main>

    {{-- SHOWTIME SELECTION MODAL --}}
    @if($showShowtimeModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="closeShowtimeModal"></div>
        
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden" @click.stop>
                {{-- Header --}}
                <div class="bg-gray-900 px-6 py-4 border-b border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white">Select Showtime</h3>
                        <button wire:click="closeShowtimeModal" class="text-gray-400 hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Movie Info --}}
                <div class="p-6 border-b border-gray-700">
                    <div class="flex gap-4">
                        @if($selectedMoviePoster)
                            <img src="https://image.tmdb.org/t/p/w154{{ $selectedMoviePoster }}" 
                                alt="{{ $selectedMovieTitle }}"
                                class="w-20 h-28 object-cover rounded-lg">
                        @endif
                        <div>
                            <h4 class="text-xl font-bold text-white">{{ $selectedMovieTitle }}</h4>
                            <p class="text-gray-400 text-sm mt-1">Today's Showtimes</p>
                        </div>
                    </div>
                </div>

                {{-- Showtimes List --}}
                <div class="p-6 max-h-80 overflow-y-auto">
                    @forelse($this->availableShowtimes as $showtime)
                        <button 
                            wire:click="selectShowtime({{ $showtime->id }})"
                            class="w-full flex items-center justify-between p-4 bg-gray-700/50 hover:bg-gray-700 rounded-xl mb-3 transition-colors group"
                        >
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-amber-500/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <p class="text-white font-semibold">{{ $showtime->start_time->format('H:i') }}</p>
                                    <p class="text-gray-400 text-sm">{{ $showtime->studio->name }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-amber-500 font-semibold">Rp {{ number_format($showtime->price * $showtime->studio->getPriceMultiplier(), 0, ',', '.') }}</p>
                                <p class="text-gray-500 text-xs">{{ $showtime->studio->type->value }}</p>
                            </div>
                        </button>
                    @empty
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-gray-500">No showtimes available today</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- SEAT SELECTION MODAL --}}
    @if($showSeatModal && $selectedShowtime)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="closeSeatModal"></div>
        
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative bg-gray-800 rounded-2xl shadow-2xl max-w-4xl w-full overflow-hidden" @click.stop>
                {{-- Header --}}
                <div class="bg-gray-900 px-6 py-4 border-b border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-white">{{ $selectedMovieTitle }}</h3>
                            <p class="text-gray-400 text-sm">
                                {{ $selectedShowtime->studio->name }} • {{ $selectedShowtime->start_time->format('H:i') }}
                            </p>
                        </div>
                        <button wire:click="closeSeatModal" class="text-gray-400 hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Seat Map --}}
                <div class="p-6">
                    {{-- Screen --}}
                    <div class="mb-8">
                        <div class="h-2 bg-gradient-to-r from-transparent via-amber-500 to-transparent rounded-full mb-2"></div>
                        <p class="text-center text-gray-500 text-xs uppercase tracking-widest">Screen</p>
                    </div>

                    {{-- Seats Grid --}}
                    <div class="flex flex-col items-center gap-1 mb-6 overflow-x-auto pb-4">
                        @foreach($this->rowLetters as $row)
                            <div class="flex items-center gap-1">
                                <span class="w-6 text-xs text-gray-500 text-right mr-2">{{ $row }}</span>
                                @foreach($this->colNumbers as $col)
                                    @php
                                        $seat = $row . $col;
                                        $status = $this->getSeatStatus($seat);
                                    @endphp
                                    <button 
                                        wire:click="toggleSeat('{{ $seat }}')"
                                        @disabled($status === 'occupied')
                                        class="w-8 h-8 rounded-t-lg text-xs font-medium transition-all duration-150
                                            @if($status === 'occupied')
                                                bg-gray-700 text-gray-600 cursor-not-allowed
                                            @elseif($status === 'selected')
                                                bg-amber-500 text-gray-900 scale-110 shadow-lg shadow-amber-500/30
                                            @else
                                                bg-gray-600 text-gray-300 hover:bg-gray-500
                                            @endif
                                        "
                                    >
                                        {{ $col }}
                                    </button>
                                @endforeach
                                <span class="w-6 text-xs text-gray-500 text-left ml-2">{{ $row }}</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Legend --}}
                    <div class="flex items-center justify-center gap-6 mb-6">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-t-lg bg-gray-600"></div>
                            <span class="text-gray-400 text-sm">Available</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-t-lg bg-amber-500"></div>
                            <span class="text-gray-400 text-sm">Selected</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-t-lg bg-gray-700"></div>
                            <span class="text-gray-400 text-sm">Occupied</span>
                        </div>
                    </div>

                    {{-- Selection Summary --}}
                    @if(count($selectedSeats) > 0)
                        <div class="bg-gray-700/50 rounded-xl p-4 mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-gray-400">Selected Seats:</span>
                                <button wire:click="clearSelection" class="text-red-400 hover:text-red-300 text-sm">
                                    Clear All
                                </button>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($selectedSeats as $seat)
                                    <span class="px-3 py-1 bg-amber-500/20 text-amber-500 rounded-lg text-sm font-medium">
                                        {{ $seat }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="bg-gray-900 px-6 py-4 border-t border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm">Total: {{ count($selectedSeats) }} seat(s)</p>
                            <p class="text-2xl font-bold text-amber-500">Rp {{ number_format($this->totalPrice, 0, ',', '.') }}</p>
                        </div>
                        <button 
                            wire:click="proceedToCheckout"
                            @disabled(count($selectedSeats) === 0)
                            class="px-6 py-3 bg-amber-500 hover:bg-amber-400 disabled:bg-gray-700 disabled:cursor-not-allowed text-gray-900 font-semibold rounded-xl transition-colors"
                        >
                            Continue to Payment →
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- CHECKOUT MODAL --}}
    @if($showCheckoutModal && $selectedShowtime)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="closeCheckoutModal"></div>
        
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full overflow-hidden" @click.stop>
                {{-- Header --}}
                <div class="bg-amber-500 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900">Checkout</h3>
                    <p class="text-gray-800 text-sm">Complete payment</p>
                </div>

                {{-- Order Summary --}}
                <div class="p-6 border-b border-gray-700">
                    <h4 class="text-white font-semibold mb-4">Order Summary</h4>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Movie</span>
                            <span class="text-white">{{ $selectedMovieTitle }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Showtime</span>
                            <span class="text-white">{{ $selectedShowtime->start_time->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Studio</span>
                            <span class="text-white">{{ $selectedShowtime->studio->name }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Seats</span>
                            <span class="text-amber-500 font-semibold">{{ implode(', ', $selectedSeats) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">Price per Seat</span>
                            <span class="text-white">Rp {{ number_format($this->ticketPrice, 0, ',', '.') }}</span>
                        </div>
                        <hr class="border-gray-700">
                        <div class="flex justify-between">
                            <span class="text-white font-semibold">Total ({{ count($selectedSeats) }} seats)</span>
                            <span class="text-amber-500 font-bold text-xl">Rp {{ number_format($this->totalPrice, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="p-6">
                    <h4 class="text-white font-semibold mb-4">Payment Method</h4>
                    
                    <div class="grid grid-cols-3 gap-3">
                        <button 
                            wire:click="$set('paymentMethod', 'cash')"
                            class="p-4 rounded-xl border-2 transition-colors {{ $paymentMethod === 'cash' ? 'border-amber-500 bg-amber-500/10' : 'border-gray-700 hover:border-gray-600' }}"
                        >
                            <svg class="w-8 h-8 mx-auto mb-2 {{ $paymentMethod === 'cash' ? 'text-amber-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="text-sm {{ $paymentMethod === 'cash' ? 'text-amber-500' : 'text-gray-400' }}">Cash</span>
                        </button>
                        
                        <button 
                            wire:click="$set('paymentMethod', 'debit')"
                            class="p-4 rounded-xl border-2 transition-colors {{ $paymentMethod === 'debit' ? 'border-amber-500 bg-amber-500/10' : 'border-gray-700 hover:border-gray-600' }}"
                        >
                            <svg class="w-8 h-8 mx-auto mb-2 {{ $paymentMethod === 'debit' ? 'text-amber-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            <span class="text-sm {{ $paymentMethod === 'debit' ? 'text-amber-500' : 'text-gray-400' }}">Debit</span>
                        </button>
                        
                        <button 
                            wire:click="$set('paymentMethod', 'qris')"
                            class="p-4 rounded-xl border-2 transition-colors {{ $paymentMethod === 'qris' ? 'border-amber-500 bg-amber-500/10' : 'border-gray-700 hover:border-gray-600' }}"
                        >
                            <svg class="w-8 h-8 mx-auto mb-2 {{ $paymentMethod === 'qris' ? 'text-amber-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                            <span class="text-sm {{ $paymentMethod === 'qris' ? 'text-amber-500' : 'text-gray-400' }}">QRIS</span>
                        </button>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="bg-gray-900 px-6 py-4 border-t border-gray-700 flex gap-3">
                    <button 
                        wire:click="backToSeats"
                        class="flex-1 px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-xl transition-colors"
                    >
                        ← Back
                    </button>
                    <button 
                        wire:click="processPayment"
                        class="flex-1 px-4 py-3 bg-green-600 hover:bg-green-500 text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Process Payment
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- SUCCESS MODAL --}}
    @if($showSuccessModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm"></div>
        
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full overflow-hidden" @click.stop>
                {{-- Success Header --}}
                <div class="bg-green-500 px-6 py-8 text-center">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white">Payment Successful!</h3>
                    <p class="text-green-100 mt-1">Transaction completed</p>
                </div>

                {{-- Details --}}
                <div class="p-6">
                    <div class="bg-gray-700/50 rounded-xl p-4 mb-4">
                        <p class="text-gray-400 text-sm mb-1">Group Code</p>
                        <p class="text-amber-500 font-mono font-bold text-lg">{{ $lastGroupCode }}</p>
                    </div>

                    <div class="bg-gray-700/50 rounded-xl p-4 mb-4">
                        <p class="text-gray-400 text-sm mb-2">Booking Codes</p>
                        <div class="space-y-1">
                            @foreach($lastBookingCodes as $code)
                                <p class="text-white font-mono text-sm">{{ $code }}</p>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Total Paid</span>
                        <span class="text-2xl font-bold text-green-500">Rp {{ number_format($lastTotal, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="bg-gray-900 px-6 py-4 border-t border-gray-700 flex gap-3">
                    <button 
                        wire:click="newTransaction"
                        class="flex-1 px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-xl transition-colors"
                    >
                        New Transaction
                    </button>
                    <a 
                        href="{{ route('ticket.print-stub', ['booking_code' => $lastBookingCodes[0] ?? '']) }}"
                        target="_blank"
                        class="flex-1 px-4 py-3 bg-amber-500 hover:bg-amber-400 text-gray-900 font-semibold rounded-xl transition-colors flex items-center justify-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Print Tickets
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Toast Notifications --}}
    <div 
        x-data="{ 
            toasts: [],
            addToast(type, message) {
                const id = Date.now();
                this.toasts.push({ id, type, message });
                setTimeout(() => this.removeToast(id), 3000);
            },
            removeToast(id) {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }
        }"
        @show-toast.window="addToast($event.detail.type, $event.detail.message)"
        class="fixed bottom-4 right-4 z-50 space-y-2"
    >
        <template x-for="toast in toasts" :key="toast.id">
            <div 
                x-show="true"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-8"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                :class="{
                    'bg-green-600': toast.type === 'success',
                    'bg-red-600': toast.type === 'error',
                    'bg-amber-600': toast.type === 'warning'
                }"
                class="px-4 py-3 rounded-lg text-white text-sm shadow-lg flex items-center gap-2"
            >
                <template x-if="toast.type === 'success'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </template>
                <template x-if="toast.type === 'error'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </template>
                <span x-text="toast.message"></span>
            </div>
        </template>
    </div>
</div>

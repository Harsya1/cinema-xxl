<div class="min-h-screen bg-gray-900 pt-24">
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
            timeout = setTimeout(() => show = false, 4000);
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
            <button @click="show = false" class="ml-2 hover:opacity-75">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    {{-- Success Modal --}}
    @if($showSuccessModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm">
        <div class="bg-gray-800 rounded-2xl p-8 max-w-md w-full mx-4 text-center animate-bounce-in">
            {{-- Success Icon --}}
            <div class="w-20 h-20 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            
            <h3 class="text-2xl font-bold text-white mb-2">Booking Successful!</h3>
            <p class="text-gray-400 mb-6">Your tickets have been reserved</p>
            
            {{-- Booking Code --}}
            <div class="bg-gray-900 rounded-xl p-4 mb-4">
                <p class="text-gray-400 text-sm mb-1">Booking Code</p>
                <p class="text-xl font-mono font-bold text-yellow-400">{{ $lastBookingCode }}</p>
            </div>
            
            {{-- Movie & Showtime Info --}}
            <div class="bg-gray-900 rounded-xl p-4 mb-4 text-left">
                <p class="text-white font-semibold">{{ $showtime->movie_title }}</p>
                <p class="text-gray-400 text-sm">{{ $showtime->studio->name }} • {{ $showtime->start_time->format('D, d M Y • H:i') }}</p>
            </div>
            
            {{-- Total & Points --}}
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="bg-gray-900 rounded-xl p-4">
                    <p class="text-gray-400 text-sm mb-1">Total Amount</p>
                    <p class="text-2xl font-bold text-green-400">Rp {{ number_format($lastBookingTotal, 0, ',', '.') }}</p>
                </div>
                <div class="bg-gray-900 rounded-xl p-4">
                    <p class="text-gray-400 text-sm mb-1">Points Earned</p>
                    <p class="text-2xl font-bold text-yellow-400">+{{ $lastPointsEarned }} pts</p>
                </div>
            </div>

            <p class="text-gray-500 text-sm mb-6">Please proceed to payment at the cashier before the show starts.</p>
            
            {{-- Actions --}}
            <div class="flex gap-3">
                <button 
                    wire:click="closeSuccessModal(false)"
                    class="flex-1 py-3 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-xl transition"
                >
                    Book More
                </button>
                <button 
                    wire:click="closeSuccessModal(true)"
                    class="flex-1 py-3 bg-yellow-500 hover:bg-yellow-400 text-gray-900 font-bold rounded-xl transition"
                >
                    View My Tickets
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Header --}}
    <div class="bg-gray-800 border-b border-gray-700">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center gap-4">
                <button 
                    wire:click="goBack"
                    class="p-2 hover:bg-gray-700 rounded-lg transition"
                >
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <div>
                    <h1 class="text-xl font-bold text-white">Select Your Seats</h1>
                    <p class="text-gray-400 text-sm">{{ $showtime->movie_title }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content: Split Layout --}}
    <div class="container mx-auto px-4 py-6">
        <div class="flex flex-col lg:flex-row gap-6">
            
            {{-- LEFT: Seat Map --}}
            <div class="flex-1 bg-gray-800 rounded-2xl p-6">
                {{-- Screen Indicator --}}
                <div class="mb-8">
                    <div class="relative">
                        <div class="h-2 bg-gradient-to-r from-transparent via-yellow-400 to-transparent rounded-full mx-8"></div>
                        <div class="h-8 bg-gradient-to-b from-yellow-400/20 to-transparent rounded-b-full mx-4 mt-1"></div>
                        <p class="text-center text-gray-500 text-sm font-medium tracking-widest mt-2">SCREEN</p>
                    </div>
                </div>

                {{-- Seat Grid Container --}}
                <div class="overflow-x-auto pb-4">
                    <div class="min-w-fit mx-auto">
                        {{-- Column Numbers --}}
                        <div class="flex items-center justify-center mb-2 {{ $this->isPremier ? 'gap-3' : 'gap-1' }}">
                            <div class="w-8"></div> {{-- Spacer for row letters --}}
                            @foreach($this->colNumbers as $col)
                                <div class="w-10 h-6 flex items-center justify-center text-gray-500 text-xs font-medium">
                                    {{ $col }}
                                </div>
                            @endforeach
                        </div>

                        {{-- Seat Rows --}}
                        @foreach($this->rowLetters as $row)
                            <div class="flex items-center justify-center mb-1 {{ $this->isPremier ? 'gap-3 mb-3' : 'gap-1' }}">
                                {{-- Row Letter --}}
                                <div class="w-8 h-10 flex items-center justify-center text-gray-500 text-sm font-medium">
                                    {{ $row }}
                                </div>
                                
                                {{-- Seats in Row --}}
                                @foreach($this->colNumbers as $col)
                                    @php
                                        $seatNumber = $row . $col;
                                        $status = $this->getSeatStatus($seatNumber);
                                    @endphp
                                    
                                    <button
                                        wire:click="toggleSeat('{{ $seatNumber }}')"
                                        wire:key="seat-{{ $seatNumber }}"
                                        @if($status === 'occupied') disabled @endif
                                        class="relative transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 focus:ring-offset-gray-800 rounded-lg
                                            {{ $status === 'occupied' ? 'cursor-not-allowed' : 'cursor-pointer hover:scale-110' }}"
                                        title="{{ $seatNumber }}"
                                    >
                                        @if($this->isPremier)
                                            {{-- Premier Seat (Wide/Luxury) --}}
                                            <svg class="w-12 h-10 {{ 
                                                $status === 'occupied' ? 'text-red-500/60' : 
                                                ($status === 'selected' ? 'text-yellow-400' : 'text-gray-600 hover:text-gray-500') 
                                            }}" viewBox="0 0 48 40" fill="currentColor">
                                                {{-- Armchair shape --}}
                                                <rect x="2" y="8" width="44" height="24" rx="4" />
                                                <rect x="0" y="6" width="8" height="28" rx="2" /> {{-- Left arm --}}
                                                <rect x="40" y="6" width="8" height="28" rx="2" /> {{-- Right arm --}}
                                                <rect x="6" y="32" width="8" height="6" rx="1" /> {{-- Left leg --}}
                                                <rect x="34" y="32" width="8" height="6" rx="1" /> {{-- Right leg --}}
                                            </svg>
                                        @else
                                            {{-- Regular/3D Seat --}}
                                            <svg class="w-10 h-10 {{ 
                                                $status === 'occupied' ? 'text-red-500/60' : 
                                                ($status === 'selected' ? 'text-yellow-400' : 'text-gray-600 hover:text-gray-500') 
                                            }}" viewBox="0 0 40 40" fill="currentColor">
                                                {{-- Regular seat shape --}}
                                                <rect x="4" y="8" width="32" height="20" rx="4" />
                                                <rect x="6" y="28" width="6" height="8" rx="1" />
                                                <rect x="28" y="28" width="6" height="8" rx="1" />
                                            </svg>
                                        @endif
                                        
                                        {{-- Selection indicator --}}
                                        @if($status === 'selected')
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        
                                        {{-- Occupied X indicator --}}
                                        @if($status === 'occupied')
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </button>
                                @endforeach
                                
                                {{-- Row Letter (Right side) --}}
                                <div class="w-8 h-10 flex items-center justify-center text-gray-500 text-sm font-medium">
                                    {{ $row }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Legend --}}
                <div class="flex flex-wrap items-center justify-center gap-6 mt-8 pt-6 border-t border-gray-700">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-gray-600 rounded-lg"></div>
                        <span class="text-gray-400 text-sm">Available</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-yellow-400 rounded-lg"></div>
                        <span class="text-gray-400 text-sm">Selected</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-red-500/60 rounded-lg"></div>
                        <span class="text-gray-400 text-sm">Occupied</span>
                    </div>
                </div>

                {{-- Studio Info Badge --}}
                <div class="flex justify-center mt-6">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 rounded-full">
                        <span class="text-gray-400 text-sm">{{ $this->studio->name }}</span>
                        <span class="px-2 py-0.5 rounded text-xs font-medium {{ 
                            $this->isPremier ? 'bg-yellow-500/20 text-yellow-400' : 
                            ($this->is3D ? 'bg-blue-500/20 text-blue-400' : 'bg-gray-500/20 text-gray-400') 
                        }}">
                            {{ $this->studio->type->label() }}
                        </span>
                        <span class="text-gray-500 text-sm">{{ $this->studio->rows }} × {{ $this->studio->cols }} seats</span>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Booking Summary --}}
            <div class="lg:w-96">
                <div class="bg-gray-800 rounded-2xl overflow-hidden sticky top-4">
                    {{-- Movie Header --}}
                    <div class="flex gap-4 p-4 border-b border-gray-700">
                        @if($showtime->poster_path)
                            <img 
                                src="https://image.tmdb.org/t/p/w200{{ $showtime->poster_path }}" 
                                alt="{{ $showtime->movie_title }}"
                                class="w-20 h-28 object-cover rounded-lg"
                            >
                        @else
                            <div class="w-20 h-28 bg-gray-700 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"></path>
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1">
                            <h2 class="text-white font-bold text-lg leading-tight mb-1">{{ $showtime->movie_title }}</h2>
                            <p class="text-gray-400 text-sm mb-2">{{ $this->studio->name }}</p>
                            <div class="flex items-center gap-2 text-sm">
                                <span class="px-2 py-0.5 rounded text-xs font-medium {{ 
                                    $this->isPremier ? 'bg-yellow-500/20 text-yellow-400' : 
                                    ($this->is3D ? 'bg-blue-500/20 text-blue-400' : 'bg-gray-500/20 text-gray-400') 
                                }}">
                                    {{ $this->studio->type->label() }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Showtime Details --}}
                    <div class="p-4 border-b border-gray-700">
                        <div class="flex items-center gap-3 text-gray-300">
                            <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>{{ $showtime->start_time->format('l, d F Y') }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-300 mt-2">
                            <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ $showtime->start_time->format('H:i') }} - {{ $showtime->end_time->format('H:i') }}</span>
                        </div>
                    </div>

                    {{-- Selected Seats --}}
                    <div class="p-4 border-b border-gray-700">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-gray-400 font-medium">Selected Seats</h3>
                            @if(count($selectedSeats) > 0)
                                <button 
                                    wire:click="clearSelection"
                                    class="text-red-400 hover:text-red-300 text-sm"
                                >
                                    Clear
                                </button>
                            @endif
                        </div>
                        
                        @if(count($selectedSeats) > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($selectedSeats as $seat)
                                    <span class="px-3 py-1.5 bg-yellow-500/20 text-yellow-400 rounded-lg text-sm font-medium">
                                        {{ $seat }}
                                    </span>
                                @endforeach
                            </div>
                            <p class="text-gray-500 text-sm mt-2">{{ count($selectedSeats) }} of {{ $maxSeatsPerBooking }} max seats</p>
                        @else
                            <p class="text-gray-500 text-sm">No seats selected</p>
                            <p class="text-gray-600 text-xs mt-1">Click on available seats to select</p>
                        @endif
                    </div>

                    {{-- Price Breakdown --}}
                    <div class="p-4 border-b border-gray-700">
                        <h3 class="text-gray-400 font-medium mb-3">Price Details</h3>
                        
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between text-gray-400">
                                <span>Base Price</span>
                                <span>Rp {{ number_format($showtime->price, 0, ',', '.') }}</span>
                            </div>
                            @if($this->studio->getPriceMultiplier() > 1)
                                <div class="flex justify-between text-gray-400">
                                    <span>{{ $this->studio->type->label() }} Surcharge</span>
                                    <span>×{{ number_format($this->studio->getPriceMultiplier(), 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-gray-300">
                                <span>Ticket Price</span>
                                <span>Rp {{ number_format($this->ticketPrice, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-gray-300">
                                <span>Quantity</span>
                                <span>× {{ count($selectedSeats) }}</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-700">
                            <span class="text-gray-300 font-medium">Total</span>
                            <span class="text-2xl font-bold text-white">Rp {{ number_format($this->totalPrice, 0, ',', '.') }}</span>
                        </div>

                        {{-- Points to earn --}}
                        @if(count($selectedSeats) > 0)
                            <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-700/50">
                                <span class="text-gray-400 text-sm flex items-center gap-2">
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    Points Earned
                                </span>
                                <span class="text-yellow-400 font-semibold">+{{ $this->pointsToEarn }} pts</span>
                            </div>
                        @endif
                    </div>

                    {{-- Action Button --}}
                    <div class="p-4">
                        @auth
                            <button 
                                wire:click="bookTickets"
                                wire:loading.attr="disabled"
                                @if(count($selectedSeats) === 0) disabled @endif
                                class="w-full py-4 bg-yellow-500 hover:bg-yellow-400 disabled:bg-gray-700 disabled:cursor-not-allowed text-gray-900 disabled:text-gray-500 font-bold text-lg rounded-xl transition flex items-center justify-center gap-3"
                            >
                                <span wire:loading.remove wire:target="bookTickets">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                    </svg>
                                    Book {{ count($selectedSeats) }} Ticket{{ count($selectedSeats) !== 1 ? 's' : '' }}
                                </span>
                                <span wire:loading wire:target="bookTickets" class="flex items-center gap-2">
                                    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing...
                                </span>
                            </button>
                        @else
                            <a 
                                href="{{ route('login') }}"
                                class="w-full py-4 bg-yellow-500 hover:bg-yellow-400 text-gray-900 font-bold text-lg rounded-xl transition flex items-center justify-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                                Login to Book
                            </a>
                        @endauth

                        <p class="text-gray-500 text-xs text-center mt-3">
                            By booking, you agree to our terms and conditions
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Custom Animation --}}
    <style>
        @keyframes bounce-in {
            0% { transform: scale(0.9); opacity: 0; }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-bounce-in {
            animation: bounce-in 0.3s ease-out forwards;
        }
    </style>
</div>

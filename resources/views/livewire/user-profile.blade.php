<div class="min-h-screen bg-gray-900 pt-24 pb-8 md:pt-28 md:pb-12">
    <div class="container mx-auto px-4 md:px-8 lg:px-16">
        
        {{-- Profile Header --}}
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 md:p-8 mb-8 border border-gray-700/50">
            <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
                {{-- Avatar --}}
                <div class="relative">
                    <img 
                        src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=128&background=f59e0b&color=111827&bold=true"
                        alt="{{ $user->name }}"
                        class="w-24 h-24 md:w-32 md:h-32 rounded-full ring-4 ring-amber-500/30"
                    >
                    <div class="absolute -bottom-2 -right-2 bg-amber-500 rounded-full p-2">
                        <svg class="w-5 h-5 text-gray-900" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                
                {{-- User Info --}}
                <div class="flex-1 text-center md:text-left">
                    <h1 class="text-2xl md:text-3xl font-bold text-white mb-1">{{ $user->name }}</h1>
                    <p class="text-gray-400 mb-4">{{ $user->email }}</p>
                    
                    {{-- Points Badge --}}
                    <div class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-500/20 to-yellow-500/20 px-4 py-2 rounded-full border border-amber-500/30">
                        <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span class="text-amber-400 font-bold">{{ number_format($user->points) }}</span>
                        <span class="text-amber-400/80 text-sm">XXL Points</span>
                    </div>
                </div>

                {{-- Quick Stats --}}
                <div class="flex gap-6 md:gap-8">
                    <div class="text-center">
                        <p class="text-2xl md:text-3xl font-bold text-white">{{ $activeBookings->count() }}</p>
                        <p class="text-gray-400 text-sm">Active Tickets</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl md:text-3xl font-bold text-white">{{ $watchlist->count() }}</p>
                        <p class="text-gray-400 text-sm">Watchlist</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Navigation Tabs --}}
        <div class="flex gap-2 mb-8 overflow-x-auto pb-2">
            <button 
                wire:click="setTab('tickets')"
                class="flex items-center gap-2 px-6 py-3 rounded-xl font-semibold transition-all duration-300 whitespace-nowrap {{ $activeTab === 'tickets' ? 'bg-amber-500 text-gray-900' : 'bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white' }}"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                </svg>
                My Tickets
            </button>
            <button 
                wire:click="setTab('watchlist')"
                class="flex items-center gap-2 px-6 py-3 rounded-xl font-semibold transition-all duration-300 whitespace-nowrap {{ $activeTab === 'watchlist' ? 'bg-amber-500 text-gray-900' : 'bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white' }}"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                </svg>
                Watchlist
            </button>
            <button 
                wire:click="setTab('settings')"
                class="flex items-center gap-2 px-6 py-3 rounded-xl font-semibold transition-all duration-300 whitespace-nowrap {{ $activeTab === 'settings' ? 'bg-amber-500 text-gray-900' : 'bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white' }}"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Settings
            </button>
        </div>

        {{-- Tab Content --}}
        <div class="min-h-[400px]">
            
            {{-- My Tickets Tab --}}
            @if($activeTab === 'tickets')
            <div class="space-y-8">
                {{-- Active Tickets --}}
                <div>
                    <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                        Active Tickets
                    </h2>
                    
                    @if($activeBookings->count() > 0)
                    <div class="grid gap-4">
                        @foreach($activeBookings as $booking)
                        <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-4 md:p-6 border border-gray-700/50 hover:border-amber-500/30 transition-all duration-300">
                            <div class="flex flex-col md:flex-row gap-4">
                                {{-- Movie Poster Placeholder --}}
                                <div class="w-full md:w-24 h-36 bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                                    </svg>
                                </div>
                                
                                {{-- Booking Details --}}
                                <div class="flex-1">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <h3 class="text-lg font-bold text-white">{{ $booking->showtime->movie_title ?? 'Movie Title' }}</h3>
                                            <p class="text-amber-400 text-sm font-mono">{{ $booking->booking_code }}</p>
                                        </div>
                                        <span class="px-3 py-1 bg-green-500/20 text-green-400 text-xs font-semibold rounded-full">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 text-sm">
                                        <div>
                                            <p class="text-gray-500">Date</p>
                                            <p class="text-white font-medium">{{ $booking->showtime->start_time->format('d M Y') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">Time</p>
                                            <p class="text-white font-medium">{{ $booking->showtime->start_time->format('H:i') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">Studio</p>
                                            <p class="text-white font-medium">{{ $booking->showtime->studio->name ?? 'Studio' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">Seat</p>
                                            <p class="text-white font-medium">{{ $booking->seat_number }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- QR Code Button --}}
                                <div class="flex items-center">
                                    <button class="flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-gray-900 font-semibold px-4 py-2 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                        </svg>
                                        Show QR
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="bg-gray-800/30 rounded-xl p-8 text-center border border-dashed border-gray-700">
                        <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                        <h3 class="text-lg font-semibold text-white mb-2">No Active Tickets</h3>
                        <p class="text-gray-400 mb-4">Book your next movie experience!</p>
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-gray-900 font-semibold px-6 py-3 rounded-lg transition-colors">
                            Browse Movies
                        </a>
                    </div>
                    @endif
                </div>

                {{-- Booking History --}}
                <div>
                    <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        History
                    </h2>
                    
                    @if($pastBookings->count() > 0)
                    <div class="bg-gray-800/30 rounded-xl overflow-hidden border border-gray-700/50">
                        <table class="w-full">
                            <thead class="bg-gray-800/50">
                                <tr>
                                    <th class="text-left text-gray-400 text-sm font-medium px-4 py-3">Movie</th>
                                    <th class="text-left text-gray-400 text-sm font-medium px-4 py-3 hidden md:table-cell">Date</th>
                                    <th class="text-left text-gray-400 text-sm font-medium px-4 py-3 hidden md:table-cell">Seat</th>
                                    <th class="text-left text-gray-400 text-sm font-medium px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700/50">
                                @foreach($pastBookings as $booking)
                                <tr class="hover:bg-gray-800/30 transition-colors">
                                    <td class="px-4 py-3">
                                        <p class="text-white font-medium">{{ $booking->showtime->movie_title ?? 'Movie' }}</p>
                                        <p class="text-gray-500 text-sm md:hidden">{{ $booking->showtime->start_time->format('d M Y') }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-gray-400 hidden md:table-cell">{{ $booking->showtime->start_time->format('d M Y, H:i') }}</td>
                                    <td class="px-4 py-3 text-gray-400 hidden md:table-cell">{{ $booking->seat_number }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $booking->status === 'redeemed' ? 'bg-blue-500/20 text-blue-400' : 'bg-gray-500/20 text-gray-400' }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="bg-gray-800/30 rounded-xl p-6 text-center border border-dashed border-gray-700">
                        <p class="text-gray-400">No booking history yet.</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Watchlist Tab --}}
            @if($activeTab === 'watchlist')
            <div>
                <h2 class="text-xl font-bold text-white mb-6">My Watchlist</h2>
                
                @if($watchlist->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($watchlist as $item)
                    <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl overflow-hidden border border-gray-700/50 hover:border-amber-500/30 transition-all duration-300 group">
                        {{-- Movie Poster --}}
                        <div class="relative aspect-[2/3] overflow-hidden">
                            <img 
                                src="{{ $item->poster_url }}" 
                                alt="{{ $item->title }}"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                loading="lazy"
                            >
                            {{-- Overlay on Hover --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            
                            {{-- Rating Badge --}}
                            <div class="absolute top-3 right-3 bg-gray-900/80 backdrop-blur-sm px-2 py-1 rounded-md flex items-center gap-1">
                                <svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span class="text-white text-xs font-medium">{{ $item->vote_average }}</span>
                            </div>

                            {{-- Remove Button --}}
                            <button 
                                wire:click="removeFromWatchlist({{ $item->id }})"
                                wire:confirm="Remove '{{ $item->title }}' from your watchlist?"
                                class="absolute top-3 left-3 bg-red-500/80 hover:bg-red-500 backdrop-blur-sm p-2 rounded-lg opacity-0 group-hover:opacity-100 transition-all duration-300"
                            >
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Movie Info --}}
                        <div class="p-4">
                            <h3 class="text-white font-bold text-lg mb-1 truncate">{{ $item->title }}</h3>
                            @if($item->release_year)
                            <p class="text-amber-400 text-sm mb-3">{{ $item->release_year }}</p>
                            @endif
                            
                            {{-- Overview with line-clamp-3 --}}
                            @if($item->overview)
                            <p class="text-gray-400 text-sm line-clamp-3">{{ $item->overview }}</p>
                            @else
                            <p class="text-gray-500 text-sm italic">No overview available.</p>
                            @endif

                            {{-- Action Button --}}
                            <a href="#" class="mt-4 w-full inline-flex items-center justify-center gap-2 bg-amber-500 hover:bg-amber-600 text-gray-900 font-semibold px-4 py-2 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                </svg>
                                Book Now
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="bg-gray-800/30 rounded-xl p-8 text-center border border-dashed border-gray-700">
                    <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-white mb-2">Your Watchlist is Empty</h3>
                    <p class="text-gray-400 mb-4">Save movies you want to watch later!</p>
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-gray-900 font-semibold px-6 py-3 rounded-lg transition-colors">
                        Discover Movies
                    </a>
                </div>
                @endif
            </div>
            @endif

            {{-- Settings Tab --}}
            @if($activeTab === 'settings')
            <div class="grid gap-8 lg:grid-cols-2">
                {{-- Profile Settings --}}
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 border border-gray-700/50">
                    <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profile Information
                    </h2>
                    
                    <form wire:submit="updateProfile" class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Full Name</label>
                            <input 
                                type="text" 
                                id="name" 
                                wire:model="name"
                                class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                                placeholder="Your name"
                            >
                            @error('name') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                            <input 
                                type="email" 
                                id="email" 
                                wire:model="email"
                                class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                                placeholder="your@email.com"
                            >
                            @error('email') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-gray-300 mb-2">Phone Number</label>
                            <input 
                                type="tel" 
                                id="phone_number" 
                                wire:model="phone_number"
                                class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                                placeholder="08123456789"
                            >
                            @error('phone_number') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-300 mb-2">Date of Birth</label>
                            <input 
                                type="date" 
                                id="date_of_birth" 
                                wire:model="date_of_birth"
                                class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                            >
                            @error('date_of_birth') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <button 
                            type="submit"
                            class="w-full bg-amber-500 hover:bg-amber-600 text-gray-900 font-semibold px-6 py-3 rounded-lg transition-colors flex items-center justify-center gap-2"
                        >
                            <svg wire:loading wire:target="updateProfile" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="updateProfile">Save Changes</span>
                            <span wire:loading wire:target="updateProfile">Saving...</span>
                        </button>
                    </form>
                </div>

                {{-- Password Settings --}}
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl p-6 border border-gray-700/50">
                    <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Change Password
                    </h2>
                    
                    <form wire:submit="updatePassword" class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-300 mb-2">Current Password</label>
                            <input 
                                type="password" 
                                id="current_password" 
                                wire:model="current_password"
                                class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                                placeholder="••••••••"
                            >
                            @error('current_password') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-300 mb-2">New Password</label>
                            <input 
                                type="password" 
                                id="new_password" 
                                wire:model="new_password"
                                class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                                placeholder="••••••••"
                            >
                            @error('new_password') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="new_password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">Confirm New Password</label>
                            <input 
                                type="password" 
                                id="new_password_confirmation" 
                                wire:model="new_password_confirmation"
                                class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                                placeholder="••••••••"
                            >
                        </div>

                        <button 
                            type="submit"
                            class="w-full bg-gray-700 hover:bg-gray-600 text-white font-semibold px-6 py-3 rounded-lg transition-colors flex items-center justify-center gap-2"
                        >
                            <svg wire:loading wire:target="updatePassword" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="updatePassword">Update Password</span>
                            <span wire:loading wire:target="updatePassword">Updating...</span>
                        </button>
                    </form>
                </div>

                {{-- Danger Zone --}}
                <div class="lg:col-span-2 bg-red-900/20 backdrop-blur-sm rounded-xl p-6 border border-red-500/30">
                    <h2 class="text-xl font-bold text-red-400 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Danger Zone
                    </h2>
                    <p class="text-gray-400 mb-4">Once you delete your account, there is no going back. Please be certain.</p>
                    <button class="bg-red-500/20 hover:bg-red-500/30 text-red-400 font-semibold px-6 py-3 rounded-lg transition-colors border border-red-500/30">
                        Delete Account
                    </button>
                </div>
            </div>
            @endif
            
        </div>
    </div>
</div>

<div class="min-h-screen bg-gray-900 pt-24">
    {{-- Page Header --}}
    <section class="px-4 md:px-8 lg:px-16 pb-8">
        <div class="container mx-auto">
            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-sm text-gray-400 mb-6">
                <a href="/" class="hover:text-amber-400 transition-colors">Home</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-white">Coming Soon</span>
            </nav>

            {{-- Title --}}
            <div class="flex items-center gap-4">
                <div class="w-1 h-12 bg-amber-500 rounded-full"></div>
                <div>
                    <h1 class="text-4xl md:text-5xl font-bold text-white">Coming Soon</h1>
                    <p class="text-gray-400 mt-2">Upcoming movies you don't want to miss</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Movies Grid --}}
    <section class="px-4 md:px-8 lg:px-16 py-8">
        <div class="container mx-auto">
            @if($loading)
            {{-- Loading Skeleton --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6">
                @for($i = 0; $i < 10; $i++)
                <div class="animate-pulse">
                    <div class="bg-gray-800 rounded-xl aspect-[2/3]"></div>
                    <div class="mt-3 h-4 bg-gray-800 rounded w-3/4"></div>
                    <div class="mt-2 h-3 bg-gray-800 rounded w-1/2"></div>
                </div>
                @endfor
            </div>
            @elseif(count($movies) === 0)
            {{-- Empty State --}}
            <div class="text-center py-20">
                <svg class="w-24 h-24 mx-auto text-gray-700 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                </svg>
                <h3 class="text-xl font-semibold text-white mb-2">No Upcoming Movies</h3>
                <p class="text-gray-400">Check back later for new releases</p>
            </div>
            @else
            {{-- Movie Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6">
                @foreach($movies as $movie)
                <a href="{{ route('movie.details', $movie['id']) }}" class="group cursor-pointer">
                    {{-- Movie Card --}}
                    <div class="relative overflow-hidden rounded-xl bg-gray-800 aspect-[2/3] shadow-lg">
                        {{-- Poster Image --}}
                        <img 
                            src="{{ $movie['poster_path'] ?? 'https://via.placeholder.com/500x750?text=No+Image' }}" 
                            alt="{{ $movie['title'] }}"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                            loading="lazy"
                        >
                        
                        {{-- Hover Overlay --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="absolute bottom-0 left-0 right-0 p-4">
                                {{-- Title --}}
                                <h3 class="text-white font-bold text-lg mb-2 line-clamp-2">
                                    {{ $movie['title'] }}
                                </h3>
                                
                                {{-- Release Date --}}
                                @if($movie['release_date'])
                                <div class="flex items-center gap-1 text-amber-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="font-medium">{{ \Carbon\Carbon::parse($movie['release_date'])->format('d M Y') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Watchlist Button --}}
                        <div class="absolute bottom-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-20" onclick="event.preventDefault(); event.stopPropagation();">
                            <livewire:watchlist-button 
                                :tmdb-id="$movie['id']"
                                :title="$movie['title']"
                                :poster-path="$movie['poster_path_raw'] ?? null"
                                :overview="$movie['overview']"
                                :vote-average="$movie['vote_average']"
                                :release-date="$movie['release_date']"
                                :wire:key="'watchlist-cs-'.$movie['id']"
                            />
                        </div>

                        {{-- Release Date Badge (Always Visible) --}}
                        @if($movie['release_date'])
                        <div class="absolute top-3 right-3 bg-amber-500 px-2 py-1 rounded-md">
                            <span class="text-gray-900 text-xs font-bold">
                                {{ \Carbon\Carbon::parse($movie['release_date'])->format('d M') }}
                            </span>
                        </div>
                        @endif

                        {{-- Coming Soon Ribbon --}}
                        <div class="absolute top-3 left-3">
                            <span class="bg-gray-900/80 backdrop-blur-sm text-white text-xs font-medium px-2 py-1 rounded-md">
                                Coming Soon
                            </span>
                        </div>
                    </div>

                    {{-- Movie Info Below Card --}}
                    <div class="mt-3">
                        <h3 class="text-white font-semibold truncate group-hover:text-amber-400 transition-colors">
                            {{ $movie['title'] }}
                        </h3>
                        @if($movie['release_date'])
                        <p class="text-gray-500 text-sm flex items-center gap-1 mt-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ \Carbon\Carbon::parse($movie['release_date'])->format('d F Y') }}
                        </p>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-gray-950 border-t border-gray-800 py-12 px-4 md:px-8 lg:px-16 mt-16">
        <div class="container mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                {{-- Brand --}}
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-900" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm3 2h6v4H7V5zm8 8v2h1v-2h-1zm-2-2H7v4h6v-4zm2 0h1V9h-1v2zm1-4V5h-1v2h1zM5 5v2H4V5h1zm0 4H4v2h1V9zm-1 4h1v2H4v-2z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-white">Cinema<span class="text-amber-500">XXL</span></span>
                    </div>
                    <p class="text-gray-400 max-w-md">
                        Experience movies like never before. Premium comfort, stunning visuals, and immersive sound in every screening.
                    </p>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h4 class="text-white font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="/#now-playing" class="text-gray-400 hover:text-amber-400 transition-colors">Now Playing</a></li>
                        <li><a href="/coming-soon" class="text-amber-400 font-medium">Coming Soon</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-amber-400 transition-colors">Promotions</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-amber-400 transition-colors">Contact Us</a></li>
                    </ul>
                </div>

                {{-- Contact --}}
                <div>
                    <h4 class="text-white font-semibold mb-4">Contact</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Jl. Cinema Raya No. 123
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            (021) 123-4567
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            info@cinema-xxl.com
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Copyright --}}
            <div class="mt-10 pt-8 border-t border-gray-800 text-center text-gray-500 text-sm">
                <p>&copy; {{ date('Y') }} Cinema XXL. All rights reserved.</p>
            </div>
        </div>
    </footer>
</div>

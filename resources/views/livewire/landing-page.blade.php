<div class="min-h-screen bg-gray-900">
    {{-- Hero Section --}}
    @if($heroMovie)
    <section class="relative h-[70vh] md:h-[85vh] overflow-hidden">
        {{-- Backdrop Image --}}
        <div class="absolute inset-0">
            <img 
                src="{{ $heroMovie['backdrop_path'] ?? $heroMovie['poster_path'] }}" 
                alt="{{ $heroMovie['title'] }}"
                class="w-full h-full object-cover"
            >
            {{-- Gradient Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-r from-gray-900 via-gray-900/80 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-gray-900/30"></div>
        </div>

        {{-- Hero Content --}}
        <div class="relative z-10 h-full flex items-center">
            <div class="container mx-auto px-4 md:px-8 lg:px-16">
                <div class="max-w-2xl">
                    {{-- Movie Title --}}
                    <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-4 leading-tight">
                        {{ $heroMovie['title'] }}
                    </h1>

                    {{-- Rating & Release Date --}}
                    <div class="flex items-center gap-4 mb-6">
                        <div class="flex items-center gap-1 bg-amber-500/20 px-3 py-1 rounded-full">
                            <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="text-amber-400 font-semibold">{{ $heroMovie['vote_average'] }}</span>
                        </div>
                        @if($heroMovie['release_date'])
                        <span class="text-gray-400">
                            {{ \Carbon\Carbon::parse($heroMovie['release_date'])->format('Y') }}
                        </span>
                        @endif
                    </div>

                    {{-- Overview --}}
                    <p class="text-gray-300 text-base md:text-lg mb-8 line-clamp-3">
                        {{ $heroMovie['overview'] }}
                    </p>

                    {{-- CTA Buttons --}}
                    <div class="flex flex-wrap gap-4">
                        <a href="#" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-gray-900 font-bold px-8 py-4 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg shadow-amber-500/25">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                            </svg>
                            Book Ticket
                        </a>
                        <a href="#" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white font-semibold px-8 py-4 rounded-lg transition-all duration-300 backdrop-blur-sm border border-white/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Watch Trailer
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Scroll Indicator --}}
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <svg class="w-8 h-8 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
            </svg>
        </div>
    </section>
    @endif

    {{-- Now Playing Section --}}
    <section id="now-playing" class="py-28 px-4 md:px-8 lg:px-16 scroll-mt-28">
        <div class="container mx-auto">
            {{-- Section Header --}}
            <div class="flex items-center justify-between mb-15 md:mb-20">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-2">Now Playing</h2>
                    <p class="text-gray-400">Discover movies currently in theaters</p>
                </div>
                <a href="#" class="hidden md:flex items-center gap-2 text-amber-400 hover:text-amber-300 font-semibold transition-colors">
                    View All
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

            {{-- Movie Grid --}}
            @if($loading)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6">
                @for($i = 0; $i < 10; $i++)
                <div class="animate-pulse">
                    <div class="bg-gray-800 rounded-xl aspect-[2/3]"></div>
                    <div class="mt-3 h-4 bg-gray-800 rounded w-3/4"></div>
                    <div class="mt-2 h-3 bg-gray-800 rounded w-1/2"></div>
                </div>
                @endfor
            </div>
            @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6">
                @foreach($movies as $movie)
                <div class="group cursor-pointer">
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
                                
                                {{-- Rating --}}
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <span class="text-amber-400 font-medium">{{ $movie['vote_average'] }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Watchlist Button --}}
                        <div class="absolute top-3 left-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <livewire:watchlist-button 
                                :tmdb-id="$movie['id']"
                                :title="$movie['title']"
                                :poster-path="$movie['poster_path_raw'] ?? null"
                                :overview="$movie['overview']"
                                :vote-average="$movie['vote_average']"
                                :release-date="$movie['release_date']"
                                :wire:key="'watchlist-'.$movie['id']"
                            />
                        </div>

                        {{-- Rating Badge (Always Visible) --}}
                        <div class="absolute top-3 right-3 bg-gray-900/80 backdrop-blur-sm px-2 py-1 rounded-md flex items-center gap-1">
                            <svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="text-white text-xs font-medium">{{ $movie['vote_average'] }}</span>
                        </div>
                    </div>

                    {{-- Movie Info Below Card --}}
                    <div class="mt-3">
                        <h3 class="text-white font-semibold truncate group-hover:text-amber-400 transition-colors">
                            {{ $movie['title'] }}
                        </h3>
                        @if($movie['release_date'])
                        <p class="text-gray-500 text-sm">
                            {{ \Carbon\Carbon::parse($movie['release_date'])->format('Y') }}
                        </p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Mobile View All Button --}}
            <div class="mt-10 text-center md:hidden">
                <a href="#" class="inline-flex items-center gap-2 text-amber-400 hover:text-amber-300 font-semibold transition-colors">
                    View All Movies
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-gray-950 border-t border-gray-800 py-12 px-4 md:px-8 lg:px-16">
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
                        <li><a href="#" class="text-gray-400 hover:text-amber-400 transition-colors">Now Playing</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-amber-400 transition-colors">Coming Soon</a></li>
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

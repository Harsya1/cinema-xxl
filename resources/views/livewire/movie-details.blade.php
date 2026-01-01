<div class="min-h-screen bg-gray-900" x-data="{ showTrailer: false, trailerKey: '' }">
    @section('title', ($movie['title'] ?? 'Movie Details') . ' - Cinema XXL')
    
    @if($loading)
    {{-- Loading State --}}
    <div class="flex items-center justify-center min-h-screen">
        <div class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-amber-500"></div>
    </div>
    @elseif(empty($movie))
    {{-- Not Found --}}
    <div class="flex flex-col items-center justify-center min-h-screen px-4">
        <svg class="w-24 h-24 text-gray-700 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
        </svg>
        <h1 class="text-2xl font-bold text-white mb-2">Movie Not Found</h1>
        <p class="text-gray-400 mb-6">The movie you're looking for doesn't exist.</p>
        <a href="{{ route('home') }}" class="bg-amber-500 hover:bg-amber-600 text-gray-900 font-semibold px-6 py-3 rounded-lg transition-colors">
            Back to Home
        </a>
    </div>
    @else
    {{-- Hero Section with Backdrop --}}
    <section class="relative min-h-[70vh] md:min-h-[80vh]">
        {{-- Backdrop Image --}}
        <div class="absolute inset-0">
            @if($movie['backdrop_path'])
            <img 
                src="{{ $movie['backdrop_path'] }}" 
                alt="{{ $movie['title'] }}"
                class="w-full h-full object-cover"
            >
            @endif
            {{-- Gradient Overlays --}}
            <div class="absolute inset-0 bg-gradient-to-r from-gray-900 via-gray-900/90 to-gray-900/50"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/50 to-transparent"></div>
        </div>

        {{-- Content --}}
        <div class="relative z-10 container mx-auto px-4 md:px-8 lg:px-16 pt-28 md:pt-32 pb-12">
            <div class="flex flex-col md:flex-row gap-8 lg:gap-12">
                {{-- Poster --}}
                <div class="flex-shrink-0 mx-auto md:mx-0">
                    <div class="relative group">
                        <img 
                            src="{{ $movie['poster_path'] ?? 'https://via.placeholder.com/400x600?text=No+Poster' }}" 
                            alt="{{ $movie['title'] }}"
                            class="w-64 md:w-72 lg:w-80 rounded-2xl shadow-2xl shadow-black/50 ring-1 ring-white/10"
                        >
                        {{-- Play Trailer Button on Poster --}}
                        @if(count($videos) > 0)
                        <button 
                            @click="trailerKey = '{{ $videos[0]['key'] ?? '' }}'; showTrailer = true"
                            class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-2xl"
                        >
                            <div class="w-20 h-20 bg-amber-500 rounded-full flex items-center justify-center transform group-hover:scale-110 transition-transform">
                                <svg class="w-10 h-10 text-gray-900 ml-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </button>
                        @endif
                    </div>
                </div>

                {{-- Movie Info --}}
                <div class="flex-1 text-center md:text-left">
                    {{-- Title --}}
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-2">
                        {{ $movie['title'] }}
                    </h1>

                    {{-- Tagline --}}
                    @if($movie['tagline'])
                    <p class="text-gray-400 italic text-lg mb-4">"{{ $movie['tagline'] }}"</p>
                    @endif

                    {{-- Genres --}}
                    @if(count($movie['genres']) > 0)
                    <div class="flex flex-wrap justify-center md:justify-start gap-2 mb-6">
                        @foreach($movie['genres'] as $genre)
                        <span class="px-3 py-1 bg-white/10 text-gray-300 text-sm rounded-full border border-white/10">
                            {{ $genre }}
                        </span>
                        @endforeach
                    </div>
                    @endif

                    {{-- Stats --}}
                    <div class="flex flex-wrap justify-center md:justify-start items-center gap-4 md:gap-6 mb-6">
                        {{-- Rating --}}
                        <div class="flex items-center gap-2 bg-amber-500/20 px-4 py-2 rounded-full">
                            <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="text-amber-400 font-bold">{{ $movie['vote_average'] }}</span>
                            <span class="text-gray-400 text-sm">({{ number_format($movie['vote_count']) }})</span>
                        </div>

                        {{-- Duration --}}
                        @if($movie['runtime'] > 0)
                        <div class="flex items-center gap-2 text-gray-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ floor($movie['runtime'] / 60) }}h {{ $movie['runtime'] % 60 }}m</span>
                        </div>
                        @endif

                        {{-- Release Year --}}
                        @if($movie['release_date'])
                        <div class="flex items-center gap-2 text-gray-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ \Carbon\Carbon::parse($movie['release_date'])->format('d M Y') }}</span>
                        </div>
                        @endif
                    </div>

                    {{-- Overview --}}
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-white mb-2">Overview</h3>
                        <p class="text-gray-300 leading-relaxed max-w-3xl">
                            {{ $movie['overview'] ?: 'No overview available.' }}
                        </p>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-wrap justify-center md:justify-start gap-4">
                        {{-- Book Ticket --}}
                        <a href="#showtimes" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-gray-900 font-bold px-8 py-4 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg shadow-amber-500/25">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                            </svg>
                            Book Ticket
                        </a>

                        {{-- Watchlist Button --}}
                        <button 
                            wire:click="toggleWatchlist"
                            class="inline-flex items-center gap-2 px-6 py-4 rounded-lg font-semibold transition-all duration-300 {{ $isInWatchlist ? 'bg-red-500/20 text-red-400 border border-red-500/30 hover:bg-red-500/30' : 'bg-white/10 text-white border border-white/20 hover:bg-white/20' }}"
                        >
                            <svg class="w-6 h-6" fill="{{ $isInWatchlist ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            {{ $isInWatchlist ? 'In Watchlist' : 'Add to Watchlist' }}
                        </button>

                        {{-- Watch Trailer --}}
                        @if(count($videos) > 0)
                        <button 
                            @click="trailerKey = '{{ $videos[0]['key'] ?? '' }}'; showTrailer = true"
                            class="inline-flex items-center gap-2 bg-transparent text-white border border-white/20 hover:bg-white/10 font-semibold px-6 py-4 rounded-lg transition-all duration-300"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Watch Trailer
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Cast Section --}}
    @if(count($cast) > 0)
    <section class="py-12 px-4 md:px-8 lg:px-16 bg-gray-900">
        <div class="container mx-auto">
            <h2 class="text-2xl font-bold text-white mb-6">Cast</h2>
            
            <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-thin scrollbar-thumb-gray-700 scrollbar-track-gray-800">
                @foreach($cast as $person)
                <div class="flex-shrink-0 w-32 text-center">
                    <div class="w-28 h-28 mx-auto mb-3 rounded-full overflow-hidden bg-gray-800 ring-2 ring-gray-700">
                        @if($person['profile_path'])
                        <img 
                            src="{{ $person['profile_path'] }}" 
                            alt="{{ $person['name'] }}"
                            class="w-full h-full object-cover"
                            loading="lazy"
                        >
                        @else
                        <div class="w-full h-full flex items-center justify-center text-gray-600">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        @endif
                    </div>
                    <h4 class="text-white font-medium text-sm truncate">{{ $person['name'] }}</h4>
                    <p class="text-gray-500 text-xs truncate">{{ $person['character'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Showtimes Section --}}
    <section id="showtimes" class="py-12 px-4 md:px-8 lg:px-16 bg-gray-950 scroll-mt-20">
        <div class="container mx-auto">
            <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Showtimes
            </h2>
            
            @if($showtimes->count() > 0)
            <div class="space-y-6">
                @foreach($showtimes as $date => $times)
                <div class="bg-gray-800/50 rounded-xl p-6 border border-gray-700/50">
                    {{-- Date Header --}}
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-amber-500 rounded-lg flex flex-col items-center justify-center">
                            <span class="text-gray-900 text-xs font-bold uppercase">{{ \Carbon\Carbon::parse($date)->format('M') }}</span>
                            <span class="text-gray-900 text-lg font-bold leading-none">{{ \Carbon\Carbon::parse($date)->format('d') }}</span>
                        </div>
                        <div>
                            <p class="text-white font-semibold">{{ \Carbon\Carbon::parse($date)->format('l') }}</p>
                            <p class="text-gray-400 text-sm">{{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
                        </div>
                    </div>

                    {{-- Time Slots --}}
                    <div class="flex flex-wrap gap-3">
                        @foreach($times as $showtime)
                        <a 
                            href="#" 
                            class="group flex flex-col items-center px-4 py-3 bg-gray-900/50 hover:bg-amber-500 rounded-lg border border-gray-700 hover:border-amber-500 transition-all duration-300"
                        >
                            <span class="text-white group-hover:text-gray-900 font-bold">{{ $showtime->start_time->format('H:i') }}</span>
                            <span class="text-gray-500 group-hover:text-gray-900/70 text-xs">{{ $showtime->studio->name }}</span>
                            <span class="text-amber-400 group-hover:text-gray-900/80 text-xs font-medium mt-1">Rp {{ number_format($showtime->price, 0, ',', '.') }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @else
            {{-- No Showtimes --}}
            <div class="bg-gray-800/30 rounded-xl p-8 text-center border border-dashed border-gray-700">
                <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-lg font-semibold text-white mb-2">Not Currently Showing</h3>
                <p class="text-gray-400 mb-4">This movie is not currently playing at Cinema XXL.</p>
                <p class="text-gray-500 text-sm">Check back later or add it to your watchlist to get notified.</p>
            </div>
            @endif
        </div>
    </section>

    {{-- Similar Movies Section --}}
    @if(count($similarMovies) > 0)
    <section class="py-12 px-4 md:px-8 lg:px-16 bg-gray-900">
        <div class="container mx-auto">
            <h2 class="text-2xl font-bold text-white mb-6">Similar Movies</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 md:gap-6">
                @foreach($similarMovies as $similar)
                <a href="{{ route('movie.details', $similar['id']) }}" class="group cursor-pointer">
                    <div class="relative overflow-hidden rounded-xl bg-gray-800 aspect-[2/3] shadow-lg">
                        <img 
                            src="{{ $similar['poster_path'] ?? 'https://via.placeholder.com/300x450?text=No+Image' }}" 
                            alt="{{ $similar['title'] }}"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                            loading="lazy"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        
                        {{-- Rating Badge --}}
                        <div class="absolute top-2 right-2 bg-gray-900/80 backdrop-blur-sm px-2 py-1 rounded-md flex items-center gap-1">
                            <svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="text-white text-xs font-medium">{{ $similar['vote_average'] }}</span>
                        </div>
                    </div>
                    <h4 class="mt-2 text-white font-medium text-sm truncate group-hover:text-amber-400 transition-colors">{{ $similar['title'] }}</h4>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Trailer Modal --}}
    <div 
        x-show="showTrailer" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/90"
        style="display: none;"
        @keydown.escape.window="showTrailer = false"
    >
        {{-- Modal Content --}}
        <div 
            x-show="showTrailer"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-5xl"
            @click.away="showTrailer = false"
        >
            {{-- Close Button --}}
            <button 
                @click="showTrailer = false"
                class="absolute -top-12 right-0 text-white hover:text-amber-400 transition-colors"
            >
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            {{-- Video Container --}}
            <div class="relative pb-[56.25%] h-0 bg-gray-900 rounded-xl overflow-hidden">
                <template x-if="showTrailer && trailerKey">
                    <iframe 
                        :src="`https://www.youtube.com/embed/${trailerKey}?autoplay=1&rel=0`"
                        class="absolute top-0 left-0 w-full h-full"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                    ></iframe>
                </template>
            </div>
        </div>
    </div>
    @endif
</div>

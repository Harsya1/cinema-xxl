<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Search and Filter Section --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex flex-col md:flex-row gap-4">
                {{-- Search Input --}}
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search Movies</label>
                    <input 
                        type="text" 
                        wire:model.live.debounce.500ms="search" 
                        placeholder="Search for a movie..."
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    >
                </div>
                
                {{-- Category Filter --}}
                <div class="w-full md:w-48">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                    <select 
                        wire:model.live="category"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        {{ !empty($search) ? 'disabled' : '' }}
                    >
                        <option value="now_playing">Now Playing</option>
                        <option value="upcoming">Upcoming</option>
                        <option value="popular">Popular</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Movies Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @forelse($movies as $movie)
                <div 
                    wire:click="selectMovie({{ $movie['id'] }})"
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden cursor-pointer hover:shadow-lg hover:scale-105 transition-all duration-200"
                >
                    {{-- Poster --}}
                    <div class="aspect-[2/3] bg-gray-200 dark:bg-gray-700 relative">
                        @if($movie['poster_path'])
                            <img 
                                src="{{ $this->getPosterUrl($movie['poster_path']) }}" 
                                alt="{{ $movie['title'] }}"
                                class="w-full h-full object-cover"
                                loading="lazy"
                            >
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <x-heroicon-o-film class="w-12 h-12 text-gray-400" />
                            </div>
                        @endif
                        
                        {{-- Rating Badge --}}
                        @if(isset($movie['vote_average']))
                            <div class="absolute top-2 right-2 bg-black/70 text-white text-xs font-bold px-2 py-1 rounded-lg flex items-center gap-1">
                                <x-heroicon-s-star class="w-3 h-3 text-yellow-400" />
                                {{ number_format($movie['vote_average'], 1) }}
                            </div>
                        @endif
                    </div>
                    
                    {{-- Info --}}
                    <div class="p-3">
                        <h3 class="font-semibold text-gray-900 dark:text-white text-sm line-clamp-2">
                            {{ $movie['title'] }}
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ isset($movie['release_date']) ? \Carbon\Carbon::parse($movie['release_date'])->format('M d, Y') : 'TBA' }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <x-heroicon-o-film class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                    <p class="text-gray-500 dark:text-gray-400">No movies found</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($totalPages > 1)
            <div class="flex items-center justify-center gap-4">
                <button 
                    wire:click="previousPage"
                    @disabled($page <= 1)
                    class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <x-heroicon-o-chevron-left class="w-5 h-5" />
                </button>
                
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Page {{ $page }} of {{ $totalPages }}
                </span>
                
                <button 
                    wire:click="nextPage"
                    @disabled($page >= $totalPages)
                    class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <x-heroicon-o-chevron-right class="w-5 h-5" />
                </button>
            </div>
        @endif
    </div>

    {{-- Movie Detail Modal --}}
    @if($selectedMovie)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Backdrop --}}
                <div 
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                    wire:click="clearSelection"
                ></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                {{-- Modal Content --}}
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    {{-- Backdrop Image --}}
                    @if(isset($selectedMovie['backdrop_path']))
                        <div class="h-48 md:h-64 bg-gray-900 relative">
                            <img 
                                src="{{ $this->getBackdropUrl($selectedMovie['backdrop_path']) }}" 
                                alt="{{ $selectedMovie['title'] }}"
                                class="w-full h-full object-cover opacity-50"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent"></div>
                        </div>
                    @endif

                    <div class="p-6 {{ isset($selectedMovie['backdrop_path']) ? '-mt-24 relative' : '' }}">
                        <div class="flex flex-col md:flex-row gap-6">
                            {{-- Poster --}}
                            <div class="flex-shrink-0">
                                @if(isset($selectedMovie['poster_path']))
                                    <img 
                                        src="{{ $this->getPosterUrl($selectedMovie['poster_path'], 'w342') }}" 
                                        alt="{{ $selectedMovie['title'] }}"
                                        class="w-32 md:w-48 rounded-lg shadow-lg"
                                    >
                                @endif
                            </div>

                            {{-- Details --}}
                            <div class="flex-1">
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $selectedMovie['title'] }}
                                </h2>
                                
                                @if(isset($selectedMovie['tagline']) && $selectedMovie['tagline'])
                                    <p class="text-gray-500 dark:text-gray-400 italic mt-1">
                                        "{{ $selectedMovie['tagline'] }}"
                                    </p>
                                @endif

                                <div class="flex flex-wrap items-center gap-3 mt-3 text-sm">
                                    @if(isset($selectedMovie['release_date']))
                                        <span class="text-gray-600 dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($selectedMovie['release_date'])->format('Y') }}
                                        </span>
                                    @endif
                                    
                                    @if(isset($selectedMovie['runtime']))
                                        <span class="text-gray-600 dark:text-gray-400">
                                            {{ $this->formatRuntime($selectedMovie['runtime']) }}
                                        </span>
                                    @endif

                                    @if(isset($selectedMovie['vote_average']))
                                        <span class="flex items-center gap-1 text-yellow-500">
                                            <x-heroicon-s-star class="w-4 h-4" />
                                            {{ number_format($selectedMovie['vote_average'], 1) }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Genres --}}
                                @if(isset($selectedMovie['genres']) && count($selectedMovie['genres']) > 0)
                                    <div class="flex flex-wrap gap-2 mt-3">
                                        @foreach($selectedMovie['genres'] as $genre)
                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-xs">
                                                {{ $genre['name'] }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Overview --}}
                                @if(isset($selectedMovie['overview']))
                                    <p class="mt-4 text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                        {{ $selectedMovie['overview'] }}
                                    </p>
                                @endif

                                {{-- Actions --}}
                                <div class="flex gap-3 mt-6">
                                    <button 
                                        wire:click="createShowtime({{ $selectedMovie['id'] }}, '{{ addslashes($selectedMovie['title']) }}', '{{ $selectedMovie['poster_path'] ?? '' }}')"
                                        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium text-sm flex items-center gap-2"
                                    >
                                        <x-heroicon-o-plus class="w-4 h-4" />
                                        Create Showtime
                                    </button>
                                    
                                    <button 
                                        wire:click="clearSelection"
                                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium text-sm"
                                    >
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>

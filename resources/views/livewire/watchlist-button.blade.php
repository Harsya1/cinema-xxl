<button 
    wire:click="toggleWatchlist"
    class="p-2 rounded-lg transition-all duration-300 {{ $isInWatchlist ? 'bg-amber-500 text-gray-900' : 'bg-gray-900/80 backdrop-blur-sm text-white hover:bg-amber-500 hover:text-gray-900' }}"
    title="{{ $isInWatchlist ? 'Remove from Watchlist' : 'Add to Watchlist' }}"
>
    <svg class="w-4 h-4" fill="{{ $isInWatchlist ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
    </svg>
</button>

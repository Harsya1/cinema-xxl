<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Watchlist;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class WatchlistButton extends Component
{
    public int $tmdbId;
    public string $title;
    public ?string $posterPath;
    public ?string $overview;
    public float $voteAverage;
    public ?string $releaseDate;
    
    public bool $isInWatchlist = false;

    public function mount(
        int $tmdbId,
        string $title,
        ?string $posterPath = null,
        ?string $overview = null,
        float $voteAverage = 0,
        ?string $releaseDate = null
    ): void {
        $this->tmdbId = $tmdbId;
        $this->title = $title;
        $this->posterPath = $posterPath;
        $this->overview = $overview;
        $this->voteAverage = $voteAverage;
        $this->releaseDate = $releaseDate;

        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            $this->isInWatchlist = $user->hasInWatchlist($tmdbId);
        }
    }

    public function toggleWatchlist(): void
    {
        if (!Auth::check()) {
            $this->redirect(route('login'));
            return;
        }

        /** @var User $user */
        $user = Auth::user();

        if ($this->isInWatchlist) {
            // Remove from watchlist
            Watchlist::where('user_id', $user->id)
                ->where('tmdb_id', $this->tmdbId)
                ->delete();
            
            $this->isInWatchlist = false;
            $this->dispatch('notify', type: 'success', message: 'Removed from watchlist.');
        } else {
            // Add to watchlist
            Watchlist::create([
                'user_id' => $user->id,
                'tmdb_id' => $this->tmdbId,
                'title' => $this->title,
                'poster_path' => $this->posterPath,
                'overview' => $this->overview,
                'vote_average' => $this->voteAverage,
                'release_date' => $this->releaseDate,
            ]);
            
            $this->isInWatchlist = true;
            $this->dispatch('notify', type: 'success', message: 'Added to watchlist!');
        }
    }

    public function render()
    {
        return view('livewire.watchlist-button');
    }
}

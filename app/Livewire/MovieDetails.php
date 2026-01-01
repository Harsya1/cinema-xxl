<?php

namespace App\Livewire;

use App\Models\Showtime;
use App\Models\User;
use App\Models\Watchlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.public')]
class MovieDetails extends Component
{
    public int $movieId;
    public array $movie = [];
    public array $cast = [];
    public array $videos = [];
    public array $similarMovies = [];
    public bool $isInWatchlist = false;
    public bool $loading = true;

    protected string $posterBaseUrl = 'https://image.tmdb.org/t/p/w500';
    protected string $backdropBaseUrl = 'https://image.tmdb.org/t/p/original';
    protected string $profileBaseUrl = 'https://image.tmdb.org/t/p/w185';

    public function mount(int $id): void
    {
        $this->movieId = $id;
        $this->loadMovieDetails();
        $this->checkWatchlistStatus();
    }

    public function loadMovieDetails(): void
    {
        $this->loading = true;

        $cacheKey = "tmdb_movie_{$this->movieId}";
        
        $data = Cache::remember($cacheKey, 3600, function () {
            $response = Http::get("https://api.themoviedb.org/3/movie/{$this->movieId}", [
                'api_key' => env('TMDB_API_KEY'),
                'language' => 'id-ID',
                'append_to_response' => 'credits,videos,similar',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        });

        if ($data) {
            $this->movie = $this->formatMovie($data);
            $this->cast = $this->formatCast($data['credits']['cast'] ?? []);
            $this->videos = $this->formatVideos($data['videos']['results'] ?? []);
            $this->similarMovies = $this->formatSimilarMovies($data['similar']['results'] ?? []);
        }

        $this->loading = false;
    }

    protected function formatMovie(array $data): array
    {
        return [
            'id' => $data['id'],
            'title' => $data['title'],
            'tagline' => $data['tagline'] ?? '',
            'overview' => $data['overview'] ?? '',
            'poster_path' => $data['poster_path'] 
                ? $this->posterBaseUrl . $data['poster_path'] 
                : null,
            'poster_path_raw' => $data['poster_path'] ?? null,
            'backdrop_path' => $data['backdrop_path'] 
                ? $this->backdropBaseUrl . $data['backdrop_path'] 
                : null,
            'vote_average' => round($data['vote_average'] ?? 0, 1),
            'vote_count' => $data['vote_count'] ?? 0,
            'release_date' => $data['release_date'] ?? null,
            'runtime' => $data['runtime'] ?? 0,
            'genres' => collect($data['genres'] ?? [])->pluck('name')->toArray(),
            'status' => $data['status'] ?? '',
            'budget' => $data['budget'] ?? 0,
            'revenue' => $data['revenue'] ?? 0,
            'production_companies' => collect($data['production_companies'] ?? [])->pluck('name')->toArray(),
        ];
    }

    protected function formatCast(array $cast): array
    {
        return collect($cast)
            ->take(12)
            ->map(fn($person) => [
                'id' => $person['id'],
                'name' => $person['name'],
                'character' => $person['character'] ?? '',
                'profile_path' => $person['profile_path'] 
                    ? $this->profileBaseUrl . $person['profile_path'] 
                    : null,
            ])
            ->toArray();
    }

    protected function formatVideos(array $videos): array
    {
        return collect($videos)
            ->filter(fn($video) => $video['site'] === 'YouTube' && $video['type'] === 'Trailer')
            ->take(3)
            ->map(fn($video) => [
                'key' => $video['key'],
                'name' => $video['name'],
                'type' => $video['type'],
            ])
            ->values()
            ->toArray();
    }

    protected function formatSimilarMovies(array $movies): array
    {
        return collect($movies)
            ->take(6)
            ->map(fn($movie) => [
                'id' => $movie['id'],
                'title' => $movie['title'],
                'poster_path' => $movie['poster_path'] 
                    ? $this->posterBaseUrl . $movie['poster_path'] 
                    : null,
                'vote_average' => round($movie['vote_average'] ?? 0, 1),
            ])
            ->toArray();
    }

    public function checkWatchlistStatus(): void
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            $this->isInWatchlist = $user->hasInWatchlist($this->movieId);
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
            Watchlist::where('user_id', $user->id)
                ->where('tmdb_id', $this->movieId)
                ->delete();
            
            $this->isInWatchlist = false;
            $this->dispatch('notify', type: 'success', message: 'Removed from watchlist.');
        } else {
            Watchlist::create([
                'user_id' => $user->id,
                'tmdb_id' => $this->movieId,
                'title' => $this->movie['title'],
                'poster_path' => $this->movie['poster_path_raw'],
                'overview' => $this->movie['overview'],
                'vote_average' => $this->movie['vote_average'],
                'release_date' => $this->movie['release_date'],
            ]);
            
            $this->isInWatchlist = true;
            $this->dispatch('notify', type: 'success', message: 'Added to watchlist!');
        }
    }

    public function getShowtimesProperty()
    {
        return Showtime::with('studio')
            ->forMovie($this->movieId)
            ->upcoming()
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn($showtime) => $showtime->start_time->format('Y-m-d'));
    }

    public function getPageTitleProperty(): string
    {
        return ($this->movie['title'] ?? 'Movie Details') . ' - Cinema XXL';
    }

    public function render()
    {
        return view('livewire.movie-details', [
            'showtimes' => $this->showtimes,
        ]);
    }
}

<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

#[Layout('layouts.public')]
class LandingPage extends Component
{
    public array $movies = [];
    public ?array $heroMovie = null;
    public bool $loading = true;

    protected string $posterBaseUrl = 'https://image.tmdb.org/t/p/w500';
    protected string $backdropBaseUrl = 'https://image.tmdb.org/t/p/original';

    public function mount(): void
    {
        $this->loadMovies();
    }

    public function loadMovies(): void
    {
        $this->loading = true;

        $data = Cache::remember('tmdb_now_playing', 3600, function () {
            $response = Http::get('https://api.themoviedb.org/3/movie/now_playing', [
                'api_key' => env('TMDB_API_KEY'),
                'language' => 'id-ID',
                'region' => 'ID',
                'page' => 1,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return ['results' => []];
        });

        $results = $data['results'] ?? [];

        if (count($results) > 0) {
            // First movie for hero section
            $this->heroMovie = $this->formatMovie($results[0], true);
            
            // Rest for the grid
            $this->movies = collect(array_slice($results, 1))
                ->map(fn($movie) => $this->formatMovie($movie))
                ->toArray();
        }

        $this->loading = false;
    }

    protected function formatMovie(array $movie, bool $isHero = false): array
    {
        return [
            'id' => $movie['id'],
            'title' => $movie['title'],
            'overview' => $movie['overview'] ?? '',
            'poster_path' => $movie['poster_path'] 
                ? $this->posterBaseUrl . $movie['poster_path'] 
                : null,
            'poster_path_raw' => $movie['poster_path'] ?? null,
            'backdrop_path' => $movie['backdrop_path'] 
                ? ($isHero ? $this->backdropBaseUrl : $this->posterBaseUrl) . $movie['backdrop_path'] 
                : null,
            'vote_average' => round($movie['vote_average'] ?? 0, 1),
            'release_date' => $movie['release_date'] ?? null,
            'genre_ids' => $movie['genre_ids'] ?? [],
        ];
    }

    public function getPosterUrl(?string $path): string
    {
        return $path ?? 'https://via.placeholder.com/500x750?text=No+Image';
    }

    public function render()
    {
        return view('livewire.landing-page');
    }
}

<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

#[Layout('layouts.public')]
class ComingSoon extends Component
{
    public array $movies = [];
    public bool $loading = true;

    protected string $posterBaseUrl = 'https://image.tmdb.org/t/p/w500';

    public function mount(): void
    {
        $this->loadMovies();
    }

    public function loadMovies(): void
    {
        $this->loading = true;

        $data = Cache::remember('tmdb_upcoming', 3600, function () {
            $response = Http::get('https://api.themoviedb.org/3/movie/upcoming', [
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

        $this->movies = collect($results)
            ->map(fn($movie) => $this->formatMovie($movie))
            ->toArray();

        $this->loading = false;
    }

    protected function formatMovie(array $movie): array
    {
        return [
            'id' => $movie['id'],
            'title' => $movie['title'],
            'overview' => $movie['overview'] ?? '',
            'poster_path' => $movie['poster_path'] 
                ? $this->posterBaseUrl . $movie['poster_path'] 
                : null,
            'vote_average' => round($movie['vote_average'] ?? 0, 1),
            'release_date' => $movie['release_date'] ?? null,
        ];
    }

    public function render()
    {
        return view('livewire.coming-soon');
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class TmdbService
{
    protected string $baseUrl = 'https://api.themoviedb.org/3';
    protected string $imageBaseUrl = 'https://image.tmdb.org/t/p';
    protected ?string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.tmdb.api_key');
    }

    /**
     * Get now playing movies.
     */
    public function getNowPlaying(int $page = 1): array
    {
        return $this->cachedRequest('movie/now_playing', [
            'page' => $page,
            'region' => 'ID',
        ], 'tmdb_now_playing_' . $page, 3600); // Cache 1 hour
    }

    /**
     * Get upcoming movies.
     */
    public function getUpcoming(int $page = 1): array
    {
        return $this->cachedRequest('movie/upcoming', [
            'page' => $page,
            'region' => 'ID',
        ], 'tmdb_upcoming_' . $page, 3600);
    }

    /**
     * Get popular movies.
     */
    public function getPopular(int $page = 1): array
    {
        return $this->cachedRequest('movie/popular', [
            'page' => $page,
        ], 'tmdb_popular_' . $page, 3600);
    }

    /**
     * Get movie details by ID.
     */
    public function getMovie(int $movieId): ?array
    {
        return $this->cachedRequest("movie/{$movieId}", [
            'append_to_response' => 'videos,credits,images',
        ], 'tmdb_movie_' . $movieId, 86400); // Cache 24 hours
    }

    /**
     * Search movies by query.
     */
    public function searchMovies(string $query, int $page = 1): array
    {
        return $this->request('search/movie', [
            'query' => $query,
            'page' => $page,
        ]);
    }

    /**
     * Get movie genres.
     */
    public function getGenres(): array
    {
        return $this->cachedRequest('genre/movie/list', [], 'tmdb_genres', 604800); // Cache 1 week
    }

    /**
     * Get poster URL.
     */
    public function getPosterUrl(?string $path, string $size = 'w500'): ?string
    {
        if (!$path) {
            return null;
        }

        return "{$this->imageBaseUrl}/{$size}{$path}";
    }

    /**
     * Get backdrop URL.
     */
    public function getBackdropUrl(?string $path, string $size = 'w1280'): ?string
    {
        if (!$path) {
            return null;
        }

        return "{$this->imageBaseUrl}/{$size}{$path}";
    }

    /**
     * Make a cached API request.
     */
    protected function cachedRequest(string $endpoint, array $params = [], string $cacheKey = '', int $ttl = 3600): array
    {
        if ($cacheKey) {
            return Cache::remember($cacheKey, $ttl, fn() => $this->request($endpoint, $params));
        }

        return $this->request($endpoint, $params);
    }

    /**
     * Make an API request.
     */
    protected function request(string $endpoint, array $params = []): array
    {
        if (!$this->apiKey) {
            return ['error' => 'TMDb API key not configured'];
        }

        try {
            $response = Http::get("{$this->baseUrl}/{$endpoint}", array_merge($params, [
                'api_key' => $this->apiKey,
                'language' => 'id-ID',
            ]));

            if ($response->successful()) {
                return $response->json();
            }

            return ['error' => 'API request failed', 'status' => $response->status()];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Clear all TMDb cache.
     */
    public function clearCache(): void
    {
        // Clear common cache keys
        Cache::forget('tmdb_genres');

        for ($i = 1; $i <= 10; $i++) {
            Cache::forget('tmdb_now_playing_' . $i);
            Cache::forget('tmdb_upcoming_' . $i);
            Cache::forget('tmdb_popular_' . $i);
        }
    }

    /**
     * Format movie data for storage.
     */
    public function formatMovieForStorage(array $movie): array
    {
        return [
            'tmdb_movie_id' => $movie['id'],
            'movie_title' => $movie['title'],
            'poster_path' => $movie['poster_path'] ?? null,
        ];
    }

    /**
     * Get runtime in hours and minutes format.
     */
    public function formatRuntime(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0) {
            return "{$hours}h {$mins}m";
        }

        return "{$mins}m";
    }
}

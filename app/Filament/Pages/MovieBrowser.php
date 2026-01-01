<?php

namespace App\Filament\Pages;

use App\Services\TmdbService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;

class MovieBrowser extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-film';
    protected static ?string $navigationLabel = 'Browse Movies';
    protected static ?string $navigationGroup = 'Cinema Operations';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.movie-browser';

    public string $search = '';
    public string $category = 'now_playing';
    public array $movies = [];
    public ?array $selectedMovie = null;
    public int $page = 1;
    public int $totalPages = 1;

    protected TmdbService $tmdbService;

    public function boot(TmdbService $tmdbService): void
    {
        $this->tmdbService = $tmdbService;
    }

    public function mount(): void
    {
        $this->loadMovies();
    }

    public function loadMovies(): void
    {
        $tmdb = app(TmdbService::class);
        
        if (!empty($this->search)) {
            $result = $tmdb->searchMovies($this->search, $this->page);
        } else {
            $result = match ($this->category) {
                'now_playing' => $tmdb->getNowPlaying($this->page),
                'upcoming' => $tmdb->getUpcoming($this->page),
                'popular' => $tmdb->getPopular($this->page),
                default => $tmdb->getNowPlaying($this->page),
            };
        }

        $this->movies = $result['results'] ?? [];
        $this->totalPages = $result['total_pages'] ?? 1;
    }

    public function updatedSearch(): void
    {
        $this->page = 1;
        $this->loadMovies();
    }

    public function updatedCategory(): void
    {
        $this->search = '';
        $this->page = 1;
        $this->loadMovies();
    }

    public function nextPage(): void
    {
        if ($this->page < $this->totalPages) {
            $this->page++;
            $this->loadMovies();
        }
    }

    public function previousPage(): void
    {
        if ($this->page > 1) {
            $this->page--;
            $this->loadMovies();
        }
    }

    public function selectMovie(int $movieId): void
    {
        $tmdb = app(TmdbService::class);
        $this->selectedMovie = $tmdb->getMovie($movieId);
    }

    public function clearSelection(): void
    {
        $this->selectedMovie = null;
    }

    public function createShowtime(int $tmdbId, string $title, ?string $posterPath): \Illuminate\Http\RedirectResponse
    {
        // Redirect to showtime creation with movie data
        return redirect()->route('filament.admin.resources.showtimes.create', [
            'tmdb_movie_id' => $tmdbId,
            'movie_title' => $title,
            'poster_path' => $posterPath,
        ]);
    }

    public function getPosterUrl(?string $path, string $size = 'w342'): ?string
    {
        if (!$path) {
            return null;
        }
        return "https://image.tmdb.org/t/p/{$size}{$path}";
    }

    public function getBackdropUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }
        return "https://image.tmdb.org/t/p/w1280{$path}";
    }

    public function formatRuntime(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return $hours > 0 ? "{$hours}h {$mins}m" : "{$mins}m";
    }
}

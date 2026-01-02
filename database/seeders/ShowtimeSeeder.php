<?php

namespace Database\Seeders;

use App\Models\Showtime;
use App\Models\Studio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShowtimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Fetches 'Now Playing' movies from TMDb API and creates
     * showtimes for the next 7 days across all 5 studios.
     */
    public function run(): void
    {
        $this->command->info('🎬 Fetching movies from TMDb API...');

        // Fetch movies from TMDb API
        $movies = $this->fetchMoviesFromTMDb();

        if (empty($movies)) {
            $this->command->warn('⚠️ Could not fetch movies from TMDb. Using fallback data.');
            $movies = $this->getFallbackMovies();
        }

        $this->command->info("📽️ Found " . count($movies) . " movies to schedule.");

        // Get all studios
        $studios = Studio::all();

        if ($studios->isEmpty()) {
            $this->command->error('❌ No studios found! Please run StudioSeeder first.');
            return;
        }

        // Time slots for showtimes
        $timeSlots = ['10:00', '13:00', '16:00', '19:00', '21:30'];
        
        // Base prices (weekday/weekend)
        $basePriceWeekday = 45000;
        $basePriceWeekend = 55000;

        $totalShowtimes = 0;

        // Generate showtimes for next 7 days
        for ($day = 0; $day < 7; $day++) {
            $date = now()->addDays($day);
            $isWeekend = $date->isWeekend();
            $basePrice = $isWeekend ? $basePriceWeekend : $basePriceWeekday;

            foreach ($studios as $studio) {
                // Assign movies to this studio (rotate through movies)
                // Each studio gets 2-3 different movies per day
                $studioMovies = $this->getMoviesForStudio($movies, $studio->id);

                foreach ($studioMovies as $movieIndex => $movie) {
                    // Each movie gets 1-2 time slots in this studio
                    $movieTimeSlots = array_slice($timeSlots, $movieIndex * 2, 2);

                    foreach ($movieTimeSlots as $time) {
                        $startTime = $date->copy()->setTimeFromTimeString($time);

                        // Skip if showtime is in the past
                        if ($startTime < now()) {
                            continue;
                        }

                        // Calculate end time based on movie runtime (default 2h 15min)
                        $runtime = $movie['runtime'] ?? 135;
                        $endTime = $startTime->copy()->addMinutes($runtime);

                        // Create or update showtime
                        Showtime::updateOrCreate(
                            [
                                'tmdb_movie_id' => $movie['tmdb_movie_id'],
                                'studio_id' => $studio->id,
                                'start_time' => $startTime,
                            ],
                            [
                                'movie_title' => $movie['movie_title'],
                                'poster_path' => $movie['poster_path'],
                                'end_time' => $endTime,
                                'price' => $basePrice,
                            ]
                        );

                        $totalShowtimes++;
                    }
                }
            }
        }

        $this->command->info("✅ Created {$totalShowtimes} showtimes across " . $studios->count() . " studios!");
        
        // Show summary
        $this->command->newLine();
        $this->command->info('📊 Showtime Summary:');
        $this->command->table(
            ['Movie', 'Showtimes'],
            Showtime::selectRaw('movie_title, COUNT(*) as count')
                ->groupBy('movie_title')
                ->get()
                ->map(fn($s) => [$s->movie_title, $s->count])
                ->toArray()
        );
    }

    /**
     * Fetch 'Now Playing' movies from TMDb API.
     * 
     * @return array<int, array{tmdb_movie_id: int, movie_title: string, poster_path: string|null, runtime: int}>
     */
    private function fetchMoviesFromTMDb(): array
    {
        $apiKey = config('services.tmdb.api_key') ?? env('TMDB_API_KEY');

        if (!$apiKey) {
            $this->command->warn('⚠️ TMDB_API_KEY not configured in .env');
            return [];
        }

        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::timeout(10)->get('https://api.themoviedb.org/3/movie/now_playing', [
                'api_key' => $apiKey,
                'language' => 'en-US',
                'page' => 1,
                'region' => 'US',
            ]);

            if ($response->successful()) {
                $results = $response->json()['results'] ?? [];
                
                // Take top 8 movies
                return collect($results)
                    ->take(8)
                    ->map(function ($movie) use ($apiKey) {
                        // Optionally fetch runtime
                        $runtime = 135; // Default
                        try {
                            /** @var \Illuminate\Http\Client\Response $detailResponse */
                            $detailResponse = Http::timeout(5)->get("https://api.themoviedb.org/3/movie/{$movie['id']}", [
                                'api_key' => $apiKey,
                            ]);
                            if ($detailResponse->successful()) {
                                $runtime = $detailResponse->json()['runtime'] ?? 135;
                            }
                        } catch (\Exception $e) {
                            // Use default runtime
                        }

                        return [
                            'tmdb_movie_id' => $movie['id'],
                            'movie_title' => $movie['title'],
                            'poster_path' => $movie['poster_path'],
                            'runtime' => $runtime,
                        ];
                    })
                    ->toArray();
            }

            $this->command->error('TMDb API error: ' . $response->status());
            return [];

        } catch (\Exception $e) {
            Log::error('TMDb API fetch failed: ' . $e->getMessage());
            $this->command->error('TMDb API error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get fallback movies if TMDb API fails.
     */
    private function getFallbackMovies(): array
    {
        return [
            [
                'tmdb_movie_id' => 912649,
                'movie_title' => 'Venom: The Last Dance',
                'poster_path' => '/aosm8NMQ3UyoBVpSxyimorCQykC.jpg',
                'runtime' => 109,
            ],
            [
                'tmdb_movie_id' => 1184918,
                'movie_title' => 'The Wild Robot',
                'poster_path' => '/wTnV3PCVW5O92JMrFvvrRcV39RU.jpg',
                'runtime' => 102,
            ],
            [
                'tmdb_movie_id' => 533535,
                'movie_title' => 'Deadpool & Wolverine',
                'poster_path' => '/8cdWjvZQUExUUTzyp4t6EDMubfO.jpg',
                'runtime' => 128,
            ],
            [
                'tmdb_movie_id' => 698687,
                'movie_title' => 'Transformers One',
                'poster_path' => '/iRCgqpdVE4wyLQvGYU3ZP7pAtUc.jpg',
                'runtime' => 104,
            ],
            [
                'tmdb_movie_id' => 1022789,
                'movie_title' => 'Inside Out 2',
                'poster_path' => '/vpnVM9B6NMmQpWeZvzLvDESb2QY.jpg',
                'runtime' => 96,
            ],
            [
                'tmdb_movie_id' => 573435,
                'movie_title' => 'Bad Boys: Ride or Die',
                'poster_path' => '/nP6RliHjxsz4irTKsxe8FRhKZYl.jpg',
                'runtime' => 115,
            ],
            [
                'tmdb_movie_id' => 762441,
                'movie_title' => 'A Quiet Place: Day One',
                'poster_path' => '/hU42CRk14JuPEdqZG3AWmagiPAP.jpg',
                'runtime' => 99,
            ],
            [
                'tmdb_movie_id' => 823464,
                'movie_title' => 'Godzilla x Kong: The New Empire',
                'poster_path' => '/z1p34vh7dEOnLDmyCrlUVLuoDzd.jpg',
                'runtime' => 115,
            ],
        ];
    }

    /**
     * Get movies assigned to a specific studio.
     * Different studio types get different movie distributions.
     */
    private function getMoviesForStudio(array $movies, int $studioId): array
    {
        // Rotate movies across studios to create variety
        $movieCount = count($movies);
        
        // Each studio gets 2-3 movies
        $startIndex = ($studioId - 1) % $movieCount;
        $selectedMovies = [];
        
        for ($i = 0; $i < 3; $i++) {
            $index = ($startIndex + $i) % $movieCount;
            $selectedMovies[] = $movies[$index];
        }

        return $selectedMovies;
    }
}

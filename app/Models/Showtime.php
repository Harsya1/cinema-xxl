<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Showtime extends Model
{
    use HasFactory;

    protected $fillable = [
        'tmdb_movie_id',
        'movie_title',
        'poster_path',
        'studio_id',
        'start_time',
        'end_time',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'price' => 'decimal:2',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the studio for this showtime.
     */
    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }

    /**
     * Get bookings for this showtime.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    // ==================== SCOPES ====================

    /**
     * Scope: Only upcoming showtimes.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now());
    }

    /**
     * Scope: Showtimes for today.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('start_time', today());
    }

    /**
     * Scope: Showtimes for a specific movie.
     */
    public function scopeForMovie($query, int $tmdbMovieId)
    {
        return $query->where('tmdb_movie_id', $tmdbMovieId);
    }

    // ==================== HELPERS ====================

    /**
     * Get booked seats for this showtime.
     */
    public function getBookedSeats(): array
    {
        return $this->bookings()
            ->whereIn('status', ['booked', 'paid', 'redeemed'])
            ->pluck('seat_number')
            ->toArray();
    }

    /**
     * Get available seats for this showtime.
     */
    public function getAvailableSeats(): array
    {
        $allSeats = $this->studio->generateSeatLayout();
        $bookedSeats = $this->getBookedSeats();

        return array_diff($allSeats, $bookedSeats);
    }

    /**
     * Check if a seat is available.
     */
    public function isSeatAvailable(string $seatNumber): bool
    {
        return !in_array($seatNumber, $this->getBookedSeats());
    }

    /**
     * Get TMDb poster URL.
     */
    public function getPosterUrl(string $size = 'w500'): ?string
    {
        if (!$this->poster_path) {
            return null;
        }

        return "https://image.tmdb.org/t/p/{$size}{$this->poster_path}";
    }

    /**
     * Check if showtime has ended.
     */
    public function hasEnded(): bool
    {
        return $this->end_time->isPast();
    }

    /**
     * Check if showtime is currently playing.
     */
    public function isPlaying(): bool
    {
        return now()->between($this->start_time, $this->end_time);
    }
}

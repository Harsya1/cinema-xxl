<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Studio extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'total_seats',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Get showtimes for this studio.
     */
    public function showtimes(): HasMany
    {
        return $this->hasMany(Showtime::class);
    }

    /**
     * Get cleaning tasks for this studio.
     */
    public function cleaningTasks(): HasMany
    {
        return $this->hasMany(CleaningTask::class);
    }

    // ==================== HELPERS ====================

    /**
     * Get the price multiplier based on studio type.
     */
    public function getPriceMultiplier(): float
    {
        return match ($this->type) {
            'Premier' => 1.5,
            '3D' => 1.3,
            default => 1.0,
        };
    }

    /**
     * Generate seat layout (e.g., A1-A10, B1-B10).
     */
    public function generateSeatLayout(): array
    {
        $seats = [];
        $rows = ceil($this->total_seats / 10);
        $seatsPerRow = 10;
        $seatCount = 0;

        for ($row = 0; $row < $rows && $seatCount < $this->total_seats; $row++) {
            $rowLetter = chr(65 + $row); // A, B, C, ...
            for ($seat = 1; $seat <= $seatsPerRow && $seatCount < $this->total_seats; $seat++) {
                $seats[] = $rowLetter . $seat;
                $seatCount++;
            }
        }

        return $seats;
    }
}

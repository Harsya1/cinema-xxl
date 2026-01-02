<?php

namespace App\Models;

use App\Enums\StudioType;
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
        'rows',
        'cols',
        'price_multiplier',
    ];

    protected $casts = [
        'type' => StudioType::class,
        'price_multiplier' => 'decimal:2',
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
        // Use database value if set, otherwise fallback to type-based calculation
        if ($this->price_multiplier && $this->price_multiplier > 0) {
            return (float) $this->price_multiplier;
        }

        return match ($this->type) {
            StudioType::Premier => 1.5,
            StudioType::ThreeD => 1.3,
            default => 1.0,
        };
    }

    /**
     * Generate seat layout based on rows and cols.
     */
    public function generateSeatLayout(): array
    {
        $seats = [];
        $rows = $this->rows ?? ceil($this->total_seats / 10);
        $cols = $this->cols ?? 10;

        for ($row = 0; $row < $rows; $row++) {
            $rowLetter = chr(65 + $row); // A, B, C, ...
            for ($col = 1; $col <= $cols; $col++) {
                $seats[] = $rowLetter . $col;
            }
        }

        return $seats;
    }

    /**
     * Get row letters for this studio.
     */
    public function getRowLetters(): array
    {
        $letters = [];
        $rows = $this->rows ?? ceil($this->total_seats / 10);
        
        for ($row = 0; $row < $rows; $row++) {
            $letters[] = chr(65 + $row);
        }

        return $letters;
    }

    /**
     * Get column numbers for this studio.
     */
    public function getColNumbers(): array
    {
        $cols = $this->cols ?? 10;
        return range(1, $cols);
    }
}

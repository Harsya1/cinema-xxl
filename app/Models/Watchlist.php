<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Watchlist extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'tmdb_id',
        'title',
        'poster_path',
        'overview',
        'vote_average',
        'release_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tmdb_id' => 'integer',
            'vote_average' => 'decimal:1',
            'release_date' => 'date',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the user that owns this watchlist item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ==================== SCOPES ====================

    /**
     * Scope to filter by TMDb ID.
     */
    public function scopeByTmdbId($query, int $tmdbId)
    {
        return $query->where('tmdb_id', $tmdbId);
    }

    // ==================== HELPERS ====================

    /**
     * Get full poster URL from TMDb.
     */
    public function getPosterUrlAttribute(): string
    {
        if ($this->poster_path) {
            return 'https://image.tmdb.org/t/p/w500' . $this->poster_path;
        }
        return 'https://via.placeholder.com/500x750?text=No+Image';
    }

    /**
     * Get formatted release year.
     */
    public function getReleaseYearAttribute(): ?string
    {
        if ($this->release_date instanceof \DateTimeInterface) {
            return $this->release_date->format('Y');
        }
        return null;
    }
}

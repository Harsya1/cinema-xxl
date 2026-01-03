<?php

namespace App\Models;

use App\Enums\CleaningStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CleaningTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'studio_id',
        'cleaner_id',
        'assigned_at',
        'completed_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'completed_at' => 'datetime',
            'status' => CleaningStatus::class,
        ];
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the studio for this cleaning task.
     */
    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }

    /**
     * Get the cleaner assigned to this task.
     */
    public function cleaner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cleaner_id');
    }

    // ==================== SCOPES ====================

    /**
     * Scope: Pending tasks.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: In progress tasks.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope: Unassigned tasks.
     */
    public function scopeUnassigned($query)
    {
        return $query->whereNull('cleaner_id');
    }

    // ==================== HELPERS ====================

    /**
     * Assign a cleaner to this task.
     */
    public function assignCleaner(int $cleanerId): bool
    {
        return $this->update([
            'cleaner_id' => $cleanerId,
            'assigned_at' => now(),
            'status' => 'in_progress',
        ]);
    }

    /**
     * Mark task as in progress.
     */
    public function startCleaning(): bool
    {
        return $this->update([
            'status' => 'in_progress',
        ]);
    }

    /**
     * Mark task as completed.
     */
    public function markAsCompleted(): bool
    {
        return $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Create a cleaning task after a showtime ends.
     */
    public static function createForShowtime(Showtime $showtime): self
    {
        return self::create([
            'studio_id' => $showtime->studio_id,
            'status' => 'pending',
        ]);
    }
}

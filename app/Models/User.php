<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'date_of_birth',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
        ];
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get user's bookings (as customer).
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'user_id');
    }

    /**
     * Get bookings processed by this user (as cashier).
     */
    public function processedBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'cashier_id');
    }

    /**
     * Get audit logs created by this user.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Get cleaning tasks assigned to this user.
     */
    public function cleaningTasks(): HasMany
    {
        return $this->hasMany(CleaningTask::class, 'cleaner_id');
    }

    /**
     * Get FnB orders processed by this user (as cashier).
     */
    public function fnbOrders(): HasMany
    {
        return $this->hasMany(FnbOrder::class, 'cashier_id');
    }

    /**
     * Get FnB orders made by this user (as customer).
     */
    public function fnbPurchases(): HasMany
    {
        return $this->hasMany(FnbOrder::class, 'user_id');
    }

    // ==================== ROLE HELPERS ====================

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isCashier(): bool
    {
        return $this->role === 'cashier';
    }

    public function isFnbStaff(): bool
    {
        return $this->role === 'fnb_staff';
    }

    public function isCleaner(): bool
    {
        return $this->role === 'cleaner';
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['admin', 'manager', 'cashier', 'fnb_staff', 'cleaner']);
    }
}

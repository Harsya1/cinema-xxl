<?php

namespace App\Filament\Widgets;

use App\Enums\BookingStatus;
use App\Enums\CleaningStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\FnbOrder;
use App\Models\Showtime;
use App\Models\CleaningTask;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    /**
     * Only Admin and Manager can see the stats overview widget.
     */
    public static function canView(): bool
    {
        $user = Auth::user();
        
        return $user && in_array($user->role, [
            UserRole::Admin,
            UserRole::Manager,
        ]);
    }

    protected function getStats(): array
    {
        $today = today();
        
        // Today's revenue from bookings
        $todayBookingRevenue = Booking::where('status', BookingStatus::Paid)
            ->whereDate('created_at', $today)
            ->sum('total_price');

        // Today's revenue from F&B
        $todayFnbRevenue = FnbOrder::where('status', BookingStatus::Paid)
            ->whereDate('created_at', $today)
            ->sum('total_amount');

        // Today's total revenue
        $todayRevenue = $todayBookingRevenue + $todayFnbRevenue;

        // This week's comparison
        $lastWeekRevenue = Booking::where('status', BookingStatus::Paid)
            ->whereBetween('created_at', [now()->subWeek(), now()->subWeek()->endOfDay()])
            ->sum('total_price') + FnbOrder::where('status', BookingStatus::Paid)
            ->whereBetween('created_at', [now()->subWeek(), now()->subWeek()->endOfDay()])
            ->sum('total_amount');

        $revenueChange = $lastWeekRevenue > 0 
            ? round((($todayRevenue - $lastWeekRevenue) / $lastWeekRevenue) * 100, 1) 
            : 0;

        // Today's shows
        $todayShows = Showtime::whereDate('start_time', $today)->count();
        $upcomingShows = Showtime::where('start_time', '>', now())->count();

        // Pending bookings (need payment)
        $pendingBookings = Booking::where('status', BookingStatus::Booked)->count();

        // Today's tickets sold
        $todayTickets = Booking::whereDate('created_at', $today)
            ->whereIn('status', [BookingStatus::Booked, BookingStatus::Paid])
            ->count();

        // Pending cleaning tasks
        $pendingCleaning = CleaningTask::where('status', CleaningStatus::Pending)->count();

        return [
            Stat::make('Today\'s Revenue', 'Rp ' . number_format($todayRevenue, 0, ',', '.'))
                ->description($revenueChange >= 0 ? "+{$revenueChange}% from last week" : "{$revenueChange}% from last week")
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueChange >= 0 ? 'success' : 'danger')
                ->chart([7, 4, 6, 8, 5, 9, 12]),

            Stat::make('Today\'s Shows', $todayShows)
                ->description("{$upcomingShows} upcoming shows")
                ->descriptionIcon('heroicon-m-film')
                ->color('info'),

            Stat::make('Tickets Sold Today', $todayTickets)
                ->description("{$pendingBookings} pending payment")
                ->descriptionIcon('heroicon-m-ticket')
                ->color($pendingBookings > 10 ? 'warning' : 'success'),

            Stat::make('Cleaning Tasks', $pendingCleaning . ' Pending')
                ->description('Tasks awaiting completion')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color($pendingCleaning > 5 ? 'danger' : ($pendingCleaning > 0 ? 'warning' : 'success')),
        ];
    }
}

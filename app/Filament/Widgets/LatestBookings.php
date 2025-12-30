<?php

namespace App\Filament\Widgets;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestBookings extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 1;
    protected static ?string $heading = 'Latest Bookings';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Booking::query()
                    ->with(['user', 'showtime'])
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('booking_code')
                    ->label('Code')
                    ->weight('bold')
                    ->size('sm'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->limit(15),

                Tables\Columns\TextColumn::make('showtime.movie_title')
                    ->label('Movie')
                    ->limit(15),

                Tables\Columns\TextColumn::make('total_price')
                    ->money('IDR')
                    ->size('sm'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (BookingStatus $state): string => $state->color()),
            ])
            ->paginated(false);
    }
}

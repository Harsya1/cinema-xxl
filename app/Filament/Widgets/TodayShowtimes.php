<?php

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Models\Showtime;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class TodayShowtimes extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Today\'s Shows';

    /**
     * Only Admin, Manager, and Cashier can see today's showtimes widget.
     */
    public static function canView(): bool
    {
        $user = Auth::user();
        
        return $user && in_array($user->role, [
            UserRole::Admin,
            UserRole::Manager,
            UserRole::Cashier,
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Showtime::query()
                    ->whereDate('start_time', today())
                    ->orderBy('start_time')
            )
            ->columns([
                Tables\Columns\ImageColumn::make('poster_path')
                    ->label('')
                    ->getStateUsing(fn ($record) => $record->poster_path 
                        ? "https://image.tmdb.org/t/p/w92{$record->poster_path}" 
                        : null)
                    ->width(40)
                    ->height(60),

                Tables\Columns\TextColumn::make('movie_title')
                    ->label('Movie')
                    ->weight('bold')
                    ->limit(30),

                Tables\Columns\TextColumn::make('studio.name')
                    ->label('Studio')
                    ->badge(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Time')
                    ->time('H:i'),

                Tables\Columns\TextColumn::make('price')
                    ->money('IDR'),

                Tables\Columns\TextColumn::make('bookings_count')
                    ->label('Bookings')
                    ->counts('bookings')
                    ->badge()
                    ->color('success'),

                Tables\Columns\IconColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(fn ($record) => $record->hasEnded() ? 'ended' : ($record->isPlaying() ? 'playing' : 'upcoming'))
                    ->icon(fn (string $state): string => match ($state) {
                        'ended' => 'heroicon-o-check-circle',
                        'playing' => 'heroicon-o-play-circle',
                        default => 'heroicon-o-clock',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'ended' => 'gray',
                        'playing' => 'success',
                        default => 'warning',
                    }),
            ])
            ->paginated(false);
    }
}

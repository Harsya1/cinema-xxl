<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShowtimeResource\Pages;
use App\Models\Showtime;
use App\Models\Studio;
use App\Services\TmdbService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ShowtimeResource extends Resource
{
    protected static ?string $model = Showtime::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Cinema Operations';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'movie_title';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['studio']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Movie Information')
                    ->description('Select or search for a movie from TMDb')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('tmdb_movie_id')
                                    ->label('TMDb Movie ID')
                                    ->required()
                                    ->numeric()
                                    ->default(fn () => request()->query('tmdb_movie_id'))
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state) {
                                            $tmdb = app(TmdbService::class);
                                            $movie = $tmdb->getMovie((int) $state);
                                            if ($movie && !isset($movie['error'])) {
                                                $set('movie_title', $movie['title']);
                                                $set('poster_path', $movie['poster_path']);
                                            }
                                        }
                                    }),

                                Forms\Components\TextInput::make('movie_title')
                                    ->label('Movie Title')
                                    ->required()
                                    ->maxLength(255)
                                    ->default(fn () => request()->query('movie_title'))
                                    ->columnSpan(2),
                            ]),

                        Forms\Components\Hidden::make('poster_path')
                            ->default(fn () => request()->query('poster_path')),

                        Forms\Components\Placeholder::make('poster_preview')
                            ->label('Poster Preview')
                            ->content(function ($get) {
                                $posterPath = $get('poster_path');
                                if ($posterPath) {
                                    $url = "https://image.tmdb.org/t/p/w185{$posterPath}";
                                    return new \Illuminate\Support\HtmlString(
                                        "<img src='{$url}' alt='Movie Poster' class='rounded-lg shadow-md w-32'>"
                                    );
                                }
                                return 'No poster available';
                            })
                            ->visible(fn ($get) => !empty($get('poster_path'))),
                    ]),

                Forms\Components\Section::make('Schedule Details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('studio_id')
                                    ->label('Studio')
                                    ->options(Studio::pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        // Auto-adjust price based on studio type
                                        if ($state) {
                                            $studio = Studio::find($state);
                                            $basePrice = $get('price') ?: 50000;
                                            $multiplier = $studio?->getPriceMultiplier() ?? 1;
                                            $set('price', $basePrice * $multiplier);
                                        }
                                    }),

                                Forms\Components\TextInput::make('price')
                                    ->label('Ticket Price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(50000),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DateTimePicker::make('start_time')
                                    ->label('Start Time')
                                    ->required()
                                    ->seconds(false)
                                    ->minutesStep(15)
                                    ->default(now()->addDay()->setTime(14, 0))
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state) {
                                            // Default duration 2.5 hours
                                            $endTime = \Carbon\Carbon::parse($state)->addMinutes(150);
                                            $set('end_time', $endTime);
                                        }
                                    }),

                                Forms\Components\DateTimePicker::make('end_time')
                                    ->label('End Time')
                                    ->required()
                                    ->seconds(false)
                                    ->minutesStep(15)
                                    ->after('start_time'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('poster_path')
                    ->label('Poster')
                    ->getStateUsing(fn ($record) => $record->poster_path 
                        ? "https://image.tmdb.org/t/p/w92{$record->poster_path}" 
                        : null)
                    ->width(50)
                    ->height(75),

                Tables\Columns\TextColumn::make('movie_title')
                    ->label('Movie')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('studio.name')
                    ->label('Studio')
                    ->badge()
                    ->color(fn ($record) => match ($record->studio?->type) {
                        'Premier' => 'warning',
                        '3D' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Show Time')
                    ->dateTime('D, M j Y - H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money('IDR')
                    ->sortable(),

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
            ->filters([
                Tables\Filters\SelectFilter::make('studio')
                    ->relationship('studio', 'name'),
                    
                Tables\Filters\Filter::make('upcoming')
                    ->label('Upcoming Only')
                    ->query(fn (Builder $query): Builder => $query->where('start_time', '>', now()))
                    ->toggle(),

                Tables\Filters\Filter::make('today')
                    ->label('Today')
                    ->query(fn (Builder $query): Builder => $query->whereDate('start_time', today()))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Showtime $record) {
                        // Check if there are any active bookings
                        if ($record->bookings()->whereIn('status', ['booked', 'paid'])->exists()) {
                            throw new \Exception('Cannot delete showtime with active bookings.');
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('start_time', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShowtimes::route('/'),
            'create' => Pages\CreateShowtime::route('/create'),
            'edit' => Pages\EditShowtime::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('start_time', '>', now())->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }
}

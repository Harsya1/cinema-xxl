<?php

namespace App\Filament\Resources;

use App\Enums\StudioType;
use App\Filament\Resources\StudioResource\Pages;
use App\Models\Studio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StudioResource extends Resource
{
    protected static ?string $model = Studio::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Cinema Operations';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Studio Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Studio Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Studio 1, Premier Hall'),

                        Forms\Components\Select::make('type')
                            ->label('Studio Type')
                            ->options(StudioType::class)
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('capacity')
                            ->label('Seating Capacity')
                            ->required()
                            ->numeric()
                            ->minValue(10)
                            ->maxValue(500)
                            ->default(100)
                            ->suffix('seats'),

                        Forms\Components\Textarea::make('seat_layout')
                            ->label('Seat Layout (JSON)')
                            ->helperText('Define the seat layout in JSON format. Leave empty for auto-generated layout.')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])->columns(3),

                Forms\Components\Section::make('Preview')
                    ->schema([
                        Forms\Components\Placeholder::make('price_multiplier')
                            ->label('Price Multiplier')
                            ->content(function (Forms\Get $get) {
                                $type = $get('type');
                                if (!$type) return '-';
                                
                                $multiplier = match ($type) {
                                    'Regular' => '1.0x',
                                    '3D' => '1.25x',
                                    'Premier' => '1.5x',
                                    default => '1.0x',
                                };
                                return $multiplier;
                            }),

                        Forms\Components\Placeholder::make('estimated_revenue')
                            ->label('Max Revenue per Show')
                            ->content(function (Forms\Get $get) {
                                $capacity = (int) $get('capacity') ?: 100;
                                $type = $get('type') ?: 'Regular';
                                
                                $multiplier = match ($type) {
                                    'Regular' => 1,
                                    '3D' => 1.25,
                                    'Premier' => 1.5,
                                    default => 1,
                                };
                                
                                $basePrice = 50000;
                                $revenue = $capacity * $basePrice * $multiplier;
                                
                                return 'Rp ' . number_format($revenue, 0, ',', '.');
                            }),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof StudioType ? $state->label() : $state)
                    ->color(fn ($state): string => $state instanceof StudioType ? $state->color() : 'gray'),

                Tables\Columns\TextColumn::make('capacity')
                    ->label('Capacity')
                    ->suffix(' seats')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('showtimes_count')
                    ->label('Active Shows')
                    ->counts(['showtimes' => fn ($query) => $query->where('start_time', '>', now())])
                    ->badge()
                    ->color('info')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('bookings_count')
                    ->label('Total Bookings')
                    ->getStateUsing(fn (Studio $record) => $record->showtimes()->withCount('bookings')->get()->sum('bookings_count'))
                    ->badge()
                    ->color('success')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(StudioType::class),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Studio $record) {
                        if ($record->showtimes()->where('start_time', '>', now())->exists()) {
                            throw new \Exception('Cannot delete studio with upcoming showtimes.');
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListStudios::route('/'),
            'create' => Pages\CreateStudio::route('/create'),
            'edit' => Pages\EditStudio::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}

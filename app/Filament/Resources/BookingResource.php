<?php

namespace App\Filament\Resources;

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Cinema Operations';
    protected static ?int $navigationSort = 3;
    protected static ?string $recordTitleAttribute = 'booking_code';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Booking Information')
                    ->schema([
                        Forms\Components\TextInput::make('booking_code')
                            ->label('Booking Code')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn (string $operation) => $operation === 'edit'),

                        Forms\Components\Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('showtime_id')
                            ->label('Showtime')
                            ->relationship('showtime', 'movie_title', fn (Builder $query) => $query->where('start_time', '>', now()))
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->movie_title} - {$record->studio->name} - {$record->start_time->format('M j, H:i')}")
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),

                        Forms\Components\TextInput::make('seat_number')
                            ->label('Seat Number')
                            ->required()
                            ->maxLength(10)
                            ->placeholder('e.g., A1, B5'),
                    ])->columns(2),

                Forms\Components\Section::make('Payment Details')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(BookingStatus::class)
                            ->required()
                            ->native(false)
                            ->default(BookingStatus::Booked),

                        Forms\Components\Select::make('payment_method')
                            ->options(PaymentMethod::class)
                            ->native(false),

                        Forms\Components\TextInput::make('total_price')
                            ->label('Total Price')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('showtime.movie_title')
                    ->label('Movie')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('showtime.start_time')
                    ->label('Show Time')
                    ->dateTime('M j, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('seat_number')
                    ->label('Seat')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (BookingStatus $state): string => $state->color()),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Payment')
                    ->badge()
                    ->color('gray')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Booked At')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(BookingStatus::class)
                    ->multiple(),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->options(PaymentMethod::class),

                Tables\Filters\Filter::make('today')
                    ->label('Today\'s Shows')
                    ->query(fn (Builder $query): Builder => $query->whereHas('showtime', fn ($q) => $q->whereDate('start_time', today())))
                    ->toggle(),

                Tables\Filters\Filter::make('unpaid')
                    ->label('Unpaid Only')
                    ->query(fn (Builder $query): Builder => $query->where('status', BookingStatus::Booked))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_paid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Booking $record) => $record->status === BookingStatus::Booked)
                    ->form([
                        Forms\Components\Select::make('payment_method')
                            ->options(PaymentMethod::class)
                            ->required()
                            ->native(false),
                    ])
                    ->action(function (Booking $record, array $data) {
                        $record->update([
                            'status' => BookingStatus::Paid,
                            'payment_method' => $data['payment_method'],
                        ]);
                    }),

                Tables\Actions\Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Booking $record) => in_array($record->status, [BookingStatus::Booked, BookingStatus::Paid]))
                    ->requiresConfirmation()
                    ->action(fn (Booking $record) => $record->update(['status' => BookingStatus::Cancelled])),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', BookingStatus::Booked)->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}

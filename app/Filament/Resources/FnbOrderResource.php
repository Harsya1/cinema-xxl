<?php

namespace App\Filament\Resources;

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Filament\Resources\FnbOrderResource\Pages;
use App\Models\FnbOrder;
use App\Models\MenuItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FnbOrderResource extends Resource
{
    protected static ?string $model = FnbOrder::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Food & Beverage';
    protected static ?int $navigationSort = 3;
    protected static ?string $recordTitleAttribute = 'order_code';
    protected static ?string $modelLabel = 'F&B Order';
    protected static ?string $pluralModelLabel = 'F&B Orders';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Information')
                    ->schema([
                        Forms\Components\TextInput::make('order_code')
                            ->label('Order Code')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn (string $operation) => $operation === 'edit'),

                        Forms\Components\Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->placeholder('Walk-in Customer'),

                        Forms\Components\Select::make('booking_id')
                            ->label('Linked to Booking')
                            ->relationship('booking', 'booking_code')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->placeholder('No Booking'),
                    ])->columns(3),

                Forms\Components\Section::make('Order Items')
                    ->schema([
                        Forms\Components\Repeater::make('details')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('menu_item_id')
                                    ->label('Menu Item')
                                    ->options(MenuItem::where('is_available', true)->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state) {
                                            $menuItem = MenuItem::find($state);
                                            $set('unit_price', $menuItem?->price ?? 0);
                                        }
                                    }),

                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(1),

                                Forms\Components\TextInput::make('unit_price')
                                    ->label('Unit Price')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated(true),

                                Forms\Components\Placeholder::make('subtotal')
                                    ->label('Subtotal')
                                    ->content(function (Forms\Get $get) {
                                        $qty = (int) $get('quantity') ?: 1;
                                        $price = (float) $get('unit_price') ?: 0;
                                        return 'Rp ' . number_format($qty * $price, 0, ',', '.');
                                    }),
                            ])
                            ->columns(4)
                            ->itemLabel(fn (array $state): ?string => MenuItem::find($state['menu_item_id'] ?? null)?->name ?? 'New Item')
                            ->addActionLabel('Add Item')
                            ->reorderable(false)
                            ->collapsible()
                            ->defaultItems(1),
                    ]),

                Forms\Components\Section::make('Payment')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(BookingStatus::class)
                            ->required()
                            ->native(false)
                            ->default(BookingStatus::Booked),

                        Forms\Components\Select::make('payment_method')
                            ->options(PaymentMethod::class)
                            ->native(false),

                        Forms\Components\TextInput::make('total_amount')
                            ->label('Total Amount')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(true),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->placeholder('Walk-in'),

                Tables\Columns\TextColumn::make('booking.booking_code')
                    ->label('Booking')
                    ->placeholder('-')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('details_count')
                    ->label('Items')
                    ->counts('details')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('total_amount')
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
                    ->label('Ordered At')
                    ->dateTime('M j, H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(BookingStatus::class),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->options(PaymentMethod::class),

                Tables\Filters\Filter::make('today')
                    ->label('Today')
                    ->query(fn (Builder $query): Builder => $query->whereDate('created_at', today()))
                    ->toggle(),

                Tables\Filters\Filter::make('pending')
                    ->label('Pending Payment')
                    ->query(fn (Builder $query): Builder => $query->where('status', BookingStatus::Booked))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_paid')
                    ->label('Mark Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (FnbOrder $record) => $record->status === BookingStatus::Booked)
                    ->form([
                        Forms\Components\Select::make('payment_method')
                            ->options(PaymentMethod::class)
                            ->required()
                            ->native(false),
                    ])
                    ->action(function (FnbOrder $record, array $data) {
                        $record->update([
                            'status' => BookingStatus::Paid,
                            'payment_method' => $data['payment_method'],
                        ]);
                    }),

                Tables\Actions\Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (FnbOrder $record) => $record->status === BookingStatus::Booked)
                    ->requiresConfirmation()
                    ->action(fn (FnbOrder $record) => $record->update(['status' => BookingStatus::Cancelled])),

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
            'index' => Pages\ListFnbOrders::route('/'),
            'create' => Pages\CreateFnbOrder::route('/create'),
            'edit' => Pages\EditFnbOrder::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::where('status', BookingStatus::Booked)->count();
        return $pending > 0 ? $pending : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}

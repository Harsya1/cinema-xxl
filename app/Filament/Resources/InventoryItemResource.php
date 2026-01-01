<?php

namespace App\Filament\Resources;

use App\Enums\InventoryType;
use App\Filament\Resources\InventoryItemResource\Pages;
use App\Models\InventoryItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InventoryItemResource extends Resource
{
    protected static ?string $model = InventoryItem::class;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Food & Beverage';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationLabel = 'Inventory';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Item Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('type')
                            ->options(InventoryType::class)
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('unit')
                            ->required()
                            ->maxLength(50)
                            ->placeholder('e.g., pcs, kg, ml, liter'),
                    ])->columns(2),

                Forms\Components\Section::make('Stock Management')
                    ->schema([
                        Forms\Components\TextInput::make('stock_quantity')
                            ->label('Current Stock')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Forms\Components\TextInput::make('min_stock_level')
                            ->label('Minimum Stock Level')
                            ->required()
                            ->numeric()
                            ->default(10)
                            ->minValue(0)
                            ->helperText('Alert will be shown when stock falls below this level'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof InventoryType ? $state->label() : ucfirst($state))
                    ->color(fn ($state): string => $state instanceof InventoryType ? $state->color() : 'gray'),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->sortable()
                    ->color(fn (InventoryItem $record): string => $record->isLowStock() ? 'danger' : 'success')
                    ->weight(fn (InventoryItem $record): string => $record->isLowStock() ? 'bold' : 'normal')
                    ->formatStateUsing(fn (InventoryItem $record): string => "{$record->stock_quantity} {$record->unit}"),

                Tables\Columns\TextColumn::make('min_stock_level')
                    ->label('Min. Stock')
                    ->formatStateUsing(fn (InventoryItem $record): string => "{$record->min_stock_level} {$record->unit}")
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('low_stock')
                    ->label('Low Stock')
                    ->getStateUsing(fn (InventoryItem $record): bool => $record->isLowStock())
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->trueColor('danger')
                    ->falseIcon('heroicon-o-check-circle')
                    ->falseColor('success'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(InventoryType::class),

                Tables\Filters\Filter::make('low_stock')
                    ->label('Low Stock Only')
                    ->query(fn (Builder $query): Builder => $query->whereColumn('stock_quantity', '<=', 'min_stock_level'))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\Action::make('add_stock')
                    ->label('Add Stock')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantity to Add')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                    ])
                    ->action(function (InventoryItem $record, array $data) {
                        $record->increment('stock_quantity', $data['quantity']);
                    }),

                Tables\Actions\Action::make('reduce_stock')
                    ->label('Reduce Stock')
                    ->icon('heroicon-o-minus-circle')
                    ->color('warning')
                    ->form([
                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantity to Remove')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                    ])
                    ->action(function (InventoryItem $record, array $data) {
                        $newStock = max(0, $record->stock_quantity - $data['quantity']);
                        $record->update(['stock_quantity' => $newStock]);
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
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
            'index' => Pages\ListInventoryItems::route('/'),
            'create' => Pages\CreateInventoryItem::route('/create'),
            'edit' => Pages\EditInventoryItem::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $lowStock = static::getModel()::whereColumn('stock_quantity', '<=', 'min_stock_level')->count();
        return $lowStock > 0 ? $lowStock : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }
}

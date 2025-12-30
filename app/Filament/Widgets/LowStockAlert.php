<?php

namespace App\Filament\Widgets;

use App\Models\InventoryItem;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockAlert extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 1;
    protected static ?string $heading = 'Low Stock Alert';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                InventoryItem::query()
                    ->whereColumn('stock_quantity', '<=', 'min_stock_level')
                    ->orderBy('stock_quantity')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->weight('bold')
                    ->size('sm'),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Current')
                    ->formatStateUsing(fn ($record) => "{$record->stock_quantity} {$record->unit}")
                    ->color('danger')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('min_stock_level')
                    ->label('Min')
                    ->formatStateUsing(fn ($record) => "{$record->min_stock_level} {$record->unit}"),
            ])
            ->paginated(false)
            ->emptyStateHeading('All Good!')
            ->emptyStateDescription('No items are running low on stock.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}

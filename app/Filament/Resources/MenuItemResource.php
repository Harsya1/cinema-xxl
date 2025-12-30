<?php

namespace App\Filament\Resources;

use App\Enums\MenuCategory;
use App\Filament\Resources\MenuItemResource\Pages;
use App\Models\MenuItem;
use App\Models\InventoryItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;
    protected static ?string $navigationIcon = 'heroicon-o-cake';
    protected static ?string $navigationGroup = 'Food & Beverage';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Menu Item Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('category')
                            ->options(MenuCategory::class)
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0),

                        Forms\Components\Toggle::make('is_available')
                            ->label('Available for Sale')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Recipe / Ingredients')
                    ->description('Define the ingredients needed to make this menu item')
                    ->schema([
                        Forms\Components\Repeater::make('recipes')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('inventory_item_id')
                                    ->label('Ingredient')
                                    ->options(InventoryItem::pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\TextInput::make('quantity_needed')
                                    ->label('Quantity Needed')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0.01)
                                    ->step(0.01),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => InventoryItem::find($state['inventory_item_id'] ?? null)?->name ?? 'New Ingredient')
                            ->addActionLabel('Add Ingredient')
                            ->reorderable(false)
                            ->collapsible()
                            ->cloneable(),
                    ]),
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

                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn (MenuCategory $state): string => $state->color()),

                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_available')
                    ->label('Available')
                    ->boolean(),

                Tables\Columns\TextColumn::make('recipes_count')
                    ->label('Ingredients')
                    ->counts('recipes')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('fnb_order_details_count')
                    ->label('Times Ordered')
                    ->counts('fnbOrderDetails')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options(MenuCategory::class),

                Tables\Filters\TernaryFilter::make('is_available')
                    ->label('Availability'),
            ])
            ->actions([
                Tables\Actions\Action::make('toggle_availability')
                    ->label(fn (MenuItem $record) => $record->is_available ? 'Mark Unavailable' : 'Mark Available')
                    ->icon(fn (MenuItem $record) => $record->is_available ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (MenuItem $record) => $record->is_available ? 'danger' : 'success')
                    ->action(fn (MenuItem $record) => $record->update(['is_available' => !$record->is_available])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('category');
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
            'index' => Pages\ListMenuItems::route('/'),
            'create' => Pages\CreateMenuItem::route('/create'),
            'edit' => Pages\EditMenuItem::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_available', true)->count();
    }
}

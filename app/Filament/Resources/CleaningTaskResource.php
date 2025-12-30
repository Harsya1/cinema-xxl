<?php

namespace App\Filament\Resources;

use App\Enums\CleaningStatus;
use App\Filament\Resources\CleaningTaskResource\Pages;
use App\Models\CleaningTask;
use App\Models\Studio;
use App\Models\User;
use App\Enums\UserRole;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CleaningTaskResource extends Resource
{
    protected static ?string $model = CleaningTask::class;
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = 'Cinema Operations';
    protected static ?int $navigationSort = 5;
    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Task Details')
                    ->schema([
                        Forms\Components\Select::make('studio_id')
                            ->label('Studio')
                            ->options(Studio::pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('showtime_id')
                            ->label('After Showtime')
                            ->relationship('showtime', 'movie_title', fn (Builder $query) => $query->where('end_time', '<', now()->addHours(6)))
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->movie_title} - {$record->end_time->format('M j, H:i')}")
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\Select::make('assigned_to')
                            ->label('Assigned To')
                            ->options(User::where('role', UserRole::Cleaner)->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\Select::make('status')
                            ->options(CleaningStatus::class)
                            ->required()
                            ->native(false)
                            ->default(CleaningStatus::Pending),
                    ])->columns(2),

                Forms\Components\Section::make('Completion')
                    ->schema([
                        Forms\Components\DateTimePicker::make('completed_at')
                            ->label('Completed At')
                            ->seconds(false)
                            ->visible(fn (Forms\Get $get) => $get('status') === CleaningStatus::Completed->value),
                    ])
                    ->visible(fn (string $operation) => $operation === 'edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('studio.name')
                    ->label('Studio')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('showtime.movie_title')
                    ->label('After Movie')
                    ->limit(25)
                    ->placeholder('General Cleaning'),

                Tables\Columns\TextColumn::make('showtime.end_time')
                    ->label('Show Ended')
                    ->dateTime('M j, H:i')
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Assigned To')
                    ->searchable()
                    ->placeholder('Unassigned')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (CleaningStatus $state): string => $state->color()),

                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Completed')
                    ->dateTime('M j, H:i')
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(CleaningStatus::class),

                Tables\Filters\SelectFilter::make('studio')
                    ->relationship('studio', 'name'),

                Tables\Filters\SelectFilter::make('assigned_to')
                    ->label('Assigned To')
                    ->options(User::where('role', UserRole::Cleaner)->pluck('name', 'id')),

                Tables\Filters\Filter::make('pending')
                    ->label('Pending Only')
                    ->query(fn (Builder $query): Builder => $query->where('status', CleaningStatus::Pending))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\Action::make('start')
                    ->label('Start Cleaning')
                    ->icon('heroicon-o-play')
                    ->color('warning')
                    ->visible(fn (CleaningTask $record) => $record->status === CleaningStatus::Pending)
                    ->action(fn (CleaningTask $record) => $record->update(['status' => CleaningStatus::InProgress])),

                Tables\Actions\Action::make('complete')
                    ->label('Mark Complete')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (CleaningTask $record) => $record->status === CleaningStatus::InProgress)
                    ->action(fn (CleaningTask $record) => $record->update([
                        'status' => CleaningStatus::Completed,
                        'completed_at' => now(),
                    ])),

                Tables\Actions\Action::make('assign')
                    ->label('Assign')
                    ->icon('heroicon-o-user-plus')
                    ->color('info')
                    ->visible(fn (CleaningTask $record) => !$record->assigned_to && $record->status !== CleaningStatus::Completed)
                    ->form([
                        Forms\Components\Select::make('assigned_to')
                            ->label('Assign To')
                            ->options(User::where('role', UserRole::Cleaner)->pluck('name', 'id'))
                            ->required(),
                    ])
                    ->action(fn (CleaningTask $record, array $data) => $record->update($data)),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListCleaningTasks::route('/'),
            'create' => Pages\CreateCleaningTask::route('/create'),
            'edit' => Pages\EditCleaningTask::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::where('status', CleaningStatus::Pending)->count();
        return $pending > 0 ? $pending : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}

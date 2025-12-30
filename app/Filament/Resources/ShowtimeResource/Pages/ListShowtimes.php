<?php

namespace App\Filament\Resources\ShowtimeResource\Pages;

use App\Filament\Resources\ShowtimeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShowtimes extends ListRecords
{
    protected static string $resource = ShowtimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('browse_movies')
                ->label('Browse TMDb Movies')
                ->icon('heroicon-o-magnifying-glass')
                ->url(route('filament.admin.pages.movie-browser'))
                ->color('info'),
            Actions\CreateAction::make()
                ->label('Quick Add Showtime'),
        ];
    }
}

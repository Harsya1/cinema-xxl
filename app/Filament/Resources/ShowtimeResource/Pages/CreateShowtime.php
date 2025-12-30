<?php

namespace App\Filament\Resources\ShowtimeResource\Pages;

use App\Filament\Resources\ShowtimeResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateShowtime extends CreateRecord
{
    protected static string $resource = ShowtimeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Showtime Created')
            ->body('The showtime has been added to the schedule.');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure proper data types
        $data['tmdb_movie_id'] = (int) $data['tmdb_movie_id'];
        $data['price'] = (int) $data['price'];
        
        return $data;
    }
}

<?php

namespace App\Filament\Resources\ShowtimeResource\Pages;

use App\Filament\Resources\ShowtimeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditShowtime extends EditRecord
{
    protected static string $resource = ShowtimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function () {
                    if ($this->record->bookings()->whereIn('status', ['booked', 'paid'])->exists()) {
                        Notification::make()
                            ->danger()
                            ->title('Cannot Delete')
                            ->body('This showtime has active bookings and cannot be deleted.')
                            ->send();
                            
                        $this->halt();
                    }
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Showtime Updated')
            ->body('The showtime has been updated successfully.');
    }
}

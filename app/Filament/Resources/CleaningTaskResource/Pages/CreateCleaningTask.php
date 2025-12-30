<?php

namespace App\Filament\Resources\CleaningTaskResource\Pages;

use App\Filament\Resources\CleaningTaskResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCleaningTask extends CreateRecord
{
    protected static string $resource = CleaningTaskResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

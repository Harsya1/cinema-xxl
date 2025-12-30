<?php

namespace App\Filament\Resources\FnbOrderResource\Pages;

use App\Filament\Resources\FnbOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFnbOrders extends ListRecords
{
    protected static string $resource = FnbOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

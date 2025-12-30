<?php

namespace App\Filament\Resources\FnbOrderResource\Pages;

use App\Filament\Resources\FnbOrderResource;
use App\Models\MenuItem;
use Filament\Resources\Pages\CreateRecord;

class CreateFnbOrder extends CreateRecord
{
    protected static string $resource = FnbOrderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generate order code
        $data['order_code'] = 'FNB-' . strtoupper(uniqid());
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // Calculate total amount from details
        $total = $this->record->details->sum(fn ($detail) => $detail->quantity * $detail->unit_price);
        $this->record->update(['total_amount' => $total]);
    }
}

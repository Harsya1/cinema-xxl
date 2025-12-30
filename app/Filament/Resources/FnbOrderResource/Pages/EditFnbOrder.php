<?php

namespace App\Filament\Resources\FnbOrderResource\Pages;

use App\Filament\Resources\FnbOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFnbOrder extends EditRecord
{
    protected static string $resource = FnbOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        // Recalculate total amount
        $total = $this->record->details->sum(fn ($detail) => $detail->quantity * $detail->unit_price);
        $this->record->update(['total_amount' => $total]);
    }
}

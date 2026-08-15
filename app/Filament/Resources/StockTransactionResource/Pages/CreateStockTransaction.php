<?php

namespace App\Filament\Resources\StockTransactionResource\Pages;

use App\Filament\Resources\StockTransactionResource;
use App\Models\MedicalStockItem;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateStockTransaction extends CreateRecord
{
    protected static string $resource = StockTransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['recorded_by'] = auth()->id();

        // Negative-stock guard for expired/damaged writes (usage lines go
        // through the visit form which already guards).
        if ($data['transaction_type'] === 'expired_damaged') {
            $item = MedicalStockItem::find($data['medical_stock_item_id']);
            if ($item && $item->current_balance - (int) $data['quantity'] < 0) {
                Notification::make()
                    ->title('Would push balance below zero')
                    ->body("Current balance for {$item->name} is {$item->current_balance}. Log an adjustment instead.")
                    ->danger()->persistent()->send();
                $this->halt();
            }
        }
        return $data;
    }
}

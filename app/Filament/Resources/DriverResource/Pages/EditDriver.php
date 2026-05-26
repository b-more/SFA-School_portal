<?php

namespace App\Filament\Resources\DriverResource\Pages;

use App\Filament\Resources\DriverResource;
use App\Models\BusFareStructure;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDriver extends EditRecord
{
    protected static string $resource = DriverResource::class;

    protected ?array $assignedRouteIds = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['route_ids'] = $this->record->busRoutes()->pluck('id')->toArray();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->assignedRouteIds = array_map('intval', array_filter((array) ($data['route_ids'] ?? [])));
        unset($data['route_ids']);

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->assignedRouteIds === null) {
            return;
        }

        BusFareStructure::query()
            ->where('driver_user_id', $this->record->id)
            ->whereNotIn('id', $this->assignedRouteIds ?: [0])
            ->update(['driver_user_id' => null]);

        if (! empty($this->assignedRouteIds)) {
            BusFareStructure::query()
                ->whereIn('id', $this->assignedRouteIds)
                ->where(function ($q) {
                    $q->whereNull('driver_user_id')->orWhere('driver_user_id', $this->record->id);
                })
                ->update(['driver_user_id' => $this->record->id]);
        }
    }
}

<?php

namespace App\Filament\Resources\HomeworkResource\Pages;

use App\Filament\Resources\HomeworkResource;
use App\Traits\HasPageGuide;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHomework extends ListRecords
{
    use HasPageGuide;

    protected static string $resource = HomeworkResource::class;

    protected function getGuideSlug(): string
    {
        return 'homework';
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getPageGuideAction(),
            Actions\CreateAction::make(),
        ];
    }
}

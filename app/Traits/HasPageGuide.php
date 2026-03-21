<?php

namespace App\Traits;

use Filament\Actions\Action;

trait HasPageGuide
{
    protected function getPageGuideAction(): Action
    {
        return Action::make('pageGuide')
            ->label('Page Guide')
            ->icon('heroicon-o-book-open')
            ->color('gray')
            ->url(route('guides.page', ['page' => $this->getGuideSlug()]))
            ->openUrlInNewTab();
    }

    abstract protected function getGuideSlug(): string;
}

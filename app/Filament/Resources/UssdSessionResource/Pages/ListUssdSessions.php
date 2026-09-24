<?php

namespace App\Filament\Resources\UssdSessionResource\Pages;

use App\Filament\Resources\UssdSessionResource;
use Filament\Resources\Pages\ListRecords;

class ListUssdSessions extends ListRecords
{
    protected static string $resource = UssdSessionResource::class;

    public function getHeading(): string
    {
        return 'USSD Sessions';
    }

    public function getSubheading(): ?string
    {
        return 'Every parent who dialed the school USSD code (*388*XX#). Click a row to see the full session transcript.';
    }
}

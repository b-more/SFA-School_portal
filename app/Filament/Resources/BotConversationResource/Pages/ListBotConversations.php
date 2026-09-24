<?php

namespace App\Filament\Resources\BotConversationResource\Pages;

use App\Filament\Resources\BotConversationResource;
use Filament\Resources\Pages\ListRecords;

class ListBotConversations extends ListRecords
{
    protected static string $resource = BotConversationResource::class;

    public function getHeading(): string
    {
        return 'WhatsApp Bot Conversations';
    }

    public function getSubheading(): ?string
    {
        return 'Every parent (or prospective parent) who has messaged the school WhatsApp bot. Click a row to see the full thread.';
    }
}

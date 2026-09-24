<?php

namespace App\Filament\Resources\BotConversationResource\Pages;

use App\Filament\Resources\BotConversationResource;
use App\Models\BotConversation;
use App\Models\ParentGuardian;
use Filament\Resources\Pages\ViewRecord;

class ViewBotConversation extends ViewRecord
{
    protected static string $resource = BotConversationResource::class;

    // Custom Blade so we can render WhatsApp-style chat bubbles.
    protected static string $view = 'filament.resources.bot-conversation.thread';

    public ?ParentGuardian $guardian = null;
    public string $phone = '';
    public string $displayPhone = '';
    public array $groupedByDay = [];
    public int $inboundCount = 0;
    public int $outboundCount = 0;

    /**
     * Called by ViewRecord::mount() after the record is resolved from
     * the URL. The seed record's id came from our aggregated MAX(id) query
     * on the list page — we use it only to find the phone, then load the
     * full thread by phone.
     */
    protected function afterFill(): void
    {
        $this->hydrateThread();
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);
        $this->hydrateThread();
    }

    private function hydrateThread(): void
    {
        /** @var BotConversation $seed */
        $seed = $this->record;
        if (! $seed) {
            return;
        }
        $this->phone = $seed->phone;
        $this->displayPhone = $seed->display_phone;
        $this->guardian = $seed->guardian;

        $messages = BotConversation::where('phone', $this->phone)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $this->inboundCount  = $messages->where('direction', 'in')->count();
        $this->outboundCount = $messages->where('direction', 'out')->count();

        $this->groupedByDay = $messages->groupBy(fn ($m) => $m->created_at->format('Y-m-d'))
            ->map(fn ($group, $day) => [
                'day' => $day,
                'label' => \Carbon\Carbon::parse($day)->isToday() ? 'Today'
                       : (\Carbon\Carbon::parse($day)->isYesterday() ? 'Yesterday'
                       : \Carbon\Carbon::parse($day)->format('D, d M Y')),
                'messages' => $group->map(fn ($m) => [
                    'id'        => $m->id,
                    'direction' => $m->direction,
                    'kind'      => $m->kind,
                    'content'   => $m->content,
                    'meta'      => $m->meta,
                    'time'      => $m->created_at->format('H:i'),
                    'when'      => $m->created_at->format('d M Y, H:i:s'),
                    'state'     => $m->flow_state,
                ])->values()->all(),
            ])
            ->values()
            ->all();
    }

    public function getHeading(): string
    {
        return $this->displayPhone ?: 'Conversation';
    }

    public function getSubheading(): ?string
    {
        if ($this->guardian) {
            $name = trim($this->guardian->name ?? '') ?: ('Guardian #' . $this->guardian->id);
            return "🟢 Linked to {$name}";
        }
        return '⚪ Not linked to any parent record';
    }

    // ViewRecord auto-shows the built-in infolist form; suppress it since
    // our Blade renders everything itself.
    protected function hasInfolist(): bool
    {
        return false;
    }
}

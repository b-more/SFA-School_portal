<?php

namespace App\Filament\Resources\UssdSessionResource\Pages;

use App\Filament\Resources\UssdSessionResource;
use App\Models\UssdSession;
use Filament\Resources\Pages\ViewRecord;

class ViewUssdSession extends ViewRecord
{
    protected static string $resource = UssdSessionResource::class;
    protected static string $view = 'filament.resources.ussd-session.transcript';

    public array $steps = [];
    public string $callerDisplay = '';
    public ?string $guardianName = null;

    protected function afterFill(): void
    {
        $this->hydrate();
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);
        $this->hydrate();
    }

    private function hydrate(): void
    {
        /** @var UssdSession $s */
        $s = $this->record;
        if (! $s) return;
        $this->callerDisplay = $s->display_msisdn;
        $this->guardianName = optional($s->guardian)->name;

        $this->steps = collect($s->transcript ?? [])
            ->map(function ($row) {
                $ts = null;
                try { $ts = \Carbon\Carbon::parse($row['ts'] ?? null); } catch (\Throwable) { $ts = null; }
                return [
                    'direction' => $row['dir'] ?? 'out',
                    'text'      => $row['text'] ?? '',
                    'time'      => $ts?->format('H:i:s'),
                    'when'      => $ts?->format('d M Y, H:i:s'),
                ];
            })
            ->values()
            ->all();
    }

    public function getHeading(): string
    {
        return $this->callerDisplay ?: 'USSD session';
    }

    public function getSubheading(): ?string
    {
        if ($this->guardianName) return "🟢 Linked to {$this->guardianName}";
        return '⚪ Not linked to any parent record';
    }

    protected function hasInfolist(): bool
    {
        return false;
    }
}

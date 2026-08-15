<?php

namespace App\Filament\Resources\ClinicVisitResource\Pages;

use App\Filament\Resources\ClinicVisitResource;
use App\Models\ClinicComplaint;
use App\Models\ClinicVisit;
use App\Models\MedicalStockItem;
use App\Services\ClinicParentNotifier;
use App\Services\StockLedgerService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateClinicVisit extends CreateRecord
{
    protected static string $resource = ClinicVisitResource::class;

    // ---- S5: after save, land back on the create form (student picker autofocuses) ----
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('create');
    }

    protected function getCreatedNotification(): ?Notification
    {
        $body = 'Stock deducted where applicable. Ready for the next patient.';
        if ($this->parentSmsFired) {
            $body .= ' · Parent SMS sent.';
        }
        return Notification::make()
            ->title('Visit recorded')
            ->body($body)
            ->success();
    }

    /* ------------------------------------------------------------------ */
    /*  S1 + S2: header actions to pre-fill the form                        */
    /* ------------------------------------------------------------------ */

    protected function getHeaderActions(): array
    {
        $quickPatterns = [
            'headache_panadol'    => ['label' => 'Headache + Panadol',          'complaints' => ['Headache'],       'item' => 'Panadol'],
            'stomach_brufen'      => ['label' => 'Stomach ache + Brufen',       'complaints' => ['Stomach ache'],   'item' => 'Brufen'],
            'fever_panadol_syrup' => ['label' => 'Fever + Panadol Syrup',       'complaints' => ['Fever'],          'item' => 'Panadol Syrup'],
        ];

        $actions = [];

        foreach ($quickPatterns as $key => $spec) {
            $actions[] = Actions\Action::make("quick_{$key}")
                ->label($spec['label'])
                ->icon('heroicon-o-bolt')
                ->color('warning')
                ->outlined()
                ->action(function () use ($spec) {
                    $complaintIds = ClinicComplaint::whereIn('name', $spec['complaints'])->pluck('id')->all();
                    $item         = MedicalStockItem::where('name', $spec['item'])->first();

                    $this->form->fill([
                        ...$this->form->getState(),  // preserve student + name + date + grade if already typed
                        'clinic_complaints' => $complaintIds,
                        'treatments'        => $item ? [['item_id' => $item->id, 'quantity' => 1]] : [],
                    ]);

                    Notification::make()->title("Filled: {$spec['label']}")->body('Pick a pupil and save.')->success()->send();
                });
        }

        // S1 — clone the most recent visit's complaint + treatment scaffold
        $actions[] = Actions\Action::make('same_as_last')
            ->label('Same as last patient')
            ->icon('heroicon-o-arrow-uturn-left')
            ->color('gray')
            ->action(function () {
                $prev = ClinicVisit::with(['complaints', 'stockTransactions'])
                    ->where('recorded_by', auth()->id())
                    ->orderByDesc('id')
                    ->first()
                    ?? ClinicVisit::with(['complaints', 'stockTransactions'])->orderByDesc('id')->first();

                if (! $prev) {
                    Notification::make()->title('No previous visit')->body('Record one first, then this button will clone it.')->warning()->send();
                    return;
                }

                $treatments = $prev->stockTransactions
                    ->where('transaction_type', 'usage')
                    ->map(fn ($tx) => ['item_id' => $tx->medical_stock_item_id, 'quantity' => (int) $tx->quantity])
                    ->values()
                    ->all();

                $this->form->fill([
                    ...$this->form->getState(),
                    'clinic_complaints' => $prev->complaints->pluck('id')->all(),
                    'treatments'        => $treatments,
                    'sick_note_issued'  => $prev->sick_note_issued,
                    'outcome'           => $prev->outcome ?? 'returned_to_class',
                ]);

                Notification::make()->title('Cloned last patient\'s clinical picture')
                    ->body('Pick a pupil — complaints + treatments already filled.')
                    ->success()->send();
            });

        return $actions;
    }

    /* ------------------------------------------------------------------ */
    /*  Save pipeline                                                       */
    /* ------------------------------------------------------------------ */

    protected array $pendingTreatments = [];
    protected bool  $parentSmsFired    = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['recorded_by']   = auth()->id();
        $data['outcome']       = $data['outcome'] ?? 'returned_to_class';
        $this->pendingTreatments = $data['treatments'] ?? [];
        unset($data['treatments']);
        return $data;
    }

    protected function afterCreate(): void
    {
        try {
            app(StockLedgerService::class)->syncVisitUsage($this->record, $this->pendingTreatments, auth()->id());
        } catch (\RuntimeException $e) {
            $this->record->delete();
            Notification::make()->title('Cannot dispense — visit not saved')
                ->body($e->getMessage())->danger()->persistent()->send();
            $this->halt();
        }

        // N1 — SMS the parent if the outcome warrants immediate contact.
        try {
            $notifier = app(ClinicParentNotifier::class);
            $notifier->notifyIfNeeded($this->record);
            $this->parentSmsFired = in_array($this->record->outcome, ['sent_home', 'referred'], true)
                                 || $this->record->sick_note_issued;
        } catch (\Throwable $e) {
            // notifier is fire-and-forget; visit stays saved regardless
        }
    }
}

<?php

namespace App\Filament\Resources\ClinicVisitResource\Pages;

use App\Constants\RoleConstants;
use App\Filament\Resources\ClinicVisitResource;
use App\Models\Student;
use App\Models\StockTransaction;
use App\Services\ClinicParentNotifier;
use App\Services\StockLedgerService;
use App\Support\PhoneNormalizer;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditClinicVisit extends EditRecord
{
    protected static string $resource = ClinicVisitResource::class;

    protected array $pendingTreatments = [];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Pre-fill the repeater from existing usage transactions on this visit.
        $data['treatments'] = StockTransaction::where('clinic_visit_id', $this->record->id)
            ->where('transaction_type', 'usage')
            ->get()
            ->map(fn ($tx) => ['item_id' => $tx->medical_stock_item_id, 'quantity' => $tx->quantity])
            ->toArray();
        return $data;
    }

    // Only the recording clinician can edit their own visits within 48 hours;
    // after that (or for anyone else), Admin must open the record.
    protected function beforeFill(): void
    {
        $user = auth()->user();
        $isAdmin = $user?->role_id === RoleConstants::ADMIN;
        $isRecorder = $this->record->recorded_by === $user?->id;
        $withinWindow = $this->record->created_at && $this->record->created_at->gt(now()->subHours(48));

        if (! $isAdmin && ! ($isRecorder && $withinWindow)) {
            Notification::make()
                ->title('Edit locked')
                ->body('This visit is older than 48 hours. Ask an Admin to make the correction.')
                ->warning()->persistent()->send();
            $this->halt();
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->pendingTreatments = $data['treatments'] ?? [];
        unset($data['treatments']);
        return $data;
    }

    protected function afterSave(): void
    {
        try {
            app(StockLedgerService::class)->syncVisitUsage($this->record, $this->pendingTreatments, auth()->id());
        } catch (\RuntimeException $e) {
            Notification::make()
                ->title('Stock refused this edit')
                ->body($e->getMessage() . ' Reverting.')
                ->danger()->persistent()->send();
            $this->halt();
        }

        // If the edit flipped the outcome to something parent-relevant, notify.
        try {
            app(ClinicParentNotifier::class)->notifyIfNeeded($this->record);
        } catch (\Throwable $e) { /* fire-and-forget */ }
    }

    protected function getHeaderActions(): array
    {
        // N4 — quick-dial parent. Only rendered when the visit is linked to a
        // student whose parent has a phone we can normalise to +260…
        $parentPhone = null;
        $parentName  = null;
        if ($this->record->student_id) {
            $student = Student::with('parentGuardian')->find($this->record->student_id);
            $raw     = $student?->parentGuardian?->phone;
            $parentPhone = PhoneNormalizer::normalize($raw ?? '');
            $parentName  = $student?->parentGuardian?->name;
        }

        return [
            Actions\Action::make('call_parent')
                ->label($parentPhone ? "Call {$parentName}" : 'Call parent')
                ->icon('heroicon-o-phone')
                ->color('success')
                ->url($parentPhone ? "tel:{$parentPhone}" : null)
                ->extraAttributes($parentPhone ? [] : ['disabled' => 'disabled', 'style' => 'opacity:0.5'])
                ->tooltip($parentPhone ? "Dial {$parentPhone}" : 'No parent phone on file for this pupil'),

            Actions\Action::make('sms_parent_now')
                ->label('SMS parent now')
                ->icon('heroicon-o-chat-bubble-oval-left')
                ->color('primary')
                ->visible(fn () => $parentPhone !== null)
                ->requiresConfirmation()
                ->modalHeading("SMS {$parentName}")
                ->modalDescription('Sends a message based on the visit\'s current outcome (sent-home / referred / sick-note).')
                ->action(function () {
                    app(ClinicParentNotifier::class)->notifyIfNeeded($this->record->fresh(['student.parentGuardian']));
                    Notification::make()->title('SMS dispatched (if the outcome warrants it)')->success()->send();
                }),

            Actions\DeleteAction::make()->visible(fn () => auth()->user()?->role_id === RoleConstants::ADMIN),
        ];
    }
}

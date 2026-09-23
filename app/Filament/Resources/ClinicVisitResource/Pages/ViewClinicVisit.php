<?php

namespace App\Filament\Resources\ClinicVisitResource\Pages;

use App\Filament\Resources\ClinicVisitResource;
use App\Models\ClinicVisit;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\DB;

class ViewClinicVisit extends ViewRecord
{
    protected static string $resource = ClinicVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('audit_trail')
                ->label('Audit trail')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('gray')
                ->modalHeading(fn () => 'Audit trail — visit #' . $this->record->id)
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->modalWidth('3xl')
                ->modalContent(fn () => view('filament.resources.clinic-visit-resource.audit-trail', [
                    'entries' => $this->auditEntries(),
                ])),

            Actions\EditAction::make(),
        ];
    }

    /**
     * Chronological trail of changes to this visit, oldest first, with the
     * actor's name resolved for display. Kept here (not in the observer) so
     * the write path stays lean and the read path can be shaped for UI.
     */
    protected function auditEntries(): array
    {
        return DB::select("
            SELECT a.id, a.event, a.user_id, u.name AS actor, a.ip_address,
                   a.old_values, a.new_values, a.created_at
              FROM audit_logs a
         LEFT JOIN users u ON u.id = a.user_id
             WHERE a.auditable_type = ?
               AND a.auditable_id   = ?
          ORDER BY a.id
        ", [ClinicVisit::class, $this->record->id]);
    }
}

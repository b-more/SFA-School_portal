<?php

namespace App\Filament\Resources\StudentFeeResource\Pages;

use App\Filament\Resources\StudentFeeResource;
use App\Models\AcademicYear;
use App\Models\Term;
use App\Services\StudentFeeService;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListStudentFees extends ListRecords
{
    protected static string $resource = StudentFeeResource::class;

    /**
     * Modify the table query to include necessary relationships
     */
    protected function getTableQuery(): Builder
    {
        return static::getResource()::getEloquentQuery()
            ->with([
                'student.grade',
                'student.parentGuardian',
                'feeStructure.grade',
                'feeStructure.term',
                'feeStructure.academicYear'
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Create Student Fee'),

            Actions\Action::make('exportUnpaid')
                ->label('Export Unpaid Fees')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->url(fn (): string => route('student-fees.export-unpaid'))
                ->openUrlInNewTab(),

            Actions\Action::make('sendBulkReminders')
                ->label('Send Payment Reminders')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Send Payment Reminders')
                ->modalDescription('This will send SMS payment reminders to all parents/guardians with outstanding balances. Continue?')
                ->action(function () {
                    $unpaidFees = static::getResource()::getEloquentQuery()
                        ->where('payment_status', '!=', 'paid')
                        ->where('balance', '>', 0)
                        ->with(['student.parentGuardian', 'feeStructure'])
                        ->get();

                    $successCount = 0;
                    $failedCount = 0;

                    foreach ($unpaidFees as $fee) {
                        try {
                            StudentFeeResource::sendPaymentSMS($fee);
                            $successCount++;
                        } catch (\Exception $e) {
                            $failedCount++;
                            \Illuminate\Support\Facades\Log::error('Failed to send bulk reminder SMS', [
                                'student_fee_id' => $fee->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }

                    Notification::make()
                        ->title('Bulk Reminders Sent')
                        ->body("Successfully sent: {$successCount}, Failed: {$failedCount}")
                        ->success($successCount > 0)
                        ->warning($failedCount > 0)
                        ->send();
                }),

            Actions\Action::make('previewTermFees')
                ->label('Preview Term Fees')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->form(self::termFeeFormSchema())
                ->modalHeading('Preview Term Fee Generation')
                ->modalSubmitActionLabel('Run Preview')
                ->action(function (array $data) {
                    $term = Term::find($data['term_id']);
                    $academicYear = AcademicYear::find($data['academic_year_id']);

                    if (!$term || !$academicYear) {
                        Notification::make()->title('Invalid selection')->danger()->send();
                        return;
                    }

                    $result = StudentFeeService::bulkCreateFeesForTerm(
                        $term,
                        $academicYear,
                        $data['enforce_balance_check'] ?? true,
                        dryRun: true
                    );

                    $body = "Will create: {$result['will_create']}\n"
                        . "Already exist: {$result['already_exists']}\n"
                        . "Blocked (balance): {$result['blocked_by_balance']}\n"
                        . "No fee structure: {$result['no_fee_structure']}";

                    if ($result['previous_term']) {
                        $body .= "\nPrevious term checked: {$result['previous_term']}";
                    }

                    if (!empty($result['blocked_students'])) {
                        $body .= "\n\n--- Blocked Students ---";
                        foreach (array_slice($result['blocked_students'], 0, 10) as $s) {
                            $body .= "\n{$s['name']} ({$s['grade']}) - K" . number_format($s['outstanding'], 2);
                        }
                        if (count($result['blocked_students']) > 10) {
                            $body .= "\n... and " . (count($result['blocked_students']) - 10) . " more";
                        }
                    }

                    Notification::make()
                        ->title("Preview: {$result['target_term']} {$result['academic_year']}")
                        ->body($body)
                        ->info()
                        ->persistent()
                        ->send();
                }),

            Actions\Action::make('generateTermFees')
                ->label('Generate Term Fees')
                ->icon('heroicon-o-plus-circle')
                ->color('success')
                ->form(self::termFeeFormSchema())
                ->modalHeading('Generate Term Fees')
                ->modalDescription('This will create fee records for all eligible active students. Use "Preview Term Fees" first to check the expected results.')
                ->modalSubmitActionLabel('Generate Fees')
                ->requiresConfirmation()
                ->action(function (array $data) {
                    $term = Term::find($data['term_id']);
                    $academicYear = AcademicYear::find($data['academic_year_id']);

                    if (!$term || !$academicYear) {
                        Notification::make()->title('Invalid selection')->danger()->send();
                        return;
                    }

                    $result = StudentFeeService::bulkCreateFeesForTerm(
                        $term,
                        $academicYear,
                        $data['enforce_balance_check'] ?? true,
                        dryRun: false
                    );

                    $title = $result['success']
                        ? "Fees Generated: {$result['target_term']} {$result['academic_year']}"
                        : 'Fee generation failed';

                    $body = "Created: {$result['created']}\n"
                        . "Blocked (balance): {$result['blocked_by_balance']}\n"
                        . "Already existed: {$result['already_exists']}\n"
                        . "No fee structure: {$result['no_fee_structure']}\n"
                        . "Errors: {$result['errors']}";

                    if (!empty($result['blocked_students'])) {
                        $body .= "\n\n--- Blocked Students ---";
                        foreach (array_slice($result['blocked_students'], 0, 10) as $s) {
                            $body .= "\n{$s['name']} ({$s['grade']}) - K" . number_format($s['outstanding'], 2);
                        }
                        if (count($result['blocked_students']) > 10) {
                            $body .= "\n... and " . (count($result['blocked_students']) - 10) . " more";
                        }
                    }

                    Notification::make()
                        ->title($title)
                        ->body($body)
                        ->{$result['success'] ? 'success' : 'danger'}()
                        ->persistent()
                        ->send();
                }),
        ];
    }

    /**
     * Shared form schema for the Generate/Preview term fee actions.
     */
    private static function termFeeFormSchema(): array
    {
        $activeYear = AcademicYear::where('is_active', true)->first();

        return [
            Select::make('academic_year_id')
                ->label('Academic Year')
                ->options(AcademicYear::orderByDesc('start_date')->pluck('name', 'id'))
                ->default($activeYear?->id)
                ->required()
                ->live(),

            Select::make('term_id')
                ->label('Term')
                ->options(function (Get $get) {
                    $yearId = $get('academic_year_id');
                    if (!$yearId) {
                        return [];
                    }
                    return Term::where('academic_year_id', $yearId)
                        ->orderBy('start_date')
                        ->pluck('name', 'id');
                })
                ->required()
                ->live(),

            Toggle::make('enforce_balance_check')
                ->label('Require previous term balance cleared')
                ->helperText('Students with outstanding balances from the previous term will be skipped')
                ->default(true),
        ];
    }

    /**
     * Get stats for the page header
     */
    public function getHeaderWidgets(): array
    {
        return [
            StudentFeeResource\Widgets\StudentFeeStatsWidget::class,
        ];
    }
}

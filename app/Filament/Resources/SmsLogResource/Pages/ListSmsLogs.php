<?php

namespace App\Filament\Resources\SmsLogResource\Pages;

use App\Filament\Resources\SmsLogResource;
use App\Models\SmsLog;
use App\Services\SmsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListSmsLogs extends ListRecords
{
    protected static string $resource = SmsLogResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\SmsLogResource\Widgets\SmsDashboardWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_report')
                ->label('Download Report')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->form([
                    DatePicker::make('from')
                        ->label('From Date')
                        ->required()
                        ->default(now()->startOfMonth())
                        ->native(false),

                    DatePicker::make('until')
                        ->label('To Date')
                        ->required()
                        ->default(now())
                        ->native(false),

                    Select::make('status')
                        ->label('Status (Optional)')
                        ->options([
                            'sent' => 'Sent',
                            'delivered' => 'Delivered',
                            'failed' => 'Failed',
                            'pending' => 'Pending',
                        ])
                        ->placeholder('All Statuses'),

                    Select::make('message_type')
                        ->label('Message Type (Optional)')
                        ->options([
                            'homework_notification' => 'Homework Notification',
                            'result_notification' => 'Result Notification',
                            'fee_reminder' => 'Fee Reminder',
                            'event_notification' => 'Event Notification',
                            'general' => 'General Message',
                            'broadcast' => 'Broadcast',
                            'student_credentials' => 'Student Credentials',
                            'staff_credentials' => 'Staff Credentials',
                            'leave_notification' => 'Leave Notification',
                            'other' => 'Other',
                        ])
                        ->placeholder('All Types'),
                ])
                ->action(function (array $data) {
                    return $this->generateReport($data);
                }),

            Actions\Action::make('retry_all_failed')
                ->label('Retry All Failed Messages')
                ->icon('heroicon-o-arrow-path')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function () {
                    $failedLogs = SmsLog::where('status', 'failed')
                        ->take(30)
                        ->get();

                    $total = $failedLogs->count();
                    $success = 0;
                    $failed = 0;
                    $smsService = app(SmsService::class);

                    foreach ($failedLogs as $log) {
                        $sent = $smsService->send(
                            $log->message,
                            $log->recipient,
                            $log->message_type ?? 'general',
                            $log->reference_id,
                        );

                        if ($sent) {
                            $success++;
                        } else {
                            $failed++;
                        }

                        // Small delay between sends
                        if ($total > 5) {
                            usleep(200000); // 200ms
                        }
                    }

                    Notification::make()
                        ->title('Retry All Failed Complete')
                        ->body("Total: $total, Successfully retried: $success, Failed: $failed")
                        ->color($failed === 0 ? 'success' : 'warning')
                        ->send();
                })
                ->visible(fn () => SmsLog::where('status', 'failed')->exists()),
        ];
    }

    protected function generateReport(array $data)
    {
        try {
            ini_set('memory_limit', '256M');

            $from = $data['from'];
            $until = $data['until'];

            $query = SmsLog::with('sender')
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $until)
                ->orderBy('created_at', 'desc');

            if (!empty($data['status'])) {
                $query->where('status', $data['status']);
            }

            if (!empty($data['message_type'])) {
                $query->where('message_type', $data['message_type']);
            }

            $logs = $query->limit(1000)->get();

            // Summary stats
            $summary = [
                'total' => $logs->count(),
                'sent' => $logs->whereIn('status', ['sent', 'delivered'])->count(),
                'failed' => $logs->where('status', 'failed')->count(),
                'pending' => $logs->where('status', 'pending')->count(),
                'total_cost' => $logs->sum('cost'),
            ];

            // Breakdown by type
            $typeBreakdown = SmsLog::whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $until)
                ->when(!empty($data['status']), fn ($q) => $q->where('status', $data['status']))
                ->when(!empty($data['message_type']), fn ($q) => $q->where('message_type', $data['message_type']))
                ->select(
                    'message_type',
                    DB::raw('COUNT(*) as count'),
                    DB::raw("SUM(CASE WHEN status IN ('sent', 'delivered') THEN 1 ELSE 0 END) as sent_count"),
                    DB::raw("SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count"),
                    DB::raw('COALESCE(SUM(cost), 0) as total_cost')
                )
                ->groupBy('message_type')
                ->orderByDesc('count')
                ->get();

            $startDate = \Carbon\Carbon::parse($from)->format('d M Y');
            $endDate = \Carbon\Carbon::parse($until)->format('d M Y');

            $pdf = Pdf::loadView('pdf.sms-report', [
                'logs' => $logs,
                'summary' => $summary,
                'typeBreakdown' => $typeBreakdown,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'schoolName' => 'St. Francis Of Assisi Private School',
                'schoolLogo' => public_path('images/logo.png'),
                'reportDate' => now()->format('d M Y, h:i A'),
            ]);

            $pdf->setPaper('A4', 'landscape');

            $filename = 'sms-report-' . $startDate . '-to-' . $endDate . '.pdf';

            return response()->streamDownload(
                fn () => print($pdf->output()),
                $filename,
                ['Content-Type' => 'application/pdf']
            );
        } catch (\Exception $e) {
            Notification::make()
                ->title('Report Generation Failed')
                ->body('Error: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}

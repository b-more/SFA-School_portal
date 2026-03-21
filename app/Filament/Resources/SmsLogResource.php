<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SmsLogResource\Pages;
use App\Filament\Resources\SmsLogResource\Widgets;
use App\Models\SmsLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Constants\RoleConstants;
use App\Services\SmsService;
use Filament\Notifications\Notification;

class SmsLogResource extends Resource
{
    protected static ?string $model = SmsLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?string $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'id';

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        return in_array($user->role_id, [RoleConstants::ADMIN, RoleConstants::SCHOOL_SECRETARY]);
    }

    // Disable resource creation
    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date & Time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('recipient')
                    ->label('Phone Number')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('message')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'delivered',
                        'primary' => 'sent',
                        'danger' => 'failed',
                        'warning' => 'pending',
                    ])
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('message_type')
                    ->label('Type')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost')
                    ->money('ZMW')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sender.name')
                    ->label('Sent By')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'sent' => 'Sent',
                        'delivered' => 'Delivered',
                        'failed' => 'Failed',
                        'pending' => 'Pending',
                    ]),
                Tables\Filters\SelectFilter::make('message_type')
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
                    ]),
                Tables\Filters\SelectFilter::make('sent_by')
                    ->relationship('sender', 'name'),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('resend')
                    ->label('Resend')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (SmsLog $record) => $record->status !== 'pending')
                    ->action(function (SmsLog $record) {
                        $smsService = app(SmsService::class);
                        $sent = $smsService->send(
                            $record->message,
                            $record->recipient,
                            $record->message_type ?? 'general',
                            $record->reference_id,
                        );

                        Notification::make()
                            ->title($sent ? 'SMS Resent Successfully' : 'Failed to Resend SMS')
                            ->body($sent
                                ? "Message was successfully resent to {$record->recipient}"
                                : "Could not resend message to {$record->recipient}. Check SMS logs for details.")
                            ->color($sent ? 'success' : 'danger')
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('resendBulk')
                        ->label('Resend Selected')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $smsService = app(SmsService::class);
                            $successCount = 0;
                            $failCount = 0;

                            foreach ($records as $record) {
                                $sent = $smsService->send(
                                    $record->message,
                                    $record->recipient,
                                    $record->message_type ?? 'general',
                                    $record->reference_id,
                                );

                                if ($sent) {
                                    $successCount++;
                                } else {
                                    $failCount++;
                                }

                                // Small delay between sends
                                if ($records->count() > 5) {
                                    usleep(200000); // 200ms
                                }
                            }

                            Notification::make()
                                ->title('Bulk Resend Complete')
                                ->body("Successfully resent: $successCount, Failed: $failCount")
                                ->color($failCount === 0 ? 'success' : 'warning')
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s'); // Automatically refresh every 30 seconds
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSmsLogs::route('/'),
            //'view' => Pages\ViewSmsLog::route('/{record}'),
        ];
    }

    // Add widgets
    public static function getWidgets(): array
    {
        return [
            //
            // Widgets\SmsDashboardWidget::class,
            // Widgets\SmsTypeDistributionWidget::class,
            // Widgets\DailySmsTrendWidget::class,
            // Widgets\SmsCostOverview::class,
        ];
    }
}

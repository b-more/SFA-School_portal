<?php

namespace App\Filament\Resources\SmsCreditResource\Pages;

use App\Filament\Resources\SmsCreditResource;
use App\Models\SmsCredit;
use App\Models\SmsCreditTransaction;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSmsCredits extends ListRecords
{
    protected static string $resource = SmsCreditResource::class;

    protected function getHeaderActions(): array
    {
        $credit = SmsCredit::first();
        $isActive = $credit?->is_active ?? false;

        return [
            Actions\Action::make('toggleSms')
                ->label($isActive ? 'Disable SMS' : 'Enable SMS')
                ->icon($isActive ? 'heroicon-o-no-symbol' : 'heroicon-o-check-circle')
                ->color($isActive ? 'danger' : 'success')
                ->requiresConfirmation()
                ->modalHeading($isActive ? 'Disable All SMS Sending?' : 'Enable All SMS Sending?')
                ->modalDescription($isActive
                    ? 'This will stop ALL SMS messages — both automatic (new student fees) and manual (payment receipts, bulk sends). No messages will be sent until you re-enable.'
                    : 'This will re-enable ALL SMS sending — both automatic notifications and manual sends.')
                ->modalSubmitActionLabel($isActive ? 'Yes, Disable SMS' : 'Yes, Enable SMS')
                ->action(function () use ($credit, $isActive) {
                    $credit->update(['is_active' => !$isActive]);

                    Notification::make()
                        ->title($isActive ? 'SMS Disabled' : 'SMS Enabled')
                        ->body($isActive
                            ? 'All SMS sending has been disabled. No messages will be sent.'
                            : 'SMS sending has been enabled. Auto and manual messages will now be sent.')
                        ->color($isActive ? 'danger' : 'success')
                        ->send();
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SmsCreditResource\Widgets\SmsCreditOverviewWidget::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Transactions')
                ->badge(SmsCreditTransaction::count()),

            'credits' => Tab::make('Top Ups')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'credit'))
                ->badge(SmsCreditTransaction::where('type', 'credit')->count())
                ->badgeColor('success'),

            'debits' => Tab::make('SMS Sent')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'debit'))
                ->badge(SmsCreditTransaction::where('type', 'debit')->count())
                ->badgeColor('danger'),

            'today' => Tab::make('Today')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('created_at', today()))
                ->badge(SmsCreditTransaction::whereDate('created_at', today())->count()),

            'this_month' => Tab::make('This Month')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year))
                ->badge(SmsCreditTransaction::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->count()),
        ];
    }
}

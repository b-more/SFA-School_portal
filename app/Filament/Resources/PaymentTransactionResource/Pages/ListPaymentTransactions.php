<?php

namespace App\Filament\Resources\PaymentTransactionResource\Pages;

use App\Constants\RoleConstants;
use App\Filament\Resources\PaymentTransactionResource;
use App\Filament\Resources\PaymentTransactionResource\Widgets\MonthPaymentsWidget;
use App\Filament\Resources\PaymentTransactionResource\Widgets\ParentFeeOverviewWidget;
use App\Filament\Resources\PaymentTransactionResource\Widgets\TermPaymentsWidget;
use App\Filament\Resources\PaymentTransactionResource\Widgets\TodayPaymentsWidget;
use App\Models\AcademicYear;
use App\Models\ParentGuardian;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\Term;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListPaymentTransactions extends ListRecords
{
    protected static string $resource = PaymentTransactionResource::class;

    public function getTitle(): string
    {
        $user = Auth::user();

        if ($user?->role_id === RoleConstants::STUDENT) return 'My Payment History';
        if ($user?->role_id === RoleConstants::PARENT) return 'Fee Payments';
        return 'Payment Transactions';
    }

    public function getSubheading(): ?string
    {
        $user = Auth::user();

        if ($user?->role_id === RoleConstants::PARENT) {
            $parent = ParentGuardian::where('user_id', $user->id)->first();
            if ($parent) {
                $children = $parent->students()
                    ->where('enrollment_status', 'active')
                    ->pluck('name')
                    ->toArray();

                return 'Showing payments for: ' . implode(', ', $children);
            }
            return 'View payment history for your children';
        }

        if ($user?->role_id === RoleConstants::STUDENT) {
            return 'View all your payment transactions and download receipts';
        }

        $year = AcademicYear::where('is_current', true)->orWhere('is_active', true)->value('name');
        $term = Term::where('is_current', true)->orWhere('is_active', true)->value('name');
        $context = collect([$term, $year])->filter()->implode(' — ');

        return $context ? "Showing payments for: {$context}" : 'Track and manage all payment transactions';
    }

    protected function getHeaderActions(): array
    {
        $user = Auth::user();

        if ($user?->role_id === RoleConstants::PARENT) {
            $parent = ParentGuardian::where('user_id', $user->id)->first();
            if (!$parent) return [];

            $children = $parent->students()->where('enrollment_status', 'active')->get();
            $actions = [];

            foreach ($children as $child) {
                $label = $children->count() > 1
                    ? "Statement — {$child->name}"
                    : 'Download Fee Statement';

                $actions[] = Actions\Action::make('statement_' . $child->id)
                    ->label($label)
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(route('payment-statement.generate', $child))
                    ->openUrlInNewTab();
            }

            return $actions;
        }

        if ($user?->role_id === RoleConstants::STUDENT) {
            $student = Student::where('user_id', $user->id)->first();
            if (!$student) return [];

            return [
                Actions\Action::make('my_statement')
                    ->label('Download Fee Statement')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(route('payment-statement.generate', $student))
                    ->openUrlInNewTab(),
            ];
        }

        return [];
    }

    protected function getHeaderWidgets(): array
    {
        $user = Auth::user();

        if (in_array($user?->role_id, [RoleConstants::PARENT, RoleConstants::STUDENT])) {
            return [
                ParentFeeOverviewWidget::class,
            ];
        }

        return [
            TodayPaymentsWidget::class,
            MonthPaymentsWidget::class,
            TermPaymentsWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        $user = Auth::user();
        if (in_array($user?->role_id, [RoleConstants::PARENT, RoleConstants::STUDENT])) {
            return 1;
        }
        return 3;
    }
}

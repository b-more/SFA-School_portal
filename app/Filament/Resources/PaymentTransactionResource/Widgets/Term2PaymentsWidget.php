<?php

namespace App\Filament\Resources\PaymentTransactionResource\Widgets;

use App\Models\AcademicYear;
use App\Models\ParentGuardian;
use App\Models\Term;
use App\Models\Student;
use App\Models\StudentFee;
use App\Constants\RoleConstants;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class Term2PaymentsWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role_id, [RoleConstants::STUDENT, RoleConstants::PARENT])) {
            return [];
        }

        $currentYear = AcademicYear::where('is_current', true)->orWhere('is_active', true)->first();
        $term2 = Term::where('name', 'Term 2')->orWhere('name', 'Second Term')->first();

        if (!$term2 || !$currentYear) {
            return [
                Stat::make('Term 2', 'N/A')
                    ->description('Term data not available')
                    ->color('gray'),
            ];
        }

        $studentIds = $this->getStudentIds($user);
        if (empty($studentIds)) return [];

        $fees = StudentFee::whereIn('student_id', $studentIds)
            ->where('term_id', $term2->id)
            ->where('academic_year_id', $currentYear->id)
            ->get();

        $totalFee = $fees->sum(fn ($f) => $f->feeStructure?->total_fee ?? 0);
        $totalPaid = $fees->sum('amount_paid');
        $totalBalance = $fees->sum('balance');
        $progress = $totalFee > 0 ? round(($totalPaid / $totalFee) * 100, 1) : 0;

        $description = $totalBalance <= 0
            ? 'Fully Paid'
            : number_format($progress, 0) . '% paid — K ' . number_format($totalBalance, 0) . ' due';

        return [
            Stat::make('Term 2', 'K ' . number_format($totalPaid, 2))
                ->description($description)
                ->descriptionIcon($totalBalance <= 0 ? 'heroicon-m-check-circle' : 'heroicon-m-clock')
                ->color($totalBalance <= 0 ? 'success' : ($progress >= 50 ? 'info' : 'warning')),
        ];
    }

    private function getStudentIds($user): array
    {
        if ($user->role_id === RoleConstants::STUDENT) {
            $student = Student::where('user_id', $user->id)->first();
            return $student ? [$student->id] : [];
        }

        if ($user->role_id === RoleConstants::PARENT) {
            $parent = ParentGuardian::where('user_id', $user->id)->first();
            return $parent
                ? $parent->students()->where('enrollment_status', 'active')->pluck('id')->toArray()
                : [];
        }

        return [];
    }
}

<?php

namespace App\Filament\Resources\PaymentTransactionResource\Widgets;

use App\Models\AcademicYear;
use App\Models\ParentGuardian;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\Term;
use App\Constants\RoleConstants;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class ParentFeeOverviewWidget extends Widget
{
    protected static string $view = 'filament.widgets.parent-fee-overview';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 1;

    public function getData(): array
    {
        $user = Auth::user();
        if (!$user) return ['children' => [], 'year' => null];

        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) return ['children' => [], 'year' => null];

        // Get terms for the active academic year
        $terms = Term::where('academic_year_id', $activeYear->id)->orderBy('id')->get();

        $studentIds = $this->getStudentIds($user);
        if (empty($studentIds)) return ['children' => [], 'year' => $activeYear];

        $students = Student::whereIn('id', $studentIds)->with('grade')->get();

        $children = $students->map(function ($student) use ($terms, $activeYear) {
            $termData = $terms->map(function ($term) use ($student, $activeYear) {
                $fee = StudentFee::where('student_id', $student->id)
                    ->where('term_id', $term->id)
                    ->where('academic_year_id', $activeYear->id)
                    ->with('feeStructure')
                    ->first();

                // Use basic_fee (tuition only) — excludes additional charges like PTA, bus, uniforms
                $tuitionFee = $fee?->feeStructure?->basic_fee ?? 0;
                $amountPaid = $fee?->amount_paid ?? 0;
                $balance = max($tuitionFee - $amountPaid, 0);
                $status = !$fee ? 'no_fee' : ($amountPaid >= $tuitionFee ? 'paid' : ($amountPaid > 0 ? 'partial' : 'unpaid'));
                $progress = $tuitionFee > 0 ? round(($amountPaid / $tuitionFee) * 100) : 0;
                $totalFee = $tuitionFee;

                return [
                    'term_name' => $term->name,
                    'total_fee' => $totalFee,
                    'amount_paid' => $amountPaid,
                    'balance' => $balance,
                    'status' => $status,
                    'progress' => $progress,
                    'fee_id' => $fee?->id,
                ];
            });

            $totalOwed = $termData->sum('total_fee');
            $totalPaid = $termData->sum('amount_paid');
            $totalBalance = $termData->sum('balance');

            return [
                'student' => $student,
                'terms' => $termData,
                'total_owed' => $totalOwed,
                'total_paid' => $totalPaid,
                'total_balance' => $totalBalance,
                'overall_progress' => $totalOwed > 0 ? round(($totalPaid / $totalOwed) * 100) : 0,
            ];
        });

        return [
            'children' => $children,
            'year' => $activeYear,
            'terms' => $terms,
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

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user && in_array($user->role_id, [RoleConstants::PARENT, RoleConstants::STUDENT]);
    }
}

<?php

namespace App\Filament\Resources\StudentResource\Widgets;

use App\Constants\RoleConstants;
use App\Models\Student;
use App\Models\Teacher;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentGradeSummary extends Widget
{
    protected static string $view = 'filament.resources.student-resource.widgets.student-grade-summary';

    protected int | string | array $columnSpan = 'full';

    public function getGradeData(): array
    {
        $query = Student::where('students.enrollment_status', 'active')
            ->join('grades', 'students.grade_id', '=', 'grades.id');

        $user = Auth::user();

        // For class teachers, only show their own class
        if (in_array($user->role_id, RoleConstants::teaching())) {
            $teacher = Teacher::where('user_id', $user->id)->first();

            if ($teacher && $teacher->is_class_teacher && $teacher->class_section_id) {
                $query->where('students.class_section_id', $teacher->class_section_id);
            } elseif ($teacher) {
                // Non-class teachers: show classes they teach
                $classSectionIds = $teacher->classSections()->pluck('class_sections.id')->toArray();
                if (!empty($classSectionIds)) {
                    $query->whereIn('students.class_section_id', $classSectionIds);
                } else {
                    $query->where('students.id', 0);
                }
            } else {
                $query->where('students.id', 0);
            }
        }

        $data = $query->select(
                'grades.id as grade_id',
                'grades.name as grade_name',
                'grades.level',
                DB::raw("SUM(CASE WHEN students.gender = 'male' THEN 1 ELSE 0 END) as boys"),
                DB::raw("SUM(CASE WHEN students.gender = 'female' THEN 1 ELSE 0 END) as girls"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('grades.id', 'grades.name', 'grades.level')
            ->orderBy('grades.level')
            ->get()
            ->toArray();

        $totalBoys = array_sum(array_column($data, 'boys'));
        $totalGirls = array_sum(array_column($data, 'girls'));
        $totalAll = array_sum(array_column($data, 'total'));

        return [
            'grades' => $data,
            'totalBoys' => $totalBoys,
            'totalGirls' => $totalGirls,
            'totalAll' => $totalAll,
        ];
    }
}

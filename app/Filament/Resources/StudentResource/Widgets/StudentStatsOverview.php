<?php

namespace App\Filament\Resources\StudentResource\Widgets;

use App\Constants\RoleConstants;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\Teacher;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class StudentStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $currentYear = AcademicYear::current();
        $currentYearId = $currentYear?->id;
        $previousYearId = null;

        if ($currentYear) {
            $previousYear = AcademicYear::where('id', '<', $currentYearId)
                ->orderBy('id', 'desc')
                ->first();
            $previousYearId = $previousYear?->id;
        }

        $baseQuery = $this->getScopedQuery();

        $totalActive = (clone $baseQuery)->where('enrollment_status', 'active')->count();
        $newThisYear = $currentYearId
            ? (clone $baseQuery)->where('academic_year_id', $currentYearId)
                ->where('enrollment_status', 'active')
                ->count()
            : 0;

        $retained = $previousYearId
            ? (clone $baseQuery)->where('academic_year_id', $previousYearId)
                ->where('enrollment_status', 'active')
                ->count()
            : ($totalActive - $newThisYear);

        $transferred = (clone $baseQuery)->where('enrollment_status', 'transferred')->count();

        $maleCount = (clone $baseQuery)->where('enrollment_status', 'active')->where('gender', 'male')->count();
        $femaleCount = (clone $baseQuery)->where('enrollment_status', 'active')->where('gender', 'female')->count();

        $label = $this->getStatsLabel();

        return [
            Stat::make("Total Active Students{$label}", number_format($totalActive))
                ->description("{$maleCount} male, {$femaleCount} female")
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5]),

            Stat::make('New This Year', number_format($newThisYear))
                ->description($currentYear ? "Enrolled in {$currentYear->name}" : 'No active year')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Retained Students', number_format($retained))
                ->description('From previous year')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info'),

            Stat::make('Transferred', number_format($transferred))
                ->description('Left the school')
                ->descriptionIcon('heroicon-m-arrow-right-start-on-rectangle')
                ->color('warning'),
        ];
    }

    protected function getScopedQuery(): Builder
    {
        $query = Student::query();
        $user = Auth::user();

        if (in_array($user->role_id, [RoleConstants::ADMIN, RoleConstants::SCHOOL_SECRETARY])) {
            return $query;
        }

        if (in_array($user->role_id, RoleConstants::teaching())) {
            $teacher = Teacher::where('user_id', $user->id)->first();

            if ($teacher) {
                if ($user->role_id === RoleConstants::TEACHER) {
                    if ($teacher->is_class_teacher && $teacher->class_section_id) {
                        return $query->where('class_section_id', $teacher->class_section_id);
                    }

                    $classSectionIds = $teacher->classSections()->pluck('class_sections.id')->toArray();
                    if (!empty($classSectionIds)) {
                        return $query->whereIn('class_section_id', $classSectionIds);
                    }

                    return $query->where('id', 0);
                }

                $query->where(function ($q) use ($teacher) {
                    $classSectionIds = $teacher->classSections()->pluck('class_sections.id')->toArray();
                    if (!empty($classSectionIds)) {
                        $q->orWhereIn('class_section_id', $classSectionIds);
                    }
                    if ($teacher->class_section_id) {
                        $q->orWhere('class_section_id', $teacher->class_section_id);
                    }
                    if ($teacher->is_grade_teacher && $teacher->grade_id) {
                        $q->orWhere('grade_id', $teacher->grade_id);
                    }
                });

                return $query;
            }

            return $query->where('id', 0);
        }

        if ($user->role_id === RoleConstants::PARENT) {
            $parent = $user->parentGuardian;
            if ($parent) {
                return $query->where('parent_guardian_id', $parent->id);
            }
            return $query->where('id', 0);
        }

        return $query->where('id', 0);
    }

    protected function getStatsLabel(): string
    {
        $user = Auth::user();

        if (in_array($user->role_id, RoleConstants::teaching())) {
            $teacher = Teacher::where('user_id', $user->id)->first();
            if ($teacher && $teacher->is_class_teacher && $teacher->classSection) {
                $section = $teacher->classSection;
                $gradeName = $section->grade?->name ?? '';
                return " — {$gradeName} {$section->name}";
            }
        }

        return '';
    }
}

<?php

namespace App\Filament\Resources\ResultResource\Pages;

use App\Constants\RoleConstants;
use App\Filament\Resources\ResultResource;
use App\Models\AcademicYear;
use App\Models\ParentGuardian;
use App\Models\ReportCardComment;
use App\Models\Student;
use App\Models\Term;
use App\Traits\HasPageGuide;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListResults extends ListRecords
{
    use HasPageGuide;

    protected static string $resource = ResultResource::class;

    protected function getGuideSlug(): string
    {
        return 'results';
    }

    protected function getHeaderActions(): array
    {
        $user = Auth::user();

        // Parent: show report card downloads
        if ($user?->role_id === RoleConstants::PARENT) {
            return $this->getParentReportCardActions();
        }

        // Student: show own report card downloads
        if ($user?->role_id === RoleConstants::STUDENT) {
            return $this->getStudentReportCardActions();
        }

        return [
            $this->getPageGuideAction(),
            Actions\CreateAction::make(),
        ];
    }

    private function getParentReportCardActions(): array
    {
        $parent = ParentGuardian::where('user_id', Auth::id())->first();
        if (!$parent) return [];

        $children = $parent->students()->where('enrollment_status', 'active')->get();
        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) return [];

        $terms = Term::where('academic_year_id', $activeYear->id)->orderBy('id')->get();
        $actions = [];

        foreach ($children as $child) {
            foreach ($terms as $term) {
                $comment = ReportCardComment::where('student_id', $child->id)
                    ->where('term_id', $term->id)
                    ->where('academic_year_id', $activeYear->id)
                    ->first();

                if ($comment && $comment->generated_at) {
                    $label = $children->count() > 1
                        ? "{$child->name} — {$term->name}"
                        : "Report Card — {$term->name}";

                    $actions[] = Actions\Action::make("report_{$child->id}_{$term->id}")
                        ->label($label)
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->url(route('report-cards.generate', [
                            'student' => $child->id,
                            'term' => $term->id,
                            'year' => $activeYear->name,
                        ]))
                        ->openUrlInNewTab();
                }
            }
        }

        if (empty($actions)) {
            $actions[] = Actions\Action::make('no_reports')
                ->label('Report cards not yet available')
                ->icon('heroicon-o-clock')
                ->color('gray')
                ->disabled();
        }

        return $actions;
    }

    private function getStudentReportCardActions(): array
    {
        $student = Student::where('user_id', Auth::id())->first();
        if (!$student) return [];

        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) return [];

        $terms = Term::where('academic_year_id', $activeYear->id)->orderBy('id')->get();
        $actions = [];

        foreach ($terms as $term) {
            $comment = ReportCardComment::where('student_id', $student->id)
                ->where('term_id', $term->id)
                ->where('academic_year_id', $activeYear->id)
                ->first();

            if ($comment && $comment->generated_at) {
                $actions[] = Actions\Action::make("report_{$term->id}")
                    ->label("Report Card — {$term->name}")
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(route('report-cards.generate', [
                        'student' => $student->id,
                        'term' => $term->id,
                        'year' => $activeYear->name,
                    ]))
                    ->openUrlInNewTab();
            }
        }

        if (empty($actions)) {
            $actions[] = Actions\Action::make('no_reports')
                ->label('Report cards not yet available')
                ->icon('heroicon-o-clock')
                ->color('gray')
                ->disabled();
        }

        return $actions;
    }
}

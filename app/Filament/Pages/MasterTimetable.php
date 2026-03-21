<?php

namespace App\Filament\Pages;

use App\Constants\RoleConstants;
use App\Models\AcademicYear;
use App\Models\ClassSection;
use App\Models\SchoolSettings;
use App\Models\TimetableEntry;
use App\Models\TimetablePeriod;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class MasterTimetable extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationLabel = 'Master Timetable';
    protected static ?string $navigationGroup = 'Timetable Management';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'master-timetable';
    protected static ?string $title = 'Master Timetable';
    protected static string $view = 'filament.pages.master-timetable';

    public ?int $selectedAcademicYear = null;
    public ?int $selectedClassSection = null;

    public static function shouldRegisterNavigation(): bool
    {
        return in_array(auth()->user()?->role_id, [
            RoleConstants::ADMIN,
            RoleConstants::SCHOOL_SECRETARY,
        ]);
    }

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role_id, [
            RoleConstants::ADMIN,
            RoleConstants::SCHOOL_SECRETARY,
        ]);
    }

    public function mount(): void
    {
        $this->selectedAcademicYear = AcademicYear::current()?->id;
    }

    public function getPeriods(): Collection
    {
        if (!$this->selectedAcademicYear) {
            return collect();
        }

        return TimetablePeriod::where('academic_year_id', $this->selectedAcademicYear)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
    }

    public function getLessonPeriods(): Collection
    {
        return $this->getPeriods()->filter(fn($p) => $p->isLesson());
    }

    public function getClassSections(): Collection
    {
        if (!$this->selectedAcademicYear) {
            return collect();
        }

        return ClassSection::with('grade')
            ->where('academic_year_id', $this->selectedAcademicYear)
            ->where('is_active', true)
            ->get()
            ->sortBy(fn($cs) => $cs->grade->level ?? 0);
    }

    public function getDays(): array
    {
        return TimetableEntry::DAYS;
    }

    public function getTimetableForClass(int $classSectionId): array
    {
        if (!$this->selectedAcademicYear) {
            return [];
        }

        return TimetableEntry::getClassTimetable($classSectionId, $this->selectedAcademicYear);
    }

    public function getAcademicYears(): array
    {
        return AcademicYear::where('name', '>=', '2025')
            ->orderBy('name', 'desc')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function downloadPdf()
    {
        $periods = $this->getLessonPeriods();
        $allPeriods = $this->getPeriods();
        $classSections = $this->getClassSections();
        $settings = SchoolSettings::first();
        $year = AcademicYear::find($this->selectedAcademicYear);

        // Load all entries for ALL days for the PDF
        $allEntries = TimetableEntry::where('academic_year_id', $this->selectedAcademicYear)
            ->where('is_active', true)
            ->with(['timetablePeriod', 'subject', 'teacher'])
            ->get();

        $timetableByDay = [];
        foreach (TimetableEntry::DAYS as $day) {
            $dayEntries = $allEntries->where('day_of_week', $day);
            $data = [];
            foreach ($dayEntries as $entry) {
                $key = $entry->class_section_id . '-' . $entry->timetable_period_id;
                $data[$key] = $entry;
            }
            $timetableByDay[$day] = $data;
        }

        $pdf = Pdf::loadView('pdf.master-timetable', [
            'periods' => $periods,
            'allPeriods' => $allPeriods,
            'classSections' => $classSections,
            'timetableByDay' => $timetableByDay,
            'days' => TimetableEntry::DAYS,
            'schoolName' => $settings->school_name ?? 'St. Francis of Assisi Private School',
            'academicYear' => $year->name ?? '',
            'reportDate' => now()->format('F d, Y'),
        ])->setPaper('A4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'master-timetable-' . now()->format('Y-m-d') . '.pdf'
        );
    }
}

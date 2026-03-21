<?php

namespace App\Filament\Pages;

use App\Constants\RoleConstants;
use App\Models\AcademicYear;
use App\Models\TimetableEntry;
use App\Services\TimetableGeneratorService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class GenerateTimetable extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Generate Timetable';
    protected static ?string $navigationGroup = 'Timetable Management';
    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'generate-timetable';
    protected static ?string $title = 'Auto-Generate Timetable';
    protected static string $view = 'filament.pages.generate-timetable';

    public ?int $selectedAcademicYear = null;
    public bool $generated = false;
    public array $generationStats = [];
    public array $generationLogs = [];
    public array $conflicts = [];

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role_id === RoleConstants::ADMIN;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->role_id === RoleConstants::ADMIN;
    }

    public function mount(): void
    {
        $this->selectedAcademicYear = AcademicYear::current()?->id;
    }

    public function getAcademicYears(): array
    {
        return AcademicYear::where('name', '>=', '2025')
            ->orderBy('name', 'desc')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function getExistingEntryCount(): int
    {
        if (!$this->selectedAcademicYear) return 0;
        return TimetableEntry::where('academic_year_id', $this->selectedAcademicYear)->count();
    }

    public function generate(): void
    {
        $this->generated = false;
        $this->generationStats = [];
        $this->generationLogs = [];
        $this->conflicts = [];

        try {
            $generator = new TimetableGeneratorService($this->selectedAcademicYear);
            $result = $generator->generate();

            $this->generationStats = $result['stats'];
            $this->generationLogs = $result['logs'];

            if ($result['success']) {
                // Validate for conflicts
                $this->conflicts = $generator->validateSchedule();

                $this->generated = true;

                Notification::make()
                    ->title('Timetable Generated Successfully')
                    ->body("{$result['stats']['entries_created']} entries created for {$result['stats']['total_classes']} classes.")
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Generation Failed')
                    ->body($result['logs'][0] ?? 'Unknown error')
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            $this->generationLogs[] = 'ERROR: ' . $e->getMessage();

            Notification::make()
                ->title('Generation Error')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}

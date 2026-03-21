<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\Result;
use App\Models\PaymentTransaction;
use App\Models\Homework;
use Illuminate\Support\Facades\Artisan;

class AcademicYearManager extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static string $view = 'filament.pages.academic-year-manager';
    protected static ?string $navigationGroup = 'System Management';
    protected static ?string $navigationLabel = 'Academic Year Manager';
    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role_id === \App\Constants\RoleConstants::ADMIN;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->role_id === \App\Constants\RoleConstants::ADMIN;
    }

    public ?int $selectedYearId = null;
    public array $yearStats = [];

    public function mount(): void
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $this->selectedYearId = session('selected_academic_year_id', $activeYear?->id);
        $this->loadStats();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('rollover')
                ->label('Rollover to Next Year')
                ->icon('heroicon-o-arrow-right-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Rollover Academic Year')
                ->modalDescription('This will promote students, copy teacher assignments, and generate fees for the next academic year.')
                ->action(function () {
                    try {
                        Artisan::call('academic-year:rollover', ['--all' => true]);
                        
                        Notification::make()
                            ->title('Rollover Complete')
                            ->success()
                            ->body('Successfully rolled over to the next academic year.')
                            ->send();
                        
                        $this->redirect(static::getUrl());
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Rollover Failed')
                            ->danger()
                            ->body($e->getMessage())
                            ->send();
                    }
                }),
        ];
    }

    public function switchYear(int $yearId): void
    {
        session(['selected_academic_year_id' => $yearId]);
        $this->selectedYearId = $yearId;
        $this->loadStats();
        
        $year = AcademicYear::find($yearId);
        Notification::make()
            ->title('Academic Year Switched')
            ->success()
            ->body('Now viewing ' . $year->name)
            ->send();

        $this->redirect(static::getUrl());
    }

    public function activateYear(int $yearId): void
    {
        AcademicYear::query()->update(['is_active' => false]);
        
        $year = AcademicYear::find($yearId);
        $year->update(['is_active' => true]);

        session(['selected_academic_year_id' => $yearId]);
        $this->selectedYearId = $yearId;
        $this->loadStats();

        Notification::make()
            ->title('Academic Year Activated')
            ->success()
            ->body($year->name . ' is now the active academic year')
            ->send();

        $this->redirect(static::getUrl());
    }

    protected function loadStats(): void
    {
        if (!$this->selectedYearId) {
            return;
        }

        $this->yearStats = [
            'total_students' => Student::allYears()->where('academic_year_id', $this->selectedYearId)->count(),
            'active_students' => Student::allYears()->where('academic_year_id', $this->selectedYearId)->where('enrollment_status', 'active')->count(),
            'total_homework' => Homework::allYears()->where('academic_year_id', $this->selectedYearId)->count(),
            'total_results' => Result::allYears()->where('academic_year_id', $this->selectedYearId)->count(),
            'total_payments' => PaymentTransaction::allYears()->where('academic_year_id', $this->selectedYearId)->sum('amount') ?? 0,
            'payment_count' => PaymentTransaction::allYears()->where('academic_year_id', $this->selectedYearId)->count(),
        ];
    }

    public function getAcademicYears()
    {
        return AcademicYear::orderBy('start_date', 'desc')->get();
    }
}

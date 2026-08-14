<?php

namespace App\Filament\Pages;

use App\Constants\RoleConstants;
use App\Models\AcademicYear;
use App\Services\FeeCollectionTrackerService;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class FeeCollectionTracker extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Accounts & Finance';
    protected static ?string $navigationLabel = 'Fee Collection Tracker';
    protected static ?int    $navigationSort  = 10;
    protected static ?string $slug            = 'fee-collection-tracker';
    protected static ?string $title           = 'Fee Collection Tracker';
    protected static string  $view            = 'filament.pages.fee-collection-tracker';

    public ?int $academicYearId = null;

    public static function shouldRegisterNavigation(): bool
    {
        return self::canAccess();
    }

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role_id, [
            RoleConstants::ADMIN,
            RoleConstants::ACCOUNTANT,
            RoleConstants::DIRECTOR,
        ], true);
    }

    public function mount(): void
    {
        $this->academicYearId = AcademicYear::where('is_active', true)->value('id');
        $this->form->fill(['academicYearId' => $this->academicYearId]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('')
            ->schema([
                Select::make('academicYearId')
                    ->label('Academic Year')
                    ->options(AcademicYear::orderByDesc('is_active')->orderBy('name')->pluck('name', 'id'))
                    ->default($this->academicYearId)
                    ->required()
                    ->live(),
            ]);
    }

    public function pdfUrl(): string
    {
        return route('reports.fee-collection-tracker.pdf', ['academic_year_id' => $this->academicYearId]);
    }

    public function xlsxUrl(): string
    {
        return route('reports.fee-collection-tracker.xlsx', ['academic_year_id' => $this->academicYearId]);
    }
}

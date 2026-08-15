<?php

namespace App\Filament\Pages;

use App\Constants\RoleConstants;
use App\Models\Term;
use App\Services\ClinicReportService;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class ClinicReports extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationGroup = 'Clinic';
    protected static ?string $navigationLabel = 'Clinic reports';
    protected static ?int    $navigationSort  = 5;
    protected static ?string $slug            = 'clinic-reports';
    protected static ?string $title           = 'Clinic Reports';
    protected static string  $view            = 'filament.pages.clinic-reports';

    public string $period    = 'weekly';       // weekly | monthly | termly | custom
    public ?string $anchor   = null;           // date used to derive the week/month
    public ?int    $termId   = null;
    public ?string $from     = null;
    public ?string $to       = null;
    public ?int    $gradeLevel = null;

    public array $data = [];

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role_id, [RoleConstants::ADMIN, RoleConstants::CLINICIAN], true);
    }

    public static function shouldRegisterNavigation(): bool { return self::canAccess(); }

    public function mount(): void
    {
        $this->anchor = now()->toDateString();
        $this->termId = Term::where('is_current', true)->value('id');
        $this->form->fill([
            'period' => $this->period, 'anchor' => $this->anchor,
            'termId' => $this->termId, 'gradeLevel' => null,
        ]);
        $this->refresh();
    }

    public function form(Form $form): Form
    {
        return $form->statePath('')->schema([
            Select::make('period')
                ->label('Report period')
                ->options(['weekly' => 'Weekly', 'monthly' => 'Monthly', 'termly' => 'Termly', 'custom' => 'Custom range'])
                ->required()
                ->default($this->period)
                ->live(),
            DatePicker::make('anchor')
                ->label(fn () => $this->period === 'weekly' ? 'Any day in the week' : 'Any day in the month')
                ->native(false)
                ->default(now())
                ->visible(fn () => in_array($this->period, ['weekly', 'monthly']))
                ->live(),
            Select::make('termId')
                ->label('Term')
                ->options(Term::orderByDesc('academic_year_id')->orderBy('start_date')->get()
                    ->mapWithKeys(fn ($t) => [$t->id => $t->name . '  (' . optional($t->academicYear)->name . ')']))
                ->visible(fn () => $this->period === 'termly')
                ->live(),
            DatePicker::make('from')->native(false)->visible(fn () => $this->period === 'custom')->live(),
            DatePicker::make('to')->native(false)->visible(fn () => $this->period === 'custom')->live(),
            Select::make('gradeLevel')
                ->label('Grade filter (optional)')
                ->options(collect(range(1, 12))->mapWithKeys(fn ($g) => [$g => "Grade / Form-equivalent {$g}"]))
                ->placeholder('All grades')
                ->live(),
        ])->columns(3);
    }

    public function updated($property): void
    {
        // Any form input change → recompute.
        $this->refresh();
    }

    public function refresh(): void
    {
        $spec = match ($this->period) {
            'weekly'  => ['kind' => 'weekly',  'anchor' => $this->anchor],
            'monthly' => ['kind' => 'monthly', 'anchor' => $this->anchor],
            'termly'  => ['kind' => 'termly',  'term_id' => $this->termId],
            'custom'  => ['kind' => 'custom',  'from' => $this->from, 'to' => $this->to],
            default   => ['kind' => 'weekly',  'anchor' => now()->toDateString()],
        };
        $svc = app(ClinicReportService::class);
        [$from, $to] = $svc->resolvePeriod($spec);
        $this->data = [
            'from'   => $from,
            'to'     => $to,
            'visits' => $svc->visitStats($from, $to, $this->gradeLevel),
            'stock'  => $svc->stockStats($from, $to),
        ];
    }

    public function termlyPdfUrl(): ?string
    {
        return $this->termId ? route('reports.clinic-termly.pdf', ['term_id' => $this->termId]) : null;
    }

    public function pdfUrl(): ?string
    {
        // One URL that always downloads a PDF for whatever period the
        // clinician has currently selected. Simpler than one button per period.
        return match ($this->period) {
            'weekly'  => route('reports.clinic-weekly.pdf',  ['anchor'  => $this->anchor]),
            'monthly' => route('reports.clinic-monthly.pdf', ['anchor'  => $this->anchor]),
            'termly'  => $this->termId ? route('reports.clinic-termly.pdf', ['term_id' => $this->termId]) : null,
            'custom'  => ($this->from && $this->to)
                ? route('reports.clinic-custom.pdf', ['from' => $this->from, 'to' => $this->to])
                : null,
            default   => null,
        };
    }
}

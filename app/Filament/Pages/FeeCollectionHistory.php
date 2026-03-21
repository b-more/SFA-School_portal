<?php

namespace App\Filament\Pages;

use App\Constants\RoleConstants;
use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\Term;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FeeCollectionHistory extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Fee Collection History';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.fee-collection-history';

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return in_array(auth()->user()?->role_id, [RoleConstants::ADMIN, RoleConstants::ACCOUNTANT]) ?? false;
    }

    public function mount(): void
    {
        $this->form->fill([
            'academic_year_id' => AcademicYear::where('is_active', true)->first()?->id,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('academic_year_id')
                    ->label('Academic Year')
                    ->options(AcademicYear::orderBy('name', 'desc')->pluck('name', 'id'))
                    ->default(AcademicYear::where('is_active', true)->first()?->id)
                    ->required()
                    ->live()
                    ->columnSpan(1),
            ])
            ->columns(3)
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        $yearId = $this->data['academic_year_id'] ?? null;
        $terms = $yearId
            ? Term::where('academic_year_id', $yearId)->orderBy('start_date')->get()
            : collect();

        $columns = [
            TextColumn::make('student_id_number')
                ->label('Student ID')
                ->searchable()
                ->sortable(),

            TextColumn::make('name')
                ->label('Student Name')
                ->searchable()
                ->sortable(),

            TextColumn::make('grade.name')
                ->label('Grade')
                ->sortable(),
        ];

        foreach ($terms as $term) {
            $termId = $term->id;
            $columns[] = TextColumn::make("term_{$termId}_paid")
                ->label($term->name)
                ->getStateUsing(function (Student $record) use ($termId) {
                    $fee = $record->fees->firstWhere('term_id', $termId);

                    return $fee ? (float) $fee->amount_paid : 0;
                })
                ->money('ZMW')
                ->alignEnd();
        }

        $columns = array_merge($columns, [
            TextColumn::make('total_expected')
                ->label('Expected')
                ->getStateUsing(fn (Student $record) => $record->fees->sum(fn ($f) => (float) ($f->feeStructure->basic_fee ?? 0)))
                ->money('ZMW')
                ->alignEnd(),

            TextColumn::make('total_paid')
                ->label('Paid')
                ->getStateUsing(fn (Student $record) => $record->fees->sum(fn ($f) => (float) $f->amount_paid))
                ->money('ZMW')
                ->alignEnd()
                ->color('success'),

            TextColumn::make('total_balance')
                ->label('Balance')
                ->getStateUsing(fn (Student $record) => $record->fees->sum(fn ($f) => (float) $f->balance))
                ->money('ZMW')
                ->alignEnd()
                ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),

            TextColumn::make('annual_status')
                ->label('Status')
                ->badge()
                ->getStateUsing(function (Student $record) {
                    if ($record->fees->isEmpty()) {
                        return 'N/A';
                    }
                    $allCleared = $record->fees->every(fn ($f) => in_array($f->payment_status, ['paid', 'overpaid']));
                    if ($allCleared) {
                        return 'Cleared';
                    }
                    $anyPaid = $record->fees->contains(fn ($f) => (float) $f->amount_paid > 0);

                    return $anyPaid ? 'Partial' : 'Unpaid';
                })
                ->color(fn (string $state): string => match ($state) {
                    'Cleared' => 'success',
                    'Partial' => 'warning',
                    'Unpaid' => 'danger',
                    default => 'gray',
                }),
        ]);

        return $table
            ->query($this->getTableQuery())
            ->columns($columns)
            ->filters([
                SelectFilter::make('grade_id')
                    ->label('Grade')
                    ->options(Grade::orderBy('level')->pluck('name', 'id')),

                Filter::make('annual_payment_status')
                    ->form([
                        Select::make('status')
                            ->label('Payment Status')
                            ->options([
                                'cleared' => 'Cleared',
                                'partial' => 'Partial',
                                'unpaid' => 'Unpaid',
                            ]),
                    ])
                    ->query(function (Builder $query, array $data) use ($yearId): Builder {
                        $status = $data['status'] ?? null;
                        if (! $status || ! $yearId) {
                            return $query;
                        }

                        return match ($status) {
                            'cleared' => $query->whereDoesntHave('fees', fn ($q) => $q->where('academic_year_id', $yearId)->whereNotIn('payment_status', ['paid', 'overpaid'])),
                            'unpaid' => $query->whereDoesntHave('fees', fn ($q) => $q->where('academic_year_id', $yearId)->where('amount_paid', '>', 0)),
                            'partial' => $query->where(function ($q) use ($yearId) {
                                $q->whereHas('fees', fn ($q2) => $q2->where('academic_year_id', $yearId)->whereNotIn('payment_status', ['paid', 'overpaid']))
                                    ->whereHas('fees', fn ($q2) => $q2->where('academic_year_id', $yearId)->where('amount_paid', '>', 0));
                            }),
                            default => $query,
                        };
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (empty($data['status'])) {
                            return null;
                        }

                        return 'Status: '.match ($data['status']) {
                            'cleared' => 'Cleared',
                            'partial' => 'Partial',
                            'unpaid' => 'Unpaid',
                            default => $data['status'],
                        };
                    }),
            ])
            ->headerActions([
                Action::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(fn () => $this->exportPdf()),
            ])
            ->defaultSort('name', 'asc')
            ->paginated([10, 25, 50, 100]);
    }

    public function getTableQuery(): Builder
    {
        $yearId = $this->data['academic_year_id'] ?? null;

        if (! $yearId) {
            return Student::query()->whereRaw('1 = 0');
        }

        return Student::query()
            ->whereHas('fees', fn ($q) => $q->where('academic_year_id', $yearId))
            ->with([
                'grade',
                'fees' => fn ($q) => $q->where('academic_year_id', $yearId)->with('feeStructure'),
            ]);
    }

    public function getSummaryStats(): array
    {
        $yearId = $this->data['academic_year_id'] ?? null;

        if (! $yearId) {
            return [
                'total_students' => 0,
                'total_expected' => 0,
                'total_paid' => 0,
                'total_balance' => 0,
            ];
        }

        $fees = StudentFee::where('academic_year_id', $yearId)
            ->with('feeStructure')
            ->get();

        return [
            'total_students' => $fees->pluck('student_id')->unique()->count(),
            'total_expected' => $fees->sum(fn ($f) => (float) ($f->feeStructure->basic_fee ?? 0)),
            'total_paid' => $fees->sum(fn ($f) => (float) $f->amount_paid),
            'total_balance' => $fees->sum(fn ($f) => (float) $f->balance),
        ];
    }

    protected function exportPdf()
    {
        try {
            $yearId = $this->data['academic_year_id'] ?? null;

            if (! $yearId) {
                Notification::make()
                    ->title('Please select an academic year')
                    ->warning()
                    ->send();

                return;
            }

            $academicYear = AcademicYear::find($yearId);
            $terms = Term::where('academic_year_id', $yearId)->orderBy('start_date')->get();

            $students = Student::query()
                ->whereHas('fees', fn ($q) => $q->where('academic_year_id', $yearId))
                ->with([
                    'grade',
                    'fees' => fn ($q) => $q->where('academic_year_id', $yearId)->with('feeStructure'),
                ])
                ->orderBy('name')
                ->get();

            $summary = $this->getSummaryStats();

            $pdf = Pdf::loadView('pdf.fee-collection-history', [
                'students' => $students,
                'terms' => $terms,
                'academicYear' => $academicYear,
                'summary' => $summary,
                'schoolName' => 'St. Francis Of Assisi Private School',
                'reportDate' => now()->format('F d, Y'),
            ]);

            $pdf->setPaper('A4', 'landscape');

            $filename = 'fee-collection-history-'.$academicYear->name.'-'.now()->format('Y-m-d').'.pdf';

            return response()->streamDownload(
                fn () => print ($pdf->output()),
                $filename,
                ['Content-Type' => 'application/pdf']
            );
        } catch (\Exception $e) {
            Notification::make()
                ->title('Export Failed')
                ->body('Error: '.$e->getMessage())
                ->danger()
                ->send();
        }
    }
}

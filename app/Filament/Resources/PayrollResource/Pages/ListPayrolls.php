<?php

namespace App\Filament\Resources\PayrollResource\Pages;

use App\Filament\Resources\PayrollResource;
use App\Models\Employee;
use App\Models\Payroll;
use App\Services\PayrollCalculationService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListPayrolls extends ListRecords
{
    protected static string $resource = PayrollResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\PayrollResource\Widgets\PayrollStatsWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus'),

            Actions\Action::make('generate_bulk')
                ->label('Generate Bulk Payroll')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('success')
                ->form([
                    Forms\Components\Section::make('Target month')
                        ->schema([
                            Forms\Components\Select::make('month')
                                ->label('Month')
                                ->options(array_combine(
                                    ['January','February','March','April','May','June','July','August','September','October','November','December'],
                                    ['January','February','March','April','May','June','July','August','September','October','November','December']
                                ))
                                ->required()
                                ->default(now()->format('F'))
                                ->native(false),

                            Forms\Components\TextInput::make('year')
                                ->label('Year')
                                ->numeric()
                                ->required()
                                ->default(now()->year)
                                ->minValue(2000)
                                ->maxValue(now()->year + 1),
                        ])
                        ->columns(2),

                    Forms\Components\Section::make('How to build each row')
                        ->description('The safest default is to clone the previous month — each employee keeps their basic salary, housing/supplement allowances, and any standing deductions. Any one-off items (Advance, School Fees, etc.) are dropped so you can add them fresh.')
                        ->schema([
                            Forms\Components\Toggle::make('clone_previous')
                                ->label('Clone previous month as template (recommended)')
                                ->default(true)
                                ->live(),

                            Forms\Components\Select::make('source_month')
                                ->label('Source month')
                                ->options(array_combine(
                                    ['January','February','March','April','May','June','July','August','September','October','November','December'],
                                    ['January','February','March','April','May','June','July','August','September','October','November','December']
                                ))
                                ->default(now()->subMonth()->format('F'))
                                ->required(fn (Forms\Get $get) => $get('clone_previous'))
                                ->visible(fn (Forms\Get $get) => $get('clone_previous'))
                                ->native(false),

                            Forms\Components\TextInput::make('source_year')
                                ->label('Source year')
                                ->numeric()
                                ->default(now()->subMonth()->year)
                                ->required(fn (Forms\Get $get) => $get('clone_previous'))
                                ->visible(fn (Forms\Get $get) => $get('clone_previous')),

                            Forms\Components\Toggle::make('carry_recurring_only')
                                ->label('Carry only recurring deductions')
                                ->helperText('Recommended. Keeps NAPSA, PAYE and other statutory items; skips one-off Cash Advance, School Fees, Battery, T-Shirt, Tracksuit, Holiday, Exchange.')
                                ->default(true)
                                ->visible(fn (Forms\Get $get) => $get('clone_previous')),
                        ])
                        ->columns(2),

                    Forms\Components\Section::make('Scope & safety')
                        ->schema([
                            Forms\Components\Select::make('departments')
                                ->label('Departments')
                                ->multiple()
                                ->options([
                                    'ecl' => 'ECL',
                                    'primary' => 'Primary School',
                                    'secondary' => 'Secondary School',
                                    'administration' => 'Administration',
                                    'support' => 'Support Staff',
                                ])
                                ->helperText('Leave empty to include all departments.')
                                ->native(false),

                            Forms\Components\Toggle::make('skip_existing')
                                ->label('Skip employees who already have payroll for the target month')
                                ->default(true),
                        ])
                        ->columns(2),
                ])
                ->modalHeading('Generate Bulk Payroll')
                ->modalDescription('Generate payroll records for multiple employees at once')
                ->modalSubmitActionLabel('Generate Payrolls')
                ->modalWidth('2xl')
                ->action(function (array $data) {
                    $month           = $data['month'];
                    $year            = (int) $data['year'];
                    $departments     = $data['departments'] ?? [];
                    $skipExisting    = $data['skip_existing'] ?? true;
                    $clonePrevious   = $data['clone_previous'] ?? true;
                    $sourceMonth     = $data['source_month'] ?? null;
                    $sourceYear      = isset($data['source_year']) ? (int) $data['source_year'] : null;
                    $recurringOnly   = $data['carry_recurring_only'] ?? true;

                    // Deductions considered "one-off" — dropped when cloning from prior month.
                    $oneOffDeductions = [
                        'advance', 'cash advance', 'holiday', 't-shirt', 'tshirt', 'tracksuit',
                        'school fees', 'battery', 'exchange',
                    ];

                    $query = Employee::where('status', 'active');
                    if (! empty($departments)) $query->whereIn('department', $departments);
                    $employees = $query->get();

                    if ($employees->isEmpty()) {
                        Notification::make()->title('No employees found')
                            ->body('No active employees matched the selected departments.')
                            ->warning()->send();
                        return;
                    }

                    $created = 0; $skipped = 0; $noTemplate = 0; $errors = 0;

                    foreach ($employees as $employee) {
                        if ($skipExisting && Payroll::where('employee_id', $employee->id)
                                ->where('month', $month)->where('year', $year)->exists()) {
                            $skipped++;
                            continue;
                        }

                        try {
                            $basic       = null;
                            $allowances  = [];
                            $deductions  = [];
                            $sourceNote  = '';

                            if ($clonePrevious) {
                                // Prefer the exact source month/year requested; if that's missing
                                // for this employee, fall back to their most recent payroll.
                                $template = Payroll::where('employee_id', $employee->id)
                                    ->where('month', $sourceMonth)
                                    ->where('year', $sourceYear)
                                    ->first()
                                    ?? Payroll::where('employee_id', $employee->id)
                                        ->orderByDesc('year')->orderByDesc('id')->first();

                                if ($template) {
                                    $basic      = (float) $template->basic_salary;
                                    $allowances = $template->allowances ?? [];
                                    $deductions = $template->deductions ?? [];
                                    if ($recurringOnly) {
                                        $deductions = array_values(array_filter(
                                            $deductions,
                                            fn ($d) => ! in_array(strtolower(trim($d['type'] ?? '')), $oneOffDeductions, true)
                                        ));
                                    }
                                    $sourceNote = "Cloned from {$template->month} {$template->year}";
                                }
                            }

                            // Fallback: no prior payroll → use Employee.basic_salary and statutory only.
                            if ($basic === null) {
                                if (! $employee->basic_salary) { $noTemplate++; continue; }
                                $basic = (float) $employee->basic_salary;
                                $calc  = (new PayrollCalculationService)->calculatePayroll($basic);
                                $deductions = $calc['deductions'];
                                $sourceNote = 'No prior payroll — used Employee.basic_salary + statutory deductions';
                            }

                            $totalAllow = array_sum(array_map(fn ($a) => (float) ($a['amount'] ?? 0), $allowances));
                            $totalDed   = array_sum(array_map(fn ($d) => (float) ($d['amount'] ?? 0), $deductions));
                            $gross      = round($basic + $totalAllow, 2);
                            $net        = round($gross - $totalDed, 2);

                            Payroll::create([
                                'employee_id'    => $employee->id,
                                'month'          => $month,
                                'year'           => $year,
                                'department'     => $employee->department,
                                'basic_salary'   => $basic,
                                'allowances'     => $allowances,
                                'deductions'     => $deductions,
                                'gross_salary'   => $gross,
                                'net_salary'     => $net,
                                'payment_status' => 'pending',
                                'notes'          => "{$month} {$year} bulk generation. {$sourceNote}",
                            ]);
                            $created++;
                        } catch (\Throwable $e) {
                            $errors++;
                            \Log::error('Failed to create payroll for employee', [
                                'employee_id' => $employee->id,
                                'month' => $month, 'year' => $year,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }

                    $bits = ["Created: {$created}"];
                    if ($skipped)    $bits[] = "already existed: {$skipped}";
                    if ($noTemplate) $bits[] = "no template and no basic salary: {$noTemplate}";
                    if ($errors)     $bits[] = "errors: {$errors}";

                    Notification::make()
                        ->title("Bulk payroll for {$month} {$year}")
                        ->body(implode(' · ', $bits))
                        ->success($created > 0)
                        ->warning($created === 0)
                        ->duration(6000)
                        ->send();
                }),
        ];
    }
}

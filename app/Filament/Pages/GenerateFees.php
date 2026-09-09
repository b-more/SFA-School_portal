<?php

namespace App\Filament\Pages;

use App\Models\AcademicYear;
use App\Models\FeeCatalogueItem;
use App\Models\FeeCategory;
use App\Models\FeeStructure;
use App\Models\Grade;
use App\Models\Student;
use App\Services\FeeProvisioningService;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class GenerateFees extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = 'Fees';
    protected static ?string $navigationLabel = 'Generate Fees';
    protected static ?int $navigationSort = 10;
    protected static string $view = 'filament.pages.generate-fees';
    protected static ?string $title = 'Generate Fees';

    public static function shouldRegisterNavigation(): bool { return self::canAccess(); }

    public static function canAccess(): bool
    {
        if (auth()->user()?->isFinanceLocked()) return false;
        return in_array(auth()->user()?->role_id, [
            \App\Constants\RoleConstants::ADMIN,
            \App\Constants\RoleConstants::ACCOUNTANT,
            \App\Constants\RoleConstants::DIRECTOR,
        ], true);
    }

    protected function getHeaderActions(): array
    {
        // Note: bus is intentionally not auto-billed — parents pay K500/month at
        // the cashier to ride that month; no payment, no debt, no boarding.
        return [
            $this->annualFeesAction(),
            $this->uniformAction(),
            $this->assignCatalogueAction('educational_tour', 'Assign Educational Tour', 'heroicon-o-map'),
        ];
    }

    private function annualFeesAction(): Action
    {
        return Action::make('annual')
            ->label('Generate Annual Fees')
            ->icon('heroicon-o-calendar-days')
            ->color('primary')
            ->form([
                Forms\Components\Select::make('academic_year_id')
                    ->label('Academic year')
                    ->options(AcademicYear::orderByDesc('id')->pluck('name', 'id'))
                    ->default(AcademicYear::where('is_active', true)->value('id') ?? AcademicYear::where('is_current', true)->value('id'))
                    ->required(),
                Forms\Components\Toggle::make('preview_only')
                    ->label('Preview only (don\'t create yet)')->default(true),
            ])
            ->action(function (array $data) {
                $year = AcademicYear::find($data['academic_year_id']);
                $svc  = app(FeeProvisioningService::class);
                $stats = $svc->generateAnnualFees($year, dryRun: (bool) $data['preview_only']);
                $this->summary('Annual fees for ' . $year->name, $stats, $data['preview_only']);
            });
    }

    private function uniformAction(): Action
    {
        return Action::make('uniform')
            ->label('Assign Uniform Item')
            ->icon('heroicon-o-shopping-bag')
            ->color('success')
            ->form([
                Forms\Components\Select::make('item_key')
                    ->label('Uniform item')
                    ->options(fn () => $this->uniformOptionsFromFeeStructures())
                    ->required()
                    ->helperText('Items come from the uniform list in Fee Structures (Boys / Girls / Sports).'),
                Forms\Components\Select::make('grade_ids')
                    ->label('Grades (optional)')
                    ->multiple()
                    ->options(Grade::orderBy('id')->pluck('name', 'id')),
                Forms\Components\Select::make('student_ids')
                    ->label('Or pick individual students')
                    ->multiple()->searchable()
                    ->options(fn () => Student::where('enrollment_status', 'active')->orderBy('name')->limit(500)->pluck('name', 'id')),
                Forms\Components\Toggle::make('preview_only')
                    ->label('Preview only (don\'t create yet)')->default(true),
            ])
            ->action(function (array $data) {
                [$name, $amount] = $this->parseUniformKey($data['item_key']);
                if (! $name || $amount === null) {
                    Notification::make()->danger()->title('Uniform item not found.')->send();
                    return;
                }

                $studentIds = collect();
                if (! empty($data['grade_ids'])) {
                    $studentIds = $studentIds->merge(
                        Student::whereIn('grade_id', $data['grade_ids'])
                            ->where('enrollment_status', 'active')->pluck('id')
                    );
                }
                if (! empty($data['student_ids'])) {
                    $studentIds = $studentIds->merge($data['student_ids']);
                }
                $studentIds = $studentIds->unique()->values()->all();
                if (empty($studentIds)) {
                    Notification::make()->warning()->title('Pick at least one grade or student.')->send();
                    return;
                }

                $svc = app(FeeProvisioningService::class);
                $stats = $svc->assignAdhocFee('uniform', $name, (float) $amount, $studentIds, dryRun: (bool) $data['preview_only']);
                $this->summary("Assign \"{$name}\" (K{$amount}) to " . count($studentIds) . ' student(s)', $stats, $data['preview_only']);
            });
    }

    /**
     * Pull uniform options (Boys/Girls/Sports prefixed) out of every active
     * fee_structure's additional_charges JSON, dedupe by name+amount.
     * Returns a name=>label map suitable for a Select component.
     */
    private function uniformOptionsFromFeeStructures(): array
    {
        $activeYearId = AcademicYear::where('is_active', true)->value('id')
            ?? AcademicYear::orderByDesc('id')->value('id');
        $structures = FeeStructure::where('is_active', true)
            ->when($activeYearId, fn ($q) => $q->where('academic_year_id', $activeYearId))
            ->get(['additional_charges']);

        $seen = [];
        foreach ($structures as $fs) {
            $charges = is_array($fs->additional_charges)
                ? $fs->additional_charges
                : (json_decode((string) $fs->additional_charges, true) ?: []);
            foreach ($charges as $c) {
                $desc = trim((string) ($c['description'] ?? ''));
                $amount = (float) ($c['amount'] ?? 0);
                if ($desc === '' || $amount <= 0) continue;
                if (! preg_match('/^(Boys|Girls|Sports)\b/i', $desc)) continue;
                $key = $desc . '|' . number_format($amount, 2, '.', '');
                $seen[$key] = $desc . ' — K' . number_format($amount, 2);
            }
        }
        ksort($seen);
        return $seen;
    }

    private function parseUniformKey(?string $key): array
    {
        if (! $key) return [null, null];
        [$name, $amount] = array_pad(explode('|', $key, 2), 2, null);
        return [$name, $amount === null ? null : (float) $amount];
    }

    private function assignCatalogueAction(string $categoryCode, string $label, string $icon): Action
    {
        return Action::make($categoryCode)
            ->label($label)
            ->icon($icon)
            ->color('success')
            ->form([
                Forms\Components\Select::make('catalogue_item_id')
                    ->label('Catalogue item')
                    ->options(fn () => FeeCatalogueItem::whereHas('category', fn ($q) => $q->where('code', $categoryCode))
                        ->where('is_active', true)
                        ->orderBy('name')->pluck('name', 'id'))
                    ->required()
                    ->helperText('No items here? Add some under Fee Catalogue.'),
                Forms\Components\Select::make('grade_ids')
                    ->label('Grades (optional — leave blank to pick students)')
                    ->multiple()
                    ->options(Grade::orderBy('id')->pluck('name', 'id')),
                Forms\Components\Select::make('student_ids')
                    ->label('Or pick individual students')
                    ->multiple()
                    ->searchable()
                    ->options(fn () => Student::where('enrollment_status', 'active')->orderBy('name')->limit(500)->pluck('name', 'id')),
                Forms\Components\TextInput::make('period_override')
                    ->label('Period label (optional)')
                    ->placeholder('Defaults to the catalogue item name'),
                Forms\Components\Toggle::make('preview_only')
                    ->label('Preview only (don\'t create yet)')->default(true),
            ])
            ->action(function (array $data) {
                $item = FeeCatalogueItem::find($data['catalogue_item_id']);
                if (! $item) {
                    Notification::make()->danger()->title('Catalogue item not found.')->send();
                    return;
                }

                $studentIds = collect();
                if (! empty($data['grade_ids'])) {
                    $studentIds = $studentIds->merge(
                        Student::whereIn('grade_id', $data['grade_ids'])
                            ->where('enrollment_status', 'active')->pluck('id')
                    );
                }
                if (! empty($data['student_ids'])) {
                    $studentIds = $studentIds->merge($data['student_ids']);
                }
                $studentIds = $studentIds->unique()->values()->all();

                if (empty($studentIds)) {
                    Notification::make()->warning()->title('Pick at least one grade or student.')->send();
                    return;
                }

                $svc = app(FeeProvisioningService::class);
                $stats = $svc->assignCatalogueItem($item, $studentIds, $data['period_override'] ?: null, dryRun: (bool) $data['preview_only']);
                $this->summary("Assign \"{$item->name}\" to " . count($studentIds) . ' student(s)', $stats, $data['preview_only']);
            });
    }

    private function summary(string $title, array $stats, bool $previewOnly): void
    {
        $verb = $previewOnly ? 'Preview' : 'Committed';
        $created = $stats['created'] ?? 0;
        $planned = $stats['planned'] ?? 0;
        $skipped = $stats['skipped'] ?? 0;
        $failed  = $stats['failed']  ?? 0;
        $body = $previewOnly
            ? "{$planned} would be created · {$skipped} already exist (skipped) · {$failed} would fail."
            : "{$created} created · {$skipped} already existed · {$failed} failed.";

        Notification::make()
            ->title("{$verb}: {$title}")
            ->body($body)
            ->color($failed > 0 ? 'warning' : 'success')
            ->duration(8000)
            ->send();
    }
}

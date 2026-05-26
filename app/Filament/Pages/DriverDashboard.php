<?php

namespace App\Filament\Pages;

use App\Constants\RoleConstants;
use App\Models\BusBoardingLog;
use App\Models\BusFareStructure;
use App\Models\BusPayment;
use App\Models\SchoolSettings;
use App\Models\Term;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class DriverDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'My Roster';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.driver-dashboard';

    public string $search = '';

    /** 'to_school' or 'from_school'. Defaults via mount() based on time of day. */
    public string $currentTrip = BusBoardingLog::TRIP_TO_SCHOOL;

    /** When true, hide students who already have a mark for the current trip. */
    public bool $showUnmarkedOnly = false;

    /** Today's logs for the current trip, keyed by student_id. */
    protected ?\Illuminate\Support\Collection $todaysLogs = null;

    public static function canAccess(): bool
    {
        return auth()->user()?->role_id === RoleConstants::DRIVER;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role_id === RoleConstants::DRIVER;
    }

    public function mount(): void
    {
        if (! static::canAccess()) {
            abort(403);
        }

        // Sensible default — morning pickup before noon, otherwise afternoon drop-off.
        $this->currentTrip = now()->hour < 12
            ? BusBoardingLog::TRIP_TO_SCHOOL
            : BusBoardingLog::TRIP_FROM_SCHOOL;
    }

    public function setTrip(string $trip): void
    {
        if (! in_array($trip, [BusBoardingLog::TRIP_TO_SCHOOL, BusBoardingLog::TRIP_FROM_SCHOOL], true)) {
            return;
        }
        $this->currentTrip = $trip;
        $this->todaysLogs = null; // bust per-request cache
    }

    public function toggleUnmarkedOnly(): void
    {
        $this->showUnmarkedOnly = ! $this->showUnmarkedOnly;
    }

    public function getTitle(): string
    {
        $hour = now()->hour;
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
        $first = explode(' ', (string) (auth()->user()->name ?? 'Driver'))[0];

        return "{$greeting}, {$first}";
    }

    public function getHeading(): string
    {
        return $this->getTitle();
    }

    public function getSubheading(): ?string
    {
        return null;
    }

    public function clearSearch(): void
    {
        $this->search = '';
    }

    /**
     * Today's boarding logs for the current trip, indexed by student_id.
     */
    public function todaysLogs(): \Illuminate\Support\Collection
    {
        if ($this->todaysLogs === null) {
            $routeIds = $this->driverRoutes()->pluck('id');

            $this->todaysLogs = BusBoardingLog::query()
                ->whereIn('bus_fare_structure_id', $routeIds)
                ->whereDate('date', now()->toDateString())
                ->where('trip', $this->currentTrip)
                ->get()
                ->keyBy('student_id');
        }

        return $this->todaysLogs;
    }

    /**
     * "12 of 14 marked" — boarding progress for the visible roster today on the current trip.
     *
     * Returns [markedCount, totalCount].
     */
    public function boardingProgress(): array
    {
        $total = $this->totalCount();
        $marked = $this->todaysLogs()->count();

        return [min($marked, $total), $total];
    }

    /**
     * Per-status counts for the current trip, plus an "unmarked" derived figure.
     * Returns ['boarded' => int, 'absent' => int, 'no_show' => int, 'unmarked' => int].
     */
    public function boardingStats(): array
    {
        $logs = $this->todaysLogs();
        $boarded = $logs->where('status', BusBoardingLog::STATUS_BOARDED)->count();
        $absent = $logs->where('status', BusBoardingLog::STATUS_ABSENT)->count();
        $noShow = $logs->where('status', BusBoardingLog::STATUS_NO_SHOW)->count();
        $total = $this->totalCount();

        return [
            'boarded' => $boarded,
            'absent' => $absent,
            'no_show' => $noShow,
            'unmarked' => max(0, $total - ($boarded + $absent + $noShow)),
        ];
    }

    /**
     * Student IDs that have NO log for the current trip today. Used by the view to
     * highlight or filter the roster.
     */
    public function unmarkedStudentIds(): array
    {
        $marked = $this->todaysLogs()->keys()->all();
        $all = $this->basePaymentQuery()->pluck('student_id')->all();

        return array_values(array_diff($all, $marked));
    }

    /**
     * Driver taps a status button on a card. Upserts the log row for today,
     * for the currently-selected trip.
     */
    public function markBoarding(int $studentId, int $routeId, string $status): void
    {
        if (! in_array($status, [
            BusBoardingLog::STATUS_BOARDED,
            BusBoardingLog::STATUS_ABSENT,
            BusBoardingLog::STATUS_NO_SHOW,
        ], true)) {
            return;
        }

        // Confirm this driver actually owns this route (defence-in-depth).
        $ownsRoute = $this->driverRoutes()->contains('id', $routeId);
        if (! $ownsRoute) {
            return;
        }

        BusBoardingLog::updateOrCreate(
            [
                'bus_fare_structure_id' => $routeId,
                'student_id' => $studentId,
                'date' => now()->toDateString(),
                'trip' => $this->currentTrip,
            ],
            [
                'status' => $status,
                'recorded_by_user_id' => auth()->id(),
            ]
        );

        // Bust the in-request cache so the view sees the new state.
        $this->todaysLogs = null;

        $tripLabel = BusBoardingLog::trips()[$this->currentTrip];
        Notification::make()
            ->title($tripLabel . ' updated')
            ->body('Marked as ' . BusBoardingLog::statuses()[$status])
            ->success()
            ->duration(1500)
            ->send();
    }

    public function driverRoutes(): Collection
    {
        return BusFareStructure::query()
            ->where('driver_user_id', auth()->id())
            ->where('is_active', true)
            ->orderBy('route_name')
            ->get();
    }

    public function currentTerm(): ?Term
    {
        return Term::where('is_current', true)->first();
    }

    /**
     * Paid-up bus_payments for this driver's routes for the relevant period,
     * grouped by route_id. Eager-loads everything the Blade needs.
     *
     * Monthly routes  -> month = current month name & year = current year
     * Per-term routes -> term_id = current term id & year = current year
     */
    public function rosterByRoute(): \Illuminate\Support\Collection
    {
        $rows = $this->basePaymentQuery()->get();

        if ($this->search !== '') {
            $needle = mb_strtolower($this->search);
            $rows = $rows->filter(function ($r) use ($needle) {
                $hay = mb_strtolower(implode(' ', array_filter([
                    $r->student?->name,
                    $r->student?->parentGuardian?->name,
                    $r->student?->parentGuardian?->phone,
                    $r->student?->address,
                ])));

                return str_contains($hay, $needle);
            });
        }

        return $rows
            ->sortBy(fn ($r) => mb_strtolower((string) $r->student?->name))
            ->groupBy(fn ($r) => $r->bus_fare_structure_id);
    }

    public function totalCount(): int
    {
        return $this->basePaymentQuery()->count();
    }

    protected function basePaymentQuery(): Builder
    {
        $currentMonth = now()->format('F');
        $currentYear = now()->year;
        $currentTermId = $this->currentTerm()?->id;

        return BusPayment::query()
            ->whereHas('busFareStructure', fn ($q) => $q->where('driver_user_id', auth()->id()))
            ->whereIn('payment_status', ['paid', 'partial'])
            ->where('year', $currentYear)
            ->where(function (Builder $q) use ($currentMonth, $currentTermId) {
                $q->where(function (Builder $qq) use ($currentMonth) {
                    $qq->whereHas('busFareStructure', fn ($r) => $r->where('payment_plan', 'monthly'))
                        ->where('month', $currentMonth);
                });

                if ($currentTermId) {
                    $q->orWhere(function (Builder $qq) use ($currentTermId) {
                        $qq->whereHas('busFareStructure', fn ($r) => $r->where('payment_plan', 'per_term'))
                            ->where('term_id', $currentTermId);
                    });
                }
            })
            ->with([
                'busFareStructure:id,route_name,payment_plan',
                'term:id,name,start_date,end_date',
                'student:id,name,address,grade_id,parent_guardian_id',
                'student.grade:id,name,school_section_id',
                'student.grade.schoolSection:id,code,name',
                'student.parentGuardian:id,name,phone,alternate_phone',
            ]);
    }

    public function downloadPdf()
    {
        $rows = $this->basePaymentQuery()
            ->get()
            ->sortBy(fn ($r) => mb_strtolower((string) $r->student?->name));

        $pdf = Pdf::loadView('pdf.driver-paid-up', [
            'rows' => $rows,
            'driver' => auth()->user(),
            'routes' => $this->driverRoutes(),
            'term' => $this->currentTerm(),
            'period' => now()->format('F Y'),
            'settings' => SchoolSettings::first(),
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');

        $filename = 'bus_roster_' . Carbon::now()->format('Y-m-d') . '.pdf';

        return response()->streamDownload(fn () => print($pdf->output()), $filename);
    }
}

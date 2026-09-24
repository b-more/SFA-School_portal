<?php

namespace App\Filament\Pages;

use App\Constants\RoleConstants;
use App\Models\ClinicVisit;
use App\Models\MedicalStockItem;
use App\Models\StockTransaction;
use Carbon\Carbon;
use Filament\Pages\Page;

class ClinicianDashboard extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-heart';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title           = 'Clinic Dashboard';
    protected static ?string $slug            = 'clinic';
    protected static ?int    $navigationSort  = -100;   // lands first in the sidebar
    protected static string  $view            = 'filament.pages.clinician-dashboard';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role_id, [
            RoleConstants::CLINICIAN,
            RoleConstants::NURSE,
            RoleConstants::ADMIN,
        ], true);
    }

    public static function shouldRegisterNavigation(): bool { return self::canAccess(); }

    /* ---------------- Data the view reads ---------------- */

    public function getTodayStatsProperty(): array
    {
        $rows = ClinicVisit::whereDate('visit_date', today())->get();

        return [
            'total'    => $rows->count(),
            'sick'     => $rows->where('sick_note_issued', true)->count(),
            'sent_home'=> $rows->where('outcome', 'sent_home')->count(),
            'referred' => $rows->where('outcome', 'referred')->count(),
        ];
    }

    public function getWeekStatsProperty(): array
    {
        $from = now()->startOfWeek();
        $to   = now()->endOfWeek()->endOfDay();

        $rows = ClinicVisit::whereBetween('visit_date', [$from, $to])->get();
        $byDay = collect();
        foreach (\Carbon\CarbonPeriod::create($from, $to) as $d) {
            $byDay[$d->format('D')] = $rows->filter(fn ($v) => Carbon::parse($v->visit_date)->isSameDay($d))->count();
        }

        return [
            'total' => $rows->count(),
            'from'  => $from,
            'to'    => $to,
            'by_day'=> $byDay,
            'peak'  => $byDay->max() ?: 1,
        ];
    }

    public function getRecentVisitsProperty()
    {
        return ClinicVisit::with('complaints')
            ->orderByDesc('visit_date')
            ->orderByDesc('id')
            ->limit(8)
            ->get();
    }

    public function getLowStockProperty()
    {
        return MedicalStockItem::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->filter(fn ($i) => $i->is_low_stock)
            ->values();
    }

    public function getRepeatVisitorsProperty()
    {
        return ClinicVisit::selectRaw('student_name, COUNT(*) as c')
            ->whereBetween('visit_date', [now()->startOfMonth(), now()->endOfDay()])
            ->groupBy('student_name')
            ->having('c', '>=', 3)
            ->orderByDesc('c')
            ->limit(5)
            ->get();
    }

    public function getMonthSpendProperty(): float
    {
        return (float) StockTransaction::query()
            ->where('transaction_type', 'purchase')
            ->whereBetween('transaction_date', [now()->startOfMonth(), now()->endOfDay()])
            ->selectRaw('SUM(quantity * COALESCE(unit_cost, 0)) as total')
            ->value('total');
    }
}

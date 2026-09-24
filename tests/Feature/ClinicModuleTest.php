<?php

namespace Tests\Feature;

use App\Models\ClinicComplaint;
use App\Models\ClinicVisit;
use App\Models\MedicalStockItem;
use App\Models\StockTransaction;
use App\Services\ClinicReportService;
use App\Services\StockLedgerService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use RuntimeException;
use Tests\TestCase;

class ClinicModuleTest extends TestCase
{
    // Wrap each test in a DB transaction that rolls back at the end.
    // Full RefreshDatabase would re-run the project's ~200 migrations for
    // every test, which is slow and hits a pre-existing broken migration.
    use DatabaseTransactions;

    private MedicalStockItem $panadol;
    private MedicalStockItem $brufen;
    private ClinicComplaint  $headache;
    private ClinicComplaint  $fever;

    protected function setUp(): void
    {
        parent::setUp();
        $this->panadol = MedicalStockItem::create(['name' => 'Panadol',  'category' => 'tablet', 'unit' => 'tablets', 'reorder_level' => 10]);
        $this->brufen  = MedicalStockItem::create(['name' => 'Brufen',   'category' => 'tablet', 'unit' => 'tablets', 'reorder_level' => 10]);
        $this->headache = ClinicComplaint::create(['name' => 'Headache']);
        $this->fever    = ClinicComplaint::create(['name' => 'Fever']);
    }

    /* ---------------- Balance calculation ---------------- */

    public function test_current_balance_sums_ledger_correctly(): void
    {
        StockTransaction::create(['medical_stock_item_id' => $this->panadol->id, 'transaction_type' => 'opening',  'quantity' => 100, 'transaction_date' => '2026-01-01']);
        StockTransaction::create(['medical_stock_item_id' => $this->panadol->id, 'transaction_type' => 'purchase', 'quantity' => 50,  'transaction_date' => '2026-02-01']);
        StockTransaction::create(['medical_stock_item_id' => $this->panadol->id, 'transaction_type' => 'usage',    'quantity' => 20,  'transaction_date' => '2026-03-01']);
        StockTransaction::create(['medical_stock_item_id' => $this->panadol->id, 'transaction_type' => 'expired_damaged', 'quantity' => 5, 'transaction_date' => '2026-03-15']);
        StockTransaction::create(['medical_stock_item_id' => $this->panadol->id, 'transaction_type' => 'adjustment', 'quantity' => -3, 'transaction_date' => '2026-04-01']);

        // 100 + 50 - 20 - 5 - 3 = 122
        $this->assertSame(122, $this->panadol->fresh()->current_balance);
    }

    public function test_low_stock_flag_reflects_reorder_level(): void
    {
        StockTransaction::create(['medical_stock_item_id' => $this->panadol->id, 'transaction_type' => 'opening', 'quantity' => 8, 'transaction_date' => '2026-01-01']);
        $this->assertTrue($this->panadol->fresh()->is_low_stock);

        StockTransaction::create(['medical_stock_item_id' => $this->panadol->id, 'transaction_type' => 'purchase', 'quantity' => 20, 'transaction_date' => '2026-01-02']);
        $this->assertFalse($this->panadol->fresh()->is_low_stock);
    }

    /* ---------------- Auto-deduction on visit ---------------- */

    public function test_visit_dispensing_creates_usage_and_reduces_balance(): void
    {
        StockTransaction::create(['medical_stock_item_id' => $this->panadol->id, 'transaction_type' => 'opening', 'quantity' => 100, 'transaction_date' => '2026-07-01']);
        StockTransaction::create(['medical_stock_item_id' => $this->brufen->id,  'transaction_type' => 'opening', 'quantity' => 100, 'transaction_date' => '2026-07-01']);

        $visit = ClinicVisit::create([
            'visit_date' => '2026-07-16', 'student_name' => 'Test Pupil', 'grade' => '5',
        ]);

        app(StockLedgerService::class)->syncVisitUsage($visit, [
            ['item_id' => $this->panadol->id, 'quantity' => 3],
            ['item_id' => $this->brufen->id,  'quantity' => 1],
        ]);

        $this->assertSame(97, $this->panadol->fresh()->current_balance);
        $this->assertSame(99, $this->brufen->fresh()->current_balance);
        $this->assertSame(2, StockTransaction::where('clinic_visit_id', $visit->id)->where('transaction_type', 'usage')->count());
    }

    public function test_editing_visit_rewrites_usage_lines_without_double_deduct(): void
    {
        StockTransaction::create(['medical_stock_item_id' => $this->panadol->id, 'transaction_type' => 'opening', 'quantity' => 100, 'transaction_date' => '2026-07-01']);

        $visit = ClinicVisit::create(['visit_date' => '2026-07-16', 'student_name' => 'Test Pupil', 'grade' => '5']);

        app(StockLedgerService::class)->syncVisitUsage($visit, [['item_id' => $this->panadol->id, 'quantity' => 5]]);
        $this->assertSame(95, $this->panadol->fresh()->current_balance);

        // Edit — bump from 5 to 2. Old usage line must be reversed.
        app(StockLedgerService::class)->syncVisitUsage($visit, [['item_id' => $this->panadol->id, 'quantity' => 2]]);
        $this->assertSame(98, $this->panadol->fresh()->current_balance);
        $this->assertSame(1, StockTransaction::where('clinic_visit_id', $visit->id)->where('transaction_type', 'usage')->count());
    }

    public function test_dispensing_refuses_to_push_balance_negative(): void
    {
        StockTransaction::create(['medical_stock_item_id' => $this->panadol->id, 'transaction_type' => 'opening', 'quantity' => 3, 'transaction_date' => '2026-07-01']);

        $visit = ClinicVisit::create(['visit_date' => '2026-07-16', 'student_name' => 'Test Pupil', 'grade' => '5']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/Cannot dispense/');
        app(StockLedgerService::class)->syncVisitUsage($visit, [['item_id' => $this->panadol->id, 'quantity' => 5]]);
    }

    /* ---------------- Period filters ---------------- */

    public function test_weekly_and_monthly_period_boundaries(): void
    {
        StockTransaction::create(['medical_stock_item_id' => $this->panadol->id, 'transaction_type' => 'opening', 'quantity' => 100, 'transaction_date' => '2026-07-01']);

        // Visits spread across two weeks in July 2026.
        $this->recordVisit('2026-07-13', 'Pupil A', '5', [$this->headache->id]); // Mon week 29
        $this->recordVisit('2026-07-17', 'Pupil B', '5', [$this->fever->id]);    // Fri week 29
        $this->recordVisit('2026-07-20', 'Pupil C', '5', [$this->fever->id]);    // Mon week 30
        $this->recordVisit('2026-06-30', 'Pupil D', '5', [$this->headache->id]); // June — outside July

        $svc = app(ClinicReportService::class);

        // Weekly (13–19 Jul)
        [$f, $t] = $svc->resolvePeriod(['kind' => 'weekly', 'anchor' => '2026-07-15']);
        $this->assertSame('2026-07-13', $f->toDateString());
        $this->assertSame('2026-07-19', $t->toDateString());
        $this->assertSame(2, $svc->visitStats($f, $t)['total_visits']);

        // Monthly (Jul)
        [$f, $t] = $svc->resolvePeriod(['kind' => 'monthly', 'anchor' => '2026-07-15']);
        $this->assertSame('2026-07-01', $f->toDateString());
        $this->assertSame('2026-07-31', $t->toDateString());
        $this->assertSame(3, $svc->visitStats($f, $t)['total_visits']);
    }

    public function test_grade_normalization_forms_and_grades(): void
    {
        $this->assertSame(8,  ClinicVisit::normalizeGrade('Form 1'));
        $this->assertSame(12, ClinicVisit::normalizeGrade('Form 5'));
        $this->assertSame(5,  ClinicVisit::normalizeGrade('Grade 5'));
        $this->assertSame(10, ClinicVisit::normalizeGrade('10'));
        $this->assertNull(ClinicVisit::normalizeGrade('nonsense'));
    }

    /* ---------------- Helper ---------------- */

    private function recordVisit(string $date, string $name, string $grade, array $complaintIds): ClinicVisit
    {
        $v = ClinicVisit::create(['visit_date' => $date, 'student_name' => $name, 'grade' => $grade]);
        $v->complaints()->sync($complaintIds);
        return $v;
    }
}

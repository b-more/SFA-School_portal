<?php

namespace App\Services;

use App\Models\ClinicVisit;
use App\Models\MedicalStockItem;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Single writer for the medical stock ledger. Guards against negative balances
 * (see registerUsage), pipelines purchase and adjustment writes with the same
 * audit fields, and rewrites usage lines when a visit's treatments change so
 * balances always reflect what the clinician last saved.
 */
class StockLedgerService
{
    /**
     * Rewrite usage lines for a visit.
     *
     * @param  array<int, array{item_id:int, quantity:int}>  $lines
     */
    public function syncVisitUsage(ClinicVisit $visit, array $lines, ?int $actorId = null): void
    {
        DB::transaction(function () use ($visit, $lines, $actorId) {
            // Wipe the visit's old usage rows so an edit doesn't double-deduct.
            StockTransaction::where('clinic_visit_id', $visit->id)
                ->where('transaction_type', 'usage')
                ->delete();

            foreach ($lines as $line) {
                $itemId  = (int) ($line['item_id']  ?? 0);
                $qty     = (int) ($line['quantity'] ?? 0);
                if (! $itemId || $qty < 1) continue;

                $item = MedicalStockItem::findOrFail($itemId);

                // Post-write balance check — refuses to dip below zero.
                if ($item->current_balance - $qty < 0) {
                    throw new RuntimeException(
                        "Cannot dispense {$qty} × {$item->name}: current balance is {$item->current_balance}. Record a stock purchase or an adjustment first."
                    );
                }

                StockTransaction::create([
                    'medical_stock_item_id' => $itemId,
                    'transaction_type'      => 'usage',
                    'quantity'              => $qty,
                    'clinic_visit_id'       => $visit->id,
                    'transaction_date'      => $visit->visit_date,
                    'recorded_by'           => $actorId ?? auth()->id(),
                    'notes'                 => "Dispensed on visit #{$visit->id} for {$visit->student_name}",
                ]);
            }
        });
    }

    public function recordPurchase(int $itemId, int $quantity, ?float $unitCost, ?string $supplier, string $date, ?string $notes = null, ?int $actorId = null): StockTransaction
    {
        return StockTransaction::create([
            'medical_stock_item_id' => $itemId,
            'transaction_type'      => 'purchase',
            'quantity'              => max(1, $quantity),
            'unit_cost'             => $unitCost,
            'supplier'              => $supplier,
            'transaction_date'      => $date,
            'recorded_by'           => $actorId ?? auth()->id(),
            'notes'                 => $notes,
        ]);
    }

    /**
     * Adjustment: quantity is signed (negative subtracts). The reason lives in
     * `notes` so an auditor can always trace a correction.
     */
    public function recordAdjustment(int $itemId, int $signedQuantity, string $reason, string $date, ?int $actorId = null): StockTransaction
    {
        return StockTransaction::create([
            'medical_stock_item_id' => $itemId,
            'transaction_type'      => 'adjustment',
            'quantity'              => $signedQuantity,
            'transaction_date'      => $date,
            'recorded_by'           => $actorId ?? auth()->id(),
            'notes'                 => $reason,
        ]);
    }
}

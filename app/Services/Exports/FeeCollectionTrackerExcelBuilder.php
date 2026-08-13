<?php

namespace App\Services\Exports;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Regenerates the uploaded "Fee Collection Tracker" workbook from live
 * DB data. Two sheets, layout kept close to the original so anyone
 * used to that spreadsheet reads the export at a glance.
 */
class FeeCollectionTrackerExcelBuilder
{
    private const HEADER_FILL     = 'FF0F2A44';   // Deep navy — school brand
    private const HEADER_FONT     = 'FFFFFFFF';
    private const SUBHEADER_FILL  = 'FFF5EFE0';   // Parchment
    private const TOTAL_FILL      = 'FFE8E0C8';
    private const ANNUAL_FILL     = 'FFDCE4EF';

    public function __construct(private array $data)
    {
    }

    public function stream(): StreamedResponse
    {
        $ss = new Spreadsheet();
        $ss->getProperties()
            ->setCreator('St. Francis of Assisi Private School Portal')
            ->setTitle('Fee Collection Tracker ' . $this->data['year_label']);

        $this->buildTrackerSheet($ss->getActiveSheet());
        $this->buildPopulationSheet($ss->createSheet());

        $ss->setActiveSheetIndex(0);

        $filename = sprintf('fee-collection-tracker-%s-%s.xlsx', $this->data['year_label'], now()->format('Ymd-His'));
        $writer = new Xlsx($ss);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /* --------------------------------------------------------------- */
    /* Sheet 1 — Fee Collection Tracker                                  */
    /* --------------------------------------------------------------- */

    private function buildTrackerSheet($sheet): void
    {
        $sheet->setTitle('Fee Collection Tracker');

        $row = 1;

        // Header block
        $sheet->setCellValue("C{$row}", 'ST. FRANCIS OF ASSISI PRIVATE SCHOOL');
        $sheet->mergeCells("C{$row}:H{$row}");
        $this->styleTitle($sheet, "C{$row}:H{$row}", 14, true);
        $row++;

        $sheet->setCellValue("C{$row}", 'For God And Country');
        $sheet->mergeCells("C{$row}:H{$row}");
        $sheet->getStyle("C{$row}")->getFont()->setItalic(true);
        $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;

        $sheet->setCellValue("C{$row}", 'TERMLY FEE COLLECTION TRACKER — ' . $this->data['year_label']);
        $sheet->mergeCells("C{$row}:H{$row}");
        $this->styleTitle($sheet, "C{$row}:H{$row}", 12, true);
        $row++;

        $sheet->setCellValue("C{$row}", 'Actuals pulled live from completed payment transactions. Everything else calculates automatically.');
        $sheet->mergeCells("C{$row}:H{$row}");
        $sheet->getStyle("C{$row}")->getFont()->setItalic(true)->setSize(9);
        $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row += 2;

        // Per-term tables
        foreach ($this->data['terms'] as $term) {
            $row = $this->writeTermBlock($sheet, $row, $term);
            $row++;
        }

        // Annual summary
        $row = $this->writeAnnualBlock($sheet, $row);
        $row += 2;

        // Salaries vs collections
        $this->writeSalariesBlock($sheet, $row);

        // Column widths
        foreach (['A' => 26, 'B' => 12, 'C' => 22, 'D' => 18, 'E' => 22, 'F' => 18, 'G' => 14, 'H' => 14] as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }
    }

    private function writeTermBlock($sheet, int $row, $term): int
    {
        $sheet->setCellValue("A{$row}", strtoupper($term->name) . ' — ' . $this->data['year_label']);
        $sheet->mergeCells("A{$row}:H{$row}");
        $this->styleBand($sheet, "A{$row}:H{$row}", self::HEADER_FILL, self::HEADER_FONT, true);
        $row++;

        $headers = ['Section', 'Pupils', 'Fee per Pupil (ZMW)', 'Expected (ZMW)', 'Actual Collected (ZMW)', 'Shortfall (ZMW)', '% Collected', '% Loss'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValueByColumnAndRow($i + 1, $row, $h);
        }
        $this->styleBand($sheet, "A{$row}:H{$row}", self::SUBHEADER_FILL, 'FF000000', true);
        $row++;

        $rows = $this->data['by_term'][$term->id];
        foreach ($this->data['sections'] as $section) {
            $r = $rows[$section];
            $sheet->setCellValue("A{$row}", $section);
            $sheet->setCellValue("B{$row}", $r['pupils']);
            $sheet->setCellValue("C{$row}", $r['fee_per']);
            $sheet->setCellValue("D{$row}", $r['expected']);
            $sheet->setCellValue("E{$row}", $r['actual']);
            $sheet->setCellValue("F{$row}", $r['shortfall']);
            $sheet->setCellValue("G{$row}", $r['pct_collected'] / 100);
            $sheet->setCellValue("H{$row}", $r['pct_loss'] / 100);
            $this->styleMoneyRow($sheet, $row);
            $row++;
        }

        $t = $this->data['term_totals'][$term->id];
        $sheet->setCellValue("A{$row}", 'TOTAL');
        $sheet->setCellValue("B{$row}", $t['pupils']);
        $sheet->setCellValue("D{$row}", $t['expected']);
        $sheet->setCellValue("E{$row}", $t['actual']);
        $sheet->setCellValue("F{$row}", $t['shortfall']);
        $sheet->setCellValue("G{$row}", $t['pct_collected'] / 100);
        $sheet->setCellValue("H{$row}", $t['pct_loss'] / 100);
        $this->styleMoneyRow($sheet, $row);
        $this->styleBand($sheet, "A{$row}:H{$row}", self::TOTAL_FILL, 'FF000000', true);
        $row++;

        return $row;
    }

    private function writeAnnualBlock($sheet, int $row): int
    {
        $sheet->setCellValue("A{$row}", 'ANNUAL SUMMARY — ALL THREE TERMS');
        $sheet->mergeCells("A{$row}:H{$row}");
        $this->styleBand($sheet, "A{$row}:H{$row}", self::HEADER_FILL, self::HEADER_FONT, true);
        $row++;

        $headers = ['Term', '', '', 'Expected (ZMW)', 'Actual (ZMW)', 'Shortfall (ZMW)', '% Collected', '% Loss'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValueByColumnAndRow($i + 1, $row, $h);
        }
        $this->styleBand($sheet, "A{$row}:H{$row}", self::SUBHEADER_FILL, 'FF000000', true);
        $row++;

        foreach ($this->data['terms'] as $term) {
            $t = $this->data['term_totals'][$term->id];
            $sheet->setCellValue("A{$row}", strtoupper($term->name));
            $sheet->setCellValue("D{$row}", $t['expected']);
            $sheet->setCellValue("E{$row}", $t['actual']);
            $sheet->setCellValue("F{$row}", $t['shortfall']);
            $sheet->setCellValue("G{$row}", $t['pct_collected'] / 100);
            $sheet->setCellValue("H{$row}", $t['pct_loss'] / 100);
            $this->styleMoneyRow($sheet, $row);
            $row++;
        }

        $a = $this->data['annual'];
        $sheet->setCellValue("A{$row}", 'YEAR TOTAL');
        $sheet->setCellValue("D{$row}", $a['expected']);
        $sheet->setCellValue("E{$row}", $a['actual']);
        $sheet->setCellValue("F{$row}", $a['shortfall']);
        $sheet->setCellValue("G{$row}", $a['pct_collected'] / 100);
        $sheet->setCellValue("H{$row}", $a['pct_loss'] / 100);
        $this->styleMoneyRow($sheet, $row);
        $this->styleBand($sheet, "A{$row}:H{$row}", self::ANNUAL_FILL, 'FF000000', true);
        $row++;

        return $row;
    }

    private function writeSalariesBlock($sheet, int $row): int
    {
        $sheet->setCellValue("A{$row}", 'CAN COLLECTIONS COVER SALARIES?  (main expense check)');
        $sheet->mergeCells("A{$row}:H{$row}");
        $this->styleBand($sheet, "A{$row}:H{$row}", self::HEADER_FILL, self::HEADER_FONT, true);
        $row++;

        $headers = ['', 'Salary Bill (ZMW)', 'Payroll Months Matched', 'Expected Fees (ZMW)', 'Actual Fees (ZMW)', 'Surplus after Salaries (ZMW)', 'Salaries as % of Expected', 'Salaries as % of Actual'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValueByColumnAndRow($i + 1, $row, $h);
        }
        $this->styleBand($sheet, "A{$row}:H{$row}", self::SUBHEADER_FILL, 'FF000000', true);
        $row++;

        foreach ($this->data['terms'] as $term) {
            $t   = $this->data['term_totals'][$term->id];
            $sb  = $this->data['salary_bill'][$term->id];
            $sm  = $this->data['salary_meta'][$term->id];
            $sheet->setCellValue("A{$row}", strtoupper($term->name));
            $sheet->setCellValue("B{$row}", $sb);
            $sheet->setCellValueExplicit("C{$row}", (string) $sm, DataType::TYPE_STRING);
            $sheet->setCellValue("D{$row}", $t['expected']);
            $sheet->setCellValue("E{$row}", $t['actual']);
            if ($sb !== null) {
                $sheet->setCellValue("F{$row}", $t['actual'] - $sb);
                $sheet->setCellValue("G{$row}", $t['expected'] > 0 ? $sb / $t['expected'] : 0);
                $sheet->setCellValue("H{$row}", $t['actual']   > 0 ? $sb / $t['actual']   : 0);
            }
            $this->styleMoneyRow($sheet, $row);
            $row++;
        }
        return $row;
    }

    /* --------------------------------------------------------------- */
    /* Sheet 2 — School Population                                       */
    /* --------------------------------------------------------------- */

    private function buildPopulationSheet($sheet): void
    {
        $sheet->setTitle('Population');
        $row = 1;

        $sheet->setCellValue("A{$row}", 'SCHOOL POPULATION BY CLASS — ' . $this->data['year_label']);
        $sheet->mergeCells("A{$row}:D{$row}");
        $this->styleTitle($sheet, "A{$row}:D{$row}", 13, true);
        $row += 2;

        $headers = ['Section', 'Class', 'Class Teacher', 'Enrolment'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValueByColumnAndRow($i + 1, $row, $h);
        }
        $this->styleBand($sheet, "A{$row}:D{$row}", self::HEADER_FILL, self::HEADER_FONT, true);
        $row++;

        foreach ($this->data['sections'] as $section) {
            foreach ($this->data['population'][$section] ?? [] as $entry) {
                $sheet->setCellValue("A{$row}", $section);
                $sheet->setCellValue("B{$row}", $entry['class']);
                $sheet->setCellValue("C{$row}", $entry['teacher']);
                $sheet->setCellValue("D{$row}", $entry['enrolment']);
                $row++;
            }
            $subtotal = $this->data['section_totals'][$section] ?? 0;
            $sheet->setCellValue("B{$row}", strtoupper($section) . ' TOTAL');
            $sheet->setCellValue("D{$row}", $subtotal);
            $this->styleBand($sheet, "A{$row}:D{$row}", self::SUBHEADER_FILL, 'FF000000', true);
            $row++;
        }

        $sheet->setCellValue("B{$row}", 'SCHOOL TOTAL');
        $sheet->setCellValue("D{$row}", $this->data['school_total']);
        $this->styleBand($sheet, "A{$row}:D{$row}", self::ANNUAL_FILL, 'FF000000', true);

        foreach (['A' => 14, 'B' => 30, 'C' => 28, 'D' => 12] as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }
    }

    /* --------------------------------------------------------------- */
    /* Style helpers                                                     */
    /* --------------------------------------------------------------- */

    private function styleTitle($sheet, string $range, int $size, bool $bold): void
    {
        $sheet->getStyle($range)->getFont()->setSize($size)->setBold($bold);
        $sheet->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    private function styleBand($sheet, string $range, string $fill, string $font, bool $bold): void
    {
        $s = $sheet->getStyle($range);
        $s->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($fill);
        $s->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color($font))->setBold($bold);
        $s->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    }

    private function styleMoneyRow($sheet, int $row): void
    {
        $sheet->getStyle("C{$row}:F{$row}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("G{$row}:H{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle("A{$row}:H{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    }
}

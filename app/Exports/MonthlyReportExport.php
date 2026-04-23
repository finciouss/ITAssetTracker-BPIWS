<?php

namespace App\Exports;

use App\Models\Asset;
use App\Models\AssetAllocation;
use App\Exports\Concerns\HasBauerExcelFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Concerns\Exportable;
use Carbon\Carbon;

class MonthlyReportExport implements WithEvents
{
    use Exportable, HasBauerExcelFormatting;

    protected $month;
    protected $year;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year  = $year;
    }

    public function registerEvents(): array
    {
        return [
            BeforeWriting::class => function (BeforeWriting $event) {
                $spreadsheet = $event->writer->getDelegate();
                $spreadsheet->removeSheetByIndex(0);

                $sheet = new Worksheet($spreadsheet, 'Monthly Report');
                $spreadsheet->addSheet($sheet, 0);
                $spreadsheet->setActiveSheetIndex(0);

                $this->buildSheet($sheet);
            },
        ];
    }

    protected function buildSheet(Worksheet $sheet): void
    {
        // ── Column widths (exact match to sample) ─────────────────────────────
        $sheet->getColumnDimension('A')->setWidth(2);
        $sheet->getColumnDimension('B')->setWidth(4.75);
        $sheet->getColumnDimension('C')->setWidth(14.75);
        $sheet->getColumnDimension('D')->setWidth(1);
        $sheet->getColumnDimension('E')->setWidth(67.25);
        $sheet->getColumnDimension('F')->setWidth(21.9140625);
        $sheet->getColumnDimension('G')->setWidth(10.08203125);
        $sheet->getColumnDimension('G')->setVisible(false);
        $sheet->getColumnDimension('H')->setWidth(22);

        // Default font
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial')->setSize(11);

        // Page setup
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_LETTER);
        $sheet->getPageMargins()->setTop(0.75)->setBottom(0.75)->setLeft(0.7)->setRight(0.7);

        // ── Company header (rows 1-6) ──────────────────────────────────────────
        $this->applyCompanyHeader($sheet);

        // ── Title (rows 7-8) ──────────────────────────────────────────────────
        $sheet->mergeCells('B7:H8');
        $sheet->setCellValue('B7', 'MONTHLY IT ASSET REPORT');
        $sheet->getStyle('B7')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('B7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
              ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(7)->setRowHeight(22);
        $sheet->getRowDimension(8)->setRowHeight(22);

        // ── Period / Date (rows 9-10) ──────────────────────────────────────────
        $monthName   = Carbon::createFromDate($this->year, $this->month, 1)->format('F Y');
        $currentDate = now()->timezone('Asia/Jakarta')->format('d F Y');

        $sheet->getRowDimension(9)->setRowHeight(15.5);

        $sheet->mergeCells('B9:C9');
        $sheet->setCellValue('B9', 'PERIOD');
        $sheet->getStyle('B9')->getFont()->setBold(true)->setSize(11);
        $sheet->setCellValue('D9', ':');
        $sheet->getStyle('D9')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('D9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('E9', $monthName);

        $sheet->mergeCells('B10:C10');
        $sheet->setCellValue('B10', 'DATE');
        $sheet->getStyle('B10')->getFont()->setBold(true)->setSize(11);
        $sheet->setCellValue('D10', ':');
        $sheet->getStyle('D10')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('D10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('E10', $currentDate);

        // ── Spacing (row 11) ──────────────────────────────────────────────────
        $sheet->getRowDimension(11)->setRowHeight(10);

        // ── Sections ──────────────────────────────────────────────────────────
        $row = 12;
        $row = $this->writeNewAssets($sheet, $row);
        $row = $this->writeCheckedOut($sheet, $row);
        $row = $this->writeReturned($sheet, $row);
        $row = $this->writeAllAssets($sheet, $row);

        // ── Signature block ────────────────────────────────────────────────────
        $this->applySignatureBlock($sheet, $row + 3);
    }

    // ── Shared helpers ────────────────────────────────────────────────────────

    /**
     * Write a section title in column B only (no merge, no border – matches sample).
     */
    private function writeSectionTitle(Worksheet $sheet, int $row, string $title): void
    {
        $sheet->setCellValue("B{$row}", $title);
        $sheet->getStyle("B{$row}")->getFont()->setBold(true)->setSize(11);
        $sheet->getRowDimension($row)->setRowHeight(15);
    }

    /**
     * Write a single-row table header (rows merged vertically B:H like sample C13:C14).
     * Returns first data row.
     */
    private function writeTableHeader(Worksheet $sheet, int $row1, array $headers): int
    {
        $row2 = $row1 + 1;

        // Gray fill + bold + center on both rows, B through H
        foreach ([$row1, $row2] as $r) {
            $sheet->getStyle("B{$r}:H{$r}")
                  ->getFill()->setFillType(Fill::FILL_SOLID)
                  ->getStartColor()->setRGB('EDEDED');
            $sheet->getStyle("B{$r}:H{$r}")->getFont()->setBold(true);
            $sheet->getStyle("B{$r}:H{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getRowDimension($r)->setRowHeight(15);
        }

        // Exact per-column border pattern from sample inspection
        // B: top+left+right thin both rows
        // C: top+left+right thin (no right — D is the gap)
        // D: left only (spacer col)
        // E: top+right thin (no left — D is the gap)
        // F: all sides thin
        // G: all sides thin (hidden col)
        // H: all sides thin
        $this->applyHeaderBorderRow($sheet, $row1, 'top');
        $this->applyHeaderBorderRow($sheet, $row2, 'bottom');

        // Merge each column vertically across both header rows
        foreach (array_keys($headers) as $col) {
            $sheet->mergeCells("{$col}{$row1}:{$col}{$row2}");
            $sheet->getStyle("{$col}{$row1}:{$col}{$row2}")
                  ->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        }
        $sheet->mergeCells("D{$row1}:D{$row2}");
        $sheet->mergeCells("G{$row1}:G{$row2}");

        // Write header labels
        foreach ($headers as $col => $label) {
            $sheet->setCellValue("{$col}{$row1}", $label);
        }

        return $row2 + 1;
    }

    /**
     * Apply per-column border to a header row.
     * $side = 'top' (row1) or 'bottom' (row2)
     */
    private function applyHeaderBorderRow(Worksheet $sheet, int $row, string $side): void
    {
        $t = Border::BORDER_THIN;
        $n = Border::BORDER_NONE;

        // B: top, left, right
        $this->setCellBorder($sheet, "B{$row}", $side === 'top' ? $t : $n, $side === 'bottom' ? $t : $n, $t, $t);
        // C: top, left (no right — D spacer)
        $this->setCellBorder($sheet, "C{$row}", $side === 'top' ? $t : $n, $side === 'bottom' ? $t : $n, $t, $n);
        // D: top + left + bottom (spacer col — user wants full vertical border)
        $this->setCellBorder($sheet, "D{$row}", $side === 'top' ? $t : $n, $side === 'bottom' ? $t : $n, $t, $n);
        // E: top, right (no left — D spacer)
        $this->setCellBorder($sheet, "E{$row}", $side === 'top' ? $t : $n, $side === 'bottom' ? $t : $n, $n, $t);
        // F: all sides
        $this->setCellBorder($sheet, "F{$row}", $side === 'top' ? $t : $n, $side === 'bottom' ? $t : $n, $t, $t);
        // G: all sides (hidden but matches sample)
        $this->setCellBorder($sheet, "G{$row}", $t, $t, $t, $t);
        // H: all sides
        $this->setCellBorder($sheet, "H{$row}", $side === 'top' ? $t : $n, $side === 'bottom' ? $t : $n, $t, $t);
    }

    /**
     * Write a data row. $data = ['B' => val, 'C' => val, ...]
     */
    private function writeDataRow(Worksheet $sheet, int $row, array $data): void
    {
        foreach ($data as $col => $value) {
            $sheet->setCellValue("{$col}{$row}", $value);
            $sheet->getStyle("{$col}{$row}")->getAlignment()
                  ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
        // Asset name (E) is left-aligned in sample
        if (isset($data['E'])) {
            $sheet->getStyle("E{$row}")->getAlignment()
                  ->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }
        $sheet->getRowDimension($row)->setRowHeight(-1); // auto

        // Per-column border pattern (exact from sample)
        $t = Border::BORDER_THIN;
        $n = Border::BORDER_NONE;
        $this->setCellBorder($sheet, "B{$row}", $t, $t, $t, $t);
        $this->setCellBorder($sheet, "C{$row}", $t, $t, $t, $n); // no right (D is gap)
        $this->setCellBorder($sheet, "D{$row}", $t, $t, $t, $n); // top+left+bottom (spacer)
        $this->setCellBorder($sheet, "E{$row}", $t, $t, $n, $t); // no left (D is gap)
        $this->setCellBorder($sheet, "F{$row}", $t, $t, $t, $t);
        $this->setCellBorder($sheet, "G{$row}", $t, $t, $t, $t);
        $this->setCellBorder($sheet, "H{$row}", $t, $t, $t, $t);
    }

    /**
     * Helper: set all 4 borders on a single cell.
     */
    private function setCellBorder(Worksheet $sheet, string $cell, string $top, string $bottom, string $left, string $right): void
    {
        $borders = $sheet->getStyle($cell)->getBorders();
        $borders->getTop()->setBorderStyle($top);
        $borders->getBottom()->setBorderStyle($bottom);
        $borders->getLeft()->setBorderStyle($left);
        $borders->getRight()->setBorderStyle($right);
    }

    /**
     * Write italic placeholder row. Returns next row.
     */
    private function writePlaceholder(Worksheet $sheet, int $row, string $msg): int
    {
        $sheet->setCellValue("E{$row}", $msg);
        $sheet->getStyle("E{$row}")->getFont()->setItalic(true)
              ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF888888'));
        $sheet->getRowDimension($row)->setRowHeight(15);
        return $row + 1;
    }

    // ── Sections ──────────────────────────────────────────────────────────────

    private function writeNewAssets(Worksheet $sheet, int $startRow): int
    {
        $this->writeSectionTitle($sheet, $startRow, 'NEW HARDWARE ACQUIRED');

        $dataRow = $this->writeTableHeader($sheet, $startRow + 1, [
            'B' => 'No.',
            'C' => 'Tag Number',
            'E' => 'Asset Name',
            'F' => 'Category',
            'H' => 'Stock',
        ]);

        $assets = Asset::whereMonth('purchase_date', $this->month)
            ->whereYear('purchase_date', $this->year)
            ->orderBy('purchase_date', 'desc')
            ->get();

        if ($assets->isEmpty()) {
            $dataRow = $this->writePlaceholder($sheet, $dataRow, '(No new hardware procured this month)');
        } else {
            $no = 1;
            foreach ($assets as $asset) {
                $avail = $asset->availableStock();
                $alloc = $asset->allocatedQuantity();
                $stockStr = "Total: {$asset->stock} | Avail: {$avail} | Alloc: {$alloc}";
                $this->writeDataRow($sheet, $dataRow, [
                    'B' => $no++,
                    'C' => $asset->tag_number ?? 'N/A',
                    'E' => $asset->name,
                    'F' => $asset->category,
                    'H' => $stockStr,
                ]);
                $dataRow++;
            }
        }

        $sheet->getRowDimension($dataRow)->setRowHeight(10); // spacing
        return $dataRow + 1;
    }

    private function writeCheckedOut(Worksheet $sheet, int $startRow): int
    {
        $this->writeSectionTitle($sheet, $startRow, 'ASSETS CHECKED OUT');

        $dataRow = $this->writeTableHeader($sheet, $startRow + 1, [
            'B' => 'No.',
            'C' => 'Tag Number',
            'E' => 'Asset Name',
            'F' => 'Allocated To',
            'H' => 'Qty / Stock',
        ]);

        $allocations = AssetAllocation::with(['asset', 'employee', 'project'])
            ->whereMonth('check_out_date', $this->month)
            ->whereYear('check_out_date', $this->year)
            ->orderBy('check_out_date', 'desc')
            ->get();

        if ($allocations->isEmpty()) {
            $dataRow = $this->writePlaceholder($sheet, $dataRow, '(No assets checked out this month)');
        } else {
            $no = 1;
            foreach ($allocations as $alloc) {
                $parts = [];
                if ($alloc->project)  $parts[] = $alloc->project->project_name;
                if ($alloc->employee) $parts[] = 'Pak ' . $alloc->employee->last_name;
                $assignedText = $parts ? implode(' & ', $parts) : 'Unassigned';

                $asset     = $alloc->asset;
                $totalStock = $asset ? $asset->stock : '?';
                $stockStr   = "x{$alloc->quantity} / {$totalStock}";

                $this->writeDataRow($sheet, $dataRow, [
                    'B' => $no++,
                    'C' => $alloc->asset->tag_number ?? 'N/A',
                    'E' => $alloc->asset->name ?? 'Deleted',
                    'F' => $assignedText,
                    'H' => $stockStr,
                ]);
                $dataRow++;
            }
        }

        $sheet->getRowDimension($dataRow)->setRowHeight(10);
        return $dataRow + 1;
    }

    private function writeReturned(Worksheet $sheet, int $startRow): int
    {
        $this->writeSectionTitle($sheet, $startRow, 'ASSETS RETURNED TO INVENTORY');

        $dataRow = $this->writeTableHeader($sheet, $startRow + 1, [
            'B' => 'No.',
            'C' => 'Tag Number',
            'E' => 'Asset Name',
            'F' => 'Returned From',
            'H' => 'Qty Returned',
        ]);

        $returns = AssetAllocation::with(['asset', 'employee', 'project'])
            ->whereMonth('actual_return_date', $this->month)
            ->whereYear('actual_return_date', $this->year)
            ->where('is_transfer_out', false)
            ->orderBy('actual_return_date', 'desc')
            ->get();

        if ($returns->isEmpty()) {
            $dataRow = $this->writePlaceholder($sheet, $dataRow, '(No assets returned this month)');
        } else {
            $no = 1;
            foreach ($returns as $alloc) {
                $parts = [];
                if ($alloc->project)  $parts[] = $alloc->project->project_name;
                if ($alloc->employee) $parts[] = 'Pak ' . $alloc->employee->last_name;
                $assignedText = $parts ? implode(' & ', $parts) : 'Unassigned';

                $this->writeDataRow($sheet, $dataRow, [
                    'B' => $no++,
                    'C' => $alloc->asset->tag_number ?? 'N/A',
                    'E' => $alloc->asset->name ?? 'Deleted',
                    'F' => $assignedText,
                    'H' => "x{$alloc->quantity}",
                ]);
                $dataRow++;
            }
        }

        $sheet->getRowDimension($dataRow)->setRowHeight(10);
        return $dataRow + 1;
    }

    private function writeAllAssets(Worksheet $sheet, int $startRow): int
    {
        $this->writeSectionTitle($sheet, $startRow, 'ALL REGISTERED ASSETS');

        $dataRow = $this->writeTableHeader($sheet, $startRow + 1, [
            'B' => 'No.',
            'C' => 'Tag Number',
            'E' => 'Asset Name',
            'F' => 'Category',
            'H' => 'Stock (Avail/Total)',
        ]);

        $assets = Asset::orderByRaw("CASE status
                WHEN 'InStock' THEN 1
                WHEN 'Allocated' THEN 2
                WHEN 'Maintenance' THEN 3
                WHEN 'Retired' THEN 4
                ELSE 5 END")
            ->orderBy('created_at', 'desc')
            ->get();

        if ($assets->isEmpty()) {
            $dataRow = $this->writePlaceholder($sheet, $dataRow, '(No assets registered)');
        } else {
            $no = 1;
            foreach ($assets as $asset) {
                $avail = $asset->availableStock();
                $this->writeDataRow($sheet, $dataRow, [
                    'B' => $no++,
                    'C' => $asset->tag_number ?? 'N/A',
                    'E' => $asset->name,
                    'F' => $asset->category,
                    'H' => "{$avail}/{$asset->stock}",
                ]);
                $dataRow++;
            }
        }

        return $dataRow;
    }
}

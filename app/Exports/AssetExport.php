<?php

namespace App\Exports;

use App\Models\Asset;
use App\Exports\Concerns\HasBauerExcelFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Concerns\Exportable;

class AssetExport implements WithEvents
{
    use Exportable, HasBauerExcelFormatting;

    protected $search;
    protected $category;
    protected $status;

    public function __construct($search, $category, $status)
    {
        $this->search = $search;
        $this->category = $category;
        $this->status = $status;
    }

    public function registerEvents(): array
    {
        return [
            BeforeWriting::class => function (BeforeWriting $event) {
                $spreadsheet = $event->writer->getDelegate();
                // Remove default sheet
                $spreadsheet->removeSheetByIndex(0);
                
                $sheet = new Worksheet($spreadsheet, 'Master Report');
                $spreadsheet->addSheet($sheet, 0);
                $spreadsheet->setActiveSheetIndex(0);

                $this->buildSheet($sheet);
            },
        ];
    }

    protected function buildSheet(Worksheet $sheet)
    {
        $this->setupPageAndColumnWidths($sheet);
        $this->applyCompanyHeader($sheet);

        $query = Asset::query();

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'ilike', '%' . $this->search . '%')
                  ->orWhere('tag_number', 'ilike', '%' . $this->search . '%')
                  ->orWhere('serial_number', 'ilike', '%' . $this->search . '%');
            });
        }
        if (!empty($this->category)) {
            $query->where('category', $this->category);
        }
        if (!empty($this->status)) {
            $query->where('status', $this->status);
        }

        $assets = $query->orderByRaw("
            CASE status
                WHEN 'InStock' THEN 1
                WHEN 'Allocated' THEN 2
                WHEN 'Maintenance' THEN 3
                WHEN 'Retired' THEN 4
                ELSE 5
            END
        ")->orderBy('created_at', 'desc')->get();

        $currentDate = now()->timezone('Asia/Jakarta')->format('d F Y');
        $totalAssets = $assets->count();

        // ──────────────────────────────────────────
        // TITLE (Rows 7–8, merged B7:H8)
        // ──────────────────────────────────────────
        $sheet->mergeCells('B7:H8');
        $sheet->setCellValue('B7', 'IT ASSET MASTER REPORT');
        $sheet->getStyle('B7')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('B7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

        // ──────────────────────────────────────────
        // INFO (Rows 9–10)
        // ──────────────────────────────────────────
        $sheet->getRowDimension(9)->setRowHeight(15.5);

        $sheet->mergeCells('B9:C9');
        $sheet->setCellValue('B9', 'DATE');
        $sheet->getStyle('B9')->getFont()->setBold(true)->setSize(11);
        $sheet->setCellValue('D9', ':');
        $sheet->getStyle('D9')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('D9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('E9', $currentDate);

        $sheet->mergeCells('B10:C10');
        $sheet->setCellValue('B10', 'TOTAL');
        $sheet->getStyle('B10')->getFont()->setBold(true)->setSize(11);
        $sheet->setCellValue('D10', ':');
        $sheet->getStyle('D10')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('D10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('E10', "{$totalAssets} assets");

        // ──────────────────────────────────────────
        // SPACING ROW
        // ──────────────────────────────────────────
        $sheet->getRowDimension(11)->setRowHeight(23);

        $currentRow = 12;

        // ==========================================
        // SECTION: ALL REGISTERED ASSETS
        // ==========================================
        $sheet->mergeCells("B{$currentRow}:H{$currentRow}");
        $sheet->setCellValue("B{$currentRow}", 'ALL REGISTERED ASSETS');
        $sheet->getStyle("B{$currentRow}")->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle("B{$currentRow}:H{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("B{$currentRow}:H{$currentRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF');
        $sheet->getStyle("B{$currentRow}:H{$currentRow}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getRowDimension($currentRow)->setRowHeight(15);

        $this->applyTableHeader($sheet, $currentRow + 1, $currentRow + 2, [
            'B' => 'No.',
            'C' => 'Tag Number',
            'E' => 'Asset Name',
            'F' => 'Category',
            'H' => 'Status',
        ]);

        $dataRow = $currentRow + 3;
        $no = 1;

        foreach ($assets as $asset) {
            $sheet->setCellValue("B{$dataRow}", $no);
            $sheet->getStyle("B{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue("C{$dataRow}", $asset->tag_number ?? 'N/A');
            $sheet->getStyle("C{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->mergeCells("C{$dataRow}:D{$dataRow}");
            $sheet->setCellValue("E{$dataRow}", $asset->name);
            $sheet->setCellValue("F{$dataRow}", $asset->category);
            $sheet->getStyle("F{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->mergeCells("F{$dataRow}:G{$dataRow}");
            $sheet->setCellValue("H{$dataRow}", $asset->status);
            $sheet->getStyle("H{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $dataRow++;
            $no++;
        }

        if ($assets->isEmpty()) {
            $sheet->setCellValue("E{$dataRow}", '(No assets registered)');
            $sheet->getStyle("E{$dataRow}")->getFont()->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF888888'));
            $dataRow++;
        }

        // ──────────────────────────────────────────
        // SIGNATURE BLOCK
        // ──────────────────────────────────────────
        $sigStartRow = $dataRow + 3;
        $this->applySignatureBlock($sheet, $sigStartRow);
    }
}

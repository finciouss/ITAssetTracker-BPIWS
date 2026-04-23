<?php

namespace App\Exports;

use App\Models\Project;
use App\Models\AssetAllocation;
use App\Exports\Concerns\HasBauerExcelFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Concerns\Exportable;

class ProjectExport implements WithEvents
{
    use Exportable, HasBauerExcelFormatting;

    protected $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
        $this->project->load(['allocations.asset', 'allocations.employee']);
    }

    public function registerEvents(): array
    {
        return [
            BeforeWriting::class => function (BeforeWriting $event) {
                $spreadsheet = $event->writer->getDelegate();
                // Remove default sheet
                $spreadsheet->removeSheetByIndex(0);
                
                $sheet = new Worksheet($spreadsheet, 'IT Asset Report');
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

        // ──────────────────────────────────────────
        // TITLE (Rows 7–8, merged B7:H8)
        // ──────────────────────────────────────────
        $sheet->mergeCells('B7:H8');
        $sheet->setCellValue('B7', 'IT ASSET REPORT');
        $sheet->getStyle('B7')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('B7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

        // ──────────────────────────────────────────
        // PROJECT INFO (Rows 9–11)
        // ──────────────────────────────────────────
        $sheet->getRowDimension(9)->setRowHeight(15.5);

        $sheet->mergeCells('B9:C9');
        $sheet->setCellValue('B9', 'PROJECT');
        $sheet->getStyle('B9')->getFont()->setBold(true)->setSize(11);
        $sheet->setCellValue('D9', ':');
        $sheet->getStyle('D9')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('D9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('E9', $this->project->project_name);

        $sheet->mergeCells('B10:C10');
        $sheet->setCellValue('B10', 'DATE');
        $sheet->getStyle('B10')->getFont()->setBold(true)->setSize(11);
        $sheet->setCellValue('D10', ':');
        $sheet->getStyle('D10')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('D10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('E10', now()->timezone('Asia/Jakarta')->format('d F Y'));

        $sheet->mergeCells('B11:C11');
        $sheet->setCellValue('B11', 'STATUS');
        $sheet->getStyle('B11')->getFont()->setBold(true)->setSize(11);
        $sheet->setCellValue('D11', ':');
        $sheet->getStyle('D11')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('D11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('E11', $this->project->status ?? 'Ongoing');

        // ──────────────────────────────────────────
        // SPACING ROW
        // ──────────────────────────────────────────
        $sheet->getRowDimension(13)->setRowHeight(23);

        // ──────────────────────────────────────────
        // ACTIVE SECTION
        // ──────────────────────────────────────────
        $activeAllocations = $this->project->allocations->filter(fn($a) => is_null($a->actual_return_date));

        // Section title (Row 14)
        $sheet->mergeCells('B14:H14');
        $sheet->setCellValue('B14', 'ACTIVELY ALLOCATED ASSETS ');
        $sheet->getStyle('B14')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('B14:H14')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B14:H14')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF');
        $sheet->getStyle('B14:H14')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getRowDimension(14)->setRowHeight(15);

        // Table header rows 15-16 (two-row header with gray bg)
        $this->applyTableHeader($sheet, 15, 16, [
            'B' => 'No.',
            'C' => 'Tag Number',
            'E' => 'Asset Name',
            'F' => 'Category',
            'H' => 'Employee',
        ]);

        // Data rows starting at 17
        $dataRow = 17;
        $no = 1;
        foreach ($activeAllocations as $alloc) {
            $sheet->setCellValue("B{$dataRow}", $no);
            $sheet->getStyle("B{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue("C{$dataRow}", $alloc->asset->tag_number ?? 'N/A');
            $sheet->getStyle("C{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->mergeCells("C{$dataRow}:D{$dataRow}");
            $sheet->setCellValue("E{$dataRow}", $alloc->asset->name ?? 'Deleted');
            $sheet->setCellValue("F{$dataRow}", $alloc->asset->category ?? '');
            $sheet->getStyle("F{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->mergeCells("F{$dataRow}:G{$dataRow}");
            $sheet->setCellValue("H{$dataRow}", $alloc->employee ? $alloc->employee->full_name : '-');
            $sheet->getStyle("H{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $dataRow++;
            $no++;
        }

        if ($activeAllocations->isEmpty()) {
            $sheet->setCellValue("E{$dataRow}", '(No active allocations)');
            $sheet->getStyle("E{$dataRow}")->getFont()->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF888888'));
            $dataRow++;
        }

        // ──────────────────────────────────────────
        // TRANSFERRED SECTION (2 rows gap)
        // ──────────────────────────────────────────
        $dataRow += 1;
        $transferTitleRow = $dataRow;

        $transferredAllocations = $this->project->allocations->filter(fn($a) => !is_null($a->actual_return_date) && $a->is_transfer_out);
        // Enrich with "Transferred To" lookups
        $transferredAllocations = $transferredAllocations->map(function ($alloc) {
            $nextAlloc = AssetAllocation::with(['employee', 'project'])
                ->where('asset_id', $alloc->asset_id)
                ->where('is_transfer_in', true)
                ->where('id', '>', $alloc->id)
                ->orderBy('id', 'asc')
                ->first();

            if ($nextAlloc) {
                $parts = [];
                if ($nextAlloc->employee) $parts[] = $nextAlloc->employee->full_name ?? $nextAlloc->employee->first_name;
                if ($nextAlloc->project) $parts[] = $nextAlloc->project->project_name;
                $alloc->transferred_to_name = empty($parts) ? 'Unknown' : implode(' - ', $parts);
            } else {
                $alloc->transferred_to_name = 'N/A';
            }
            return $alloc;
        });

        // Section title
        $sheet->mergeCells("B{$transferTitleRow}:H{$transferTitleRow}");
        $sheet->setCellValue("B{$transferTitleRow}", 'TRANSFERRED ASSETS HISTORY');
        $sheet->getStyle("B{$transferTitleRow}")->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle("B{$transferTitleRow}:H{$transferTitleRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B{$transferTitleRow}:H{$transferTitleRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF');
        $sheet->getStyle("B{$transferTitleRow}:H{$transferTitleRow}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getRowDimension($transferTitleRow)->setRowHeight(15);

        // Table header
        $tHeaderRow1 = $transferTitleRow + 1;
        $tHeaderRow2 = $transferTitleRow + 2;
        $this->applyTableHeader($sheet, $tHeaderRow1, $tHeaderRow2, [
            'B' => 'No.',
            'C' => 'Tag Number',
            'E' => 'Asset Name',
            'F' => 'Previous Emp.',
            'H' => 'Transferred To',
        ]);

        // Data rows
        $tDataRow = $tHeaderRow2 + 1;
        $no = 1;
        foreach ($transferredAllocations as $alloc) {
            $sheet->setCellValue("B{$tDataRow}", $no);
            $sheet->getStyle("B{$tDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue("C{$tDataRow}", $alloc->asset->tag_number ?? 'N/A');
            $sheet->getStyle("C{$tDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->mergeCells("C{$tDataRow}:D{$tDataRow}");
            $sheet->setCellValue("E{$tDataRow}", $alloc->asset->name ?? 'Deleted');
            $sheet->setCellValue("F{$tDataRow}", $alloc->employee ? $alloc->employee->full_name : '-');
            $sheet->getStyle("F{$tDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->mergeCells("F{$tDataRow}:G{$tDataRow}");
            $sheet->setCellValue("H{$tDataRow}", $alloc->transferred_to_name ?? 'N/A');
            $sheet->getStyle("H{$tDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $tDataRow++;
            $no++;
        }

        if ($transferredAllocations->isEmpty()) {
            $sheet->setCellValue("E{$tDataRow}", '(No transferred assets)');
            $sheet->getStyle("E{$tDataRow}")->getFont()->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF888888'));
            $tDataRow++;
        }

        // ──────────────────────────────────────────
        // SIGNATURE BLOCK
        // ──────────────────────────────────────────
        $sigStartRow = $tDataRow + 3;
        $this->applySignatureBlock($sheet, $sigStartRow);
    }
}

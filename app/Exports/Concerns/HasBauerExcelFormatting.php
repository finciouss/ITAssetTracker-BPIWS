<?php

namespace App\Exports\Concerns;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

trait HasBauerExcelFormatting
{
    /**
     * Applies standard company header (Rows 1-6) including logo and address.
     */
    protected function applyCompanyHeader(Worksheet $sheet): void
    {
        // Logo
        $logoPath = public_path('img/bauer-logo.jpeg');
        if (file_exists($logoPath)) {
            $drawing = new Drawing();
            $drawing->setName('Company Logo');
            $drawing->setPath($logoPath);
            $drawing->setCoordinates('B1');
            $drawing->setWidth(79);
            $drawing->setHeight(78);
            $drawing->setWorksheet($sheet);
        }

        $sheet->getRowDimension(1)->setRowHeight(15.5);

        $sheet->setCellValue('E1', 'PT. BAUER Pratama Indonesia');
        $sheet->getStyle('E1')->getFont()->setBold(true)->setSize(12);

        $sheet->setCellValue('E2', 'International Foundation Specialist');
        $sheet->getStyle('E2')->getFont()->setBold(true)->setSize(8);
        $sheet->getStyle('E2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('E3', 'Alamanda Tower 19th Floor Jalan TB Simatupang Kav.23-24 Cilandak Barat Jakarta Selatan 12430 - Indonesia');
        $sheet->getStyle('E3')->getFont()->setSize(8);

        $sheet->setCellValue('E4', 'Telp               : +62-21 2966 1988 (Hunting) Fax. : +62 21 2966 0188 ');
        $sheet->getStyle('E4')->getFont()->setSize(8);

        $sheet->setCellValue('E5', 'Workshop     : Kp. Cipicung Rt. 18 / Rw. 04 Desa Mekarsari Kec. Cileungsi Kab. Bogor');
        $sheet->getStyle('E5')->getFont()->setSize(8);

        $sheet->setCellValue('E6', 'Telp               : +62-21-2923 2795');
        $sheet->getStyle('E6')->getFont()->setSize(8);
    }

    /**
     * Set up page configuration, default font, and column widths
     */
        protected function setupPageAndColumnWidths(Worksheet $sheet, string $type = 'default'): void
    {
        $sheet->getColumnDimension('A')->setWidth(2);
        $sheet->getColumnDimension('B')->setWidth(4.75);
        
        if ($type === 'monthly') {
            $sheet->getColumnDimension('C')->setWidth(14.75);
            $sheet->getColumnDimension('D')->setWidth(1);
            $sheet->getColumnDimension('E')->setWidth(67.25);
            $sheet->getColumnDimension('F')->setWidth(21.91);
            $sheet->getColumnDimension('G')->setWidth(10.08);
            $sheet->getColumnDimension('G')->setVisible(false);
            $sheet->getColumnDimension('H')->setWidth(22);
        } else {
            $sheet->getColumnDimension('C')->setWidth(14.5);
            $sheet->getColumnDimension('D')->setWidth(1);
            $sheet->getColumnDimension('E')->setWidth(67.27);
            $sheet->getColumnDimension('F')->setWidth(22);
            $sheet->getColumnDimension('G')->setWidth(6.27);
            $sheet->getColumnDimension('H')->setWidth(22);
        }

        // Default font
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial')->setSize(11);

        // Page setup
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_LETTER);
        $sheet->getPageMargins()->setTop(0.75);
        $sheet->getPageMargins()->setBottom(0.75);
        $sheet->getPageMargins()->setLeft(0.7);
        $sheet->getPageMargins()->setRight(0.7);
    }

    /**
     * Apply the dual-row table header style (gray bg #EDEDED, bold, centered, with top medium + bottom double borders)
     */
        protected function applyTableHeader(Worksheet $sheet, int $row1, int $row2, array $columns, string $type = 'default'): void
    {
        // Fill both rows with gray
        $sheet->getStyle("B{$row1}:H{$row1}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EDEDED');
        $sheet->getStyle("B{$row1}:H{$row1}")->getFont()->setBold(true);
        $sheet->getStyle("B{$row1}:H{$row1}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("B{$row2}:H{$row2}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EDEDED');
        $sheet->getStyle("B{$row2}:H{$row2}")->getFont()->setBold(true);
        $sheet->getStyle("B{$row2}:H{$row2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Top border on row1
        $sheet->getStyle("B{$row1}:H{$row1}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_MEDIUM);

        // Bottom double border on row2
        $sheet->getStyle("B{$row2}:H{$row2}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE);

                // Set the header text on row1
        foreach ($columns as $col => $label) {
            $sheet->setCellValue("{$col}{$row1}", $label);
            if ($type === 'monthly') {
                $sheet->mergeCells("{$col}{$row1}:{$col}{$row2}");
                $sheet->getStyle("{$col}{$row1}:{$col}{$row2}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            } else {
                if ($col === 'C') {
                    $sheet->mergeCells("C{$row1}:D{$row1}");
                    $sheet->mergeCells("C{$row2}:D{$row2}");
                }
                if ($col === 'F') {
                    $sheet->mergeCells("F{$row1}:G{$row1}");
                    $sheet->mergeCells("F{$row2}:G{$row2}");
                }
            }
        }
        
        if ($type === 'monthly') {
            $sheet->mergeCells("D{$row1}:D{$row2}");
            $sheet->mergeCells("G{$row1}:G{$row2}");
        }

        $sheet->getRowDimension($row1)->setRowHeight(15);
        $sheet->getRowDimension($row2)->setRowHeight(15);
    }

    /**
     * Apply the signature block at the bottom
     */
    protected function applySignatureBlock(Worksheet $sheet, int $startRow): void
    {
        $sheet->mergeCells("F{$startRow}:H{$startRow}");
        $sheet->setCellValue("F{$startRow}", 'PUBLISHED BY ');
        $sheet->getStyle("F{$startRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sigRow2 = $startRow + 1;
        $sheet->mergeCells("F{$sigRow2}:H{$sigRow2}");
        $sheet->setCellValue("F{$sigRow2}", 'WORKSHOP IT DEPARTMENT');
        $sheet->getStyle("F{$sigRow2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sigRow3 = $startRow + 5;
        $sheet->mergeCells("F{$sigRow3}:H{$sigRow3}");
        $sheet->setCellValue("F{$sigRow3}", 'PT. Bauer Pratama Indonesia');
        $sheet->getStyle("F{$sigRow3}")->getFont()->setSize(12);
        $sheet->getStyle("F{$sigRow3}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }
}

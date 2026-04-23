<?php

namespace App\Helpers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelTemplate
{
    protected Spreadsheet $spreadsheet;
    protected Worksheet $sheet;

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();

        // Default font
        $this->spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(11);
    }

    /**
     * Write the standard Bauer company header block (rows 1–8) with logo, company info, and report title.
     */
    public function writeCompanyHeader(string $reportTitle = 'IT ASSET REPORT'): int
    {
        $sheet = $this->sheet;

        // Column widths (matching sample)
        $sheet->getColumnDimension('A')->setWidth(2);
        $sheet->getColumnDimension('B')->setWidth(4.73);
        $sheet->getColumnDimension('C')->setWidth(11.54);
        $sheet->getColumnDimension('D')->setWidth(1);
        $sheet->getColumnDimension('E')->setWidth(67.27);
        $sheet->getColumnDimension('F')->setWidth(7.73);
        $sheet->getColumnDimension('G')->setWidth(6.27);
        $sheet->getColumnDimension('H')->setWidth(22);

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

        // Company info
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

        // Title
        $sheet->mergeCells('B7:H8');
        $sheet->setCellValue('B7', $reportTitle);
        $sheet->getStyle('B7')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('B7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

        // Page setup
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_LETTER);
        $sheet->getPageMargins()->setTop(0.75)->setBottom(0.75)->setLeft(0.7)->setRight(0.7);

        return 9; // next available row
    }

    /**
     * Write metadata rows (PROJECT: xxx, DATE: xxx, STATUS: xxx).
     */
    public function writeMetadataBlock(int $startRow, array $fields): int
    {
        $sheet = $this->sheet;
        $row = $startRow;

        foreach ($fields as $label => $value) {
            $sheet->mergeCells("B{$row}:C{$row}");
            $sheet->setCellValue("B{$row}", $label);
            $sheet->getStyle("B{$row}")->getFont()->setBold(true)->setSize(11);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            $sheet->setCellValue("D{$row}", ':');
            $sheet->getStyle("D{$row}")->getFont()->setBold(true)->setSize(11);
            $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue("E{$row}", $value);
            $sheet->getRowDimension($row)->setRowHeight(15.5);

            $row++;
        }

        return $row;
    }

    /**
     * Write a section title bar spanning B–H (e.g. "ACTIVELY ALLOCATED ASSETS").
     * Returns the next available row after the title.
     */
    public function writeSectionTitle(int $row, string $title): int
    {
        $sheet = $this->sheet;

        // Spacing row above
        $spacingRow = $row;
        $sheet->getRowDimension($spacingRow)->setRowHeight(23);

        $titleRow = $spacingRow + 1;
        $sheet->mergeCells("B{$titleRow}:H{$titleRow}");
        $sheet->setCellValue("B{$titleRow}", $title);
        $sheet->getStyle("B{$titleRow}")->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle("B{$titleRow}:H{$titleRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B{$titleRow}:H{$titleRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF');
        $sheet->getStyle("B{$titleRow}:H{$titleRow}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);
        $sheet->getRowDimension($titleRow)->setRowHeight(15);

        return $titleRow + 1;
    }

    /**
     * Write a two-row table header with gray (#EDEDED) background, medium top border, double bottom border.
     * $columns = ['B' => 'No.', 'C' => 'Tag Number', ...] - placed on the first header row  
     * Returns the first data row.
     */
    public function writeTableHeader(int $startRow, array $columns): int
    {
        $sheet = $this->sheet;
        $row1 = $startRow;
        $row2 = $startRow + 1;

        // Style both rows
        foreach ([$row1, $row2] as $r) {
            $sheet->getStyle("B{$r}:H{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EDEDED');
            $sheet->getStyle("B{$r}:H{$r}")->getFont()->setBold(true);
            $sheet->getStyle("B{$r}:H{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getRowDimension($r)->setRowHeight(15);
        }

        // Top border on row1
        $sheet->getStyle("B{$row1}:H{$row1}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_MEDIUM);

        // Double bottom border on row2
        $sheet->getStyle("B{$row2}:H{$row2}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE);

        // Set header labels
        foreach ($columns as $col => $label) {
            $sheet->setCellValue("{$col}{$row1}", $label);
            if ($col === 'C') {
                $sheet->mergeCells("C{$row1}:D{$row1}");
                $sheet->mergeCells("C{$row2}:D{$row2}");
            }
            if ($col === 'F') {
                $sheet->mergeCells("F{$row1}:G{$row1}");
                $sheet->mergeCells("F{$row2}:G{$row2}");
            }
        }

        return $row2 + 1; // first data row
    }

    /**
     * Write the signature block at the given starting row.
     */
    public function writeSignatureBlock(int $startRow): int
    {
        $sheet = $this->sheet;

        $sheet->mergeCells("F{$startRow}:H{$startRow}");
        $sheet->setCellValue("F{$startRow}", 'PUBLISHED BY ');
        $sheet->getStyle("F{$startRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row2 = $startRow + 1;
        $sheet->mergeCells("F{$row2}:H{$row2}");
        $sheet->setCellValue("F{$row2}", 'WORKSHOP IT DEPARTMENT');
        $sheet->getStyle("F{$row2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row3 = $startRow + 5;
        $sheet->mergeCells("F{$row3}:H{$row3}");
        $sheet->setCellValue("F{$row3}", 'PT. Bauer Pratama Indonesia');
        $sheet->getStyle("F{$row3}")->getFont()->setSize(12);
        $sheet->getStyle("F{$row3}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return $row3 + 2;
    }

    /**
     * Write a single data row with centered alignment on specified columns.
     */
    public function writeDataRow(int $row, array $data, array $centeredCols = ['B', 'C', 'F', 'H']): void
    {
        $sheet = $this->sheet;
        foreach ($data as $col => $value) {
            $sheet->setCellValue("{$col}{$row}", $value);
            if (in_array($col, $centeredCols)) {
                $sheet->getStyle("{$col}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
            if ($col === 'C') {
                $sheet->mergeCells("C{$row}:D{$row}");
            } elseif ($col === 'F') {
                $sheet->mergeCells("F{$row}:G{$row}");
            }
        }
    }

    /**
     * Write an italic placeholder for empty table sections.
     */
    public function writeEmptyPlaceholder(int $row, string $message): int
    {
        $this->sheet->setCellValue("E{$row}", $message);
        $this->sheet->getStyle("E{$row}")->getFont()->setItalic(true)->setColor(new Color('FF888888'));
        return $row + 1;
    }

    /**
     * Download the spreadsheet as an .xlsx response.
     */
    public function download(string $filename): StreamedResponse
    {
        $writer = new Xlsx($this->spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    public function getSheet(): Worksheet
    {
        return $this->sheet;
    }

    public function getSpreadsheet(): Spreadsheet
    {
        return $this->spreadsheet;
    }
}

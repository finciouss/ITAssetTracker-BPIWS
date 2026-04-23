<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = IOFactory::load(__DIR__.'/storage/app/public/monthly_test.xlsx');
$sheet = $spreadsheet->getActiveSheet();

echo "Column Widths:\n";
foreach (['C','D','E','F','G'] as $col) {
    echo "$col: " . $sheet->getColumnDimension($col)->getWidth() . "\n";
}

echo "C17 Alignment: " . $sheet->getStyle('C17')->getAlignment()->getHorizontal() . "\n";
echo "F17 Alignment: " . $sheet->getStyle('F17')->getAlignment()->getHorizontal() . "\n";
echo "C17 Value: " . $sheet->getCell('C17')->getValue() . "\n";
echo "F17 Value: " . $sheet->getCell('F17')->getValue() . "\n";

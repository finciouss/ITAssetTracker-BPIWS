<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = IOFactory::load(__DIR__.'/public/img/MONTHLY_ASSETS_REPORT_sample.xlsx');
$sheet = $spreadsheet->getActiveSheet();

echo "Titles found in B column:\n";
for ($row = 1; $row <= 150; $row++) {
    $val = $sheet->getCell("B{$row}")->getValue();
    if (is_string($val) && strtoupper($val) === $val && strlen($val) > 5) {
        echo "Row $row: $val\n";
    }
}

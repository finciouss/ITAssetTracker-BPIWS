<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = IOFactory::load(__DIR__.'/public/img/MONTHLY_ASSETS_REPORT_sample.xlsx');
$sheet = $spreadsheet->getActiveSheet();

echo "B12 bottom border: " . $sheet->getStyle('B12')->getBorders()->getBottom()->getBorderStyle() . "\n";
echo "C12 bottom border: " . $sheet->getStyle('C12')->getBorders()->getBottom()->getBorderStyle() . "\n";
echo "H12 bottom border: " . $sheet->getStyle('H12')->getBorders()->getBottom()->getBorderStyle() . "\n";

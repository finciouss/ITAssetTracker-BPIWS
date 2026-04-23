<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = IOFactory::load(__DIR__.'/public/img/MONTHLY_ASSETS_REPORT_sample.xlsx');
$sheet = $spreadsheet->getActiveSheet();
echo "C15 Alignment: " . $sheet->getStyle('C15')->getAlignment()->getHorizontal() . "\n";
echo "F15 Alignment: " . $sheet->getStyle('F15')->getAlignment()->getHorizontal() . "\n";
echo "C13 Alignment: " . $sheet->getStyle('C13')->getAlignment()->getHorizontal() . "\n";
echo "F13 Alignment: " . $sheet->getStyle('F13')->getAlignment()->getHorizontal() . "\n";

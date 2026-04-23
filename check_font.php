<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = IOFactory::load(__DIR__.'/public/img/MONTHLY_ASSETS_REPORT_sample.xlsx');
$sheet = $spreadsheet->getActiveSheet();
echo "Font Name: " . $sheet->getStyle('C15')->getFont()->getName() . "\n";
echo "Font Size: " . $sheet->getStyle('C15')->getFont()->getSize() . "\n";

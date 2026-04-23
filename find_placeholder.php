<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = IOFactory::load(__DIR__.'/public/img/MONTHLY_ASSETS_REPORT_sample.xlsx');
$sheet = $spreadsheet->getActiveSheet();

for ($row = 1; $row <= 120; $row++) {
    foreach (range('A', 'R') as $col) {
        $val = $sheet->getCell("{$col}{$row}")->getValue();
        if (is_string($val) && strpos($val, 'No new hardware') !== false) {
            echo "Placeholder found at {$col}{$row}\n";
        }
    }
}

<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = IOFactory::load(__DIR__.'/public/img/MONTHLY_ASSETS_REPORT_sample.xlsx');
$sheet = $spreadsheet->getActiveSheet();

echo "Rows 11-16:\n";
for ($row = 11; $row <= 16; $row++) {
    $rowStr = "Row $row: ";
    foreach (['B','C','D','E','F','G','H'] as $col) {
        $val = $sheet->getCell("{$col}{$row}")->getValue();
        if ($val) $rowStr .= "$col='$val' ";
    }
    echo $rowStr . "\n";
}

<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = IOFactory::load(__DIR__.'/public/img/IT_ASSET_MASTER_REPORT_sample.xlsx');
$sheet = $spreadsheet->getActiveSheet();

echo "Column Widths:\n";
foreach (['A','B','C','D','E','F','G','H'] as $col) {
    echo $col . ": " . $sheet->getColumnDimension($col)->getWidth() . " (Visible: " . ($sheet->getColumnDimension($col)->getVisible() ? 'Yes' : 'No') . ")\n";
}

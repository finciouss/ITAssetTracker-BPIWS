<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = IOFactory::load(__DIR__.'/public/img/MONTHLY_ASSETS_REPORT_sample.xlsx');
$sheet = $spreadsheet->getActiveSheet();

echo "Column Widths and Hidden:\n";
foreach (range('A', 'I') as $col) {
    $dim = $sheet->getColumnDimension($col);
    echo "$col: Width=" . $dim->getWidth() . ", Visible=" . ($dim->getVisible() ? 'true' : 'false') . "\n";
}

echo "Merges in Rows 13-16:\n";
$merges = $sheet->getMergeCells();
foreach ($merges as $merge) {
    if (preg_match('/(13|14|15|16)/', $merge)) {
        echo "$merge\n";
    }
}

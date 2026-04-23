<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = IOFactory::load(__DIR__.'/public/img/MONTHLY_ASSETS_REPORT_sample.xlsx');
$sheet = $spreadsheet->getActiveSheet();

echo "=== BORDER INSPECTION (Rows 12-20) ===\n";
for ($row = 12; $row <= 20; $row++) {
    echo "\n-- Row $row --\n";
    foreach (['B','C','D','E','F','G','H'] as $col) {
        $style = $sheet->getStyle("{$col}{$row}");
        $borders = $style->getBorders();
        $t = $borders->getTop()->getBorderStyle();
        $b = $borders->getBottom()->getBorderStyle();
        $l = $borders->getLeft()->getBorderStyle();
        $r = $borders->getRight()->getBorderStyle();
        if ($t !== 'none' || $b !== 'none' || $l !== 'none' || $r !== 'none') {
            echo "  {$col}{$row}: top=$t, bottom=$b, left=$l, right=$r\n";
        }
    }
}

echo "\n=== SECTION TITLE BORDERS (Row 12, 39, 56, 62) ===\n";
foreach ([12, 39, 56, 62] as $row) {
    echo "\nRow $row:\n";
    foreach (['B','C','D','E','F','G','H'] as $col) {
        $borders = $sheet->getStyle("{$col}{$row}")->getBorders();
        $t = $borders->getTop()->getBorderStyle();
        $b = $borders->getBottom()->getBorderStyle();
        if ($t !== 'none' || $b !== 'none') {
            echo "  {$col}: top=$t, bottom=$b\n";
        }
    }
}

echo "\n=== DATA ROW BORDERS (Row 15, 16) ===\n";
foreach ([15, 16] as $row) {
    echo "\nRow $row:\n";
    foreach (['B','C','D','E','F','G','H'] as $col) {
        $borders = $sheet->getStyle("{$col}{$row}")->getBorders();
        $t = $borders->getTop()->getBorderStyle();
        $b = $borders->getBottom()->getBorderStyle();
        $l = $borders->getLeft()->getBorderStyle();
        $r = $borders->getRight()->getBorderStyle();
        if ($t !== 'none' || $b !== 'none' || $l !== 'none' || $r !== 'none') {
            echo "  {$col}: top=$t, bottom=$b, left=$l, right=$r\n";
        }
    }
}

echo "\n=== LAST DATA ROW BORDER ===\n";
// Row before return section
foreach ([38, 39, 40, 41] as $row) {
    echo "\nRow $row:\n";
    foreach (['B','C','D','E','F','G','H'] as $col) {
        $borders = $sheet->getStyle("{$col}{$row}")->getBorders();
        $b = $borders->getBottom()->getBorderStyle();
        $t = $borders->getTop()->getBorderStyle();
        if ($t !== 'none' || $b !== 'none') {
            echo "  {$col}: top=$t, bottom=$b\n";
        }
    }
}

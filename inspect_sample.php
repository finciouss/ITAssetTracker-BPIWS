<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = IOFactory::load(__DIR__.'/public/img/MONTHLY_ASSETS_REPORT_sample.xlsx');
$sheet = $spreadsheet->getActiveSheet();

echo "=== EXACT CELL LAYOUT: Rows 12-22 ===\n";
for ($row = 12; $row <= 22; $row++) {
    echo "\n--- Row $row ---\n";
    foreach (['A','B','C','D','E','F','G','H','I'] as $col) {
        $cell = $sheet->getCell("{$col}{$row}");
        $val  = $cell->getFormattedValue();
        $dim  = $sheet->getColumnDimension($col);
        if ($val !== '' || true) {
            $bgColor  = $sheet->getStyle("{$col}{$row}")->getFill()->getStartColor()->getRGB();
            $boldness = $sheet->getStyle("{$col}{$row}")->getFont()->getBold() ? 'BOLD' : 'normal';
            $align    = $sheet->getStyle("{$col}{$row}")->getAlignment()->getHorizontal();
            $wrap     = $sheet->getStyle("{$col}{$row}")->getAlignment()->getWrapText() ? 'WRAP' : '';
            $w        = $dim->getWidth();
            $visible  = $dim->getVisible() ? 'Y' : 'N';
            echo "  {$col}(w:{$w},vis:{$visible}): val='{$val}' | {$boldness} | align={$align} | bg={$bgColor} {$wrap}\n";
        }
    }
}

echo "\n=== BORDER CHECK: Row 13-14 (header row) ===\n";
foreach (['B','C','D','E','F','G','H'] as $col) {
    $top = $sheet->getStyle("{$col}13")->getBorders()->getTop()->getBorderStyle();
    $bot = $sheet->getStyle("{$col}14")->getBorders()->getBottom()->getBorderStyle();
    echo "  {$col}: top={$top} bottom={$bot}\n";
}

echo "\n=== ROW HEIGHTS: Rows 12-20 ===\n";
for ($r = 12; $r <= 20; $r++) {
    echo "  Row $r: " . $sheet->getRowDimension($r)->getRowHeight() . "\n";
}

echo "\n=== ALL MERGES in rows 12-20 ===\n";
foreach ($sheet->getMergeCells() as $merge) {
    if (preg_match('/\d+/', $merge, $m) && $m[0] >= 12 && $m[0] <= 20) {
        echo "  $merge\n";
    }
}

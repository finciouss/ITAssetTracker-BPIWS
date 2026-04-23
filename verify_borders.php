<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$gen = IOFactory::load(__DIR__.'/storage/app/public/monthly_test.xlsx')->getActiveSheet();

echo "=== Generated Borders Row 13 (header row1) ===\n";
foreach (['B','C','D','E','F','G','H'] as $col) {
    $b = $gen->getStyle("{$col}13")->getBorders();
    echo "  {$col}: top={$b->getTop()->getBorderStyle()}, bottom={$b->getBottom()->getBorderStyle()}, left={$b->getLeft()->getBorderStyle()}, right={$b->getRight()->getBorderStyle()}\n";
}

echo "\n=== Generated Borders Row 15 (first data row) ===\n";
foreach (['B','C','D','E','F','G','H'] as $col) {
    $b = $gen->getStyle("{$col}15")->getBorders();
    $t = $b->getTop()->getBorderStyle();
    $bt = $b->getBottom()->getBorderStyle();
    $l = $b->getLeft()->getBorderStyle();
    $r = $b->getRight()->getBorderStyle();
    if ($t !== 'none' || $bt !== 'none' || $l !== 'none' || $r !== 'none') {
        echo "  {$col}: top=$t, bottom=$bt, left=$l, right=$r\n";
    }
}

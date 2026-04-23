<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$gen = IOFactory::load(__DIR__.'/storage/app/public/monthly_test.xlsx')->getActiveSheet();

echo "=== Row 12 (section title) ===\n";
foreach (['B','C','D','E','F','G','H'] as $col) {
    $val = $gen->getCell("{$col}12")->getValue();
    if ($val) echo "  {$col}12: '$val'\n";
}

echo "\n=== Row 13-14 (header) ===\n";
foreach (['B','C','D','E','F','G','H'] as $col) {
    $val  = $gen->getCell("{$col}13")->getValue();
    $fill = $gen->getStyle("{$col}13")->getFill()->getStartColor()->getRGB();
    echo "  {$col}13: val='$val' fill=$fill\n";
}
echo "Merges containing 13 or 14:\n";
foreach ($gen->getMergeCells() as $m) {
    if (preg_match('/(13|14)/', $m)) echo "  $m\n";
}

echo "\n=== Row 15 (first data row) ===\n";
foreach (['B','C','D','E','F','G','H'] as $col) {
    $val = $gen->getCell("{$col}15")->getValue();
    echo "  {$col}15: '$val'\n";
}

echo "\nColumn C width: " . $gen->getColumnDimension('C')->getWidth() . "\n";
echo "Column D width: " . $gen->getColumnDimension('D')->getWidth() . "\n";
echo "Column G visible: " . ($gen->getColumnDimension('G')->getVisible() ? 'yes' : 'no') . "\n";

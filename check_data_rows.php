<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$gen = IOFactory::load(__DIR__.'/storage/app/public/monthly_test.xlsx')->getActiveSheet();

echo "Rows 15-25 content and borders:\n";
for ($row = 15; $row <= 25; $row++) {
    $bVal = $gen->getCell("B{$row}")->getValue();
    $cVal = $gen->getCell("C{$row}")->getValue();
    $eVal = $gen->getCell("E{$row}")->getValue();
    
    $bBorder = $gen->getStyle("B{$row}")->getBorders();
    $hasAny = $bBorder->getTop()->getBorderStyle() !== 'none' || $bBorder->getBottom()->getBorderStyle() !== 'none';
    
    echo "  Row $row: B='$bVal' C='$cVal' E='$eVal' | borders=" . ($hasAny ? 'YES' : 'none') . "\n";
}

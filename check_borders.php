<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$genPath = __DIR__.'/storage/app/public/monthly_test.xlsx';
$genSpread = IOFactory::load($genPath);
$gen = $genSpread->getActiveSheet();

echo "D13 Borders: " . $gen->getStyle('D13')->getBorders()->getBottom()->getBorderStyle() . "\n";
echo "D14 Borders: " . $gen->getStyle('D14')->getBorders()->getBottom()->getBorderStyle() . "\n";

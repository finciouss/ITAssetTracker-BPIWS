<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$genSpread = IOFactory::load(__DIR__.'/storage/app/public/monthly_test.xlsx');
$gen = $genSpread->getActiveSheet();

$gm = $gen->getMergeCells();
echo "Merges containing D: ";
foreach ($gm as $m) if (strpos($m, 'D') !== false) echo "$m, ";
echo "\n";

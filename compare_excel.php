<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$samplePath = __DIR__.'/public/img/MONTHLY_ASSETS_REPORT_sample.xlsx';
$genPath = __DIR__.'/storage/app/public/monthly_test.xlsx';

$sampleSpread = IOFactory::load($samplePath);
$genSpread = IOFactory::load($genPath);

$sample = $sampleSpread->getActiveSheet();
$gen = $genSpread->getActiveSheet();

echo "Comparing Monthly Asset Report Generation\n";
echo "========================================\n\n";

// 1. Column Widths
echo "[Column Widths]\n";
foreach (range('A', 'J') as $col) {
    $sw = $sample->getColumnDimension($col)->getWidth();
    $gw = $gen->getColumnDimension($col)->getWidth();
    $sv = $sample->getColumnDimension($col)->getVisible();
    $gv = $gen->getColumnDimension($col)->getVisible();
    if ($sw != $gw || $sv != $gv) {
        echo "Col $col: Sample(w:$sw, v:$sv) vs Gen(w:$gw, v:$gv)\n";
    }
}
echo "\n";

// 2. Merges
echo "[Merged Cells]\n";
$sm = $sample->getMergeCells();
$gm = $gen->getMergeCells();
sort($sm);
sort($gm);
$onlySample = array_diff($sm, $gm);
$onlyGen = array_diff($gm, $sm);
if ($onlySample) echo "Only in Sample: " . implode(', ', $onlySample) . "\n";
if ($onlyGen) echo "Only in Generated: " . implode(', ', $onlyGen) . "\n";
echo "\n";

// 3. Values and Formatting in the first 20 rows
echo "[Content and Formatting (Rows 1-30)]\n";
for ($row = 1; $row <= 30; $row++) {
    foreach (range('A', 'I') as $col) {
        $sc = $sample->getCell("{$col}{$row}");
        $gc = $gen->getCell("{$col}{$row}");
        
        $sv = $sc->getValue();
        $gv = $gc->getValue();
        
        if ($sv != $gv) {
            // only report differences if sample has data
            // or if it's a structural difference
            if ($sv || $gv) {
                echo "Cell {$col}{$row} Value Diff: Sample='$sv' vs Gen='$gv'\n";
            }
        }
    }
}

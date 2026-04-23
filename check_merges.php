<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$files = [
    'PROJECT_ASSETS_sample.xlsx',
    'MONTHLY_ASSETS_REPORT_sample.xlsx',
    'IT_ASSET_MASTER_REPORT_sample.xlsx'
];

foreach ($files as $file) {
    echo "=== $file ===\n";
    try {
        $spreadsheet = IOFactory::load(__DIR__.'/public/img/' . $file);
        $sheet = $spreadsheet->getActiveSheet();
        $merges = array_keys($sheet->getMergeCells());
        
        $headerMerges = array_filter($merges, fn($m) => strpos($m, '13') !== false || strpos($m, '14') !== false);
        $dataMerges = array_filter($merges, fn($m) => strpos($m, '15') !== false);
        
        echo "Header merges: " . implode(', ', $headerMerges) . "\n";
        echo "Data merges (Row 15): " . implode(', ', $dataMerges) . "\n";
        echo "\n";
    } catch (\Exception $e) {}
}

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
        foreach ($spreadsheet->getAllSheets() as $sheet) {
            echo "Sheet: " . $sheet->getTitle() . "\n";
            echo "B7: " . $sheet->getCell('B7')->getCalculatedValue() . "\n";
            echo "B13: " . $sheet->getCell('B13')->getCalculatedValue() . "\n";
            echo "C13: " . $sheet->getCell('C13')->getCalculatedValue() . "\n";
            echo "E13: " . $sheet->getCell('E13')->getCalculatedValue() . "\n";
            echo "F13: " . $sheet->getCell('F13')->getCalculatedValue() . "\n";
            echo "G13: " . $sheet->getCell('G13')->getCalculatedValue() . "\n";
            echo "H13: " . $sheet->getCell('H13')->getCalculatedValue() . "\n";
            echo "\n";
        }
    } catch (\Exception $e) {}
}

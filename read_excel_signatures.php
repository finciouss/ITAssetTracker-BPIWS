<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

function dump_signatures($filename) {
    echo "=== $filename ===\n";
    $spreadsheet = IOFactory::load(__DIR__.'/public/img/'.$filename);
    $sheet = $spreadsheet->getActiveSheet();
    foreach ($sheet->getRowIterator() as $row) {
        $idx = $row->getRowIndex();
        foreach (['E', 'F', 'G'] as $col) {
            $cell = $sheet->getCell($col . $idx)->getCalculatedValue();
            if (is_string($cell) && strpos($cell, 'PUBLISHED BY') !== false) {
                echo "Row $idx: $cell\n";
            }
            if (is_string($cell) && strpos($cell, 'WORKSHOP IT DEPARTMENT') !== false) {
                echo "Row $idx: $cell\n";
            }
        }
    }
}
dump_signatures('MONTHLY_ASSETS_REPORT_sample.xlsx');
dump_signatures('IT_ASSET_MASTER_REPORT_sample.xlsx');

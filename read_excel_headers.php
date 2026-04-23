<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

function dump_file($filename) {
    echo "=== $filename ===\n";
    $spreadsheet = IOFactory::load(__DIR__.'/public/img/'.$filename);
    $sheet = $spreadsheet->getActiveSheet();
    $dataRowNext = false;
    foreach ($sheet->getRowIterator() as $row) {
        $idx = $row->getRowIndex();
        $cellB = $sheet->getCell('B' . $idx)->getCalculatedValue();
        if (is_string($cellB) && (strpos($cellB, 'REPORT') !== false || strpos($cellB, 'ASSET') !== false)) {
            echo "Row $idx (Title/Section): $cellB\n";
            $dataRowNext = true;
            continue;
        }
        if ($dataRowNext) {
             $cellIterator = $row->getCellIterator();
             $headers = [];
             foreach ($cellIterator as $cell) {
                 $val = $cell->getCalculatedValue();
                 if ($val) $headers[] = $val;
             }
             if (!empty($headers)) {
                 echo "Headers: " . implode(' | ', $headers) . "\n";
                 $dataRowNext = false;
             }
        }
    }
}

dump_file('MONTHLY_ASSETS_REPORT_sample.xlsx');
dump_file('IT_ASSET_MASTER_REPORT_sample.xlsx');

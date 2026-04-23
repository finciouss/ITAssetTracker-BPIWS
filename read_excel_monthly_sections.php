<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = IOFactory::load(__DIR__.'/public/img/MONTHLY_ASSETS_REPORT_sample.xlsx');
foreach ($spreadsheet->getAllSheets() as $sheet) {
    foreach ($sheet->getRowIterator() as $row) {
        $cell = $sheet->getCell('B' . $row->getRowIndex());
        if ($cell) {
            $val = $cell->getCalculatedValue();
            if (is_string($val) && strpos($val, 'ASSETS') !== false) {
                echo "Row " . $row->getRowIndex() . ": " . $val . "\n";
            }
        }
    }
}

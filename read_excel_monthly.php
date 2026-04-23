<?php
require __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = IOFactory::load(__DIR__.'/public/img/MONTHLY_ASSETS_REPORT_sample.xlsx');
foreach ($spreadsheet->getAllSheets() as $sheet) {
    echo "Sheet: " . $sheet->getTitle() . "\n";
    foreach ($sheet->getRowIterator() as $row) {
        $idx = $row->getRowIndex();
        if ($idx > 6 && $idx < 50) { // skip header
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $rowData = [];
            foreach ($cellIterator as $cell) {
                $val = $cell->getCalculatedValue();
                if ($val) $rowData[] = $cell->getCoordinate() . ": " . $val;
            }
            if (!empty($rowData)) {
                echo implode(' | ', $rowData) . "\n";
            }
        }
    }
}

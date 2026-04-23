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
            echo "Merges: " . implode(', ', array_keys($sheet->getMergeCells())) . "\n";
            foreach ($sheet->getRowIterator() as $row) {
                if ($row->getRowIndex() > 25) break;
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $val = $cell->getCalculatedValue();
                    if ($val !== null && $val !== '') {
                        $coord = $cell->getCoordinate();
                        $col = $cell->getColumn();
                        $width = $sheet->getColumnDimension($col)->getWidth();
                        // Get basic style info
                        $style = $sheet->getStyle($coord);
                        $font = $style->getFont();
                        $bold = $font->getBold() ? 'B' : '-';
                        $size = $font->getSize();
                        $align = $style->getAlignment()->getHorizontal();
                        $fill = $style->getFill()->getStartColor()->getRGB();
                        $rowData[] = "$coord($bold,s$size,$align,f$fill,w$width): $val";
                    }
                }
                if (!empty($rowData)) {
                    echo implode(' | ', $rowData) . "\n";
                }
            }
            echo "\n";
        }
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

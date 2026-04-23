<?php

$files = [
    __DIR__.'/app/Exports/ProjectExport.php',
    __DIR__.'/app/Exports/MonthlyReportExport.php',
    __DIR__.'/app/Exports/AssetExport.php',
];

foreach ($files as $file) {
    $content = file_get_contents($file);
    
    // Replace for C
    $content = preg_replace(
        '/(setCellValue\("C\{\$([a-zA-Z0-9_]+)\}",.*?\);\s*\$sheet->getStyle\("C\{\$\2\}"\)->getAlignment\(\)->setHorizontal\(Alignment::HORIZONTAL_CENTER\);)/s',
        '$1' . "\n" . '            $sheet->mergeCells("C{$' . '$2' . '}:D{$' . '$2' . '}");',
        $content
    );
    
    // Replace for F
    $content = preg_replace(
        '/(setCellValue\("F\{\$([a-zA-Z0-9_]+)\}",.*?\);\s*\$sheet->getStyle\("F\{\$\2\}"\)->getAlignment\(\)->setHorizontal\(Alignment::HORIZONTAL_CENTER\);)/s',
        '$1' . "\n" . '            $sheet->mergeCells("F{$' . '$2' . '}:G{$' . '$2' . '}");',
        $content
    );
    
    // Fallback if no Alignment is set (like in AssetExport for E and F might be different?)
    // Actually in AssetExport they are all centered:
    // $sheet->setCellValue("C{$dataRow}", ...);
    // $sheet->getStyle("C{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
    // Let's check AssetExport
    
    file_put_contents($file, $content);
}

// Now ExcelTemplate.php
$excelTpl = __DIR__.'/app/Helpers/ExcelTemplate.php';
$content = file_get_contents($excelTpl);
// In writeTableHeader
$content = preg_replace(
    '/foreach \(\$columns as \$col => \$label\) \{\s*\$sheet->setCellValue\("\{\$col\}\{\$row1\}", \$label\);\s*\}/s',
    'foreach ($columns as $col => $label) {
            $sheet->setCellValue("{$col}{$row1}", $label);
            if ($col === \'C\') {
                $sheet->mergeCells("C{$row1}:D{$row1}");
                $sheet->mergeCells("C{$row2}:D{$row2}");
            }
            if ($col === \'F\') {
                $sheet->mergeCells("F{$row1}:G{$row1}");
                $sheet->mergeCells("F{$row2}:G{$row2}");
            }
        }',
    $content
);
// In writeDataRow
$content = preg_replace(
    '/(if \(in_array\(\$col, \$centeredCols\)\) \{\s*\$sheet->getStyle\("\{\$col\}\{\$row\}"\)->getAlignment\(\)->setHorizontal\(Alignment::HORIZONTAL_CENTER\);\s*\})/s',
    '$1
            if ($col === \'C\') {
                $sheet->mergeCells("C{$row}:D{$row}");
            } elseif ($col === \'F\') {
                $sheet->mergeCells("F{$row}:G{$row}");
            }',
    $content
);
file_put_contents($excelTpl, $content);
echo "Done.";

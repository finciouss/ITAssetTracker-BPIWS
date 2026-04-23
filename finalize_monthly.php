<?php

// 1. MonthlyReportExport.php - Remove title merges
$file = __DIR__.'/app/Exports/MonthlyReportExport.php';
$content = file_get_contents($file);

$titleReplacement = <<<PHP
    protected function renderSectionTitle(Worksheet \$sheet, int \$row, string \$title)
    {
        \$sheet->setCellValue("B{\$row}", \$title);
        \$sheet->getStyle("B{\$row}")->getFont()->setBold(true)->setSize(11);
        \$sheet->getRowDimension(\$row)->setRowHeight(15);
    }
PHP;

$content = preg_replace(
    '/protected function renderSectionTitle\(Worksheet \$sheet, int \$row, string \$title\)\s*\{.*?\}/s',
    $titleReplacement,
    $content
);

file_put_contents($file, $content);

// 2. HasBauerExcelFormatting.php - Add vertical merges for D and G
$file2 = __DIR__.'/app/Exports/Concerns/HasBauerExcelFormatting.php';
$content2 = file_get_contents($file2);

$headerReplacement = <<<PHP
        // Set the header text on row1
        foreach (\$columns as \$col => \$label) {
            \$sheet->setCellValue("{\$col}{\$row1}", \$label);
            if (\$type === 'monthly') {
                \$sheet->mergeCells("{\$col}{\$row1}:{\$col}{\$row2}");
                \$sheet->getStyle("{\$col}{\$row1}:{\$col}{\$row2}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            } else {
                if (\$col === 'C') {
                    \$sheet->mergeCells("C{\$row1}:D{\$row1}");
                    \$sheet->mergeCells("C{\$row2}:D{\$row2}");
                }
                if (\$col === 'F') {
                    \$sheet->mergeCells("F{\$row1}:G{\$row1}");
                    \$sheet->mergeCells("F{\$row2}:G{\$row2}");
                }
            }
        }
        
        if (\$type === 'monthly') {
            \$sheet->mergeCells("D{\$row1}:D{\$row2}");
            \$sheet->mergeCells("G{\$row1}:G{\$row2}");
        }
PHP;

$content2 = preg_replace(
    '/\/\/ Set the header text on row1\s*foreach \(\$columns as \$col => \$label\) \{.*?\s*\}/s',
    $headerReplacement,
    $content2
);

file_put_contents($file2, $content2);

echo "Done\n";

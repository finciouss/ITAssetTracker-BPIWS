<?php

// 1. HasBauerExcelFormatting.php
$file = __DIR__.'/app/Exports/Concerns/HasBauerExcelFormatting.php';
$content = file_get_contents($file);

$setupReplacement = <<<PHP
    protected function setupPageAndColumnWidths(Worksheet \$sheet, string \$type = 'default'): void
    {
        \$sheet->getColumnDimension('A')->setWidth(2);
        \$sheet->getColumnDimension('B')->setWidth(4.75);
        
        if (\$type === 'monthly') {
            \$sheet->getColumnDimension('C')->setWidth(14.75);
            \$sheet->getColumnDimension('D')->setWidth(1);
            \$sheet->getColumnDimension('E')->setWidth(67.25);
            \$sheet->getColumnDimension('F')->setWidth(21.91);
            \$sheet->getColumnDimension('G')->setWidth(10.08);
            \$sheet->getColumnDimension('G')->setVisible(false);
            \$sheet->getColumnDimension('H')->setWidth(22);
        } else {
            \$sheet->getColumnDimension('C')->setWidth(14.5);
            \$sheet->getColumnDimension('D')->setWidth(1);
            \$sheet->getColumnDimension('E')->setWidth(67.27);
            \$sheet->getColumnDimension('F')->setWidth(22);
            \$sheet->getColumnDimension('G')->setWidth(6.27);
            \$sheet->getColumnDimension('H')->setWidth(22);
        }

        // Default font
        \$sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial')->setSize(11);
PHP;

$content = preg_replace(
    '/protected function setupPageAndColumnWidths\(Worksheet \$sheet\): void\s*\{.*?\/\/ Default font\s*\$sheet->getParent\(\)->getDefaultStyle\(\)->getFont\(\)->setName\(\'Arial\'\)->setSize\(11\);/s',
    $setupReplacement,
    $content
);

$headerReplacement = <<<PHP
    protected function applyTableHeader(Worksheet \$sheet, int \$row1, int \$row2, array \$columns, string \$type = 'default'): void
    {
        // Fill both rows with gray
        \$sheet->getStyle("B{\$row1}:H{\$row1}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EDEDED');
        \$sheet->getStyle("B{\$row1}:H{\$row1}")->getFont()->setBold(true);
        \$sheet->getStyle("B{\$row1}:H{\$row1}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        \$sheet->getStyle("B{\$row2}:H{\$row2}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EDEDED');
        \$sheet->getStyle("B{\$row2}:H{\$row2}")->getFont()->setBold(true);
        \$sheet->getStyle("B{\$row2}:H{\$row2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Top border on row1
        \$sheet->getStyle("B{\$row1}:H{\$row1}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_MEDIUM);

        // Bottom double border on row2
        \$sheet->getStyle("B{\$row2}:H{\$row2}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE);

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
PHP;

$content = preg_replace(
    '/protected function applyTableHeader\(Worksheet \$sheet, int \$row1, int \$row2, array \$columns\): void\s*\{.*?\/\/ Set the header text on row1.*?\}\s*\}/s',
    $headerReplacement,
    $content
);

file_put_contents($file, $content);


// 2. MonthlyReportExport.php
$file = __DIR__.'/app/Exports/MonthlyReportExport.php';
$content = file_get_contents($file);

// Fix buildSheet calls
$content = str_replace(
    '$this->setupPageAndColumnWidths($sheet);',
    '$this->setupPageAndColumnWidths($sheet, \'monthly\');',
    $content
);

// Fix applyTableHeader calls
$content = preg_replace(
    '/(\$this->applyTableHeader\(\$sheet, \$startRow \+ 1, \$startRow \+ 2, \[[^\]]+\])/s',
    '$1, \'monthly\'',
    $content
);

// Remove C:D and F:G merges in data rows
$content = preg_replace('/\s*\$sheet->mergeCells\("C\{\$dataRow\}:D\{\$dataRow\}"\);/', '', $content);
$content = preg_replace('/\s*\$sheet->mergeCells\("F\{\$dataRow\}:G\{\$dataRow\}"\);/', '', $content);

// Remove 'Project ' prefix
$content = str_replace(
    "if (\$alloc->project) \$assignedTo[] = 'Project ' . \$alloc->project->project_name;",
    "if (\$alloc->project) \$assignedTo[] = \$alloc->project->project_name;",
    $content
);

// Ensure the section titles have no formatting on G (it's hidden anyway)
// Actually we can leave the formatting B:H as it is since G is hidden.

file_put_contents($file, $content);

echo "Done\n";

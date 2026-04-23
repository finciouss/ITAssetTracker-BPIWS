<?php
$dir = __DIR__ . '/resources/views';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

$search_str = 'bg-white rounded-xl shadow-sm border border-slate-100';
$replace_str = 'bg-white rounded-[20px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-200/60 ring-1 ring-slate-900/5 transition-all duration-300 hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.08)] hover:-translate-y-0.5';

$button_search = 'bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md shadow-sm text-sm';
$button_replace = 'bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-medium py-2 px-4 rounded-lg shadow-md shadow-indigo-500/30 hover:shadow-lg hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all text-sm border-none';

$table_row_search = '                    <tr>
                        <td class="px-6 py-4">';
$input_search = 'w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm';
$input_replace = 'w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 transition-all';

$count = 0;
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        if (strpos($file->getPathname(), 'login.blade.php') !== false) {
            continue;
        }

        $content = file_get_contents($file->getPathname());
        $original = $content;
        
        $content = str_replace($button_search, $button_replace, $content);
        $content = str_replace($input_search, $input_replace, $content);
        
        if ($original !== $content) {
            file_put_contents($file->getPathname(), $content);
            $count++;
            echo "Updated: " . $file->getFilename() . "\n";
        }
    }
}
echo "Total files updated: $count\n";

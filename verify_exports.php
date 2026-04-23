<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $monthly = new \App\Exports\MonthlyReportExport(4, 2026);
    echo "Monthly instantiated.\n";
    $master = new \App\Exports\AssetExport(null, null, null);
    echo "Master instantiated.\n";
    
    // Attempt download logic if needed, but instantiation ensures no major syntax errors
    echo "OK";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}

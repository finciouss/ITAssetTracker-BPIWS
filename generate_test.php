<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Exports\MonthlyReportExport;

$export = new MonthlyReportExport('04', '2026');
$export->store('monthly_test.xlsx', 'public');
echo "Stored to storage/app/public/monthly_test.xlsx\n";

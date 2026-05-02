<?php

use App\Console\Commands\PruneLogs;
use Illuminate\Support\Facades\Schedule;

// Prune logs older than 365 days — runs on the 1st of every month at 02:00 server time
Schedule::command(PruneLogs::class, ['--days=365'])
    ->monthlyOn(1, '02:00')
    ->withoutOverlapping()
    ->runInBackground();

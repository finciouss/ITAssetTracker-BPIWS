<?php

namespace App\Console\Commands;

use App\Models\AssetLog;
use App\Models\SystemLog;
use Illuminate\Console\Command;

class PruneLogs extends Command
{
    /**
     * Usage:
     *   php artisan logs:prune            → deletes records older than 365 days
     *   php artisan logs:prune --days=90  → deletes records older than 90 days
     */
    protected $signature   = 'logs:prune {--days=365 : Delete logs older than this many days}';
    protected $description = 'Delete old asset_logs and system_logs to keep the database lean';

    public function handle(): int
    {
        $days   = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $this->info("Pruning logs older than {$days} days (before {$cutoff->toDateString()})...");

        $assetDeleted  = AssetLog::where('action_date', '<', $cutoff)->delete();
        $systemDeleted = SystemLog::where('timestamp',  '<', $cutoff)->delete();

        $this->info("✓ Deleted {$assetDeleted} asset log(s).");
        $this->info("✓ Deleted {$systemDeleted} system log(s).");
        $this->info('Done.');

        return Command::SUCCESS;
    }
}

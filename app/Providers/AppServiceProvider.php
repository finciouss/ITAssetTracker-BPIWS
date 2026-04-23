<?php

namespace App\Providers;

use App\Database\NeonPostgresConnector;
use App\Models\Asset;
use App\Observers\AssetObserver;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Eloquent observer — replaces C# AssetAuditInterceptor
        Asset::observe(AssetObserver::class);

        // ── Neon PostgreSQL SNI fix ──────────────────────────────────────────
        // Register a custom connector that injects the Neon endpoint ID into
        // the pgsql DSN. This runs before the connection is established.
        $this->app['db.connector.pgsql'] = function () {
            return new NeonPostgresConnector();
        };

        // Also register it on the connection factory
        $this->app->resolving('db', function ($db) {
            $db->extend('pgsql', function ($config, $name) {
                $connector = new NeonPostgresConnector();
                $connection = $connector->connect($config);
                return new \Illuminate\Database\PostgresConnection(
                    $connection,
                    $config['database'],
                    $config['prefix'],
                    $config
                );
            });
        });
    }
}

<?php

namespace App\Database;

use Illuminate\Database\Connectors\PostgresConnector;

/**
 * Custom PostgreSQL connector that injects the Neon endpoint ID
 * into the DSN for SNI support (required by Neon when libpq < 14).
 *
 * Neon error without this: "Endpoint ID is not specified"
 */
class NeonPostgresConnector extends PostgresConnector
{
    /**
     * Create a DSN string from a configuration.
     */
    protected function getDsn(array $config): string
    {
        $dsn = parent::getDsn($config);

        // Inject the endpoint ID option for Neon SNI support
        if (isset($config['host']) && str_contains($config['host'], 'neon.tech')) {
            $endpointId = preg_replace('/-pooler$/', '', explode('.', $config['host'])[0]);
            // Append options to the DSN
            $dsn .= ";options=endpoint={$endpointId}";
        }

        return $dsn;
    }
}

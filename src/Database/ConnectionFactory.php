<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use RuntimeException;

/**
 * Builds the single PDO connection from DATABASE_URL, the only place
 * that ever sees a database credential.
 */
class ConnectionFactory
{
    public static function fromEnvironment(): PDO
    {
        $databaseUrl = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL');
        if (!is_string($databaseUrl) || $databaseUrl === '') {
            throw new RuntimeException('DATABASE_URL is not set');
        }

        $parts = parse_url($databaseUrl);
        if ($parts === false || !array_key_exists('host', $parts)) {
            throw new RuntimeException('DATABASE_URL is malformed');
        }

        $host = $parts['host'];
        $port = $parts['port'] ?? 5432;
        $database = ltrim($parts['path'] ?? '', characters: '/');
        $user = $parts['user'] ?? 'postgres';
        $password = $parts['pass'] ?? '';

        $dsn = "pgsql:host={$host};port={$port};dbname={$database}";

        return new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Mcp\DatabaseSchema;

use Doctrine\DBAL\Connection;

class SchemaDriverFactory
{
    public function make(Connection $connection): SchemaDriverInterface
    {
        $platformClass = \get_class($connection->getDatabasePlatform());

        return match (true) {
            str_contains($platformClass, 'MySQL'),
            str_contains($platformClass, 'MariaDB') => new MySQLSchemaDriver($connection),
            str_contains($platformClass, 'PostgreSQL') => new PostgreSQLSchemaDriver($connection),
            str_contains($platformClass, 'SQLite') => new SQLiteSchemaDriver($connection),
            default => new NullSchemaDriver(),
        };
    }
}

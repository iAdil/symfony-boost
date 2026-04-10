<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Mcp\DatabaseSchema;

use Doctrine\DBAL\Connection;

class SQLiteSchemaDriver implements SchemaDriverInterface
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function getViews(): array
    {
        return $this->connection->executeQuery(
            "SELECT name, sql as definition FROM sqlite_master WHERE type = 'view'"
        )->fetchAllAssociative();
    }

    public function getStoredProcedures(): array
    {
        return [];
    }

    public function getFunctions(): array
    {
        return [];
    }

    public function getTriggers(?string $tableName = null): array
    {
        if ($tableName !== null) {
            return $this->connection->executeQuery(
                "SELECT name, sql as definition FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ?",
                [$tableName]
            )->fetchAllAssociative();
        }

        return $this->connection->executeQuery(
            "SELECT name, sql as definition FROM sqlite_master WHERE type = 'trigger'"
        )->fetchAllAssociative();
    }

    public function getCheckConstraints(?string $tableName = null): array
    {
        return [];
    }

    public function getSequences(): array
    {
        return [];
    }
}

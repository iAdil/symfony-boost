<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Mcp\DatabaseSchema;

use Doctrine\DBAL\Connection;

class MySQLSchemaDriver implements SchemaDriverInterface
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function getViews(): array
    {
        $dbName = $this->connection->getDatabase();

        return $this->connection->executeQuery(
            'SELECT TABLE_NAME as name, VIEW_DEFINITION as definition FROM information_schema.VIEWS WHERE TABLE_SCHEMA = ?',
            [$dbName]
        )->fetchAllAssociative();
    }

    public function getStoredProcedures(): array
    {
        $dbName = $this->connection->getDatabase();

        return $this->connection->executeQuery(
            'SELECT ROUTINE_NAME as name, ROUTINE_DEFINITION as definition, ROUTINE_TYPE as type FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = ? AND ROUTINE_TYPE = ?',
            [$dbName, 'PROCEDURE']
        )->fetchAllAssociative();
    }

    public function getFunctions(): array
    {
        $dbName = $this->connection->getDatabase();

        return $this->connection->executeQuery(
            'SELECT ROUTINE_NAME as name, ROUTINE_DEFINITION as definition, ROUTINE_TYPE as type FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = ? AND ROUTINE_TYPE = ?',
            [$dbName, 'FUNCTION']
        )->fetchAllAssociative();
    }

    public function getTriggers(?string $tableName = null): array
    {
        $dbName = $this->connection->getDatabase();

        if ($tableName !== null) {
            return $this->connection->executeQuery(
                'SELECT TRIGGER_NAME as name, EVENT_MANIPULATION as event, EVENT_OBJECT_TABLE as `table`, ACTION_STATEMENT as statement, ACTION_TIMING as timing FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA = ? AND EVENT_OBJECT_TABLE = ?',
                [$dbName, $tableName]
            )->fetchAllAssociative();
        }

        return $this->connection->executeQuery(
            'SELECT TRIGGER_NAME as name, EVENT_MANIPULATION as event, EVENT_OBJECT_TABLE as `table`, ACTION_STATEMENT as statement, ACTION_TIMING as timing FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA = ?',
            [$dbName]
        )->fetchAllAssociative();
    }

    public function getCheckConstraints(?string $tableName = null): array
    {
        $dbName = $this->connection->getDatabase();

        if ($tableName !== null) {
            return $this->connection->executeQuery(
                'SELECT CONSTRAINT_NAME as name, CHECK_CLAUSE as definition FROM information_schema.CHECK_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = ? AND TABLE_NAME = ?',
                [$dbName, $tableName]
            )->fetchAllAssociative();
        }

        return $this->connection->executeQuery(
            'SELECT CONSTRAINT_NAME as name, CHECK_CLAUSE as definition FROM information_schema.CHECK_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = ?',
            [$dbName]
        )->fetchAllAssociative();
    }

    public function getSequences(): array
    {
        return [];
    }
}

<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Mcp\DatabaseSchema;

use Doctrine\DBAL\Connection;

class PostgreSQLSchemaDriver implements SchemaDriverInterface
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function getViews(): array
    {
        return $this->connection->executeQuery(
            "SELECT viewname as name, definition FROM pg_views WHERE schemaname NOT IN ('pg_catalog', 'information_schema')"
        )->fetchAllAssociative();
    }

    public function getStoredProcedures(): array
    {
        return $this->connection->executeQuery(
            "SELECT p.proname as name, pg_get_functiondef(p.oid) as definition FROM pg_proc p JOIN pg_namespace n ON p.pronamespace = n.oid WHERE n.nspname NOT IN ('pg_catalog', 'information_schema') AND p.prokind = 'p'"
        )->fetchAllAssociative();
    }

    public function getFunctions(): array
    {
        return $this->connection->executeQuery(
            "SELECT p.proname as name, pg_get_functiondef(p.oid) as definition FROM pg_proc p JOIN pg_namespace n ON p.pronamespace = n.oid WHERE n.nspname NOT IN ('pg_catalog', 'information_schema') AND p.prokind = 'f'"
        )->fetchAllAssociative();
    }

    public function getTriggers(?string $tableName = null): array
    {
        if ($tableName !== null) {
            return $this->connection->executeQuery(
                "SELECT trigger_name as name, event_manipulation as event, event_object_table as \"table\", action_statement as statement, action_timing as timing FROM information_schema.triggers WHERE event_object_table = ?",
                [$tableName]
            )->fetchAllAssociative();
        }

        return $this->connection->executeQuery(
            "SELECT trigger_name as name, event_manipulation as event, event_object_table as \"table\", action_statement as statement, action_timing as timing FROM information_schema.triggers WHERE trigger_schema NOT IN ('pg_catalog', 'information_schema')"
        )->fetchAllAssociative();
    }

    public function getCheckConstraints(?string $tableName = null): array
    {
        if ($tableName !== null) {
            return $this->connection->executeQuery(
                "SELECT conname as name, pg_get_constraintdef(c.oid) as definition FROM pg_constraint c JOIN pg_class r ON c.conrelid = r.oid WHERE c.contype = 'c' AND r.relname = ?",
                [$tableName]
            )->fetchAllAssociative();
        }

        return $this->connection->executeQuery(
            "SELECT conname as name, pg_get_constraintdef(c.oid) as definition FROM pg_constraint c JOIN pg_namespace n ON c.connamespace = n.oid WHERE c.contype = 'c' AND n.nspname NOT IN ('pg_catalog', 'information_schema')"
        )->fetchAllAssociative();
    }

    public function getSequences(): array
    {
        return $this->connection->executeQuery(
            "SELECT sequence_name as name, data_type, start_value, minimum_value, maximum_value, increment FROM information_schema.sequences WHERE sequence_schema NOT IN ('pg_catalog', 'information_schema')"
        )->fetchAllAssociative();
    }
}

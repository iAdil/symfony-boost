<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Mcp\DatabaseSchema;

class NullSchemaDriver implements SchemaDriverInterface
{
    public function getViews(): array
    {
        return [];
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
        return [];
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

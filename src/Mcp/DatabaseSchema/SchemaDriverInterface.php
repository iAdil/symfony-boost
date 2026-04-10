<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Mcp\DatabaseSchema;

interface SchemaDriverInterface
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function getViews(): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getStoredProcedures(): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getFunctions(): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getTriggers(?string $tableName = null): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getCheckConstraints(?string $tableName = null): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getSequences(): array;
}

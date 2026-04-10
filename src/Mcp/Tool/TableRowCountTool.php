<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Mcp\Tool;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ConnectionRegistry;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(name: 'table-row-count')]
class TableRowCountTool
{
    public function __construct(
        private readonly ?ConnectionRegistry $connectionRegistry = null,
    ) {
    }

    public function __invoke(?string $database = null, ?string $filter = null): string
    {
        if ($this->connectionRegistry === null) {
            return 'Error: No database connection registry available.';
        }

        try {
            /** @var Connection $connection */
            $connection = $database
                ? $this->connectionRegistry->getConnection($database)
                : $this->connectionRegistry->getConnection($this->connectionRegistry->getDefaultConnectionName());

            $schemaManager = $connection->createSchemaManager();
            $tables = $schemaManager->listTableNames();
            $counts = [];

            foreach ($tables as $tableName) {
                if ($filter !== null && !str_contains(strtolower($tableName), strtolower($filter))) {
                    continue;
                }

                try {
                    $result = $connection->executeQuery("SELECT COUNT(*) as cnt FROM {$tableName}");
                    $count = $result->fetchOne();
                    $counts[$tableName] = (int) $count;
                } catch (\Throwable) {
                    $counts[$tableName] = 'error';
                }
            }

            arsort($counts);

            return json_encode($counts, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        } catch (\Throwable $e) {
            return 'Error: '.$e->getMessage();
        }
    }
}

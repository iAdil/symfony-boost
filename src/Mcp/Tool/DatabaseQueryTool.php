<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Mcp\Tool;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ConnectionRegistry;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(name: 'database-query')]
class DatabaseQueryTool
{
    public function __construct(
        private readonly ?ConnectionRegistry $connectionRegistry = null,
    ) {
    }

    public function __invoke(string $query, ?string $database = null, int $limit = 100): string
    {
        $query = trim($query);
        $token = strtok(ltrim($query), " \t\n\r");

        if (!$token) {
            return 'Error: Please pass a valid query';
        }

        $firstWord = strtoupper($token);

        $allowList = [
            'SELECT',
            'SHOW',
            'EXPLAIN',
            'DESCRIBE',
            'DESC',
            'WITH',
            'VALUES',
            'TABLE',
        ];

        $isReadOnly = \in_array($firstWord, $allowList, true);

        if ($firstWord === 'WITH') {
            if (!preg_match('/\)\s*SELECT\b/i', $query)) {
                $isReadOnly = false;
            }

            if (preg_match('/\)\s*(DELETE|UPDATE|INSERT|DROP|ALTER|TRUNCATE|REPLACE|RENAME|CREATE)\b/i', $query)) {
                $isReadOnly = false;
            }
        }

        if (!$isReadOnly) {
            return 'Error: Only read-only queries are allowed (SELECT, SHOW, EXPLAIN, DESCRIBE, DESC, WITH … SELECT).';
        }

        if ($this->connectionRegistry === null) {
            return 'Error: No database connection registry available.';
        }

        try {
            /** @var Connection $connection */
            $connection = $database
                ? $this->connectionRegistry->getConnection($database)
                : $this->connectionRegistry->getConnection($this->connectionRegistry->getDefaultConnectionName());

            // Auto-add LIMIT if not present and query is SELECT
            if (strtoupper(substr(trim($query), 0, 6)) === 'SELECT' && !preg_match('/\bLIMIT\b/i', $query)) {
                $query = rtrim($query, "; \t\n\r")." LIMIT {$limit}";
            }

            $result = $connection->executeQuery($query);
            $rows = $result->fetchAllAssociative();

            return json_encode($rows, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES);
        } catch (\Throwable $throwable) {
            return 'Query failed: '.$throwable->getMessage();
        }
    }
}

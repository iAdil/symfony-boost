<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Mcp\Tool;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\Persistence\ConnectionRegistry;
use Mcp\Capability\Attribute\McpTool;
use IAdil\SymfonyBoostBundle\Mcp\DatabaseSchema\SchemaDriverFactory;

#[McpTool(name: 'database-schema')]
class DatabaseSchemaTool
{
    public function __construct(
        private readonly SchemaDriverFactory $schemaDriverFactory,
        private readonly ?ConnectionRegistry $connectionRegistry = null,
    ) {
    }

    public function __invoke(
        bool $summary = false,
        ?string $database = null,
        ?string $filter = null,
        bool $include_views = false,
        bool $include_routines = false,
        bool $include_column_details = false,
    ): array|string {
        if ($this->connectionRegistry === null) {
            return 'Error: No database connection registry available.';
        }

        try {
            $connectionName = $database ?? $this->connectionRegistry->getDefaultConnectionName();
            /** @var Connection $connection */
            $connection = $this->connectionRegistry->getConnection($connectionName);
            $schemaManager = $connection->createSchemaManager();
            $driver = $this->schemaDriverFactory->make($connection);

            $result = [
                'engine' => $connection->getDatabasePlatform()::class,
                'tables' => $summary
                    ? $this->getAllTableColumnTypes($schemaManager, $filter ?? '')
                    : $this->getAllTablesStructure($schemaManager, $connection, $filter ?? '', $include_column_details),
            ];

            if ($summary) {
                return $result;
            }

            if ($include_views) {
                $result['views'] = $driver->getViews();
            }

            if ($include_routines) {
                $result['routines'] = [
                    'stored_procedures' => $driver->getStoredProcedures(),
                    'functions' => $driver->getFunctions(),
                    'sequences' => $driver->getSequences(),
                ];
            }

            return $result;
        } catch (\Throwable $throwable) {
            return 'Error: '.$throwable->getMessage();
        }
    }

    private function getAllTableColumnTypes(AbstractSchemaManager $schemaManager, string $filter): array
    {
        $tables = [];

        foreach ($schemaManager->listTableNames() as $tableName) {
            if ($filter !== '' && !str_contains(strtolower($tableName), strtolower($filter))) {
                continue;
            }

            $columns = [];

            foreach ($schemaManager->listTableColumns($tableName) as $column) {
                $columns[$column->getName()] = $column->getType()->getName();
            }

            $tables[$tableName] = $columns;
        }

        return $tables;
    }

    private function getAllTablesStructure(
        AbstractSchemaManager $schemaManager,
        Connection $connection,
        string $filter,
        bool $includeColumnDetails,
    ): array {
        $structures = [];
        $driver = $this->schemaDriverFactory->make($connection);

        foreach ($schemaManager->listTableNames() as $tableName) {
            if ($filter !== '' && !str_contains(strtolower($tableName), strtolower($filter))) {
                continue;
            }

            try {
                $structures[$tableName] = $this->getTableStructure(
                    $schemaManager,
                    $driver,
                    $tableName,
                    $includeColumnDetails,
                );
            } catch (\Throwable $e) {
                $structures[$tableName] = ['error' => 'Failed to get structure: '.$e->getMessage()];
            }
        }

        return $structures;
    }

    private function getTableStructure(
        AbstractSchemaManager $schemaManager,
        $driver,
        string $tableName,
        bool $includeColumnDetails,
    ): array {
        $columns = [];

        foreach ($schemaManager->listTableColumns($tableName) as $column) {
            $detail = ['type' => $column->getType()->getName()];

            if ($includeColumnDetails) {
                $detail['nullable'] = !$column->getNotnull();
                $detail['default'] = $column->getDefault();
                $detail['auto_increment'] = $column->getAutoincrement();

                if ($column->getComment() !== null && $column->getComment() !== '') {
                    $detail['comment'] = $column->getComment();
                }
            }

            $columns[$column->getName()] = $detail;
        }

        $indexes = [];

        foreach ($schemaManager->listTableIndexes($tableName) as $index) {
            $indexes[$index->getName()] = [
                'columns' => $index->getColumns(),
                'is_unique' => $index->isUnique(),
                'is_primary' => $index->isPrimary(),
            ];
        }

        $foreignKeys = [];

        foreach ($schemaManager->listTableForeignKeys($tableName) as $fk) {
            $foreignKeys[] = [
                'name' => $fk->getName(),
                'local_columns' => $fk->getLocalColumns(),
                'foreign_table' => $fk->getForeignTableName(),
                'foreign_columns' => $fk->getForeignColumns(),
            ];
        }

        return [
            'columns' => $columns,
            'indexes' => $indexes,
            'foreign_keys' => $foreignKeys,
            'triggers' => $driver->getTriggers($tableName),
            'check_constraints' => $driver->getCheckConstraints($tableName),
        ];
    }
}

<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Mcp\Tool;

use Doctrine\Persistence\ConnectionRegistry;
use Mcp\Capability\Attribute\McpTool;

#[McpTool(name: 'database-connections')]
class DatabaseConnectionsTool
{
    public function __construct(
        private readonly ?ConnectionRegistry $connectionRegistry = null,
    ) {
    }

    public function __invoke(): array
    {
        if ($this->connectionRegistry === null) {
            return [
                'default_connection' => null,
                'connections' => [],
            ];
        }

        return [
            'default_connection' => $this->connectionRegistry->getDefaultConnectionName(),
            'connections' => $this->connectionRegistry->getConnectionNames(),
        ];
    }
}

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

    public function __invoke(): string
    {
        if ($this->connectionRegistry === null) {
            return json_encode([
                'default_connection' => null,
                'connections' => [],
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        return json_encode([
            'default_connection' => $this->connectionRegistry->getDefaultConnectionName(),
            'connections' => $this->connectionRegistry->getConnectionNames(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}

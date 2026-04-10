<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Install;

use IAdil\SymfonyBoostBundle\Install\Agent\AbstractAgent;
use IAdil\SymfonyBoostBundle\Install\Enums\McpInstallationStrategy;
use IAdil\SymfonyBoostBundle\Install\Mcp\FileWriter;
use IAdil\SymfonyBoostBundle\Install\Mcp\TomlFileWriter;

class McpWriter
{
    public function __construct(
        private readonly string $projectDir,
    ) {
    }

    public function write(AbstractAgent $agent): void
    {
        if (!$agent->supportsMcp()) {
            return;
        }

        $strategy = $agent->mcpInstallationStrategy();

        if ($strategy === McpInstallationStrategy::None) {
            return;
        }

        $phpPath = $agent->getPhpPath();
        $consolePath = $agent->getConsolePath();

        if ($agent->useAbsolutePathForMcp()) {
            $consolePath = $this->projectDir.\DIRECTORY_SEPARATOR.'bin'.\DIRECTORY_SEPARATOR.'console';
        }

        $serverConfig = $agent->mcpServerConfig($phpPath, $consolePath);

        $configPath = $agent->mcpConfigPath();

        if ($configPath === '') {
            return;
        }

        if ($strategy === McpInstallationStrategy::File) {
            if (str_ends_with($configPath, '.toml')) {
                $writer = new TomlFileWriter();
                $writer->writeServer($configPath, 'symfony-boost', $serverConfig);
            } else {
                $writer = new FileWriter();
                $writer->writeServer($configPath, 'symfony-boost', $serverConfig);
            }
        }
    }
}

<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Install\Agent;

use IAdil\SymfonyBoostBundle\Install\Detection\DetectionStrategyFactory;
use IAdil\SymfonyBoostBundle\Install\Enums\McpInstallationStrategy;
use IAdil\SymfonyBoostBundle\Install\Enums\Platform;

abstract class AbstractAgent
{
    public function __construct(
        protected readonly DetectionStrategyFactory $strategyFactory = new DetectionStrategyFactory(),
    ) {
    }

    abstract public function name(): string;

    abstract public function displayName(): string;

    /**
     * @return array{paths?: string[], command?: string, files?: string[]}
     */
    abstract public function systemDetectionConfig(Platform $platform): array;

    /**
     * @return array{paths?: string[], files?: string[]}
     */
    abstract public function projectDetectionConfig(): array;

    public function supportsGuidelines(): bool
    {
        return false;
    }

    public function supportsMcp(): bool
    {
        return false;
    }

    public function supportsSkills(): bool
    {
        return false;
    }

    public function guidelinesPath(): string
    {
        return '';
    }

    public function skillsPath(): string
    {
        return '';
    }

    public function mcpConfigPath(): string
    {
        return '';
    }

    public function frontmatter(): bool
    {
        return false;
    }

    public function transformGuidelines(string $content): string
    {
        return $content;
    }

    public function useAbsolutePathForMcp(): bool
    {
        return false;
    }

    public function getPhpPath(bool $forceAbsolutePath = false): string
    {
        if ($this->useAbsolutePathForMcp() || $forceAbsolutePath) {
            return \PHP_BINARY;
        }

        return 'php';
    }

    public function getConsolePath(bool $forceAbsolutePath = false): string
    {
        if ($this->useAbsolutePathForMcp() || $forceAbsolutePath) {
            return 'bin/console';
        }

        return 'bin/console';
    }

    public function mcpInstallationStrategy(): McpInstallationStrategy
    {
        return McpInstallationStrategy::File;
    }

    public function detectOnSystem(Platform $platform): bool
    {
        $config = $this->systemDetectionConfig($platform);
        $strategy = $this->strategyFactory->makeFromConfig($config);

        return $strategy->detect($config, $platform);
    }

    public function detectInProject(string $basePath): bool
    {
        $config = array_merge($this->projectDetectionConfig(), ['basePath' => $basePath]);
        $strategy = $this->strategyFactory->makeFromConfig($config);

        return $strategy->detect($config);
    }

    /**
     * @return array<string, mixed>
     */
    public function mcpServerConfig(string $phpPath, string $consolePath): array
    {
        return [
            'command' => $phpPath,
            'args' => [$consolePath, 'boost:mcp'],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function httpMcpServerConfig(string $url): ?array
    {
        return null;
    }

    public function shellMcpCommand(): ?string
    {
        return null;
    }
}

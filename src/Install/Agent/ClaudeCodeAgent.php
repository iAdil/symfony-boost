<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Install\Agent;

use IAdil\SymfonyBoostBundle\Install\Enums\McpInstallationStrategy;
use IAdil\SymfonyBoostBundle\Install\Enums\Platform;

class ClaudeCodeAgent extends AbstractAgent
{
    public function name(): string
    {
        return 'claude_code';
    }

    public function displayName(): string
    {
        return 'Claude Code';
    }

    public function supportsGuidelines(): bool
    {
        return true;
    }

    public function supportsMcp(): bool
    {
        return true;
    }

    public function supportsSkills(): bool
    {
        return true;
    }

    public function systemDetectionConfig(Platform $platform): array
    {
        return match ($platform) {
            Platform::Darwin, Platform::Linux => ['command' => 'command -v claude'],
            Platform::Windows => ['command' => 'cmd /c where claude 2>nul'],
        };
    }

    public function projectDetectionConfig(): array
    {
        return [
            'paths' => ['.claude'],
            'files' => ['CLAUDE.md'],
        ];
    }

    public function mcpInstallationStrategy(): McpInstallationStrategy
    {
        return McpInstallationStrategy::File;
    }

    public function mcpConfigPath(): string
    {
        return '.mcp.json';
    }

    public function guidelinesPath(): string
    {
        return 'CLAUDE.md';
    }

    public function skillsPath(): string
    {
        return '.claude/skills';
    }
}

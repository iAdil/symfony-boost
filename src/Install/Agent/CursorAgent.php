<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Install\Agent;

use IAdil\SymfonyBoostBundle\Install\Enums\Platform;

class CursorAgent extends AbstractAgent
{
    public function name(): string
    {
        return 'cursor';
    }

    public function displayName(): string
    {
        return 'Cursor';
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
            Platform::Darwin => ['paths' => ['/Applications/Cursor.app']],
            Platform::Linux => ['paths' => ['/opt/cursor', '/usr/local/bin/cursor', '~/.local/bin/cursor']],
            Platform::Windows => ['paths' => ['%ProgramFiles%\\Cursor', '%LOCALAPPDATA%\\Programs\\Cursor']],
        };
    }

    public function projectDetectionConfig(): array
    {
        return ['paths' => ['.cursor']];
    }

    public function mcpConfigPath(): string
    {
        return '.cursor/mcp.json';
    }

    public function guidelinesPath(): string
    {
        return 'AGENTS.md';
    }

    public function skillsPath(): string
    {
        return '.cursor/skills';
    }

    public function httpMcpServerConfig(string $url): ?array
    {
        return [
            'command' => 'npx',
            'args' => ['-y', 'mcp-remote', $url],
        ];
    }
}

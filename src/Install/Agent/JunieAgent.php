<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Install\Agent;

use IAdil\SymfonyBoostBundle\Install\Enums\Platform;

class JunieAgent extends AbstractAgent
{
    public function name(): string
    {
        return 'junie';
    }

    public function displayName(): string
    {
        return 'Junie';
    }

    public function supportsGuidelines(): bool
    {
        return true;
    }

    public function supportsMcp(): bool
    {
        return true;
    }

    public function useAbsolutePathForMcp(): bool
    {
        return true;
    }

    public function systemDetectionConfig(Platform $platform): array
    {
        return match ($platform) {
            Platform::Darwin => ['paths' => ['/Applications/IntelliJ IDEA.app', '/Applications/PhpStorm.app']],
            Platform::Linux => ['paths' => ['~/.local/share/JetBrains']],
            Platform::Windows => ['paths' => ['%ProgramFiles%\\JetBrains']],
        };
    }

    public function projectDetectionConfig(): array
    {
        return [
            'paths' => ['.idea', '.junie'],
        ];
    }

    public function mcpConfigPath(): string
    {
        return '.junie/mcp.json';
    }

    public function guidelinesPath(): string
    {
        return '.junie/guidelines.md';
    }

    public function skillsPath(): string
    {
        return '.junie/skills';
    }
}

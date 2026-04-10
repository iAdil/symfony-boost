<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Install\Agent;

use IAdil\SymfonyBoostBundle\Install\Enums\Platform;

class OpenCodeAgent extends AbstractAgent
{
    public function name(): string
    {
        return 'opencode';
    }

    public function displayName(): string
    {
        return 'OpenCode';
    }

    public function supportsGuidelines(): bool
    {
        return true;
    }

    public function supportsMcp(): bool
    {
        return true;
    }

    public function systemDetectionConfig(Platform $platform): array
    {
        return match ($platform) {
            Platform::Darwin, Platform::Linux => ['command' => 'command -v opencode'],
            Platform::Windows => ['command' => 'cmd /c where opencode 2>nul'],
        };
    }

    public function projectDetectionConfig(): array
    {
        return [
            'files' => ['opencode.json'],
        ];
    }

    public function mcpConfigPath(): string
    {
        return 'opencode.json';
    }

    public function guidelinesPath(): string
    {
        return 'AGENTS.md';
    }

    public function skillsPath(): string
    {
        return '.opencode/skills';
    }
}

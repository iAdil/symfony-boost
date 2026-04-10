<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Install\Agent;

use IAdil\SymfonyBoostBundle\Install\Enums\Platform;

class CodexAgent extends AbstractAgent
{
    public function name(): string
    {
        return 'codex';
    }

    public function displayName(): string
    {
        return 'Codex';
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
            Platform::Darwin, Platform::Linux => ['command' => 'command -v codex'],
            Platform::Windows => ['command' => 'cmd /c where codex 2>nul'],
        };
    }

    public function projectDetectionConfig(): array
    {
        return [
            'files' => ['AGENTS.md', 'codex.toml'],
        ];
    }

    public function mcpConfigPath(): string
    {
        return 'codex.toml';
    }

    public function guidelinesPath(): string
    {
        return 'AGENTS.md';
    }

    public function skillsPath(): string
    {
        return '.codex/skills';
    }
}

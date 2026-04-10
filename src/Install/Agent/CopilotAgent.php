<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Install\Agent;

use IAdil\SymfonyBoostBundle\Install\Enums\Platform;

class CopilotAgent extends AbstractAgent
{
    public function name(): string
    {
        return 'copilot';
    }

    public function displayName(): string
    {
        return 'GitHub Copilot';
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
            Platform::Darwin, Platform::Linux => ['command' => 'command -v gh'],
            Platform::Windows => ['command' => 'cmd /c where gh 2>nul'],
        };
    }

    public function projectDetectionConfig(): array
    {
        return [
            'paths' => ['.github'],
            'files' => ['.github/copilot-instructions.md'],
        ];
    }

    public function mcpConfigPath(): string
    {
        return '.vscode/mcp.json';
    }

    public function guidelinesPath(): string
    {
        return '.github/copilot-instructions.md';
    }

    public function skillsPath(): string
    {
        return '.github/skills';
    }
}

<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Install\Agent;

use IAdil\SymfonyBoostBundle\Install\Enums\Platform;

class GeminiAgent extends AbstractAgent
{
    public function name(): string
    {
        return 'gemini';
    }

    public function displayName(): string
    {
        return 'Gemini';
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
            Platform::Darwin, Platform::Linux => ['command' => 'command -v gemini'],
            Platform::Windows => ['command' => 'cmd /c where gemini 2>nul'],
        };
    }

    public function projectDetectionConfig(): array
    {
        return [
            'files' => ['GEMINI.md'],
        ];
    }

    public function mcpConfigPath(): string
    {
        return '.gemini/settings.json';
    }

    public function guidelinesPath(): string
    {
        return 'GEMINI.md';
    }

    public function skillsPath(): string
    {
        return '.gemini/skills';
    }

    public function transformGuidelines(string $content): string
    {
        return preg_replace('/```[\w]*\n(.*?)```/s', '$1', $content) ?? $content;
    }
}

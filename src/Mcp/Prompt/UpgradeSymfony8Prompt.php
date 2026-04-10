<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Mcp\Prompt;

use Mcp\Capability\Attribute\McpPrompt;
use IAdil\SymfonyBoostBundle\Service\PackageDiscovery;
use Twig\Environment;

#[McpPrompt(name: 'upgrade-symfony-8')]
class UpgradeSymfony8Prompt
{
    public function __construct(
        private readonly Environment $twig,
        private readonly PackageDiscovery $packageDiscovery,
    ) {
    }

    public function __invoke(): array
    {
        $symfonyVersion = $this->packageDiscovery->getPackageVersion('symfony/framework-bundle');

        $content = $this->twig->render('@IAdilSymfonyBoost/prompts/upgrade-symfony-8.md.twig', [
            'current_version' => $symfonyVersion ?? 'unknown',
        ]);

        return [
            ['role' => 'user', 'content' => $content],
        ];
    }
}

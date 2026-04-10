<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Mcp\Prompt;

use Mcp\Capability\Attribute\McpPrompt;
use IAdil\SymfonyBoostBundle\Service\PackageDiscovery;
use Twig\Environment;

#[McpPrompt(name: 'upgrade-twig')]
class UpgradeTwigPrompt
{
    public function __construct(
        private readonly Environment $twig,
        private readonly PackageDiscovery $packageDiscovery,
    ) {
    }

    public function __invoke(): array
    {
        $twigVersion = $this->packageDiscovery->getPackageVersion('twig/twig');

        $content = $this->twig->render('@IAdilSymfonyBoost/prompts/upgrade-twig.md.twig', [
            'current_version' => $twigVersion ?? 'unknown',
        ]);

        return [
            ['role' => 'user', 'content' => $content],
        ];
    }
}

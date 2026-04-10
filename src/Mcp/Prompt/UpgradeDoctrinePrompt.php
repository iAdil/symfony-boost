<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Mcp\Prompt;

use Mcp\Capability\Attribute\McpPrompt;
use IAdil\SymfonyBoostBundle\Service\PackageDiscovery;
use Twig\Environment;

#[McpPrompt(name: 'upgrade-doctrine')]
class UpgradeDoctrinePrompt
{
    public function __construct(
        private readonly Environment $twig,
        private readonly PackageDiscovery $packageDiscovery,
    ) {
    }

    public function __invoke(): array
    {
        $ormVersion = $this->packageDiscovery->getPackageVersion('doctrine/orm');
        $dbalVersion = $this->packageDiscovery->getPackageVersion('doctrine/dbal');

        $content = $this->twig->render('@IAdilSymfonyBoost/prompts/upgrade-doctrine.md.twig', [
            'orm_version' => $ormVersion ?? 'unknown',
            'dbal_version' => $dbalVersion ?? 'unknown',
        ]);

        return [
            ['role' => 'user', 'content' => $content],
        ];
    }
}

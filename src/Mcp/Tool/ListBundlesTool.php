<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Mcp\Tool;

use Mcp\Capability\Attribute\McpTool;
use Symfony\Component\HttpKernel\KernelInterface;

#[McpTool(name: 'list-bundles')]
class ListBundlesTool
{
    public function __construct(
        private readonly KernelInterface $kernel,
    ) {
    }

    public function __invoke(): array
    {
        $bundles = [];

        foreach ($this->kernel->getBundles() as $name => $bundle) {
            $bundles[] = [
                'name' => $name,
                'class' => $bundle::class,
                'path' => $bundle->getPath(),
            ];
        }

        usort($bundles, fn (array $a, array $b) => $a['name'] <=> $b['name']);

        return $bundles;
    }
}

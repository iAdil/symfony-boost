<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Mcp\Tool;

use Mcp\Capability\Attribute\McpTool;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[McpTool(name: 'get-config-value')]
class GetConfigValueTool
{
    public function __construct(
        private readonly ContainerInterface $container,
    ) {
    }

    public function __invoke(string $key): array|string
    {
        if (!$this->container->hasParameter($key)) {
            return "Error: Parameter '{$key}' does not exist.";
        }

        $value = $this->container->getParameter($key);

        if (\is_array($value) || \is_object($value)) {
            return ['key' => $key, 'value' => $value];
        }

        return ['key' => $key, 'value' => $value];
    }
}

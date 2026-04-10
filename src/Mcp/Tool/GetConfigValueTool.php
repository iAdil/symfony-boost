<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Mcp\Tool;

use Mcp\Capability\Attribute\McpTool;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[McpTool(name: 'get-config-value')]
class GetConfigValueTool
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
    ) {
    }

    public function __invoke(string $key): string
    {
        if (!$this->parameterBag->has($key)) {
            return "Error: Parameter '{$key}' does not exist.";
        }

        $value = $this->parameterBag->get($key);

        return json_encode(['key' => $key, 'value' => $value], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}

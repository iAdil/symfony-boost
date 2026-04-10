<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Mcp\Prompt;

use Mcp\Capability\Attribute\McpPrompt;
use Twig\Environment;

#[McpPrompt(name: 'symfony-code-simplifier')]
class SymfonyCodeSimplifierPrompt
{
    public function __construct(
        private readonly Environment $twig,
    ) {
    }

    public function __invoke(): array
    {
        $content = $this->twig->render('@IAdilSymfonyBoost/prompts/symfony-code-simplifier.md.twig');

        return [
            ['role' => 'user', 'content' => $content],
        ];
    }
}

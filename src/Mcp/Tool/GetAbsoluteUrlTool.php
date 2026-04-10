<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Mcp\Tool;

use Mcp\Capability\Attribute\McpTool;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[McpTool(name: 'get-absolute-url')]
class GetAbsoluteUrlTool
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function __invoke(?string $path = null, ?string $route = null): string
    {
        if ($path) {
            $baseUrl = $this->urlGenerator->generate('_', [], UrlGeneratorInterface::ABSOLUTE_URL);
            $baseUrl = rtrim(preg_replace('#/[^/]*$#', '', $baseUrl), '/');

            return $baseUrl.'/'.ltrim($path, '/');
        }

        if ($route) {
            try {
                return $this->urlGenerator->generate($route, [], UrlGeneratorInterface::ABSOLUTE_URL);
            } catch (\Throwable $e) {
                return 'Error: '.$e->getMessage();
            }
        }

        $baseUrl = $this->urlGenerator->generate('_', [], UrlGeneratorInterface::ABSOLUTE_URL);

        return rtrim(preg_replace('#/[^/]*$#', '', $baseUrl), '/').'/';
    }
}

<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Mcp\Tool;

use Mcp\Capability\Attribute\McpTool;
use Symfony\Component\Routing\RouterInterface;

#[McpTool(name: 'list-routes')]
class ListRoutesTool
{
    public function __construct(
        private readonly RouterInterface $router,
    ) {
    }

    public function __invoke(?string $filter = null): array
    {
        $routes = [];

        foreach ($this->router->getRouteCollection()->all() as $name => $route) {
            if ($filter !== null && !str_contains(strtolower($name), strtolower($filter))
                && !str_contains(strtolower($route->getPath()), strtolower($filter))) {
                continue;
            }

            $controller = $route->getDefault('_controller') ?? '';

            $routes[] = [
                'name' => $name,
                'path' => $route->getPath(),
                'methods' => $route->getMethods() ?: ['ANY'],
                'controller' => $controller,
            ];
        }

        usort($routes, fn (array $a, array $b) => $a['name'] <=> $b['name']);

        return $routes;
    }
}

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

    public function __invoke(?string $filter = null, int $limit = 50, int $offset = 0): string
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

        $total = \count($routes);
        $routes = \array_slice($routes, $offset, $limit);

        $result = [
            'total' => $total,
            'showing' => \count($routes),
            'offset' => $offset,
            'limit' => $limit,
            'routes' => $routes,
        ];

        if ($total > $offset + $limit) {
            $result['hint'] = "Showing {$offset}-".($offset + \count($routes))." of {$total}. Use offset=".($offset + $limit)." to see more, or use filter to narrow results.";
        }

        return json_encode($result, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES);
    }
}

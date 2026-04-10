<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Mcp\Tool;

use GuzzleHttp\Client;
use Mcp\Capability\Attribute\McpTool;
use IAdil\SymfonyBoostBundle\Service\PackageDiscovery;

#[McpTool(name: 'search-docs')]
class SearchDocsTool
{
    public function __construct(
        private readonly PackageDiscovery $packageDiscovery,
        private readonly string $apiUrl = 'https://boost.laravel.com',
    ) {
    }

    /**
     * @param array<string> $queries
     * @param array<string>|null $packages
     */
    public function __invoke(array $queries, ?array $packages = null, int $token_limit = 3000): string
    {
        $apiEndpoint = rtrim($this->apiUrl, '/').'/api/docs';

        $queries = array_filter(
            array_map('trim', $queries),
            fn (string $query): bool => $query !== '' && $query !== '*'
        );

        if (empty($queries)) {
            return 'Error: No valid queries provided.';
        }

        try {
            $allPackages = $this->packageDiscovery->getPackagesForApi();

            if ($packages !== null && !empty($packages)) {
                $allPackages = array_filter(
                    $allPackages,
                    fn (array $pkg): bool => \in_array($pkg['name'], $packages, true)
                );
                $allPackages = array_values($allPackages);
            }
        } catch (\Throwable $throwable) {
            return 'Error: Failed to get packages: '.$throwable->getMessage();
        }

        $tokenLimit = min($token_limit, 1000000);

        $payload = [
            'queries' => $queries,
            'packages' => $allPackages,
            'token_limit' => $tokenLimit,
            'format' => 'markdown',
        ];

        try {
            $client = new Client([
                'headers' => [
                    'User-Agent' => 'SymfonyBoost/1.0',
                    'Accept' => 'application/json',
                ],
                'verify' => false,
            ]);

            $response = $client->post($apiEndpoint, [
                'json' => $payload,
            ]);

            if ($response->getStatusCode() >= 400) {
                return 'Error: Failed to search documentation: '.$response->getBody()->getContents();
            }

            return $response->getBody()->getContents();
        } catch (\Throwable $throwable) {
            return 'Error: HTTP request failed: '.$throwable->getMessage();
        }
    }
}

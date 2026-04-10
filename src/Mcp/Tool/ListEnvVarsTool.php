<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Mcp\Tool;

use Mcp\Capability\Attribute\McpTool;

#[McpTool(name: 'list-env-vars')]
class ListEnvVarsTool
{
    public function __invoke(?string $filter = null): string
    {
        $envFile = $_SERVER['DOCUMENT_ROOT'] ?? '';

        // Try to read from .env file for project-specific vars
        $projectDir = $_SERVER['APP_PROJECT_DIR'] ?? $_SERVER['SYMFONY_PROJECT_DIR'] ?? null;
        $vars = [];

        // Collect relevant env vars (APP_, DATABASE_, MAILER_, etc.)
        $prefixes = ['APP_', 'DATABASE_', 'MAILER_', 'MESSENGER_', 'REDIS_', 'SENTRY_', 'CORS_', 'JWT_', 'SYMFONY_'];

        foreach ($_SERVER as $key => $value) {
            if (!\is_string($key)) {
                continue;
            }

            $matchesPrefix = false;

            foreach ($prefixes as $prefix) {
                if (str_starts_with($key, $prefix)) {
                    $matchesPrefix = true;

                    break;
                }
            }

            if (!$matchesPrefix) {
                continue;
            }

            if ($filter !== null && !str_contains(strtolower($key), strtolower($filter))) {
                continue;
            }

            // Mask sensitive values
            $maskedValue = $this->maskSensitive($key, (string) $value);
            $vars[$key] = $maskedValue;
        }

        ksort($vars);

        return json_encode($vars, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    private function maskSensitive(string $key, string $value): string
    {
        $sensitivePatterns = ['SECRET', 'PASSWORD', 'TOKEN', 'KEY', 'DSN', 'URL', 'SENTRY'];

        foreach ($sensitivePatterns as $pattern) {
            if (str_contains(strtoupper($key), $pattern)) {
                if (\strlen($value) <= 8) {
                    return '***';
                }

                return substr($value, 0, 4).'...'.substr($value, -4);
            }
        }

        return $value;
    }
}

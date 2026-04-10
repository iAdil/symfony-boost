<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Install\Detection;

use IAdil\SymfonyBoostBundle\Install\Enums\Platform;

class DirectoryDetectionStrategy implements DetectionStrategyInterface
{
    public function detect(array $config, ?Platform $platform = null): bool
    {
        $paths = $config['paths'] ?? [];
        $basePath = $config['basePath'] ?? null;

        foreach ($paths as $path) {
            $expandedPath = $this->expandPath($path);

            if ($basePath !== null) {
                $expandedPath = $basePath.\DIRECTORY_SEPARATOR.ltrim($expandedPath, '/\\');
            }

            if (is_dir($expandedPath)) {
                return true;
            }
        }

        return false;
    }

    private function expandPath(string $path): string
    {
        if (str_starts_with($path, '~')) {
            $home = $_SERVER['HOME'] ?? $_SERVER['USERPROFILE'] ?? '';
            $path = $home.substr($path, 1);
        }

        $path = preg_replace_callback('/%([^%]+)%/', function (array $matches): string {
            return $_SERVER[$matches[1]] ?? getenv($matches[1]) ?: $matches[0];
        }, $path);

        return $path;
    }
}

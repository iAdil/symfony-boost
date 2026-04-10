<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Install\Detection;

use IAdil\SymfonyBoostBundle\Install\Enums\Platform;

class FileDetectionStrategy implements DetectionStrategyInterface
{
    public function detect(array $config, ?Platform $platform = null): bool
    {
        $files = $config['files'] ?? [];
        $basePath = $config['basePath'] ?? null;

        foreach ($files as $file) {
            $filePath = $basePath !== null
                ? $basePath.\DIRECTORY_SEPARATOR.$file
                : $file;

            if (file_exists($filePath)) {
                return true;
            }
        }

        return false;
    }
}

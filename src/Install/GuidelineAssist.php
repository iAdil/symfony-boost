<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Install;

use Doctrine\ORM\EntityManagerInterface;
use IAdil\SymfonyBoostBundle\Service\PackageDiscovery;

class GuidelineAssist
{
    public readonly PackageDiscovery $packageDiscovery;

    public function __construct(
        private readonly string $projectDir,
        ?PackageDiscovery $packageDiscovery = null,
        private readonly ?EntityManagerInterface $entityManager = null,
    ) {
        $this->packageDiscovery = $packageDiscovery ?? new PackageDiscovery($projectDir);
    }

    /**
     * @return array<string, string>
     */
    public function models(): array
    {
        $models = [];

        if ($this->entityManager !== null) {
            try {
                foreach ($this->entityManager->getMetadataFactory()->getAllMetadata() as $metadata) {
                    $models[$metadata->getName()] = $metadata->getName();
                }
            } catch (\Throwable) {
                // Entity manager may not be available
            }
        }

        return $models;
    }

    /**
     * @return array<string, string>
     */
    public function controllers(): array
    {
        $controllers = [];
        $controllerDir = $this->projectDir.'/src/Controller';

        if (!is_dir($controllerDir)) {
            return $controllers;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($controllerDir)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $className = $file->getBasename('.php');
                $controllers[$className] = $className;
            }
        }

        return $controllers;
    }

    public function nodePackageManagerCommand(string $command): string
    {
        if (file_exists($this->projectDir.'/yarn.lock')) {
            return "yarn {$command}";
        }

        if (file_exists($this->projectDir.'/pnpm-lock.yaml')) {
            return "pnpm {$command}";
        }

        if (file_exists($this->projectDir.'/bun.lockb')) {
            return "bun {$command}";
        }

        return "npm {$command}";
    }
}

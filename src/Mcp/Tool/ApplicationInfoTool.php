<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Mcp\Tool;

use Doctrine\ORM\EntityManagerInterface;
use Mcp\Capability\Attribute\McpTool;
use IAdil\SymfonyBoostBundle\Service\PackageDiscovery;
use Symfony\Component\HttpKernel\Kernel;

#[McpTool(name: 'application-info')]
class ApplicationInfoTool
{
    public function __construct(
        private readonly PackageDiscovery $packageDiscovery,
        private readonly string $projectDir,
        private readonly ?EntityManagerInterface $entityManager = null,
    ) {
    }

    public function __invoke(): string
    {
        $entities = [];

        if ($this->entityManager !== null) {
            try {
                $metadataFactory = $this->entityManager->getMetadataFactory();

                foreach ($metadataFactory->getAllMetadata() as $metadata) {
                    $entities[] = $metadata->getName();
                }
            } catch (\Throwable) {
                // Entity manager may not be available
            }
        }

        $packages = [];

        foreach ($this->packageDiscovery->getPackages() as $package) {
            $packages[] = [
                'package_name' => $package['name'],
                'version' => $package['version'],
            ];
        }

        return json_encode([
            'php_version' => \PHP_MAJOR_VERSION.'.'.\PHP_MINOR_VERSION,
            'symfony_version' => Kernel::VERSION,
            'database_engine' => $this->detectDatabaseEngine(),
            'packages' => $packages,
            'entities' => $entities,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    private function detectDatabaseEngine(): string
    {
        if ($this->entityManager === null) {
            return 'unknown';
        }

        try {
            $connection = $this->entityManager->getConnection();
            $platform = $connection->getDatabasePlatform();

            return match (true) {
                str_contains(\get_class($platform), 'MySQL') => 'mysql',
                str_contains(\get_class($platform), 'PostgreSQL') => 'postgresql',
                str_contains(\get_class($platform), 'SQLite') => 'sqlite',
                default => \get_class($platform),
            };
        } catch (\Throwable) {
            return 'unknown';
        }
    }
}

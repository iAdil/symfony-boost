<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Mcp\Resource;

use Doctrine\ORM\EntityManagerInterface;
use Mcp\Capability\Attribute\McpResource;
use IAdil\SymfonyBoostBundle\Service\PackageDiscovery;
use Symfony\Component\HttpKernel\Kernel;

#[McpResource(uri: 'file://instructions/application-info.md', name: 'application-info')]
class ApplicationInfoResource
{
    public function __construct(
        private readonly PackageDiscovery $packageDiscovery,
        private readonly string $projectDir,
        private readonly ?EntityManagerInterface $entityManager = null,
    ) {
    }

    public function __invoke(): array
    {
        $lines = [];
        $lines[] = '# Application Info';
        $lines[] = '';
        $lines[] = '- **PHP Version**: '.\PHP_MAJOR_VERSION.'.'.\PHP_MINOR_VERSION;
        $lines[] = '- **Symfony Version**: '.Kernel::VERSION;
        $lines[] = '- **Project Directory**: '.$this->projectDir;
        $lines[] = '';

        $lines[] = '## Installed Packages';
        $lines[] = '';

        foreach ($this->packageDiscovery->getPackages() as $package) {
            $lines[] = '- '.$package['name'].': '.$package['version'];
        }

        $lines[] = '';

        if ($this->entityManager !== null) {
            $lines[] = '## Doctrine Entities';
            $lines[] = '';

            try {
                $metadataFactory = $this->entityManager->getMetadataFactory();

                foreach ($metadataFactory->getAllMetadata() as $metadata) {
                    $lines[] = '- '.$metadata->getName();
                }
            } catch (\Throwable) {
                $lines[] = '- (unable to discover entities)';
            }
        }

        $content = implode("\n", $lines);

        return [
            'uri' => 'file://instructions/application-info.md',
            'mimeType' => 'text/markdown',
            'text' => $content,
        ];
    }
}

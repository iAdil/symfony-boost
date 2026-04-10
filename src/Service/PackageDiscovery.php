<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Service;

class PackageDiscovery
{
    /** @var array<string, array{name: string, version: string, major_version: string}>|null */
    private ?array $packages = null;

    public function __construct(
        private readonly string $projectDir,
    ) {
    }

    /**
     * @return array<string, array{name: string, version: string, major_version: string}>
     */
    public function getPackages(): array
    {
        if ($this->packages !== null) {
            return $this->packages;
        }

        $lockFile = $this->projectDir.'/composer.lock';

        if (!file_exists($lockFile)) {
            return $this->packages = [];
        }

        $lockData = json_decode(file_get_contents($lockFile), true);

        if (!\is_array($lockData)) {
            return $this->packages = [];
        }

        $packages = [];
        $allPackages = array_merge(
            $lockData['packages'] ?? [],
            $lockData['packages-dev'] ?? [],
        );

        foreach ($allPackages as $package) {
            $name = $package['name'] ?? '';
            $version = ltrim($package['version'] ?? '', 'v');

            $majorVersion = explode('.', $version)[0] ?? '0';

            $packages[$name] = [
                'name' => $name,
                'version' => $version,
                'major_version' => $majorVersion,
            ];
        }

        ksort($packages);

        return $this->packages = $packages;
    }

    public function getPackageVersion(string $name): ?string
    {
        return $this->getPackages()[$name]['version'] ?? null;
    }

    public function getPackageMajorVersion(string $name): ?string
    {
        return $this->getPackages()[$name]['major_version'] ?? null;
    }

    public function hasPackage(string $name): bool
    {
        return isset($this->getPackages()[$name]);
    }

    /**
     * @return array<int, array{name: string, version: string}>
     */
    public function getPackagesForApi(): array
    {
        $result = [];

        foreach ($this->getPackages() as $package) {
            $result[] = [
                'name' => $package['name'],
                'version' => $package['major_version'].'.x',
            ];
        }

        return $result;
    }

    public function isFirstPartySymfonyPackage(string $name): bool
    {
        return str_starts_with($name, 'symfony/')
            || str_starts_with($name, 'doctrine/')
            || str_starts_with($name, 'twig/');
    }
}

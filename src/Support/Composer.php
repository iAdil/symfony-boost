<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Support;

class Composer
{
    private const FIRST_PARTY_SCOPES = [
        'symfony/',
        'doctrine/',
        'twig/',
    ];

    private const FIRST_PARTY_PACKAGES = [
        'symfony/framework-bundle',
        'symfony/console',
        'doctrine/orm',
        'doctrine/dbal',
        'twig/twig',
    ];

    public function __construct(
        private readonly string $projectDir,
    ) {
    }

    public function isFirstParty(string $name): bool
    {
        foreach (self::FIRST_PARTY_SCOPES as $scope) {
            if (str_starts_with($name, $scope)) {
                return true;
            }
        }

        return \in_array($name, self::FIRST_PARTY_PACKAGES, true);
    }

    public function vendorPath(): string
    {
        return $this->projectDir.\DIRECTORY_SEPARATOR.'vendor';
    }

    /**
     * @return array<string, string>
     */
    public function getInstalledPackagesWithBoostGuidelines(): array
    {
        $packages = [];
        $vendorDir = $this->vendorPath();

        if (!is_dir($vendorDir)) {
            return $packages;
        }

        $iterator = new \DirectoryIterator($vendorDir);

        foreach ($iterator as $vendorEntry) {
            if ($vendorEntry->isDot() || !$vendorEntry->isDir()) {
                continue;
            }

            $packageIterator = new \DirectoryIterator($vendorEntry->getPathname());

            foreach ($packageIterator as $packageEntry) {
                if ($packageEntry->isDot() || !$packageEntry->isDir()) {
                    continue;
                }

                $boostGuidelinesDir = $packageEntry->getPathname().\DIRECTORY_SEPARATOR.'.ai'.\DIRECTORY_SEPARATOR.'guidelines';

                if (is_dir($boostGuidelinesDir)) {
                    $packages[$vendorEntry->getFilename().'/'.$packageEntry->getFilename()] = $boostGuidelinesDir;
                }
            }
        }

        return $packages;
    }

    /**
     * @return array<string, string>
     */
    public function getInstalledPackagesWithBoostSkills(): array
    {
        $packages = [];
        $vendorDir = $this->vendorPath();

        if (!is_dir($vendorDir)) {
            return $packages;
        }

        $iterator = new \DirectoryIterator($vendorDir);

        foreach ($iterator as $vendorEntry) {
            if ($vendorEntry->isDot() || !$vendorEntry->isDir()) {
                continue;
            }

            $packageIterator = new \DirectoryIterator($vendorEntry->getPathname());

            foreach ($packageIterator as $packageEntry) {
                if ($packageEntry->isDot() || !$packageEntry->isDir()) {
                    continue;
                }

                $boostSkillsDir = $packageEntry->getPathname().\DIRECTORY_SEPARATOR.'.ai'.\DIRECTORY_SEPARATOR.'skills';

                if (is_dir($boostSkillsDir)) {
                    $packages[$vendorEntry->getFilename().'/'.$packageEntry->getFilename()] = $boostSkillsDir;
                }
            }
        }

        return $packages;
    }
}

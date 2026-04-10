<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Support;

class Npm
{
    private const FIRST_PARTY_SCOPES = [
        '@symfony/',
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

        return false;
    }

    public function nodeModulesPath(): string
    {
        return $this->projectDir.\DIRECTORY_SEPARATOR.'node_modules';
    }

    /**
     * @return array<string, string>
     */
    public function getInstalledPackagesWithBoostGuidelines(): array
    {
        $packages = [];
        $nodeModulesDir = $this->nodeModulesPath();

        if (!is_dir($nodeModulesDir)) {
            return $packages;
        }

        $iterator = new \DirectoryIterator($nodeModulesDir);

        foreach ($iterator as $entry) {
            if ($entry->isDot() || !$entry->isDir()) {
                continue;
            }

            if (str_starts_with($entry->getFilename(), '@')) {
                $scopeIterator = new \DirectoryIterator($entry->getPathname());

                foreach ($scopeIterator as $scopeEntry) {
                    if ($scopeEntry->isDot() || !$scopeEntry->isDir()) {
                        continue;
                    }

                    $guidelinesDir = $scopeEntry->getPathname().\DIRECTORY_SEPARATOR.'.ai'.\DIRECTORY_SEPARATOR.'guidelines';

                    if (is_dir($guidelinesDir)) {
                        $packages[$entry->getFilename().'/'.$scopeEntry->getFilename()] = $guidelinesDir;
                    }
                }
            } else {
                $guidelinesDir = $entry->getPathname().\DIRECTORY_SEPARATOR.'.ai'.\DIRECTORY_SEPARATOR.'guidelines';

                if (is_dir($guidelinesDir)) {
                    $packages[$entry->getFilename()] = $guidelinesDir;
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
        $nodeModulesDir = $this->nodeModulesPath();

        if (!is_dir($nodeModulesDir)) {
            return $packages;
        }

        $iterator = new \DirectoryIterator($nodeModulesDir);

        foreach ($iterator as $entry) {
            if ($entry->isDot() || !$entry->isDir()) {
                continue;
            }

            if (str_starts_with($entry->getFilename(), '@')) {
                $scopeIterator = new \DirectoryIterator($entry->getPathname());

                foreach ($scopeIterator as $scopeEntry) {
                    if ($scopeEntry->isDot() || !$scopeEntry->isDir()) {
                        continue;
                    }

                    $skillsDir = $scopeEntry->getPathname().\DIRECTORY_SEPARATOR.'.ai'.\DIRECTORY_SEPARATOR.'skills';

                    if (is_dir($skillsDir)) {
                        $packages[$entry->getFilename().'/'.$scopeEntry->getFilename()] = $skillsDir;
                    }
                }
            } else {
                $skillsDir = $entry->getPathname().\DIRECTORY_SEPARATOR.'.ai'.\DIRECTORY_SEPARATOR.'skills';

                if (is_dir($skillsDir)) {
                    $packages[$entry->getFilename()] = $skillsDir;
                }
            }
        }

        return $packages;
    }
}

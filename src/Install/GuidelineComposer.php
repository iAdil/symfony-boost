<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Install;

use IAdil\SymfonyBoostBundle\Support\Composer;
use IAdil\SymfonyBoostBundle\Support\Npm;

class GuidelineComposer
{
    public function __construct(
        private readonly string $projectDir,
        private readonly Composer $composer,
        private readonly Npm $npm,
    ) {
    }

    /**
     * Compose all guidelines from various sources.
     *
     * @param string[] $selectedPackages
     * @return string[]
     */
    public function compose(array $selectedPackages = []): array
    {
        $guidelines = [];

        // 1. Discover from vendor packages (Composer)
        $composerGuidelines = $this->composer->getInstalledPackagesWithBoostGuidelines();

        foreach ($composerGuidelines as $packageName => $guidelinesDir) {
            if (!empty($selectedPackages) && !\in_array($packageName, $selectedPackages, true)) {
                continue;
            }

            $guidelines = array_merge($guidelines, $this->readGuidelinesFromDirectory($guidelinesDir, $packageName));
        }

        // 2. Discover from node_modules (NPM)
        $npmGuidelines = $this->npm->getInstalledPackagesWithBoostGuidelines();

        foreach ($npmGuidelines as $packageName => $guidelinesDir) {
            if (!empty($selectedPackages) && !\in_array($packageName, $selectedPackages, true)) {
                continue;
            }

            $guidelines = array_merge($guidelines, $this->readGuidelinesFromDirectory($guidelinesDir, $packageName));
        }

        // 3. Discover from project-level .ai/guidelines/
        $projectGuidelinesDir = $this->projectDir.'/.ai/guidelines';

        if (is_dir($projectGuidelinesDir)) {
            $guidelines = array_merge($guidelines, $this->readGuidelinesFromDirectory($projectGuidelinesDir, 'project'));
        }

        return $guidelines;
    }

    /**
     * @return string[]
     */
    private function readGuidelinesFromDirectory(string $directory, string $source): array
    {
        $guidelines = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $ext = $file->getExtension();

            if (!\in_array($ext, ['md', 'php', 'twig'], true)) {
                continue;
            }

            $content = file_get_contents($file->getPathname());

            if ($content !== false && trim($content) !== '') {
                $guidelines[] = "<!-- Source: {$source} -->\n".$content;
            }
        }

        return $guidelines;
    }
}

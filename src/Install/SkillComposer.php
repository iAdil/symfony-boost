<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Install;

use IAdil\SymfonyBoostBundle\Support\Composer;
use IAdil\SymfonyBoostBundle\Support\Npm;

class SkillComposer
{
    public function __construct(
        private readonly string $projectDir,
        private readonly Composer $composer,
        private readonly Npm $npm,
    ) {
    }

    /**
     * @param string[] $selectedPackages
     * @return Skill[]
     */
    public function compose(array $selectedPackages = []): array
    {
        $skills = [];

        // 0. Built-in Symfony Boost skills
        $builtInDir = \dirname(__DIR__, 2).'/resources/skills';

        if (is_dir($builtInDir)) {
            $skills = array_merge($skills, $this->discoverSkillsInDirectory($builtInDir, 'symfony-boost'));
        }

        // 1. Discover from vendor packages (Composer)
        $composerSkills = $this->composer->getInstalledPackagesWithBoostSkills();

        foreach ($composerSkills as $packageName => $skillsDir) {
            if (!empty($selectedPackages) && !\in_array($packageName, $selectedPackages, true)) {
                continue;
            }

            $skills = array_merge($skills, $this->discoverSkillsInDirectory($skillsDir, $packageName));
        }

        // 2. Discover from node_modules (NPM)
        $npmSkills = $this->npm->getInstalledPackagesWithBoostSkills();

        foreach ($npmSkills as $packageName => $skillsDir) {
            if (!empty($selectedPackages) && !\in_array($packageName, $selectedPackages, true)) {
                continue;
            }

            $skills = array_merge($skills, $this->discoverSkillsInDirectory($skillsDir, $packageName));
        }

        // 3. Discover from project-level .ai/skills/
        $projectSkillsDir = $this->projectDir.'/.ai/skills';

        if (is_dir($projectSkillsDir)) {
            $skills = array_merge($skills, $this->discoverSkillsInDirectory($projectSkillsDir, 'project'));
        }

        return $skills;
    }

    /**
     * @return Skill[]
     */
    private function discoverSkillsInDirectory(string $directory, string $package): array
    {
        $skills = [];

        $iterator = new \DirectoryIterator($directory);

        foreach ($iterator as $entry) {
            if ($entry->isDot() || !$entry->isDir()) {
                continue;
            }

            $skillFile = $entry->getPathname().\DIRECTORY_SEPARATOR.'SKILL.md';
            $skillTwigFile = $entry->getPathname().\DIRECTORY_SEPARATOR.'SKILL.md.twig';

            $file = null;

            if (file_exists($skillFile)) {
                $file = $skillFile;
            } elseif (file_exists($skillTwigFile)) {
                $file = $skillTwigFile;
            }

            if ($file === null) {
                continue;
            }

            $content = file_get_contents($file);
            $metadata = $this->parseFrontmatter($content);

            $skills[] = new Skill(
                name: $metadata['name'] ?? $entry->getFilename(),
                package: $package,
                path: $entry->getPathname(),
                description: $metadata['description'] ?? '',
            );
        }

        return $skills;
    }

    private function parseFrontmatter(string $content): array
    {
        if (!str_starts_with(trim($content), '---')) {
            return [];
        }

        $parts = preg_split('/^---\s*$/m', $content, 3);

        if (\count($parts) < 3) {
            return [];
        }

        $frontmatter = [];
        $lines = explode("\n", trim($parts[1]));

        foreach ($lines as $line) {
            if (str_contains($line, ':')) {
                [$key, $value] = explode(':', $line, 2);
                $frontmatter[trim($key)] = trim($value);
            }
        }

        return $frontmatter;
    }
}

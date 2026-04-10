<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Install;

use IAdil\SymfonyBoostBundle\Install\Agent\AbstractAgent;
use IAdil\SymfonyBoostBundle\Skills\Remote\RemoteSkill;

class SkillWriter
{
    /**
     * @param Skill[] $skills
     */
    public function write(AbstractAgent $agent, array $skills): void
    {
        if (!$agent->supportsSkills()) {
            return;
        }

        $skillsPath = $agent->skillsPath();

        if ($skillsPath === '') {
            return;
        }

        if (!is_dir($skillsPath)) {
            mkdir($skillsPath, 0755, true);
        }

        foreach ($skills as $skill) {
            $this->writeSkill($skillsPath, $skill);
        }
    }

    public function writeRemoteSkill(AbstractAgent $agent, RemoteSkill $remoteSkill, bool $force = false): void
    {
        if (!$agent->supportsSkills()) {
            return;
        }

        $skillsPath = $agent->skillsPath();

        if ($skillsPath === '') {
            return;
        }

        $targetDir = $skillsPath.\DIRECTORY_SEPARATOR.$remoteSkill->name;

        if (is_dir($targetDir) && !$force) {
            return;
        }

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Copy files from remote skill
        if (is_dir($remoteSkill->path)) {
            $this->copyDirectory($remoteSkill->path, $targetDir);
        }
    }

    private function writeSkill(string $basePath, Skill $skill): void
    {
        $targetDir = $basePath.\DIRECTORY_SEPARATOR.$skill->name;

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Create symlink or copy
        if (\function_exists('symlink') && \PHP_OS_FAMILY !== 'Windows') {
            $this->symlinkSkill($skill->path, $targetDir);
        } else {
            $this->copyDirectory($skill->path, $targetDir);
        }
    }

    private function symlinkSkill(string $source, string $target): void
    {
        $iterator = new \DirectoryIterator($source);

        foreach ($iterator as $entry) {
            if ($entry->isDot()) {
                continue;
            }

            $targetPath = $target.\DIRECTORY_SEPARATOR.$entry->getFilename();

            if (file_exists($targetPath) || is_link($targetPath)) {
                if (is_link($targetPath)) {
                    unlink($targetPath);
                } else {
                    continue;
                }
            }

            symlink($entry->getPathname(), $targetPath);
        }
    }

    private function copyDirectory(string $source, string $target): void
    {
        if (!is_dir($target)) {
            mkdir($target, 0755, true);
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $targetPath = $target.\DIRECTORY_SEPARATOR.$iterator->getSubPathname();

            if ($item->isDir()) {
                if (!is_dir($targetPath)) {
                    mkdir($targetPath, 0755, true);
                }
            } else {
                copy($item->getPathname(), $targetPath);
            }
        }
    }
}

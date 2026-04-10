<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Skills\Remote;

use GuzzleHttp\Client;

class GitHubSkillProvider
{
    private ?Client $client = null;

    /**
     * @return RemoteSkill[]
     */
    public function fetchSkills(GitHubRepository $repo): array
    {
        $tree = $this->fetchTree($repo);

        if (empty($tree)) {
            return [];
        }

        $skills = [];

        foreach ($tree as $entry) {
            if ($entry['type'] !== 'blob') {
                continue;
            }

            $path = $entry['path'] ?? '';

            if (basename($path) === 'SKILL.md' || basename($path) === 'SKILL.md.twig') {
                $skillName = basename(\dirname($path));
                $skillDir = \dirname($path);

                // Download files for this skill
                $skillFiles = $this->downloadSkillFiles($repo, $skillDir, $tree);
                $tempDir = sys_get_temp_dir().'/boost-skills/'.$skillName;

                if (!is_dir($tempDir)) {
                    mkdir($tempDir, 0755, true);
                }

                foreach ($skillFiles as $relativePath => $content) {
                    $filePath = $tempDir.'/'.$relativePath;
                    $fileDir = \dirname($filePath);

                    if (!is_dir($fileDir)) {
                        mkdir($fileDir, 0755, true);
                    }

                    file_put_contents($filePath, $content);
                }

                $skills[] = new RemoteSkill(
                    name: $skillName,
                    repo: $repo->fullName(),
                    path: $tempDir,
                );
            }
        }

        return $skills;
    }

    private function fetchTree(GitHubRepository $repo): array
    {
        $client = $this->getClient();

        try {
            $defaultBranch = $this->getDefaultBranch($repo);
            $response = $client->get(
                "https://api.github.com/repos/{$repo->owner()}/{$repo->repo()}/git/trees/{$defaultBranch}",
                ['query' => ['recursive' => '1']]
            );

            $data = json_decode($response->getBody()->getContents(), true);

            return $data['tree'] ?? [];
        } catch (\Throwable) {
            return [];
        }
    }

    private function getDefaultBranch(GitHubRepository $repo): string
    {
        $client = $this->getClient();

        try {
            $response = $client->get("https://api.github.com/repos/{$repo->owner()}/{$repo->repo()}");
            $data = json_decode($response->getBody()->getContents(), true);

            return $data['default_branch'] ?? 'main';
        } catch (\Throwable) {
            return 'main';
        }
    }

    /**
     * @return array<string, string>
     */
    private function downloadSkillFiles(GitHubRepository $repo, string $skillDir, array $tree): array
    {
        $files = [];
        $client = $this->getClient();

        foreach ($tree as $entry) {
            if ($entry['type'] !== 'blob') {
                continue;
            }

            $path = $entry['path'] ?? '';

            if (!str_starts_with($path, $skillDir.'/')) {
                continue;
            }

            $relativePath = substr($path, \strlen($skillDir) + 1);

            try {
                $rawUrl = "https://raw.githubusercontent.com/{$repo->owner()}/{$repo->repo()}/HEAD/{$path}";
                $response = $client->get($rawUrl);
                $files[$relativePath] = $response->getBody()->getContents();
            } catch (\Throwable) {
                // Skip files that fail to download
            }
        }

        return $files;
    }

    private function getClient(): Client
    {
        if ($this->client === null) {
            $headers = [
                'User-Agent' => 'SymfonyBoost/1.0',
                'Accept' => 'application/vnd.github.v3+json',
            ];

            $token = $_SERVER['GITHUB_TOKEN'] ?? getenv('GITHUB_TOKEN');

            if ($token !== false && $token !== '') {
                $headers['Authorization'] = "token {$token}";
            }

            $this->client = new Client([
                'headers' => $headers,
                'timeout' => 10,
            ]);
        }

        return $this->client;
    }
}

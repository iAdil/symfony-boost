<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Skills\Remote;

class GitHubRepository
{
    private string $owner;
    private string $repo;
    private ?string $path;

    public function __construct(string $input)
    {
        $parsed = $this->parse($input);

        if ($parsed === null) {
            throw new \InvalidArgumentException("Invalid GitHub repository: {$input}. Use 'owner/repo' or a GitHub URL.");
        }

        $this->owner = $parsed['owner'];
        $this->repo = $parsed['repo'];
        $this->path = $parsed['path'] ?? null;
    }

    public function owner(): string
    {
        return $this->owner;
    }

    public function repo(): string
    {
        return $this->repo;
    }

    public function path(): ?string
    {
        return $this->path;
    }

    public function fullName(): string
    {
        return "{$this->owner}/{$this->repo}";
    }

    private function parse(string $input): ?array
    {
        // Handle GitHub URLs
        if (str_contains($input, 'github.com')) {
            $path = parse_url($input, \PHP_URL_PATH);

            if ($path === null || $path === false) {
                return null;
            }

            $parts = array_values(array_filter(explode('/', trim($path, '/'))));

            if (\count($parts) < 2) {
                return null;
            }

            $result = [
                'owner' => $parts[0],
                'repo' => $parts[1],
            ];

            // Extract path after tree/branch/
            if (\count($parts) > 3 && $parts[2] === 'tree') {
                $result['path'] = implode('/', \array_slice($parts, 4));
            }

            return $result;
        }

        // Handle owner/repo format
        $parts = explode('/', $input, 3);

        if (\count($parts) < 2) {
            return null;
        }

        $result = [
            'owner' => $parts[0],
            'repo' => $parts[1],
        ];

        if (isset($parts[2])) {
            $result['path'] = $parts[2];
        }

        return $result;
    }
}

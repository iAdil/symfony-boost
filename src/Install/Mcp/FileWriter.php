<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Install\Mcp;

class FileWriter
{
    public function writeServer(string $filePath, string $serverName, array $serverConfig): void
    {
        $data = $this->readJsonFile($filePath);

        $data['mcpServers'] ??= [];
        $data['mcpServers'][$serverName] = $serverConfig;

        $this->writeJsonFile($filePath, $data);
    }

    public function serverExists(string $filePath, string $serverName): bool
    {
        $data = $this->readJsonFile($filePath);

        return isset($data['mcpServers'][$serverName]);
    }

    private function readJsonFile(string $filePath): array
    {
        if (!file_exists($filePath)) {
            return [];
        }

        $content = file_get_contents($filePath);
        $data = json_decode($content, true);

        return \is_array($data) ? $data : [];
    }

    private function writeJsonFile(string $filePath, array $data): void
    {
        $dir = \dirname($filePath);

        if ($dir !== '.' && !is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents(
            $filePath,
            json_encode($data, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES)."\n"
        );
    }
}

<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Install\Mcp;

class TomlFileWriter
{
    public function writeServer(string $filePath, string $serverName, array $serverConfig): void
    {
        $content = file_exists($filePath) ? file_get_contents($filePath) : '';

        $sectionHeader = "[mcp_servers.{$serverName}]";

        if (str_contains($content, $sectionHeader)) {
            return;
        }

        $tomlSection = "\n{$sectionHeader}\n";

        foreach ($serverConfig as $key => $value) {
            if (\is_array($value)) {
                $encoded = json_encode($value);
                $tomlSection .= "{$key} = {$encoded}\n";
            } elseif (\is_string($value)) {
                $tomlSection .= "{$key} = \"{$value}\"\n";
            } elseif (\is_bool($value)) {
                $tomlSection .= "{$key} = ".($value ? 'true' : 'false')."\n";
            } else {
                $tomlSection .= "{$key} = {$value}\n";
            }
        }

        $dir = \dirname($filePath);

        if ($dir !== '.' && !is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($filePath, $content.$tomlSection);
    }
}

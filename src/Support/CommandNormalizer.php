<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Support;

class CommandNormalizer
{
    /**
     * Splits a space-separated command string while preserving absolute paths.
     *
     * @return array{command: string, args: array<string>}
     */
    public static function normalize(string $command): array
    {
        $parts = preg_split('/\s+/', trim($command));

        if (empty($parts)) {
            return ['command' => $command, 'args' => []];
        }

        return [
            'command' => array_shift($parts),
            'args' => $parts,
        ];
    }
}

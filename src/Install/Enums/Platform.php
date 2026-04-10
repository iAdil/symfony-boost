<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Install\Enums;

enum Platform: string
{
    case Darwin = 'Darwin';
    case Linux = 'Linux';
    case Windows = 'Windows';

    public static function current(): self
    {
        return match (\PHP_OS_FAMILY) {
            'Darwin' => self::Darwin,
            'Windows' => self::Windows,
            default => self::Linux,
        };
    }
}

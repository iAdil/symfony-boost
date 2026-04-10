<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Install;

class Skill
{
    public function __construct(
        public readonly string $name,
        public readonly string $package,
        public readonly string $path,
        public readonly string $description = '',
    ) {
    }
}

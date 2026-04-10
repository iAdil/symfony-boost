<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Install\Detection;

use IAdil\SymfonyBoostBundle\Install\Enums\Platform;

interface DetectionStrategyInterface
{
    public function detect(array $config, ?Platform $platform = null): bool;
}

<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Install;

class GuidelineConfig
{
    public bool $enforceTests = true;

    /** @var string[] */
    public array $selectedPackages = [];
}

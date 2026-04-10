<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class SymfonyBoostBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}

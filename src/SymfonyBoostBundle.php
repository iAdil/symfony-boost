<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle;

use IAdil\SymfonyBoostBundle\DependencyInjection\SymfonyBoostExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class SymfonyBoostBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return new SymfonyBoostExtension();
    }
}

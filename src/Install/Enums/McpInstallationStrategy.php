<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Install\Enums;

enum McpInstallationStrategy: string
{
    case File = 'file';
    case Shell = 'shell';
    case None = 'none';
}

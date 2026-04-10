<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Install\Detection;

use IAdil\SymfonyBoostBundle\Install\Enums\Platform;
use Symfony\Component\Process\Process;

class CommandDetectionStrategy implements DetectionStrategyInterface
{
    public function detect(array $config, ?Platform $platform = null): bool
    {
        $command = $config['command'] ?? null;

        if ($command === null) {
            return false;
        }

        try {
            $process = Process::fromShellCommand($command);
            $process->setTimeout(5);
            $process->run();

            return $process->isSuccessful();
        } catch (\Throwable) {
            return false;
        }
    }
}

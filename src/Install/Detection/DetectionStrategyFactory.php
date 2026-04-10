<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Install\Detection;

class DetectionStrategyFactory
{
    public function makeFromConfig(array $config): DetectionStrategyInterface
    {
        $strategies = [];

        if (isset($config['command'])) {
            $strategies[] = new CommandDetectionStrategy();
        }

        if (isset($config['paths'])) {
            $strategies[] = new DirectoryDetectionStrategy();
        }

        if (isset($config['files'])) {
            $strategies[] = new FileDetectionStrategy();
        }

        if (\count($strategies) === 0) {
            return new class implements DetectionStrategyInterface {
                public function detect(array $config, ?\IAdil\SymfonyBoostBundle\Install\Enums\Platform $platform = null): bool
                {
                    return false;
                }
            };
        }

        if (\count($strategies) === 1) {
            return $strategies[0];
        }

        return new CompositeDetectionStrategy($strategies);
    }
}

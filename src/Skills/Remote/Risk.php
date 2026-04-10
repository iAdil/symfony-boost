<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Skills\Remote;

enum Risk: string
{
    case Critical = 'critical';
    case High = 'high';
    case Medium = 'medium';
    case Low = 'low';
    case Safe = 'safe';

    public function weight(): int
    {
        return match ($this) {
            self::Critical => 5,
            self::High => 4,
            self::Medium => 3,
            self::Low => 2,
            self::Safe => 1,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Critical => 'Critical',
            self::High => 'High',
            self::Medium => 'Medium',
            self::Low => 'Low',
            self::Safe => 'Safe',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Critical => 'red',
            self::High => 'yellow',
            self::Medium => 'yellow',
            self::Low => 'green',
            self::Safe => 'green',
        };
    }
}

<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Skills\Remote;

class AuditResult
{
    public function __construct(
        public readonly ?string $partner,
        public readonly Risk $risk,
        /** @var string[] */
        public readonly array $alerts = [],
        public readonly ?string $analyzedAt = null,
    ) {
    }
}

<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Install;

use IAdil\SymfonyBoostBundle\Install\Agent\AbstractAgent;
use IAdil\SymfonyBoostBundle\Install\Agent\AmpAgent;
use IAdil\SymfonyBoostBundle\Install\Agent\ClaudeCodeAgent;
use IAdil\SymfonyBoostBundle\Install\Agent\CodexAgent;
use IAdil\SymfonyBoostBundle\Install\Agent\CopilotAgent;
use IAdil\SymfonyBoostBundle\Install\Agent\CursorAgent;
use IAdil\SymfonyBoostBundle\Install\Agent\GeminiAgent;
use IAdil\SymfonyBoostBundle\Install\Agent\JunieAgent;
use IAdil\SymfonyBoostBundle\Install\Agent\OpenCodeAgent;
use IAdil\SymfonyBoostBundle\Install\Detection\DetectionStrategyFactory;
use IAdil\SymfonyBoostBundle\Install\Enums\Platform;

class AgentsDetector
{
    /** @var class-string<AbstractAgent>[] */
    private const AGENT_CLASSES = [
        AmpAgent::class,
        ClaudeCodeAgent::class,
        CodexAgent::class,
        CopilotAgent::class,
        CursorAgent::class,
        GeminiAgent::class,
        JunieAgent::class,
        OpenCodeAgent::class,
    ];

    public function __construct(
        private readonly string $projectDir,
        private readonly DetectionStrategyFactory $strategyFactory = new DetectionStrategyFactory(),
    ) {
    }

    /**
     * @return string[]
     */
    public function discoverSystemInstalledAgents(): array
    {
        $platform = Platform::current();
        $detected = [];

        foreach ($this->getAllAvailableAgents() as $agent) {
            if ($agent->detectOnSystem($platform)) {
                $detected[] = $agent->name();
            }
        }

        return $detected;
    }

    /**
     * @return string[]
     */
    public function discoverProjectInstalledAgents(string $basePath): array
    {
        $detected = [];

        foreach ($this->getAllAvailableAgents() as $agent) {
            if ($agent->detectInProject($basePath)) {
                $detected[] = $agent->name();
            }
        }

        return $detected;
    }

    /**
     * @return AbstractAgent[]
     */
    public function getAllAvailableAgents(): array
    {
        $agents = [];

        foreach (self::AGENT_CLASSES as $class) {
            $agents[] = new $class($this->strategyFactory);
        }

        return $agents;
    }

    public function findAgentByName(string $name): ?AbstractAgent
    {
        foreach ($this->getAllAvailableAgents() as $agent) {
            if ($agent->name() === $name) {
                return $agent;
            }
        }

        return null;
    }
}

<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Mcp\Tool;

use Mcp\Capability\Attribute\McpTool;
use IAdil\SymfonyBoostBundle\Service\LogReader;
use Symfony\Contracts\Cache\CacheInterface;

#[McpTool(name: 'last-error')]
class LastErrorTool
{
    public function __construct(
        private readonly LogReader $logReader,
        private readonly ?CacheInterface $cache = null,
    ) {
    }

    public function __invoke(): string
    {
        if ($this->cache !== null) {
            try {
                $cached = $this->cache->get('boost_last_error', fn () => null);

                if ($cached !== null && \is_array($cached)) {
                    $entry = "[{$cached['timestamp']}] {$cached['level']}: {$cached['message']}";

                    if (!empty($cached['context'])) {
                        $entry .= ' '.json_encode($cached['context']);
                    }

                    return $entry;
                }
            } catch (\Throwable) {
                // Cache may not be available
            }
        }

        $logFile = $this->logReader->resolveLogFilePath();

        if (!file_exists($logFile)) {
            return "Error: Log file not found at {$logFile}";
        }

        $entry = $this->logReader->readLastErrorEntry($logFile);

        if ($entry !== null) {
            if (mb_strlen($entry) > 500) {
                return mb_substr($entry, 0, 500).'... more logs';
            }

            return $entry;
        }

        return 'Error: Unable to find an ERROR entry in the inspected portion of the log file.';
    }
}

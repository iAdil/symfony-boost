<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Mcp\Tool;

use Mcp\Capability\Attribute\McpTool;
use IAdil\SymfonyBoostBundle\Service\LogReader;

#[McpTool(name: 'browser-logs')]
class BrowserLogsTool
{
    public function __construct(
        private readonly LogReader $logReader,
        private readonly string $logsDir,
    ) {
    }

    public function __invoke(int $entries): string
    {
        if ($entries <= 0) {
            return 'Error: The "entries" argument must be greater than 0.';
        }

        $logFile = $this->logsDir.\DIRECTORY_SEPARATOR.'browser.log';

        if (!file_exists($logFile)) {
            return 'No log file found, probably means no logs yet.';
        }

        $logEntries = $this->logReader->readLastLogEntries($logFile, $entries);

        if ($logEntries === []) {
            return 'Unable to retrieve log entries, or no logs';
        }

        $logs = implode("\n\n", $logEntries);

        if (empty(trim($logs))) {
            return 'No log entries yet.';
        }

        return $logs;
    }
}

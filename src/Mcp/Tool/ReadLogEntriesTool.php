<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Mcp\Tool;

use Mcp\Capability\Attribute\McpTool;
use IAdil\SymfonyBoostBundle\Service\LogReader;

#[McpTool(name: 'read-log-entries')]
class ReadLogEntriesTool
{
    public function __construct(
        private readonly LogReader $logReader,
    ) {
    }

    public function __invoke(int $entries): string
    {
        if ($entries <= 0) {
            return 'Error: The "entries" argument must be greater than 0.';
        }

        $logFile = $this->logReader->resolveLogFilePath();

        if (!file_exists($logFile)) {
            return "Error: Log file not found at {$logFile}";
        }

        $logEntries = $this->logReader->readLastLogEntries($logFile, $entries);

        if ($logEntries === []) {
            return 'Unable to retrieve log entries, or no entries yet.';
        }

        $logs = implode("\n\n", $logEntries);

        if (empty(trim($logs))) {
            return 'No log entries yet.';
        }

        return $logs;
    }
}

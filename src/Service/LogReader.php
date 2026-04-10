<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Service;

class LogReader
{
    public function __construct(
        private readonly string $logsDir,
        private readonly string $environment,
    ) {
    }

    public function getTimestampRegex(): string
    {
        return '\\[\\d{4}-\\d{2}-\\d{2} \\d{2}:\\d{2}:\\d{2}\\]';
    }

    public function getEntrySplitRegex(): string
    {
        return '/(?='.$this->getTimestampRegex().')/';
    }

    public function getErrorEntryRegex(): string
    {
        return '/^'.$this->getTimestampRegex().'.*\\.ERROR:/';
    }

    public function getChunkSizeStart(): int
    {
        return 64 * 1024;
    }

    public function getChunkSizeMax(): int
    {
        return 1024 * 1024;
    }

    public function resolveLogFilePath(): string
    {
        $dailyPath = $this->logsDir.\DIRECTORY_SEPARATOR.$this->environment.'-'.date('Y-m-d').'.log';

        if (file_exists($dailyPath)) {
            return $dailyPath;
        }

        $pattern = $this->logsDir.\DIRECTORY_SEPARATOR.$this->environment.'-*.log';
        $files = glob($pattern) ?: [];

        if (!empty($files)) {
            sort($files);

            return end($files);
        }

        $singlePath = $this->logsDir.\DIRECTORY_SEPARATOR.$this->environment.'.log';

        if (file_exists($singlePath)) {
            return $singlePath;
        }

        return $this->logsDir.\DIRECTORY_SEPARATOR.'dev.log';
    }

    public function isErrorEntry(string $line): bool
    {
        if (str_starts_with(trim($line), '{')) {
            return $this->isJsonErrorEntry($line);
        }

        return preg_match($this->getErrorEntryRegex(), $line) === 1;
    }

    /**
     * @return string[]
     */
    public function readLastLogEntries(string $logFile, int $count): array
    {
        $chunkSize = $this->getChunkSizeStart();

        do {
            $entries = $this->scanLogChunkForEntries($logFile, $chunkSize);

            if (\count($entries) >= $count || $chunkSize >= $this->getChunkSizeMax()) {
                break;
            }

            $chunkSize *= 2;
        } while (true);

        return \array_slice($entries, -$count);
    }

    public function readLastErrorEntry(string $logFile): ?string
    {
        $chunkSize = $this->getChunkSizeStart();

        do {
            $entries = $this->scanLogChunkForEntries($logFile, $chunkSize);

            for ($i = \count($entries) - 1; $i >= 0; $i--) {
                if ($this->isErrorEntry($entries[$i])) {
                    return trim((string) $entries[$i]);
                }
            }

            if ($chunkSize >= $this->getChunkSizeMax()) {
                return null;
            }

            $chunkSize *= 2;
        } while (true);
    }

    public function isJsonLogFormat(string $content): bool
    {
        $firstLine = strtok($content, "\n");

        if ($firstLine === false || trim($firstLine) === '') {
            return false;
        }

        $trimmed = trim($firstLine);

        if (!str_starts_with($trimmed, '{')) {
            return false;
        }

        json_decode($trimmed);

        return json_last_error() === \JSON_ERROR_NONE;
    }

    public function isJsonErrorEntry(string $entry): bool
    {
        $decoded = json_decode(trim($entry), true);

        if (!\is_array($decoded)) {
            return false;
        }

        $level = $decoded['level'] ?? $decoded['level_name'] ?? '';

        return strtoupper((string) $level) === 'ERROR' || (int) ($decoded['level'] ?? 0) >= 400;
    }

    /**
     * @return string[]
     */
    public function scanLogChunkForEntries(string $logFile, int $chunkSize): array
    {
        $fileSize = filesize($logFile);

        if ($fileSize === false) {
            return [];
        }

        $handle = fopen($logFile, 'r');

        if (!$handle) {
            return [];
        }

        try {
            $offset = max($fileSize - $chunkSize, 0);
            fseek($handle, $offset);

            if ($offset > 0) {
                fgets($handle);
            }

            $content = stream_get_contents($handle);

            if ($this->isJsonLogFormat($content)) {
                return array_values(array_filter(
                    explode("\n", $content),
                    fn (string $line): bool => trim($line) !== '',
                ));
            }

            $entries = preg_split($this->getEntrySplitRegex(), $content, -1, \PREG_SPLIT_NO_EMPTY);

            if (!$entries) {
                return [];
            }

            return $entries;
        } finally {
            fclose($handle);
        }
    }
}

<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BrowserLogController
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function receive(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $logs = $data['logs'] ?? [];

        if (empty($logs) && !$request->isXmlHttpRequest()) {
            $decoded = json_decode($request->getContent(), true);
            $logs = $decoded['logs'] ?? [];
        }

        foreach ($logs as $log) {
            $level = match ($log['type'] ?? 'debug') {
                'warn' => 'warning',
                'log', 'table' => 'debug',
                'window_error', 'uncaught_error', 'unhandled_rejection' => 'error',
                default => $log['type'] ?? 'debug',
            };

            $message = $this->buildLogMessageFromData($log['data'] ?? []);

            $this->logger->log($level, '[browser] '.$message, [
                'url' => $log['url'] ?? '',
                'user_agent' => $log['userAgent'] ?? null,
                'timestamp' => $log['timestamp'] ?? (new \DateTimeImmutable())->format(\DATE_ATOM),
            ]);
        }

        return new JsonResponse(['status' => 'logged']);
    }

    private function buildLogMessageFromData(array $data): string
    {
        $messages = [];

        foreach ($data as $value) {
            $messages[] = match (true) {
                \is_array($value) => $this->buildLogMessageFromData($value),
                \is_string($value), is_numeric($value) => (string) $value,
                \is_bool($value) => $value ? 'true' : 'false',
                $value === null => 'null',
                \is_object($value) => json_encode($value),
                default => (string) $value,
            };
        }

        return implode(' ', $messages);
    }
}

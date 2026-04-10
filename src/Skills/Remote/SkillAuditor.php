<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Skills\Remote;

use GuzzleHttp\Client;

class SkillAuditor
{
    public function audit(GitHubRepository $repo): ?AuditResult
    {
        try {
            $client = new Client([
                'headers' => [
                    'User-Agent' => 'SymfonyBoost/1.0',
                    'Accept' => 'application/json',
                ],
                'timeout' => 3,
                'verify' => false,
            ]);

            $response = $client->post('https://boost.laravel.com/api/skills/audit', [
                'json' => [
                    'repository' => $repo->fullName(),
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                return null;
            }

            $data = json_decode($response->getBody()->getContents(), true);

            if (!\is_array($data)) {
                return null;
            }

            $risk = Risk::tryFrom($data['risk'] ?? 'safe') ?? Risk::Safe;

            return new AuditResult(
                partner: $data['partner'] ?? null,
                risk: $risk,
                alerts: $data['alerts'] ?? [],
                analyzedAt: $data['analyzed_at'] ?? null,
            );
        } catch (\Throwable) {
            return null;
        }
    }
}

<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\Install;

class ThirdPartyPackage
{
    /**
     * @return array<string, array{name: string, label: string, features: string[]}>
     */
    public static function discoverForSymfony(string $projectDir): array
    {
        $packages = [];
        $lockFile = $projectDir.'/composer.lock';

        if (!file_exists($lockFile)) {
            return $packages;
        }

        $lockData = json_decode(file_get_contents($lockFile), true);

        if (!\is_array($lockData)) {
            return $packages;
        }

        $allPackages = array_merge(
            $lockData['packages'] ?? [],
            $lockData['packages-dev'] ?? [],
        );

        $firstPartyPrefixes = ['symfony/', 'doctrine/', 'twig/'];

        foreach ($allPackages as $package) {
            $name = $package['name'] ?? '';

            $isFirstParty = false;

            foreach ($firstPartyPrefixes as $prefix) {
                if (str_starts_with($name, $prefix)) {
                    $isFirstParty = true;

                    break;
                }
            }

            if ($isFirstParty) {
                continue;
            }

            // Check if package has .ai directory with guidelines or skills
            $vendorPath = $projectDir.'/vendor/'.$name;
            $features = [];

            if (is_dir($vendorPath.'/.ai/guidelines')) {
                $features[] = 'guidelines';
            }

            if (is_dir($vendorPath.'/.ai/skills')) {
                $features[] = 'skills';
            }

            if (!empty($features)) {
                $packages[$name] = [
                    'name' => $name,
                    'label' => self::humanize($name),
                    'features' => $features,
                ];
            }
        }

        return $packages;
    }

    private static function humanize(string $packageName): string
    {
        $parts = explode('/', $packageName);
        $name = end($parts);

        return ucwords(str_replace(['-', '_'], ' ', $name));
    }
}

<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SymfonyBoostExtension extends Extension
{
    public function getAlias(): string
    {
        return 'iadil_symfony_boost';
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('iadil_symfony_boost.enabled', $config['enabled']);
        $container->setParameter('iadil_symfony_boost.browser_logs_watcher', $config['browser_logs_watcher']);
        $container->setParameter('iadil_symfony_boost.executable_paths', $config['executable_paths']);
        $container->setParameter('iadil_symfony_boost.hosted.api_url', $config['hosted']['api_url']);
        $container->setParameter('iadil_symfony_boost.mcp.tools.exclude', $config['mcp']['tools']['exclude']);
        $container->setParameter('iadil_symfony_boost.mcp.tools.include', $config['mcp']['tools']['include']);
        $container->setParameter('iadil_symfony_boost.mcp.resources.exclude', $config['mcp']['resources']['exclude']);
        $container->setParameter('iadil_symfony_boost.mcp.resources.include', $config['mcp']['resources']['include']);
        $container->setParameter('iadil_symfony_boost.mcp.prompts.exclude', $config['mcp']['prompts']['exclude']);
        $container->setParameter('iadil_symfony_boost.mcp.prompts.include', $config['mcp']['prompts']['include']);

        if (!$config['enabled']) {
            return;
        }

        $loader = new YamlFileLoader($container, new FileLocator(\dirname(__DIR__, 2).'/config'));
        $loader->load('services.yaml');

        if (!$config['browser_logs_watcher']) {
            $container->removeDefinition(\IAdil\SymfonyBoostBundle\Service\BrowserLogger::class);
            $container->removeDefinition(\IAdil\SymfonyBoostBundle\EventListener\InjectBrowserLoggerListener::class);
            $container->removeDefinition(\IAdil\SymfonyBoostBundle\Controller\BrowserLogController::class);
        }
    }
}

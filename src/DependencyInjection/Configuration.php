<?php

declare(strict_types=1);

namespace IAdil\SymfonyBoostBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('iadil_symfony_boost');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->booleanNode('enabled')
                    ->defaultTrue()
                    ->info('Master switch to enable/disable all Boost functionality.')
                ->end()
                ->booleanNode('browser_logs_watcher')
                    ->defaultTrue()
                    ->info('Enable or disable the browser logs watcher feature.')
                ->end()
                ->arrayNode('executable_paths')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('php')->defaultNull()->end()
                        ->scalarNode('composer')->defaultNull()->end()
                        ->scalarNode('npm')->defaultNull()->end()
                        ->scalarNode('vendor_bin')->defaultNull()->end()
                        ->scalarNode('current_directory')->defaultNull()->end()
                    ->end()
                ->end()
                ->arrayNode('hosted')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('api_url')
                            ->defaultValue('https://boost.laravel.com')
                            ->info('URL of the hosted documentation search API.')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('mcp')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('tools')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('exclude')
                                    ->scalarPrototype()->end()
                                    ->defaultValue([])
                                ->end()
                                ->arrayNode('include')
                                    ->scalarPrototype()->end()
                                    ->defaultValue([])
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('resources')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('exclude')
                                    ->scalarPrototype()->end()
                                    ->defaultValue([])
                                ->end()
                                ->arrayNode('include')
                                    ->scalarPrototype()->end()
                                    ->defaultValue([])
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('prompts')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('exclude')
                                    ->scalarPrototype()->end()
                                    ->defaultValue([])
                                ->end()
                                ->arrayNode('include')
                                    ->scalarPrototype()->end()
                                    ->defaultValue([])
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

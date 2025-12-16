<?php

namespace Inspyrenees\UmamiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('umami');

        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('url')
            ->isRequired()
            ->cannotBeEmpty()
            ->info('Umami instance URL (e.g., https://analytics.example.com)')
            ->end()
            ->scalarNode('username')
            ->isRequired()
            ->cannotBeEmpty()
            ->info('Umami API username')
            ->end()
            ->scalarNode('password')
            ->isRequired()
            ->cannotBeEmpty()
            ->info('Umami API password')
            ->end()
            ->scalarNode('website_id')
            ->isRequired()
            ->cannotBeEmpty()
            ->info('Umami website ID to track')
            ->end()
            ->integerNode('default_days_back')
            ->defaultValue(30)
            ->min(1)
            ->info('Default number of days to retrieve metrics for')
            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

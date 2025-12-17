<?php

namespace Inspyrenees\UmamiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('umami');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->scalarNode('url')->defaultValue('%env(UMAMI_URL)%')->end()
            ->scalarNode('username')->defaultValue('%env(UMAMI_USERNAME)%')->end()
            ->scalarNode('password')->defaultValue('%env(UMAMI_PASSWORD)%')->end()
            ->scalarNode('website_id')->defaultValue('%env(UMAMI_WEBSITE_ID)%')->end()
            ->integerNode('default_days_back')->defaultValue(30)->end()
            ->end();

        return $treeBuilder;
    }
}

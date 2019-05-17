<?php

namespace WorldFactory\QQ\DependencyInjection\Configurations;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class PDOConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('qq_pdo');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('connections')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('dsn')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('user')->defaultValue('root')->end()
                            ->scalarNode('pass')->defaultNull()->end()
                            ->booleanNode('persist')->defaultTrue()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
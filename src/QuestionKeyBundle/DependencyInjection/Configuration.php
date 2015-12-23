<?php

namespace QuestionKeyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
*  @license 3-clause BSD
*  @link https://github.com/QuestionKey/QuestionKey-Core
*
*/
class Configuration implements ConfigurationInterface
{

    /**
    * {@inheritDoc}
    */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('questionkey_core');
        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $rootNode
            ->children()
            ->scalarNode('server_host')->defaultValue('')->end()
            ->end();
        $rootNode
            ->children()
            ->booleanNode('has_ssl')->defaultValue(false)->end()
            ->end();
        return $treeBuilder;
    }

}

<?php

namespace NS\UtilBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;


class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('ns_util');
        $rootNode    = $treeBuilder->getRootNode();
        $rootNode->children()->scalarNode('template')->defaultValue('@NSUtil/Ajax/autocomplete.json.twig')->end();

        return $treeBuilder;
    }
}

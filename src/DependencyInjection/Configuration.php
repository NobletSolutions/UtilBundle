<?php

namespace NS\UtilBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        /**
         * Instantiating a new TreeBuilder without a constructor arg is deprecated in SF4 and removed in SF5
         */
        if(method_exists(TreeBuilder::class, '__construct'))
        {
            $treeBuilder = new TreeBuilder('ns_util');
            $rootNode = $treeBuilder->getRootNode();
        }
        /**
         * Included for backward-compatibility with SF3
         */
        else
        {
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('ns_util');
        }

        $rootNode->children()->scalarNode('template')->defaultValue('NSUtilBundle:Ajax:autocomplete.json.twig')->end();

        return $treeBuilder;
    }
}

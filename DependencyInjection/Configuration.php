<?php

namespace Cowlby\Bundle\BootstrapBundle\DependencyInjection;

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
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('cowlby_bootstrap');

        $rootNode
            ->children()
            ->scalarNode('assets_dir')
                ->defaultValue('%kernel.root_dir%/../vendor/twbs/bootstrap')
            ->end()
            ->arrayNode('dist_mode')
                ->addDefaultsIfNotSet()
                ->treatTrueLike(array('enabled' => true))
                ->treatFalseLike(array('enabled' => false))
                ->children()
                    ->booleanNode('enabled')->defaultValue('%kernel.debug%')->end()
                    ->booleanNode('use_minified')->defaultTrue()->end()
                ->end()
            ->end()
            ->arrayNode('plugins')
                ->children()
                    ->booleanNode('affix')->defaultFalse()->end()
                    ->booleanNode('alert')->defaultFalse()->end()
                    ->booleanNode('button')->defaultFalse()->end()
                    ->booleanNode('carousel')->defaultFalse()->end()
                    ->booleanNode('collapse')->defaultFalse()->end()
                    ->booleanNode('dropdown')->defaultFalse()->end()
                    ->booleanNode('modal')->defaultFalse()->end()
                    ->booleanNode('popover')->defaultFalse()->end()
                    ->booleanNode('scrollspy')->defaultFalse()->end()
                    ->booleanNode('tab')->defaultFalse()->end()
                    ->booleanNode('tooltip')->defaultFalse()->end()
                    ->booleanNode('transition')->defaultFalse()->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

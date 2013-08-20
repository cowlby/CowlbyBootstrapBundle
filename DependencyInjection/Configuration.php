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
                    ->canBeDisabled()
                    ->children()
                        ->booleanNode('use_minified')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('theme')
                    ->canBeEnabled()
                ->end()
            ->end()
            ->append($this->addLessFilesNode())
            ->append($this->addPluginsNode())
        ;

        return $treeBuilder;
    }

    public function addLessFilesNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('less_files');

        $node
            ->addDefaultsIfNotSet()
            ->treatTrueLike(array(
                'components' => true,
                'javascript_components' => true,
                'utilities' => true
            ))
            ->treatFalseLike(array(
                'components' => false,
                'javascript_components' => false,
                'utilities' => false
            ))
            ->append($this->addFileNode('common_css', array(
                'print_media_styles',
                'typography',
                'code',
                'grid_system',
                'tables',
                'forms',
                'buttons'
            )))
            ->append($this->addFileNode('components', array(
                'glyphicons',
                'button_groups',
                'input_groups',
                'navs',
                'navbar',
                'breadcrumbs',
                'pagination',
                'pager',
                'labels',
                'badges',
                'jumbotron',
                'thumbnails',
                'alerts',
                'progress_bars',
                'media_items',
                'list_groups',
                'panels',
                'wells',
                'close_icon'
            )))
            ->append($this->addFileNode('javascript_components', array(
                'dropdowns',
                'tooltips',
                'popovers',
                'modals',
                'carousel'
            )))
            ->append($this->addFileNode('utilities', array(
                'basic_utilities',
                'responsive_utilities',
                'component_animations'
            )))
        ->end();

        return $node;
    }

    public function addPluginsNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('plugins');

        $node
            ->addDefaultsIfNotSet()
            ->treatTrueLike(array(
                'components' => true,
                'magic' => true
            ))
            ->treatFalseLike(array(
                'components' => false,
                'magic' => false
            ))
            ->append($this->addFileNode('components', array(
                'alert_dismissal',
                'advanced_buttons',
                'carousel',
                'dropdowns',
                'modals',
                'popovers',
                'tabs',
                'tooltips'
            )))
            ->append($this->addFileNode('magic', array(
                'affix',
                'collapse',
                'scrollspy',
                'transitions'
            )))
        ;

        return $node;
    }

    public function addFileNode($name, array $files)
    {
        $builder = new TreeBuilder();
        $node = $builder->root($name);

        $node->treatTrueLike(array_fill_keys($files, true));
        $node->treatFalseLike(array_fill_keys($files, false));

        foreach ($files as $file) {
            $node->children()->booleanNode($file)->defaultFalse()->end();
        }

        $node->children()->end();

        return $node;
    }
}

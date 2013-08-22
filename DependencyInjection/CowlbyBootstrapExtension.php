<?php

namespace Cowlby\Bundle\BootstrapBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CowlbyBootstrapExtension extends Extension implements PrependExtensionInterface
{
    protected static $lessFiles = array(
        'common_css' => array(
            'print_media_styles' => array('print.less'),
            'typography'         => array('type.less'),
            'code'               => array('code.less'),
            'grid_system'        => array('grid.less'),
            'tables'             => array('tables.less'),
            'forms'              => array('forms.less'),
            'buttons'            => array('buttons.less')
        ),
        'components' => array(
            'glyphicons'    => array('glyphicons.less'),
            'button_groups' => array('buttons.less', 'button-groups.less'),
            'input_groups'  => array('forms.less', 'input-groups.less'),
            'navs'          => array('navs.less'),
            'navbar'        => array('forms.less', 'utilities.less', 'navs.less', 'navbar.less'),
            'breadcrumbs'   => array('breadcrumbs.less'),
            'pagination'    => array('pagination.less'),
            'pager'         => array('pager.less'),
            'labels'        => array('labels.less'),
            'badges'        => array('badges.less'),
            'jumbotron'     => array('jumbotron.less'),
            'thumbnails'    => array('thumbnails.less'),
            'alerts'        => array('alerts.less'),
            'progress_bars' => array('progress-bars.less'),
            'media_items'   => array('media.less'),
            'list_groups'   => array('list-group.less'),
            'panels'        => array('panels.less'),
            'wells'         => array('wells.less'),
            'close_icon'    => array('close.less')
        ),
        'javascript_components' => array(
            'dropdowns' => array('dropdowns.less'),
            'tooltips'  => array('tooltip.less'),
            'popovers'  => array('popovers.less'),
            'modals'    => array('modals.less'),
            'carousel'  => array('carousel.less')
        ),
        'utilities' => array(
            'basic_utilities'      => array('utilities.less'),
            'responsive_utilities' => array('responsive-utilities.less'),
            'component_animations' => array('component-animations.less')
        )
    );

    protected static $plugins = array(
        'components' => array(
            'alert_dismissal'  => array('alert.js'),
            'advanced_buttons' => array('button.js'),
            'carousel'         => array('carousel.js'),
            'dropdowns'        => array('dropdown.js'),
            'modals'           => array('modal.js'),
            'popovers'         => array('tooltip.js', 'popover.js'),
            'tabs'             => array('tab.js'),
            'tooltips'         => array('tooltip.js'),
        ),
        'magic' => array(
            'affix'       => array('affix.js'),
            'collapse'    => array('collapse.js'),
            'scrollspy'   => array('scrollspy.js'),
            'transitions' => array('transition.js')
        )
    );

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        $assetic = array();
        $assetic['filters']['cssrewrite'] = null;

        if ($config['dist_mode']['enabled']) {

            $jsFile = $config['dist_mode']['use_minified'] ? 'bootstrap.min.js' : 'bootstrap.js';
            $cssFile = $config['dist_mode']['use_minified'] ? 'bootstrap.min.css' : 'bootstrap.css';

            $assetic['assets']['bootstrap_js']['inputs'][] = $config['assets_dir'] . "/dist/js/$jsFile";
            $assetic['assets']['bootstrap_css']['inputs'][] = $config['assets_dir'] . "/dist/css/$cssFile";
            $assetic['assets']['bootstrap_css']['filters'][] = 'cssrewrite';

            if ($config['theme']['enabled']) {

                $themeFile = $config['dist_mode']['use_minified'] ? 'bootstrap-theme.min.css' : 'bootstrap-theme.css';
                $assetic['assets']['bootstrap_css']['inputs'][] = $config['assets_dir'] . "/dist/css/$themeFile";
            }

        } else {

            $cssFiles = array('variables.less', 'mixins.less', 'normalize.less', 'scaffolding.less');
            foreach ($config['less_files'] as $section => $files) {
                foreach ($files as $file => $enabled) {
                    if ($enabled) {
                        foreach (self::$lessFiles[$section][$file] as $lessFile) {
                            $cssFiles[] = $lessFile;
                        }
                    }
                }
            }

            $bootstrapCss = $container->getParameter('kernel.cache_dir') . '/cowlby_bootstrap/bootstrap.less';
            $handle = fopen($bootstrapCss, 'w+');

            foreach (array_unique($cssFiles) as $file) {
                fwrite($handle, sprintf("@import \"%s/less/%s\";\n", $config['assets_dir'], $file));
            }

            $assetic['assets']['bootstrap_css']['filters'][] = 'cssrewrite';
            $assetic['assets']['bootstrap_css']['inputs'][] = $bootstrapCss;

            if ($config['theme']['enabled']) {
                $assetic['assets']['bootstrap_css']['inputs'][] = $config['assets_dir'] . '/less/theme.less';
            }

            $jsInputs = array();
            foreach ($config['plugins'] as $section => $files) {
                foreach ($files as $file => $enabled) {
                    if ($enabled) {
                        foreach (self::$plugins[$section][$file] as $plugin) {
                            $jsInputs[] = $config['assets_dir'] . '/js/' . $plugin;
                        }
                    }
                }
            }

            if (count($jsInputs) > 0) {
                $assetic['assets']['bootstrap_js']['inputs'] = array_unique($jsInputs);
            }
        }

        print_r($assetic);
        die;

        $container->prependExtensionConfig('assetic', $assetic);
    }
}

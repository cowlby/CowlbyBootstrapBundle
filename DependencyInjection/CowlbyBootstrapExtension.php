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
        $rootDir = '%kernel.root_dir%/../vendor/twitter/bootstrap';

        if ($config['dist_mode']['enabled']) {

            $jsFile = $config['dist_mode']['use_minified'] ? 'bootstrap.min.js' : 'bootstrap.js';
            $cssFile = $config['dist_mode']['use_minified'] ? 'bootstrap.min.css' : 'bootstrap.css';

            $assetic['assets']['bootstrap_js']['inputs'][] = $rootDir . "/dist/js/$jsFile";
            $assetic['assets']['bootstrap_css']['inputs'][] = $rootDir . "/dist/css/$cssFile";
            $assetic['assets']['bootstrap_css']['filters'][] = 'cssrewrite';

        } else {

            $assetic['assets']['bootstrap_css']['inputs'][] = $rootDir . '/less/bootstrap.less';
            $assetic['assets']['bootstrap_css']['filters'][] = 'cssrewrite';

            foreach ($config['plugins'] as $plugin => $enabled) {
                if ($enabled) {
                    $assetic['assets']['bootstrap_js']['inputs'][] = $rootDir . "/js/$plugin.js";
                }
            }
        }

        $container->prependExtensionConfig('assetic', $assetic);
    }
}

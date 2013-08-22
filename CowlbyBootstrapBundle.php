<?php

namespace Cowlby\Bundle\BootstrapBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CowlbyBootstrapBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $cacheDir = $container->getParameter('kernel.cache_dir') . '/cowlby_bootstrap';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir);
        }
    }
}

<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tadcka\Mapper\Cache\MapperDataCacheInterface;
use Tadcka\Mapper\Mapper;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 1/27/15 10:22 PM
 */
class MapperDataCachePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('tadcka_mapper.cache.data')) {
            return null;
        }

        $definition = $container->getDefinition('tadcka_mapper.cache.data');

        $definition->replaceArgument(0, $this->getCacheDir($container));
    }

    private function getCacheDir(ContainerBuilder $container)
    {
        return implode(
            '/',
            [
                $container->getParameter('kernel.cache_dir'),
                Mapper::NAME,
                MapperDataCacheInterface::SUB_DIR
            ]
        );
    }
}

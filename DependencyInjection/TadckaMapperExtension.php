<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadcka <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 14.7.12 14.11
 */
class TadckaMapperExtension extends Extension
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
        $loader->load('cache.xml');
        $loader->load('frontend/tree.xml');

        if (!in_array(strtolower($config['db_driver']), array('mongodb', 'orm'))) {
            throw new \InvalidArgumentException(sprintf('Invalid db driver "%s".', $config['db_driver']));
        }
        $loader->load('driver/' . sprintf('%s.xml', $config['db_driver']));

        $container->setParameter('tadcka_mapper.model.category.class', $config['class']['model']['category']);
        $container->setParameter('tadcka_mapper.model.mapping.class', $config['class']['model']['mapping']);
        $container->setParameter('tadcka_mapper.model.source.class', $config['class']['model']['source']);

        $container->setAlias('tadcka_mapper.manager.category', $config['category_manager']);
        $container->setAlias('tadcka_mapper.manager.mapping', $config['mapping_manager']);
        $container->setAlias('tadcka_mapper.manager.source', $config['source_manager']);

        $container->setAlias('tadcka_mapper.provider', $config['mapper_provider']);
        $container->setAlias('tadcka_mapper.frontend.manager.tree', $config['frontend']['tree_manager']);
    }
}

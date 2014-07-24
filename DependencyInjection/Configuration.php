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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 14.7.12 14.11
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('tadcka_mapper');

        $rootNode
            ->children()
                ->scalarNode('db_driver')->cannotBeOverwritten()->isRequired()->end()
                ->scalarNode('category_manager')->defaultValue('tadcka_mapper.manager.category.default')
                    ->cannotBeEmpty()->end()
                ->scalarNode('mapping_manager')->defaultValue('tadcka_mapper.manager.mapping.default')
                    ->cannotBeEmpty()->end()
                ->scalarNode('source_manager')->defaultValue('tadcka_mapper.manager.source.default')
                    ->cannotBeEmpty()->end()
                ->scalarNode('mapper_provider')->defaultValue('tadcka_mapper.provider.default')
                    ->cannotBeEmpty()->end()
                ->arrayNode('frontend')->isRequired()
                    ->children()
                        ->scalarNode('tree_manager')->defaultValue('tadcka_mapper.frontend.manager.tree.default')
                            ->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('class')->isRequired()
                    ->children()
                        ->arrayNode('model')->isRequired()
                            ->children()
                                ->scalarNode('category')->isRequired()->end()
                                ->scalarNode('mapping')->isRequired()->end()
                                ->scalarNode('source')->isRequired()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}

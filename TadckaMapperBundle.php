<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadcka <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tadcka\Bundle\MapperBundle\DependencyInjection\Compiler\MapperDataCachePass;
use Tadcka\Bundle\MapperBundle\DependencyInjection\Compiler\MapperSourceTypeRegistryPass;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 14.7.12 14.11
 */
class TadckaMapperBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new MapperDataCachePass());
        $container->addCompilerPass(new MapperSourceTypeRegistryPass());

        if (false === $container->hasExtension('jms_serializer')) {
            throw new \RuntimeException('JMSSerializerBundle must be registered in kernel.');
        }

        $this->addSerializerMapping($container);
//        $this->addRegisterMappingsPass($container);
    }

    /**
     * Add serializer mapping.
     *
     * @param ContainerBuilder $container
     */
    private function addSerializerMapping(ContainerBuilder $container)
    {
        $directories = array(
            'tadcka-mapper' => array(
                'namespace_prefix' => 'Tadcka\\Mapper',
                'path' => '@TadckaMapperBundle/Resources/config/serializer/lib'
            ),
        );

        $container->prependExtensionConfig(
            'jms_serializer',
            array(
                'metadata' => array(
                    'directories' => $directories
                )
            )
        );
    }

    /**
     * Add register mappings pass.
     *
     * @param ContainerBuilder $container
     */
    private function addRegisterMappingsPass(ContainerBuilder $container)
    {
        $mappings = array(
            realpath(__DIR__ . '/Resources/config/doctrine/model') => 'Tadcka\Component\Mapper\Model',
        );

        $ormCompilerClass = 'Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass';
        if (class_exists($ormCompilerClass)) {
            $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings));
        }
    }
}

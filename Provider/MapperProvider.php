<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadcka <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Provider;

use Tadcka\Component\Mapper\Exception\ResourceNotFoundException;
use Tadcka\Component\Mapper\Model\Manager\SourceManagerInterface;
use Tadcka\Component\Mapper\Model\SourceInterface;
use Tadcka\Component\Mapper\Provider\MapperProviderInterface;
use Tadcka\Component\Mapper\Registry\Config\Config;
use Tadcka\Component\Mapper\Registry\Registry;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 7/13/14 4:29 PM
 */
class MapperProvider implements MapperProviderInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var SourceManagerInterface
     */
    private $sourceManager;

    /**
     * Constructor.
     *
     * @param Registry $registry
     * @param SourceManagerInterface $sourceManager
     */
    public function __construct(Registry $registry, SourceManagerInterface $sourceManager)
    {
        $this->registry = $registry;
        $this->sourceManager = $sourceManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource($name)
    {
        $source = $this->sourceManager->findBySlug($name);

        if ((null === $source) && (null !== $config = $this->getConfig($name))) {
            $source = $this->sourceManager->create();
            $source->setSlug($config->getName());
        } elseif ((null !== $source) && (false === $config = $this->registry->getContainer()->has($name))) {
            $this->sourceManager->remove($source);

            return null;
        }

        return $source;
    }

    /**
     * {@inheritdoc}
     */
    public function getMapper(SourceInterface $source, $locale)
    {
        $config = $this->getConfig($source->getSlug());
        if ((null !== $config) && (null !== $mapper = $config->getFactory()->create()->getMapper($locale))) {
            return $mapper;
        }

        throw new ResourceNotFoundException('Not found mapper ' . $source->getSlug() . '!');
    }

    /**
     * Get mapper config.
     *
     * @param string $name
     *
     * @return null|Config
     */
    private function getConfig($name)
    {
        return $this->registry->getContainer()->get($name);
    }
}

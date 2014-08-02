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
use Tadcka\Component\Mapper\MapperItemInterface;
use Tadcka\Component\Mapper\Model\CategoryInterface;
use Tadcka\Component\Mapper\Model\Manager\MappingManagerInterface;
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
     * @var MappingManagerInterface
     */
    private $mappingManager;

    /**
     * Constructor.
     *
     * @param Registry $registry
     * @param SourceManagerInterface $sourceManager
     * @param MappingManagerInterface $mappingManager
     */
    public function __construct(
        Registry $registry,
        SourceManagerInterface $sourceManager,
        MappingManagerInterface $mappingManager
    ) {
        $this->registry = $registry;
        $this->sourceManager = $sourceManager;
        $this->mappingManager = $mappingManager;
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

            $this->sourceManager->add($source);
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
     * {@inheritdoc}
     */
    public function getMappingCategories(CategoryInterface $category, SourceInterface $source)
    {
        $mappings = $this->mappingManager->findManyMappings($category->getSlug(), $source->getSlug());
        $data = array();
        foreach ($mappings as $mapping) {
            if ($category->getSlug() === $mapping->getLeft()->getSlug()) {
                $data[] = $mapping->getRight();
            } elseif ($category->getSlug() === $mapping->getRight()->getSlug()) {
                $data[] = $mapping->getLeft();
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getMapperItems(array $categories, MapperItemInterface $mapperItem)
    {
        $items = array();
        /** @var CategoryInterface $category */
        foreach ($categories as $category) {
            if (null !== $item = $this->getMapperItemByCategory($category->getSlug(), $mapperItem)) {
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function getMapperItemByCategory($categorySlug, MapperItemInterface $mapperItem)
    {
        if ((string)$categorySlug === (string)$mapperItem->getSlug()) {
            return $mapperItem;
        }

        foreach ($mapperItem->getChildren() as $child) {
            if (null !== $item = $this->getMapperItemByCategory($categorySlug, $child)) {
                return $item;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getMappingMainCategorySlug($currentCategorySlug, $otherSourceSlug)
    {
        $mapping = $this->mappingManager->findMainMapping($currentCategorySlug, $otherSourceSlug);

        if (null !== $mapping) {
            if ($currentCategorySlug === $mapping->getLeft()->getSlug()) {
                return $mapping->getRight()->getSlug();
            }

            return $mapping->getLeft()->getSlug();
        }

        return null;
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

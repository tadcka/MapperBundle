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

use Tadcka\Component\Mapper\Cache\MapperItemCacheInterface;
use Tadcka\Component\Mapper\Model\CategoryInterface;
use Tadcka\Component\Mapper\Model\SourceInterface;
use Tadcka\Component\Mapper\Provider\MapperProviderInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 7/24/14 8:41 PM
 */
class MapperProviderWrapper implements MapperProviderInterface
{
    /**
     * @var MapperProviderInterface
     */
    private $mapperProvider;

    /**
     * @var MapperItemCacheInterface
     */
    private $mapperItemCache;

    /**
     * Constructor.
     *
     * @param MapperProviderInterface $mapperProvider
     * @param MapperItemCacheInterface $mapperItemCache
     */
    public function __construct(MapperProviderInterface $mapperProvider, MapperItemCacheInterface $mapperItemCache)
    {
        $this->mapperProvider = $mapperProvider;
        $this->mapperItemCache = $mapperItemCache;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource($name)
    {
        return $this->mapperProvider->getSource($name);
    }
    /**
     * {@inheritdoc}
     */
    public function getMapper(SourceInterface $source, $locale)
    {
        if (null !== $mapper = $this->mapperItemCache->fetch($source, $locale)) {
            return $mapper;
        }

        return $this->mapperProvider->getMapper($source, $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getMappingCategories(CategoryInterface $category, SourceInterface $source)
    {
        return $this->mapperProvider->getMappingCategories($category, $source);
    }
}

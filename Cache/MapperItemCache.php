<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadcka <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Cache;

use JMS\Serializer\SerializerInterface;
use Tadcka\Component\Mapper\Cache\CacheManagerInterface;
use Tadcka\Component\Mapper\Cache\MapperItemCacheInterface;
use Tadcka\Component\Mapper\MapperItemInterface;
use Tadcka\Component\Mapper\Model\SourceInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 7/15/14 8:18 PM
 */
class MapperItemCache implements MapperItemCacheInterface
{
    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Constructor.
     *
     * @param CacheManagerInterface $cacheManager
     * @param SerializerInterface $serializer
     * @param string $cacheDir
     */
    public function __construct(CacheManagerInterface $cacheManager, SerializerInterface $serializer, $cacheDir)
    {
        $this->cacheManager = $cacheManager;
        $this->serializer = $serializer;
        $this->cacheDir = $cacheDir;
    }

    /**
     * {@inheritdoc}
     */
    public function save(SourceInterface $source, MapperItemInterface $mapperItem, $locale)
    {
        $this->cacheManager->save(
            $this->getFilename($source, $locale),
            $this->serializer->serialize($mapperItem, 'json')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(SourceInterface $source, $locale)
    {
        if (null !== $cache = $this->cacheManager->fetch($this->getFilename($source, $locale))) {
            return $this->serializer->deserialize(
                $cache,
                'Tadcka\Component\Mapper\MapperItem',
                'json'
            );
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function has(SourceInterface $source, $locale)
    {
        return $this->cacheManager->has($this->getFilename($source, $locale));
    }

    /**
     * {@inheritdoc}
     */
    public function remove(SourceInterface $source, $locale)
    {
        if ($this->has($source, $locale)) {
            unlink($this->getFilename($source, $locale));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAll(SourceInterface $source)
    {

    }

    /**
     * Get cache dir.
     *
     * @return string
     */
    private function getCacheDir()
    {
        return $this->cacheDir . '/tadcka_mapper/mapper_item/';
    }

    /**
     * Get filename.
     *
     * @param SourceInterface $source
     * @param string $locale
     *
     * @return string
     */
    private function getFilename(SourceInterface $source, $locale)
    {
        return  $this->getCacheDir() . $source->getSlug() . '_' . $locale . '.json';
    }
}

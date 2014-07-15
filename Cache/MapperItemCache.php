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
     * @param string $cacheDir
     * @param SerializerInterface $serializer
     */
    public function __construct($cacheDir, SerializerInterface $serializer)
    {
        $this->cacheDir = $cacheDir;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function save(SourceInterface $source, MapperItemInterface $mapperItem)
    {
        if (false === is_dir($this->getCacheDir())) {
            mkdir($this->getCacheDir(), 0777, true);
        }

        file_put_contents($this->getFilename($source), $this->serializer->serialize($mapperItem, 'json'));
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(SourceInterface $source)
    {
        return $this->serializer->deserialize(
            file_get_contents($this->getFilename($source)),
            'Tadcka\Component\Mapper\MapperItem',
            'json'
        );
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
     *
     * @return string
     */
    private function getFilename(SourceInterface $source)
    {
        return  $this->getCacheDir() . $source->getSlug() . '.json';
    }
}

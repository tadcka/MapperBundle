<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Cache;

use JMS\Serializer\SerializerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Tadcka\Mapper\Cache\SourceDataCacheInterface;
use Tadcka\Mapper\Source\Data\SourceDataInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 1/27/15 10:02 PM
 */
class SourceDataCache implements SourceDataCacheInterface
{
    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Constructor.
     *
     * @param string $cacheDir
     * @param Filesystem $filesystem
     * @param SerializerInterface $serializer
     */
    public function __construct($cacheDir, Filesystem $filesystem, SerializerInterface $serializer)
    {
        $this->cacheDir = $cacheDir;
        $this->filesystem = $filesystem;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $filename = $this->getFilename($key);
        $metadataFilename = $filename . '.meta';

        if (file_exists($filename) && file_exists($metadataFilename)) {
            $this->filesystem->remove([$filename, $metadataFilename]);

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($key)
    {
        $filename = $this->getFilename($key);
        $metadataFilename = $filename . '.meta';

        if (file_exists($filename) && file_exists($metadataFilename)) {
            $metadata = json_decode(file_get_contents($metadataFilename), true);

            return $this->serializer->deserialize(file_get_contents($filename), $metadata['data_class'], 'json');
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function save($key, SourceDataInterface $data, \DateTime $ttl)
    {
        $filename = $this->getFilename($key);
        $metadataFilename = $filename . '.meta';

        $this->filesystem->dumpFile($metadataFilename, json_encode(['data_class' => get_class($data), 'ttl' => $ttl]));
        $this->filesystem->dumpFile($filename, $this->serializer->serialize($data, 'json'));
    }

    private function getFilename($key)
    {
        return rtrim($this->cacheDir, '/\\') . '/' . $key . '.json';
    }
}

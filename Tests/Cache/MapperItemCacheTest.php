<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadcka <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Tests\Cache;

use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use Tadcka\Bundle\MapperBundle\Cache\CacheManager;
use Tadcka\Bundle\MapperBundle\Cache\MapperItemCache;
use Tadcka\Bundle\MapperBundle\Tests\Mock\MockCacheFileSystem;
use Tadcka\Component\Mapper\MapperItem;
use Tadcka\Component\Mapper\MapperItemInterface;
use Tadcka\Component\Mapper\Model\Source;
use Tadcka\Component\Mapper\Model\SourceInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 7/18/14 1:02 AM
 */
class MapperItemCacheTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        MockCacheFileSystem::deleteTempDirectory(MockCacheFileSystem::getTempDirDirectory());
    }

    /**
     * @return SerializerInterface
     */
    private function getSerializer()
    {
        $serializer = SerializerBuilder::create();
        $serializer->addMetadataDir(
            dirname(__FILE__) . '/../../Resources/config/serializer/component',
            'Tadcka\Component\Mapper'
        );

        return $serializer->build();
    }

    public function testSave()
    {
        $serializer = $this->getSerializer();
        $cache = new MapperItemCache(new CacheManager(), $serializer, MockCacheFileSystem::getTempDirDirectory());

        $item = $this->getMapperItem();
        $cache->save($this->getMapperSource(), $item, 'en');
        $filename = MockCacheFileSystem::getTempDirDirectory() . '/tadcka_mapper/mapper_item/test_en.json';

        $this->assertEquals($serializer->serialize($item, 'json'), file_get_contents($filename));
    }

    public function testFetch()
    {
        $serializer = $this->getSerializer();
        $cache = new MapperItemCache(new CacheManager(), $serializer, MockCacheFileSystem::getTempDirDirectory());

        $item = $this->getMapperItem();
        $source = $this->getMapperSource();
        $cache->save($source, $item, 'en');

        $this->assertEquals($item, $cache->fetch($source, 'en'));

        $this->assertEmpty($cache->fetch($source, 'lt'));
    }

    /**
     * @return MapperItemInterface
     */
    private function getMapperItem()
    {
        $item = new MapperItem('mapper', 'mapper', true);
        $item->addChild(new MapperItem('mapper_child', 'mapper_child', true));

        return $item;
    }

    /**
     * @return SourceInterface
     */
    private function getMapperSource()
    {
        $source = new Source();
        $source->setSlug('test');

        return $source;
    }

    protected function tearDown()
    {
        MockCacheFileSystem::deleteTempDirectory(MockCacheFileSystem::getTempDirDirectory());
    }
}

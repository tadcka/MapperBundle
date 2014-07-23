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

use Tadcka\Bundle\MapperBundle\Cache\CacheManager;
use Tadcka\Bundle\MapperBundle\Tests\Mock\MockCacheFileSystem;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 7/24/14 12:19 AM
 */
class CacheManagerTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        MockCacheFileSystem::deleteTempDirectory(MockCacheFileSystem::getTempDirDirectory());
    }

    private function getFilename()
    {
        return MockCacheFileSystem::getTempDirDirectory() . 'cache_manager/test.json';
    }

    public function testSave()
    {
        $cacheManager = new CacheManager();
        $cacheManager->save($this->getFilename(), 'test');

        $this->assertEquals('test', file_get_contents($this->getFilename()));

        $cacheManager->remove($this->getFilename());
    }

    public function testRemove()
    {
        $cacheManager = new CacheManager();
        $cacheManager->save($this->getFilename(), 'test');
        $cacheManager->remove($this->getFilename());

        $this->assertFalse($cacheManager->has($this->getFilename()));
    }

    public function testFetch()
    {
        $cacheManager = new CacheManager();
        $cacheManager->save($this->getFilename(), 'test');

        $this->assertEquals('test', $cacheManager->fetch($this->getFilename()));

        $cacheManager->remove($this->getFilename());
    }

    public function testHas()
    {
        $cacheManager = new CacheManager();
        $cacheManager->save($this->getFilename(), 'test');

        $this->assertTrue($cacheManager->has($this->getFilename()));

        $cacheManager->remove($this->getFilename());

        $this->assertFalse($cacheManager->has($this->getFilename()));
    }

    protected function tearDown()
    {
        MockCacheFileSystem::deleteTempDirectory(MockCacheFileSystem::getTempDirDirectory());
    }
}

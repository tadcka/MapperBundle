<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadcka <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Tests\Frontend\Tree\Cache;

use Tadcka\Bundle\MapperBundle\Frontend\Tree\Cache\TreeCache;
use Tadcka\Bundle\MapperBundle\Tests\Mock\MockCacheFileSystem;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 7/23/14 10:24 PM
 */
class TreeCacheTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        MockCacheFileSystem::deleteTempDirectory(MockCacheFileSystem::getTempDirDirectory());
    }

    public function testSave()
    {
        $cache = new TreeCache(MockCacheFileSystem::getTempDirDirectory());

        $cache->save('test', '{test}','en');

        $filename = MockCacheFileSystem::getTempDirDirectory() . '/tadcka_mapper/frontend/tree/test_en.json';

        $this->assertEquals('{test}', file_get_contents($filename));
    }

    public function testFetch()
    {
        $cache = new TreeCache(MockCacheFileSystem::getTempDirDirectory());

        $cache->save('test', '{test}','en');

        $this->assertEquals('{test}', $cache->fetch('test', 'en'));
    }

    protected function tearDown()
    {
        MockCacheFileSystem::deleteTempDirectory(MockCacheFileSystem::getTempDirDirectory());
    }
}

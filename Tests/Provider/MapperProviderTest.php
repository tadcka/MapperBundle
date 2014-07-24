<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadcka <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Tests\Provider;

use Tadcka\Bundle\MapperBundle\Provider\MapperProvider;
use Tadcka\Component\Mapper\Model\Category;
use Tadcka\Component\Mapper\Model\Mapping;
use Tadcka\Component\Mapper\Model\Source;
use Tadcka\Component\Mapper\Tests\Mock\ModelManager\MockMappingManager;
use Tadcka\Component\Mapper\Tests\Mock\ModelManager\MockSourceManager;
use Tadcka\Component\Mapper\Registry\Config\Config;
use Tadcka\Component\Mapper\Registry\Registry;
use Tadcka\Component\Mapper\Tests\Mock\MockMapperFactory;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 7/13/14 4:35 PM
 */
class MapperProviderTest extends \PHPUnit_Framework_TestCase
{
    private function getMapperProvider(Registry $registry, MockSourceManager $sourceManager)
    {
        return new MapperProvider($registry, $sourceManager, new MockMappingManager());
    }

    public function testEmptyGetSource()
    {
        $provider = $this->getMapperProvider(new Registry(), new MockSourceManager());

        $this->assertEmpty($provider->getSource('test'));
    }

    public function testGetSourceWithEmptyManager()
    {
        $registry = new Registry();
        $registry->add(new Config('test', new MockMapperFactory()));

        $provider = $this->getMapperProvider($registry, new MockSourceManager());

        $this->assertNotEmpty($provider->getSource('test'));
        $this->assertEmpty($provider->getSource('fake'));

        $this->assertEquals('test', $provider->getSource('test')->getSlug());
    }

    public function testGetSourceWithEmptyRegistry()
    {
        $manager = new MockSourceManager();
        $source = $manager->create();
        $source->setSlug('test');
        $manager->add($source);
        $provider = $this->getMapperProvider(new Registry(), $manager);

        $this->assertEmpty($provider->getSource('test'));
        $this->assertEmpty($provider->getSource('fake'));
    }

    public function testGetSource()
    {
        $registry = new Registry();
        $registry->add(new Config('test1', new MockMapperFactory()));
        $registry->add(new Config('test2', new MockMapperFactory()));

        $manager = new MockSourceManager();
        $source = $manager->create();
        $source->setSlug('test');
        $manager->add($source);

        $source = $manager->create();
        $source->setSlug('test3');
        $manager->add($source);

        $provider = $this->getMapperProvider($registry, $manager);

        $this->assertNotEmpty($provider->getSource('test1'));
        $this->assertNotEmpty($provider->getSource('test2'));
        $this->assertEmpty($provider->getSource('test3'));
    }

    /**
     * @expectedException \Tadcka\Component\Mapper\Exception\ResourceNotFoundException
     */
    public function testEmptyGetMapper()
    {
        $manager = new MockSourceManager();
        $provider = $this->getMapperProvider(new Registry(), $manager);

        $source = $manager->create();
        $source->setSlug('test');
        $provider->getMapper($source, 'en');
    }

    public function testGetMapper()
    {
        $manager = new MockSourceManager();
        $registry = new Registry();
        $registry->add(new Config('test', new MockMapperFactory()));

        $provider = $this->getMapperProvider($registry, $manager);

        $source = $manager->create();
        $source->setSlug('test');
        $this->assertNotEmpty($provider->getMapper($source, 'en'));
    }

    public function testEmptyGetMappingCategories()
    {
        $provider = new MapperProvider(new Registry(), new MockSourceManager(), new MockMappingManager());

        $category = new Category();
        $category->setSlug('category_test');
        $source = new Source();
        $source->setSlug('source_test');


        $this->assertEmpty($provider->getMappingCategories($category, $source));
    }

    public function testGetMappingCategories()
    {
        $manager = new MockMappingManager();

        $manager->add($this->createMapping('left_category', 'left_source', 'right_category', 'right_source'));
        $manager->add($this->createMapping('test', 'test', 'test', 'test'));
        $manager->add($this->createMapping('right_category', 'right_source', 'left_category', 'left_source'));

        $provider = new MapperProvider(new Registry(), new MockSourceManager(), $manager);

        $category = new Category();
        $category->setSlug('left_category');
        $source = new Source();
        $source->setSlug('left_source');

        $this->assertEmpty($provider->getMappingCategories($category, $source));

        $source->setSlug('right_source');
        $this->assertCount(2, $provider->getMappingCategories($category, $source));
    }

    private function createMapping($leftCategory, $leftSource, $rightCategory, $rightSource)
    {
        $mapping = new Mapping();

        $category = new Category();
        $category->setSlug($leftCategory);
        $source = new Source();
        $source->setSlug($leftSource);
        $category->setSource($source);

        $mapping->setLeft($category);

        $category = new Category();
        $category->setSlug($rightCategory);
        $source = new Source();
        $source->setSlug($rightSource);
        $category->setSource($source);

        $mapping->setRight($category);

        return $mapping;
    }
}

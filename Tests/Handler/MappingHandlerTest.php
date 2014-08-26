<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadcka <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Tests\Handler;

use Symfony\Component\HttpFoundation\Request;
use Tadcka\Bundle\MapperBundle\Handler\MappingHandler;
use Tadcka\Component\Mapper\Provider\MapperProvider;
use Tadcka\Bundle\MapperBundle\Tests\Mock\MockMapper;
use Tadcka\Component\Mapper\Model\CategoryInterface;
use Tadcka\Component\Mapper\Model\SourceInterface;
use Tadcka\Component\Mapper\Registry\Config\Config;
use Tadcka\Component\Mapper\Registry\Registry;
use Tadcka\Component\Mapper\Tests\Mock\MockMapperFactory;
use Tadcka\Component\Mapper\Tests\Mock\ModelManager\MockCategoryManager;
use Tadcka\Component\Mapper\Tests\Mock\ModelManager\MockMappingManager;
use Tadcka\Component\Mapper\Tests\Mock\ModelManager\MockSourceManager;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since  14.8.1 16.29
 */
class MappingHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MockSourceManager
     */
    private $sourceManager;

    /**
     * @var MockMappingManager
     */
    private $mappingManager;

    /**
     * @var MockCategoryManager
     */
    private $categoryManager;

    protected function setUp()
    {
        $this->sourceManager = new MockSourceManager();
        $this->mappingManager = new MockMappingManager();
        $this->categoryManager = new MockCategoryManager();
    }

    /**
     * @expectedException \Tadcka\Component\Mapper\Exception\ResourceNotFoundException
     */
    public function testProcessWithEmptyRegistry()
    {
        $handler = $this->getHandler($this->getRegistry());

        $this->assertFalse($this->process($handler, new Request()));
    }

    public function testProcessWithNotEmptyRegistry()
    {
        $registry = $this->getRegistry(
            array('source' => new MockMapperFactory(), 'other_source' => new MockMapperFactory())
        );
        $handler = $this->getHandler($registry);

        $this->assertTrue($this->process($handler, new Request()));
    }

    public function testProcess()
    {
        $factory = new MockMapperFactory();
        $registry = $this->getRegistry(array('source' => $factory, 'other_source' => $factory));
        $handler = $this->getHandler($registry);

        $request = new Request();
        $request->query->set('mapper_item', array('test_1'));
        $this->assertFalse($this->process($handler, $request));

        $factory->setMapper(new MockMapper());
        $this->assertFalse($this->process($handler, $request));
        $request->query->set('mapper_item', array('test_2', 'test_3'));

        $this->assertTrue($this->process($handler, $request));

        $mappings = $this->mappingManager->getMappings();

        $this->assertCount(2, $mappings);
        $this->assertEquals('category', $mappings[0]->getLeft()->getSlug());
        $this->assertEquals('category', $mappings[1]->getLeft()->getSlug());

        $this->assertEquals('test_2', $mappings[0]->getRight()->getSlug());
        $this->assertEquals('test_3', $mappings[1]->getRight()->getSlug());

        $this->assertTrue($mappings[0]->isMain());
        $this->assertFalse($mappings[1]->isMain());
    }

    /**
     * Process.
     *
     * @param MappingHandler $handler
     * @param Request $request
     *
     * @return bool
     */
    private function process(MappingHandler $handler, Request $request)
    {
        return $handler->process(
            $request,
            $this->createSource('source'),
            $this->createSource('other_source'),
            'category'
        );
    }

    /**
     * Get registry.
     *
     * @param array $data
     *
     * @return Registry
     */
    private function getRegistry(array $data = array())
    {
        $registry = new Registry();
        foreach ($data as $name => $factory) {
            $registry->add(new Config($name, $factory));
        }

        return $registry;
    }

    /**
     * Get handler.
     *
     * @param Registry $registry
     *
     * @return MappingHandler
     */
    private function getHandler(Registry $registry)
    {
        return new MappingHandler(
            new MapperProvider($registry, $this->sourceManager, $this->mappingManager),
            $this->categoryManager,
            $this->mappingManager
        );
    }

    /**
     * Create source.
     *
     * @param string $slug
     * @param CategoryInterface $category
     *
     * @return SourceInterface
     */
    private function createSource($slug, CategoryInterface $category = null)
    {
        $source = $this->sourceManager->create();
        $source->setSlug($slug);
        if (null !== $category) {
            $source->addCategory($category);
        }

        return $source;
    }
}

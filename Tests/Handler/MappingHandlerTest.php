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
use Tadcka\Bundle\MapperBundle\Provider\MapperProvider;
use Tadcka\Component\Mapper\Model\CategoryInterface;
use Tadcka\Component\Mapper\Model\Manager\CategoryManagerInterface;
use Tadcka\Component\Mapper\Model\Manager\MappingManagerInterface;
use Tadcka\Component\Mapper\Model\Manager\SourceManagerInterface;
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
     * @var SourceManagerInterface
     */
    private $sourceManager;

    /**
     * @var MappingManagerInterface
     */
    private $mappingManager;

    /**
     * @var CategoryManagerInterface
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

    /**
     * Process
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

<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadcka <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Tests\Frontend\Tree;

use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use Tadcka\Bundle\MapperBundle\Cache\MapperItemCache;
use Tadcka\Bundle\MapperBundle\Frontend\Tree\TreeManager;
use Tadcka\Bundle\MapperBundle\Provider\MapperProvider;
use Tadcka\Bundle\MapperBundle\Tests\Mock\ModelManager\MockSourceManager;
use Tadcka\Component\Mapper\MapperItem;
use Tadcka\Component\Mapper\Registry\Registry;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 7/13/14 1:37 PM
 */
class TreeManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return SerializerInterface
     */
    private function getSerializer()
    {
        $serializer = SerializerBuilder::create();

        return $serializer->build();
    }

    private function getTreeManager()
    {
        return new TreeManager(
            new MapperProvider(new Registry(), new MockSourceManager()),
            new MapperItemCache('', $this->getSerializer())
        );
    }

    public function testGetGetNodeWithoutChildren()
    {
        $manager = $this->getTreeManager();

        $item = new MapperItem('test1', 'title1');
        $tree = $manager->getNode($item);

        $this->assertEquals($item->getSlug(), $tree->getId());
        $this->assertEquals($item->getName(), $tree->getText());
    }

    public function testGetNodeWitChildren()
    {
        $manager = $this->getTreeManager();

        $item = new MapperItem('test1', 'title1');
        $item->addChild(new MapperItem('test_child_1', 'title_child_1'));
        $item->addChild(new MapperItem('test_child_2', 'title_child_2'));

        $tree = $manager->getNode($item);

        $this->assertCount(2, $tree->getChildren());

        $this->assertEquals($item->getChildren()[0]->getSlug(), $tree->getChildren()[0]->getId());
        $this->assertEquals($item->getChildren()[0]->getName(), $tree->getChildren()[0]->getText());

        $this->assertEquals($item->getChildren()[1]->getSlug(), $tree->getChildren()[1]->getId());
        $this->assertEquals($item->getChildren()[1]->getName(), $tree->getChildren()[1]->getText());
    }
}

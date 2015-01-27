<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Twig\Extension;

use JMS\Serializer\SerializerInterface;
use Tadcka\Bundle\MapperBundle\Frontend\Icons;
use Tadcka\JsTreeBundle\Model\Node;
use Tadcka\Mapper\Extension\Source\Tree\MapperTree;
use Tadcka\Mapper\Extension\Source\Tree\MapperTreeItemInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 1/27/15 11:13 PM
 */
class MapperTreeExtension extends \Twig_Extension
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Constructor.
     *
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }


    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('mapper_tree_data', [$this, 'getTreeData']),
        ];
    }

    /**
     * Get tree data.
     *
     * @param MapperTree $tree
     *
     * @return string
     */
    public function getTreeData(MapperTree $tree)
    {
        return $this->serializer->serialize($this->getNode($tree->getTree()), 'json');
    }

    /**
     * {@inheritdoc}
     */
    private function getNode(MapperTreeItemInterface $treeItem)
    {
        $children = [];
        foreach ($treeItem->getChildren() as $child) {
            $children[] = $this->getNode($child);
        }

        return new Node(
            $treeItem->getId(),
            $treeItem->getTitle(),
            $children,
            $treeItem->isActive() ? Icons::CAN_USE_FOR_MAPPING : Icons::CAN_NOT_USE_FOR_MAPPING
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tadcka_mapper_tree';
    }
}

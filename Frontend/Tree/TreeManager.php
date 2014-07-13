<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadcka <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Frontend\Tree;

use Tadcka\Component\Mapper\MapperItemInterface;
use Tadcka\Bundle\MapperBundle\Frontend\Tree\Model\Node;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 7/13/14 12:53 PM
 */
class TreeManager
{
    /**
     * Get frontend tree.
     *
     * @param MapperItemInterface $mapperItem
     *
     * @return Node
     */
    public function getTree(MapperItemInterface $mapperItem)
    {
        $children = array();
        foreach ($mapperItem->getChildren() as $child) {
            $children[] = $this->getTree($child);
        }

        return new Node($mapperItem->getSlug(), $mapperItem->getName(), $children);
    }
}

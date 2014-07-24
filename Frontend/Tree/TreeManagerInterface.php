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

use Tadcka\Bundle\MapperBundle\Frontend\Tree\Model\Node;
use Tadcka\Component\Mapper\MapperItemInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 7/24/14 9:42 PM
 */
interface TreeManagerInterface
{
    /**
     * Get tree.
     *
     * @param string $name
     * @param string $locale
     *
     * @return string
     */
    public function getTree($name, $locale);

    /**
     * Get frontend node.
     *
     * @param MapperItemInterface $mapperItem
     *
     * @return Node
     */
    public function getNode(MapperItemInterface $mapperItem);
}

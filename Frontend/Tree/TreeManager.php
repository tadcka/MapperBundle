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

use Tadcka\Component\Mapper\Cache\MapperItemCacheInterface;
use Tadcka\Component\Mapper\MapperItemInterface;
use Tadcka\Bundle\MapperBundle\Frontend\Tree\Model\Node;
use Tadcka\Component\Mapper\Model\SourceInterface;
use Tadcka\Component\Mapper\Provider\MapperProviderInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 7/13/14 12:53 PM
 */
class TreeManager
{
    /**
     * @var MapperProviderInterface
     */
    private $provider;

    /**
     * @var MapperItemCacheInterface
     */
    private $itemCache;

    /**
     * Constructor.
     *
     * @param MapperProviderInterface $provider
     * @param MapperItemCacheInterface $itemCache
     */
    public function __construct(MapperProviderInterface $provider, MapperItemCacheInterface $itemCache)
    {
        $this->provider = $provider;
        $this->itemCache = $itemCache;
    }

    /**
     * Get tree.
     *
     * @param string $name
     * @param string $locale
     * @param bool $force
     *
     * @return null|Node
     */
    public function getTree($name, $locale, $force = false)
    {
        $source = $this->provider->getSource($name);

        if ((null !== $source)) {
            if (false === $force && null !== $item = $this->itemCache->fetch($source, $locale)) {
                return $this->getNode($item);
            }

            return $this->getNode($this->provider->getMapper($source, $locale));
        }

        return null;
    }

    /**
     * Get frontend node.
     *
     * @param MapperItemInterface $mapperItem
     *
     * @return Node
     */
    private function getNode(MapperItemInterface $mapperItem)
    {
        $children = array();
        foreach ($mapperItem->getChildren() as $child) {
            $children[] = $this->getNode($child);
        }

        return new Node($mapperItem->getSlug(), $mapperItem->getName(), $children);
    }
}

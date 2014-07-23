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

use JMS\Serializer\SerializerInterface;
use Tadcka\Bundle\MapperBundle\Frontend\Icons;
use Tadcka\Bundle\MapperBundle\Frontend\Tree\Cache\TreeCacheInterface;
use Tadcka\Component\Mapper\MapperItemInterface;
use Tadcka\Bundle\MapperBundle\Frontend\Tree\Model\Node;
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
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var TreeCacheInterface
     */
    private $treeCache;

    /**
     * Constructor.
     *
     * @param MapperProviderInterface $provider
     * @param SerializerInterface $serializer
     * @param TreeCacheInterface $treeCache
     */
    public function __construct(
        MapperProviderInterface $provider,
        SerializerInterface $serializer,
        TreeCacheInterface $treeCache
    ) {
        $this->provider = $provider;
        $this->serializer = $serializer;
        $this->treeCache = $treeCache;
    }

    /**
     * Get tree.
     *
     * @param string $name
     * @param string $locale
     * @param bool $force
     *
     * @return string
     */
    public function getTree($name, $locale, $force = false)
    {
        $source = $this->provider->getSource($name);

        if ((null !== $source)) {
            if (false === $force && null !== $json = $this->treeCache->fetch($name, $locale)) {
                return $json;
            }

            $item = $this->provider->getMapper($source, $locale);
            $json = $this->serializer->serialize($this->getNode($item), 'json');
            $this->treeCache->save($name, $json, $locale);

            return $json;
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
    public function getNode(MapperItemInterface $mapperItem)
    {
        $children = array();
        foreach ($mapperItem->getChildren() as $child) {
            $children[] = $this->getNode($child);
        }

        return new Node(
            $mapperItem->getSlug(),
            $mapperItem->getName(),
            $children,
            $mapperItem->canUseForMapping() ? Icons::CAN_USE_FOR_MAPPING : Icons::CAN_NOT_USE_FOR_MAPPING
        );
    }
}

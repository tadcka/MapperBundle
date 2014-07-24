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
class TreeManager implements TreeManagerInterface
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
     * Constructor.
     *
     * @param MapperProviderInterface $provider
     * @param SerializerInterface $serializer
     */
    public function __construct(MapperProviderInterface $provider, SerializerInterface $serializer)
    {
        $this->provider = $provider;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function getTree($name, $locale)
    {
        $source = $this->provider->getSource($name);

        if ((null !== $source)) {
            $item = $this->provider->getMapper($source, $locale);

            return $this->serializer->serialize($this->getNode($item), 'json');
        }

        return null;
    }

    /**
     * {@inheritdoc}
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

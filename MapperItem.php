<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadcka <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle;

use Tadcka\Component\Mapper\MapperItemInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 7/12/14 11:51 PM
 */
class MapperItem implements MapperItemInterface
{
    /**
     * @var string
     */
    private $slug;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $canUseForMapping;

    /**
     * @var array|MapperItemInterface[]
     */
    private $children = array();

    /**
     * Constructor.
     *
     * @param string $slug
     * @param string$name
     * @param bool $canUseForMapping
     */
    public function __construct($slug, $name, $canUseForMapping = true)
    {
        $this->slug = $slug;
        $this->name = $name;
        $this->canUseForMapping = $canUseForMapping;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setChildren(array $children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * {@inheritdoc}
     */
    public function addChild(MapperItemInterface $child)
    {
        $this->children[] = $child;
    }

    /**
     * {@inheritdoc}
     */
    public function canUseForMapping()
    {
        return $this->canUseForMapping;
    }
}

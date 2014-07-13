<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadcka <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Frontend\Tree\Model;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 7/13/14 1:03 PM
 */
class Node
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $text;

    /**
     * @var array|Node[]
     */
    private $children;

    /**
     * @var string
     */
    private $icon;

    /**
     * Constructor.
     *
     * @param string $id
     * @param string $text
     * @param array $children
     * @param null|string $icon
     */
    public function __construct($id, $text, array $children, $icon = null)
    {
        $this->id = $id;
        $this->text = $text;
        $this->children = $children;
        $this->icon = $icon;
    }

    /**
     * Get id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get text.
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Get children.
     *
     * @return array|Node[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Get icon.
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }
}

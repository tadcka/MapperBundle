<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Source\Metadata;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 1/29/15 10:24 PM
 */
class SourceMetadata
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $options;

    /**
     * @var string
     */
    private $dataFactoryName;

    /**
     * Constructor.
     *
     * @param string $type
     * @param string $dataFactoryName
     * @param string $name
     */
    public function __construct($type, $dataFactoryName, $name)
    {
        $this->type = $type;
        $this->dataFactoryName = $dataFactoryName;
        $this->name = $name;
        $this->options = [];
    }

    /**
     * Get source name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get source type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set options.
     *
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * Get options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get source data factory name.
     *
     * @return string
     */
    public function getDataFactoryName()
    {
        return $this->dataFactoryName;
    }
}

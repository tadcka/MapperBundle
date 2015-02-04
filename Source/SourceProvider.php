<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Source;

use Tadcka\Bundle\MapperBundle\Source\Metadata\SourceMetadata;
use Tadcka\Mapper\ParameterBag;
use Tadcka\Mapper\Source\Data\SourceDataFactoryRegistry;
use Tadcka\Mapper\Source\Data\SourceDataInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 1/31/15 12:43 PM
 */
class SourceProvider
{
    /**
     * @var SourceDataFactoryRegistry
     */
    private $dataFactoryRegistry;

    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor.
     *
     * @param SourceDataFactoryRegistry $dataFactoryRegistry
     */
    public function __construct(SourceDataFactoryRegistry $dataFactoryRegistry)
    {
        $this->dataFactoryRegistry = $dataFactoryRegistry;
    }

    /**
     * Get mapper source data.
     *
     * @param SourceMetadata $metadata
     *
     * @return SourceDataInterface
     */
    public function getData(SourceMetadata $metadata)
    {
        if (false === isset($this->data[$metadata->getDataFactoryName()])) {
            $factory = $this->dataFactoryRegistry->getFactory($metadata->getDataFactoryName());

            $this->data[$metadata->getName()] = $factory->create(new ParameterBag($metadata->getOptions()));
        }

        return $this->data[$metadata->getName()];
    }
}

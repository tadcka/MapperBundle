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
use Tadcka\Mapper\Source\Data\SourceDataProvider;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 1/31/15 12:43 PM
 */
class SourceProvider
{
    /**
     * @var SourceDataProvider
     */
    private $dataProvider;

    /**
     * Constructor.
     *
     * @param SourceDataProvider $dataProvider
     */
    public function __construct(SourceDataProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
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
        return $this->dataProvider->getData($metadata->getDataFactoryName(), $metadata->getOptions());
    }
}

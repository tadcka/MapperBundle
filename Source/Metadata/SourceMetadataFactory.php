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

use Tadcka\Mapper\Source\Source;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 1/30/15 1:23 AM
 */
class SourceMetadataFactory
{
    /**
     * Create mapper source metadata.
     *
     * @param Source $source
     *
     * @return SourceMetadata
     */
    public static function create(Source $source)
    {
        $metadata = new SourceMetadata($source->getTypeName(), $source->getDataFactoryName(), $source->getName());
        $metadata->setOptions($source->getOptions());

        return $metadata;
    }
}

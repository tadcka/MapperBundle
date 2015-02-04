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

use JMS\Serializer\SerializerInterface;
use Tadcka\Bundle\MapperBundle\Source\Metadata\SourceMetadata;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 1/31/15 2:23 PM
 */
class SourceHelper
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Constructor.
     *
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Get source metadata.
     *
     * @param string $json
     *
     * @return SourceMetadata
     */
    public function getMetadata($json)
    {
        return $this->serializer
            ->deserialize($json, 'Tadcka\\Bundle\\MapperBundle\\Source\\Metadata\\SourceMetadata', 'json');
    }
}

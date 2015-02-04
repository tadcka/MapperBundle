<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Tests\Source;

use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use PHPUnit_Framework_TestCase as TestCase;
use Tadcka\Bundle\MapperBundle\Source\SourceHelper;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 2/5/15 12:15 AM
 */
class SourceHelperTest extends TestCase
{
    /**
     * @var SourceHelper
     */
    private $helper;

    protected function setUp()
    {
        $this->helper = new SourceHelper($this->getSerializer());
    }

    public function testGetMetadata_Success()
    {
        $metadata = $this->helper->getMetadata(file_get_contents(__DIR__ . '/../MockFiles/source_metadata.json'));

        $this->assertEquals('tadcka_mapper_factory', $metadata->getDataFactoryName());
        $this->assertEquals('tadcka_mapper', $metadata->getName());
        $this->assertEquals(['locale' => 'en'], $metadata->getOptions());
        $this->assertEquals('mapper_tree', $metadata->getType());
    }

    /**
     * @return SerializerInterface
     */
    private function getSerializer()
    {
        $serializer = SerializerBuilder::create();
        $serializer->addMetadataDir(
            __DIR__ .'/../../Resources/config/serializer',
            'Tadcka\Bundle\MapperBundle'
        );

        return $serializer->build();
    }
}

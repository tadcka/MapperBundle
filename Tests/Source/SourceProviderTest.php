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

use PHPUnit_Framework_TestCase as TestCase;
use Tadcka\Bundle\MapperBundle\Source\Metadata\SourceMetadata;
use Tadcka\Bundle\MapperBundle\Source\SourceProvider;
use Tadcka\Mapper\Source\Data\SourceDataFactoryRegistry;
use Tadcka\Mapper\Source\Data\SourceDataProvider;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 1/31/15 12:47 PM
 */
class SourceProviderTest extends TestCase
{
    use SourceTestTrait;

    CONST FACTORY_NAME = 'source_data_factory';

    /**
     * @var SourceDataFactoryRegistry
     */
    private $dataFactoryRegistry;

    /**
     * @var SourceDataProvider
     */
    private $dataProvider;

    /**
     * @var SourceProvider
     */
    private $provider;

    protected function setUp()
    {
        $this->dataFactoryRegistry = new SourceDataFactoryRegistry();
        $this->dataProvider = new SourceDataProvider($this->dataFactoryRegistry);
        $this->provider = new SourceProvider($this->dataProvider);
    }

    public function testGetData_SourceDataExceptionRaised()
    {
        $this->setExpectedException(
            'Tadcka\\Mapper\\Exception\\SourceDataException',
            sprintf('Mapper source data factory %s not found!', self::FACTORY_NAME)
        );

        $this->provider->getData($this->createMetadata(self::FACTORY_NAME));
    }

    public function testGetData_Success()
    {
        $dataMock = $this->getSourceDataMock();

        $this->dataFactoryRegistry->add($this->getSourceDataFactoryMock($dataMock), self::FACTORY_NAME);

        $this->assertEquals($dataMock, $this->provider->getData($this->createMetadata(self::FACTORY_NAME)));
    }

    private function createMetadata($dataFactoryName)
    {
        return new SourceMetadata('mapper', $dataFactoryName, 'test');
    }
}

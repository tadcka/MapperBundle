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

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Tadcka\Mapper\Source\Data\SourceDataFactoryInterface;
use Tadcka\Mapper\Source\Data\SourceDataInterface;
use Tadcka\Mapper\Source\Type\SourceTypeInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 1/27/15 1:21 AM
 */
trait SourceTestTrait
{

    /**
     * Get mapper source data factory mock.
     *
     * @param null|MockObject|SourceDataInterface $dataMock
     *
     * @return MockObject|SourceDataFactoryInterface
     */
    private function getSourceDataFactoryMock(SourceDataInterface $dataMock = null)
    {
        $dataFactoryMock = $this->getMock('Tadcka\\Mapper\\Source\\Data\\SourceDataFactoryInterface');

        if (null !== $dataMock) {
            $dataFactoryMock->expects($this->any())
                ->method('create')
                ->willReturn($dataMock)
            ;
        }

        return $dataFactoryMock;
    }

    /**
     * Get mapper source data mock.
     *
     * @param string $dataClass
     *
     * @return MockObject|SourceDataInterface
     */
    private function getSourceDataMock($dataClass = 'Tadcka\\Mapper\\Source\\Data\\SourceDataInterface')
    {
        return $this->getMock($dataClass);
    }
}

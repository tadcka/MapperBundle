<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadcka <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Tests\Mock;

use Tadcka\Component\Mapper\MapperInterface;
use Tadcka\Component\Mapper\MapperItem;
use Tadcka\Component\Mapper\MapperItemInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 14.8.2 19.24
 */
class MockMapper implements MapperInterface
{
    /**
     * {@inheritdoc}
     */
    public function getMapper($locale)
    {
        $mapper = new MapperItem('test', 'Test');
        $mapper->addChild(new MapperItem('test_1', 'Test 1', false));
        $mapper->addChild(new MapperItem('test_2', 'Test 2', true));
        $mapper->addChild(new MapperItem('test_3', 'Test 3', true));

        return $mapper;
    }
}

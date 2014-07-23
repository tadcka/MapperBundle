<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadcka <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Cache;

use Tadcka\Component\Mapper\Cache\CacheManagerInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 7/23/14 10:05 PM
 */
class CacheManager implements CacheManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function save($filename, $string)
    {
        if (false === is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }

        file_put_contents($filename, $string);
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($filename)
    {
        if ($this->has($filename)) {
            return file_get_contents($filename);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function has($filename)
    {
        return file_exists($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($filename)
    {
        if ($this->has($filename)) {
            unlink($filename);
        }
    }
}

<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadcka <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Frontend\Tree\Cache;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 7/23/14 10:02 PM
 */
interface TreeCacheInterface
{
    /**
     * Saves tree in the cache.
     *
     * @param string $name
     * @param string $json
     * @param string $locale
     */
    public function save($name, $json, $locale);

    /**
     * Fetches tree from the cache.
     *
     * @param string $name
     * @param string $locale
     *
     * @return string
     */
    public function fetch($name, $locale);
}

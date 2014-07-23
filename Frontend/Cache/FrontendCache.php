<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadcka <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Frontend\Cache;

use Tadcka\Component\Mapper\Cache\CacheManagerInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 7/24/14 1:05 AM
 */
class FrontendCache
{
    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * Constructor.
     *
     * @param CacheManagerInterface $cacheManager
     * @param string $cacheDir
     */
    public function __construct(CacheManagerInterface $cacheManager, $cacheDir)
    {
        $this->cacheManager = $cacheManager;
        $this->cacheDir = $cacheDir;
    }

    public function remove()
    {

    }
}

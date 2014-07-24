<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadcka <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Frontend\Tree;

use Tadcka\Component\Mapper\Cache\CacheManagerInterface;
use Tadcka\Component\Mapper\MapperItemInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 7/24/14 9:41 PM
 */
class TreeManagerWrapper implements TreeManagerInterface
{
    /**
     * @var TreeManagerInterface
     */
    private $treeManager;

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
     * @param TreeManagerInterface $treeManager
     * @param CacheManagerInterface $cacheManager
     * @param string $cacheDir
     */
    public function __construct(TreeManagerInterface $treeManager, CacheManagerInterface $cacheManager, $cacheDir)
    {
        $this->treeManager = $treeManager;
        $this->cacheManager = $cacheManager;
        $this->cacheDir = $cacheDir;
    }


    /**
     * {@inheritdoc}
     */
    public function getTree($name, $locale)
    {
        $filename = $this->cacheManager->getFilename($this->getCacheDir(), $name, $locale);
        if (null !== $tree = $this->cacheManager->fetch($filename)) {
            return $tree;
        }

        $tree = $this->treeManager->getTree($name, $locale);
        $this->cacheManager->save($filename, $tree);

        return $tree;
    }

    /**
     * {@inheritdoc}
     */
    public function getNode(MapperItemInterface $mapperItem)
    {
        return $this->treeManager->getNode($mapperItem);
    }

    /**
     * Remove tree cache.
     *
     * @param string $name
     */
    public function removeCache($name)
    {
        $this->cacheManager->removeAll($this->getCacheDir(), $name);
    }

    /**
     * Get cache dir.
     *
     * @return string
     */
    private function getCacheDir()
    {
        return rtrim($this->cacheDir, '/') . '/tadcka_mapper/frontend/tree/';
    }
}

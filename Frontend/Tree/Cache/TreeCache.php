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
 * @since 7/23/14 10:05 PM
 */
class TreeCache implements TreeCacheInterface
{
    /**
     * @var string
     */
    private $cacheDir;

    /**
     * Constructor.
     *
     * @param string $cacheDir
     */
    public function __construct($cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    /**
     * {@inheritdoc}
     */
    public function save($name, $json, $locale)
    {
        if (false === is_dir($this->getCacheDir())) {
            mkdir($this->getCacheDir(), 0777, true);
        }

        file_put_contents($this->getFilename($name, $locale), $json);
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($name, $locale)
    {
        $filename = $this->getFilename($name, $locale);
        if (file_exists($filename)) {
            return file_get_contents($filename);
        }

        return null;
    }

    /**
     * Get filename.
     *
     * @param string $name
     * @param string $locale
     *
     * @return string
     */
    private function getFilename($name, $locale)
    {
        return  $this->getCacheDir() . $name . '_' . $locale . '.json';
    }

    /**
     * Get cache dir.
     *
     * @return string
     */
    private function getCacheDir()
    {
        return $this->cacheDir . '/tadcka_mapper/frontend/tree/';
    }
}

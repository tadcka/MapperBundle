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

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 7/23/14 10:30 PM
 */
class MockCacheFileSystem
{
    /**
     * @return string
     */
    public static function getTempDirDirectory()
    {
        return  dirname(__FILE__) . '/../tmp';
    }

    /**
     * @param string $dir
     *
     * @return bool
     */
    public static function deleteTempDirectory($dir)
    {
        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? self::deleteTempDirectory("$dir/$file") : unlink("$dir/$file");
            }

            return rmdir($dir);
        }
    }
}

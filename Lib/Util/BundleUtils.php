<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Util;

use Eulogix\Cool\Lib\Cool;
use Symfony\Component\Finder\Finder;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class BundleUtils
{
    /**
     * @param string $bundleName
     * @param string $reldir
     * @param string $wildCard
     * @param int $depth
     * @return string[]
     */
    public static function getFiles($bundleName, $reldir = "", $wildCard = "*.*", $depth = 0) {
        $locator = Cool::getInstance()->getFactory()->getFileLocator();

        try {
            $completePath = "@{$bundleName}/{$reldir}";
            $dir = $locator->locate($completePath);
        } catch(\InvalidArgumentException $e) {
            return [];
        }

        $finder = new Finder();
        $finder->depth($depth);
        $files = [];
        foreach( $finder->files()->name($wildCard)->in($dir) as $file) {
            $files[] = (string)$file;
        }
        return $files;
    }
}
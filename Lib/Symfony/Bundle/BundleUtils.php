<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Symfony\Bundle;

use Eulogix\Cool\Lib\Cool;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class BundleUtils {

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

    /**
     * @param  string $file
     * @param  BundleInterface $bundle
     * @return string
     * @throws \Exception
     */
    public static function toLocatableBundlePath($file, BundleInterface $bundle)
    {
        $bundlePath = $bundle->getPath();

        if(strpos($file, $bundlePath) !== false) {
            $relativePath = str_replace($bundlePath, '', $file);
            return "@{$bundle->getName()}{$relativePath}";
        }

        throw new \Exception("{$file} is not a file of bundle {$bundle->getName()}");
    }

    /**
     * @param BundleInterface $bundle
     * @return Finder
     */
    public static function getCoolProjectDirs(BundleInterface $bundle)
    {
        if (is_dir($dir = $bundle->getPath().'/Resources/databases')) {
            $finder  = new Finder();
            return $finder->directories()->depth(0)->in($dir);
        }
    }

    /**
     * @param string $bundleName
     * @return BundleInterface
     */
    public static function getBundle($bundleName) {
        try {
            return Cool::getInstance()->getContainer()->get('kernel')->getBundle($bundleName);
        } catch(\Exception $e) {};
        return null;
    }

} 
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

use Symfony\Component\Finder\Finder;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class ReflectionUtils
{
    public static function getClassesInFolder($folder) {
        $ret = [];

        $finder = new Finder();
        $finder->files()->in($folder)->name('*.php');

        foreach ($finder as $file) {
            /**
             * @var \SplFileInfo $file
             */
            $fileContent = file_get_contents($file->getRealPath());
            if(preg_match('/^namespace (.+?);.+?^(abstract |)class (.+?)$/sim', $fileContent, $m)) {
                $classNamespace = $m[1];
                $className = $m[3];
                $ret[] = $classNamespace.'\\'.$className;
            }
        }

        return $ret;
    }

    /**
     * @param \ReflectionMethod $method
     * @return string
     */
    public static function getMethodBody(\ReflectionMethod $method) {
        $source = file($method->getFileName());
        return implode("", array_slice($source, $method->getStartLine(), $method->getEndLine() - $method->getStartLine()));
    }
}
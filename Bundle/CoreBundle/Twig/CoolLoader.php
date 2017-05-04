<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Twig;

use Twig_Source;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class CoolLoader implements \Twig_LoaderInterface, \Twig_ExistsLoaderInterface
{

    /**
     * {@inheritdoc}
     */
    public function getSource($name)
    {
        return $name;
    }

    /**
     * since the approach described here
     * http://stackoverflow.com/questions/14343435/how-to-register-another-custom-twig-loader-in-symfony2-environment
     * is buggy (it causes the filesystem loader to fail loading custom namespaced templates)
     * This string loader reverts to only considering valid string templates only those strings which are not in the
     * format expected by the fs loader
     * {@inheritdoc}
     */
    public function exists($name)
    {
        if(strlen($name)>300 || strpos($name,"\n")!==false)
            return true;
        if(!preg_match('/^@{0,1}[a-zA-Z0-9_\/:\-\.]+$/sim',$name))
            return true;
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey($name)
    {
        return $name;
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh($name, $time)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceContext($name)
    {
        return new Twig_Source($name, $name);
    }
}

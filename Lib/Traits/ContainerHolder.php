<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Traits;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

trait ContainerHolder {

    /**
    * @var ContainerInterface
    */
    private $container;
    
    /**
    * 
    * @param ContainerInterface $container
    */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    /**
    * 
    * @return ContainerInterface
    */
    public function getContainer()
    {
        return $this->container;
    }
    
}
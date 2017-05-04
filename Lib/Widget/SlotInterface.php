<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Widget;

use Eulogix\Cool\Lib\Widget\Factory\WidgetFactoryInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

interface SlotInterface {

    /**
     * @return array
     */
    public function getDefinition();

    /**
     * @return boolean
     */
    public function isPreFetch();

    /**
     * @param boolean $preFetch
     * @return $this
     */
    public function setPreFetch($preFetch);

    /**
    * sets the widget factory
    * 
    * @param WidgetFactoryInterface $factory
    */
    public function setWidgetFactory($factory);

}
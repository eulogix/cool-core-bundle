<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Form\Field;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class Hidden extends Field {
    
    protected $coolDojoWidget = "cool/controls/hidden";
    protected $type = self::TYPE_HIDDEN;
        
}
<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Database\Propel;

use Eulogix\Cool\Lib\DataSource\SimpleValueMap;
use Eulogix\Cool\Lib\DataSource\ValueMapInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class Util {
    /**
     * @param \PropelCollection|CoolPropelObject[] $collection
     * @param string $valueMethod
     * @return ValueMapInterface
     */
    public static function valueMapfromPropelCollection($collection, $valueMethod = "getPrimaryKey") {
        $hash = [];
        foreach($collection as $item) {
            /** @var CoolPropelObject $item */
            $hash[] = ['value'=>$item->$valueMethod(), 'label'=>$item->getHumanDescription()];
        }
        return new SimpleValueMap($hash);
    }

} 
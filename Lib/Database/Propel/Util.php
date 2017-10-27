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

    /**
     * Converts a database schema name to php object name by Camelization.
     * Removes <code>STD_SEPARATOR_CHAR</code>, capitilizes first letter
     * of name and each letter after the <code>STD_SEPERATOR</code>,
     * converts the rest of the letters to lowercase.
     *
     * This method should be named camelizeMethod() for clarity
     *
     * my_CLASS_name -> MyClassName
     *
     * @param string $databseObjectName name to be converted.
     *
     * @return string Converted name.
     */
    public static function camelize($databseObjectName)
    {
        $name = "";
        $tok = strtok($databseObjectName, "_");
        while ($tok !== false) {
            $name .= ucfirst(strtolower($tok));
            $tok = strtok("_");
        }

        return $name;
    }


} 
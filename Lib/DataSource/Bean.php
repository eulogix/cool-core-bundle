<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\DataSource;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

abstract class Bean
{
    public function __get($propertyName)
    {
        $method = 'get' . ucfirst($propertyName);
        if (!method_exists($this, $method)) {
            $method = 'is' . ucfirst($propertyName);
            if (!method_exists($this, $method)) {
                throw new \Exception('Invalid read property ' . $propertyName . ' in ' . get_class($this) . ' class.');
            }
        }

        return $this->$method;
    }

    public function __isset($propertyName)
    {
        try {
            $_value = $this->__get($propertyName);

            return !empty( $_value );
        } catch (\Exception $e) {
            /* if a property isn't readable it isn't set*/
            return false;
        }
    }

    public function __set($propertyName, $value)
    {
        $method = 'set' . ucfirst($propertyName);
        if ('mapper' == $method || !method_exists($this, $method)) {
            throw new \Exception('Invalid write property ' . $propertyName . ' in ' . get_class($this) . ' class.');
        }

        return $this->$method($value);
    }

    public function populate(array $map = null)
    {
        if (!empty( $map )) {
            foreach ($map as $key => $value) {
                try {
                    $this->__set($key, $value);
                } catch (\Exception $e) {
                    /* evaluated $key isn't a bean writable property. Let's go to next one */
                }
            }
        }

        return $this;
    }
}

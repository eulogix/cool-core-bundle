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

class Date extends Field {
    
    protected $type = self::TYPE_DATE;
    protected $coolDojoWidget = "cool/controls/datetime";


    /**
     * @inheritdoc
     */
    public function setValue($value) {
        if(!$value)
            $this->value = null;
        else {
            if(!($value instanceof \DateTime || $value instanceof \DateTimeImmutable))
                $value = new \DateTime($value);

            $this->value = $value->format(DATE_ISO8601);
        }
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getValue() {
        if(!$this->value)
            return null;

        $ret = new \DateTime($this->value);
        return $ret;
    }
}
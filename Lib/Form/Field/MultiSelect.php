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

class MultiSelect extends Select {
    
    protected $type = self::TYPE_MULTISELECT;

    protected $coolDojoWidget = "cool/controls/multiSelect";

    /**
     * @inheritdoc
     */
    public function setValue($value) {
        $this->value = json_encode(is_array($value) ? $value : [$value]);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getValue() {
        $ret = json_decode($this->value, true);
        return $ret;
    }

    /**
     * @param $bool
     * @return $this
     */
    public function setUseChosen($bool) {
        $this->coolDojoWidget = $bool ? "cool/controls/chosenSelect" : "cool/controls/multiSelect";
        return $this;
    }

    public function isMultiple() {
        return true;
    }
}
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

class CheckBox extends Field {
    
    protected $type = self::TYPE_CHECKBOX;

    protected $coolDojoWidget = "cool/controls/checkbox";

    /**
     * @inheritdoc
     */
    public function setValue($value) {
        if($value===null)
            $this->setRawValue(null);
        else $this->setRawValue( ($value === true || $value==='true') ? 'true' : 'false' );
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getValue() {
        $rv = $this->getRawValue();
        if($rv===null)
            return null;
        return $rv == 'true';
    }
}
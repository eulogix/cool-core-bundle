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

use Eulogix\Cool\Lib\DataSource\ValueMapInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class Tab extends Field {

    const OPTIONS_LABEL_ATTR = 'label';
    const OPTIONS_VALUE_ATTR = 'value';

    protected $type = self::TYPE_TAB;
    protected $coolDojoWidget = "cool/controls/tab";

    /**
     * Use this function to set the possible options for the select
     * the array can be a flat array of values, an associative hash in the form of n $value=>$label, or an array of arrays, each element containing 'label' and 'value' fields
     *
     * @param mixed $options
     * @throws \Exception
     * @returns $this
     */
    public function setOptions($options) {
        $opt = array();
        foreach($options as $key => $value) {
            if(!is_array($value)) {
                $opt[] = array( self::OPTIONS_LABEL_ATTR => is_numeric($key) ? $value : $key, self::OPTIONS_VALUE_ATTR => $value);
            } elseif( $this->checkOption($value) ) {
                $opt[] = $value;
            } elseif( isset($value['value'])) {
                $this->addValue( $value['value'], '[NT] '.$value['value'] );
            } else {
                throw new \Exception("bad option value : ".var_export($value, true));
            }
        }
            
        $this->getParameters()->set('options', $opt);
        return $this;        
    }

    /**
     * @param mixed $option
     * @return bool
     */
    private function checkOption($option) {
        return is_array($option) && isset($option[ self::OPTIONS_LABEL_ATTR ]) && isset($option[ self::OPTIONS_VALUE_ATTR ]);
    }

    /**
     * @inheritdoc
     */
    public function setValueMap(ValueMapInterface $valueMap)
    {
        parent::setValueMap($valueMap);
        $this->setOptions($valueMap->getMap());
        return $this;
    }
        
}
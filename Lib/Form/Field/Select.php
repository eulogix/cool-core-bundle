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

class Select extends Field {
    
    protected $type = self::TYPE_SELECT;

    protected $coolDojoWidget = "cool/controls/select";
    
    /**
    * @inheritdoc
    */
    public function getDefinition() {
        if(!$this->getOptions())
            $this->setOptions([]);
        $this->getParameters()->set('multiple', $this->isMultiple());
        return parent::getDefinition();
    }

    /**
     * Use this function to set the possible options for the select
     * the array can be a flat array of values, an associative array in the form of n $value=>$label, or an array of arrays, each element containing 'label' and 'value' fields
     *
     * @param mixed $options
     * @throws \Exception
     * @returns self
     */
    public function setOptions($options) {
        $opt = array();
        foreach($options as $key => $value) {
            if(!is_array($value)) {
                $opt[] = array('label'=>is_numeric($key) ? $value : $key, 'value'=>$value);
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
    * @returns mixed
    */
    public function getOptions() {
        return $this->getParameters()->get('options');
    }

    /**
     * @param string $value
     * @param string $label
     * @return $this
     */
    public function addOption($value, $label=null) {
        $options = $this->getOptions();
        $options[] = ['label'=>$label!==null?$label:$value, 'value'=>$value];
        $this->getParameters()->set('options', $options);
        return $this;
    }

    /**
     * @param mixed $option
     * @return bool
     */
    private function checkOption($option) {
        return is_array($option) && isset($option['label']) && isset($option['value']);
    }

    /**
     * @param $bool
     * @return $this
     */
    public function setUseChosen($bool) {
        $this->coolDojoWidget = $bool ? "cool/controls/chosenSelect" : "cool/controls/select";
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setValueMap(ValueMapInterface $valueMap)
    {
        parent::setValueMap($valueMap);
        //TODO: implement an asynchronous option fetch for SELECTs when the number of options is large (so that SELECTs only fetch the map when clicked)
        $this->setOptions($valueMap->getMap('','',[],null));
        return $this;
    }

    /**
     * TODO: move to the js Select control
     *
     * makes another field in the form dependent on the value of this field
     * @param string $fieldName
     * @param string $vmapColumn
     */
    public function filterAnotherField($fieldName, $vmapColumn) {
        $f = "  var selectedValue = control.getSelectedOption().value;
                if(widget.getField('$fieldName')) {
                    widget.getField('$fieldName').filterOnFunction(function(option){
                        if(option.hasOwnProperty('$vmapColumn')) {
                            var columnValue = option['$vmapColumn'];
                            // if columnValue is an array, we look for selectedValue in it
                            if(Array.isArray(columnValue)) {
                                return dojo.indexOf(columnValue, selectedValue) >= 0;
                            }
                            // otherwise we check if the values match, or if the column value is null
                            else return !columnValue || (columnValue == selectedValue);
                        } else return true;
                    });
                } else console.error('Field {$this->getName()} is set to filter field {$fieldName}, but it is not shown in form {$this->getForm()->getId()}');";
        $this->setOnChangeOrLoad($f);
    }

}
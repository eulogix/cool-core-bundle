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

class ListPicker extends Field {

    protected $type = self::TYPE_LISTPICKER;

    protected $coolDojoWidget = "cool/controls/listPicker";

    protected $columnLayouts = [];

    /**
     * @inheritdoc
     */
    public function getDefinition() {
        if(!$this->getOptions())
            $this->setOptions([]);
        $pd = parent::getDefinition();
        $pd['columnLayouts'] = $this->columnLayouts;
        return $pd;
    }

    /**
     * @param array $options
     * @returns $this
     */
    public function setOptions($options) {
        $this->getParameters()->set('options', $options);
        return $this;
    }

    /**
     * @returns mixed
     */
    public function getOptions() {
        return $this->getParameters()->get('options');
    }

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
        return json_decode($this->value, true);
    }

    /**
     * @param string $columnName
     * @param string $layoutLiteral
     * @returns $this
     */
    public function setColumnLayout($columnName, $layoutLiteral) {
        $this->columnLayouts[$columnName] = $layoutLiteral;
        return $this;
    }
}
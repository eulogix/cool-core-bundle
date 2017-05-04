<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Dictionary;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtensionField;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtensionFieldQuery;
use Eulogix\Cool\Lib\Database\Propel\CoolColumnMap;
use Eulogix\Cool\Lib\Database\Propel\CoolTableMap;
use Eulogix\Cool\Lib\Database\Schema;
use Eulogix\Cool\Lib\Form\Field\Field as FormField;
use Eulogix\Cool\Lib\Form\Field\FieldInterface;
use Eulogix\Cool\Lib\Translation\Translator;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class Field {

    /**
     * @var \TableMap
     */
    private $tableMap;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    private function getTableName()
    {
        return $this->tableMap->getName();
    }

    /**
     * @param CoolTableMap $tableMap
     */
    public function __construct(CoolTableMap $tableMap) {
        $this->tableMap = $tableMap;
    }

    /**
     * @return Schema
     */
    public function getCoolDatabase() {
        return $this->tableMap->getCoolSchema();
    }

    /**
     * @return Dictionary
     */
    public function getDictionary() {
        return $this->getCoolDatabase()->getDictionary();
    }

    /**
     * @return Lookup|boolean
     */
    public function getLookup() {
        $schemaName = $this->getCoolDatabase()->getName();
        $translator = Translator::fromDomain( strtoupper('COOL_LOOKUPS_'.$schemaName.'_'.$this->getTableName().'_'.$this->getName()) );

        if($this->isExtension()) {
            return $this->getTableExtensionField()->getDictionaryLookup($schemaName, $translator);
        }

        if($dlookup = @$this->getDictionary()->getColumnAttribute($this->getTableName(), $this->getName(), 'lookup')) {
            $lookup = new Lookup($schemaName, $translator);
            $lookup->populate($dlookup);
            return $lookup;
        }

        return false;
    }

    /**
     * @return Control
     */
    public function getControl() {
        if($c = @$this->getDictionary()->getColumnAttribute($this->getTableName(), $this->getName(), 'control')) {
            $control = new Control();
            $control->populate($c);
            return $control;
        }
        return $this->getDefaultControl();
    }

    /**
     * @return \ColumnMap|CoolColumnMap
     */
    public function getPropelColumn() {
        return $this->tableMap->getColumn($this->getName());
    }

    /**
     * @return Control
     */
    private function getDefaultControl()
    {
        $type = FormField::TYPE_TEXTBOX;

        //see if it comes from propel
        if(!$this->isExtension()) {
            $c = $this->getPropelColumn();
            if($c->isForeignKey())
                $type = FieldInterface::TYPE_XHRPICKER;
            else $type = $this->getDefaultControlType($this->getName(), $c->getType(), $c->getSize());
        }

        $control = new Control();
        $control->setType($type);
        return $control;

    }

    /**
     * @return string
     */
    public function getSource() {
        return @$this->getDictionary()->getColumnAttribute($this->getTableName(), $this->getName(), Dictionary::COL_ATT_SOURCE);
    }

    /**
     * @return bool
     */
    public function isExtension() {
        return $this->getSource() == Dictionary::COL_ATT_SOURCE_DB_EXTENSION;
    }

    /**
     * @return string|bool
     */
    public function getExtensionContainer() {
        if($this->isExtension()) {
            return @$this->getDictionary()->getColumnAttribute($this->getTableName(), $this->getName(), Dictionary::COL_ATT_SOURCE_DB_EXTENSION_CONTAINER);
        }
        return false;
    }

    /**
     * @return TableExtensionField
     */
    public function getTableExtensionField() {
        if($this->isExtension()) {
            $tableExtensionFieldId = @$this->getDictionary()->getColumnAttribute($this->getTableName(), $this->getName(), Dictionary::COL_ATT_TABLE_EXTENSION_FIELD_ID);
            return TableExtensionFieldQuery::create()->findPk($tableExtensionFieldId);
        }
        return false;
    }

    /**
     * @return Constraint[]
     */
    public function getConstraints() {
        $ret = [];
        if($cts = @$this->getDictionary()->getColumnAttribute($this->getTableName(), $this->getName(), Dictionary::COL_ATT_CONSTRAINTS)) {
            foreach($cts as $c) {
                $constraint = new Constraint();
                $constraint->populate($c);
                $ret[] = $constraint;
            }
        }
        return $ret;
    }

    /**
     * @return bool
     */
    public function isCalculated()
    {
        return @$this->getDictionary()->getColumnAttribute($this->getTableName(), $this->getName(), Dictionary::COL_ATT_CALCULATED) == true;
    }

    /**
     * @return bool
     */
    public function isEditable()
    {
        return @$this->getDictionary()->getColumnAttribute($this->getTableName(), $this->getName(), Dictionary::COL_ATT_EDITABLE) !== false;
    }

    /**
     * @return bool
     */
    public function isFTSIndexable()
    {
        return @$this->getDictionary()->getColumnAttribute($this->getTableName(), $this->getName(), Dictionary::COL_ATT_FTS) == true;
    }

    /**
     * @param string $fieldName
     * @param string $propelType
     * @param int $size
     * @return string
     */
    public static function getDefaultControlType($fieldName, $propelType, $size=null)
    {
        $type = FormField::TYPE_TEXTBOX;
        switch ($propelType) {
            case 'CLOB':
            {
                $type = FormField::TYPE_TEXTAREA;
                break;
            }
            case 'TINYINT':
            {
                if ($size == 1) {
                    $type = FormField::TYPE_CHECKBOX;
                }
                break;
            }
            case 'SMALLINT':
            case 'INTEGER':
            case 'BIGINT':
            {
                if (preg_match(
                    '/amount/sim',
                    $fieldName
                )
                ) $type = FormField::TYPE_CURRENCY; else $type = FormField::TYPE_NUMBER;
                break;
            }
            case 'TIMESTAMP':
            {
                $type = FormField::TYPE_DATETIME;
                break;
            }
            case 'DATE':
            {
                $type = FormField::TYPE_DATE;
                break;
            }
            case 'TIME':
            {
                $type = FormField::TYPE_TIME;
                break;
            }
            case 'NUMERIC':
            case 'DECIMAL':
            case 'REAL':
            case 'FLOAT':
            case 'DOUBLE':
            {
                $type = FormField::TYPE_NUMBER;
                break;
            }
            case 'BOOLEAN':
            {
                $type = FormField::TYPE_CHECKBOX;
                break;
            }

            case 'CHAR':
            case 'VARCHAR':
            case 'LONGVARCHAR':
            {
                //$c->getSize()
                $type = FormField::TYPE_TEXTBOX;
                break;
            }
        }

        return $type;
    }

} 
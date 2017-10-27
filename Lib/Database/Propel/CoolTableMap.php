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

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Database\Schema;
use Eulogix\Cool\Lib\DataSource\CoolValueMap;
use Eulogix\Cool\Lib\DataSource\ValueMapInterface;
use Eulogix\Cool\Lib\Dictionary\Dictionary;
use Eulogix\Cool\Lib\Dictionary\FileCategory;
use Eulogix\Cool\Lib\Dictionary\Trigger;
use Eulogix\Cool\Lib\Dictionary\Field;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class CoolTableMap extends \TableMap {

    /**
     * @var Field[]
     */
    private $coolFields = [];

    /**
     * @var string
     */
    private $coolSchemaName;

    /**
     * @return string|bool
     */
    public function getCoolSchemaName() {
        if($this->coolSchemaName)
            return $this->coolSchemaName;

        $cc = get_called_class();
        if(preg_match('/^(.+?)\\\\map\\\\.+?$/sim', $cc, $m)) {
            $namespace = '\\'.$m[1];
            $availableSchemas = Cool::getInstance()->getAvailableSchemas();
            foreach($availableSchemas as $schemaName => $schemaProperties) {
                if($schemaProperties['namespace'] == $namespace)
                    return $this->coolSchemaName = $schemaName;
            }
        }
        return false;
    }

    /**
     * returns the raw name of this table (without the schema prefix)
     * @return string|null
     */
    public function getCoolRawName() {
        return @$this->getDictionary()->getTableRawName($this->getName());
    }

    /**
     * @return Schema
     */
    public function getCoolSchema() {
       return Cool::getInstance()->getSchema( $this->getCoolSchemaName() );
    }

    /**
     * @return Dictionary
     */
    public function getDictionary() {
        return $this->getCoolSchema()->getDictionary();
    }

    /**
    * @return Trigger[]
    */
    public function getCoolTriggers() {
        $ret = [];
        if($triggers = @$this->getDictionary()->getTableTriggers($this->getName())) {
            foreach($triggers as $t) {
                $trigger = new Trigger();
                $trigger->populate($t);
                $ret[] = $trigger;
            }
        }
        return $ret;
    }

    /**
     * returns an array of field names that comprise the Pk for a given table
     *
     * @return string[]
     */
    public function getPkFields() {
        $pk_arr = [];
        $columns = $this->getPrimaryKeys();
        foreach($columns as $c)
            $pk_arr[] = $c->getName();
        return $pk_arr;
    }

    /**
     * returns the name of the view that produces the calculated fields for this table
     * @return string
     */
    public function getCalcViewName() {
        return $this->getName().'_calc';
    }

    /**
     * @return Field[]
     */
    public function getCoolFields() {
        if(empty($this->coolFields)) {
            $cols = $this->getDictionary()->getTableColumns($this->getName(), $this->getCoolSchema()->getCurrentSchema());
            $propelColumns = $this->getColumns();
            foreach($cols as $fieldName => $fieldSettings) {
                $field = new Field($this);
                $field->setName($fieldName);
                $this->coolFields[$fieldName] = $field;
            }
            foreach($propelColumns as $fieldName => $propelColumnMap) {
                if(!isset($this->coolFields[$fieldName])) {
                    //the field comes from a behavior
                    $field = new Field($this);
                    $field->setName($fieldName);
                    $this->coolFields[$fieldName] = $field;
                }
            }
        }

        return $this->coolFields;
    }

    /**
     * @param string $fieldName
     * @return Field|boolean
     */
    public function getCoolField($fieldName) {
        if($field = @$this->getCoolFields()[$fieldName]) {
            return $field;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getCoolDefaultEditor() {
        $de = $this->getDictionary()->getTableAttribute($this->getName(), Dictionary::TBL_ATT_DEFAULT_EDITOR);
        return $de ? $de : 'Eulogix\Cool\Lib\Form\CoolForm';
    }

    /**
     * @return string
     */
    public function getCoolDefaultLister() {
        $dl = $this->getDictionary()->getTableAttribute($this->getName(), Dictionary::TBL_ATT_DEFAULT_LISTER);
        return $dl ? $dl : 'Eulogix\Cool\Lib\Lister\CoolLister';
    }

    /**
     * @return string
     */
    public function getCoolValueMapClass() {
        $dl = $this->getDictionary()->getTableAttribute($this->getName(), Dictionary::TBL_ATT_VALUE_MAP_CLASS);
        return $dl ? $dl : 'Eulogix\Cool\Lib\DataSource\CoolValueMap';
    }

    /**
     * @return string
     */
    public function getCoolValueMapDecodingSQL() {
        return $this->getDictionary()->getTableAttribute($this->getName(), Dictionary::TBL_ATT_VALUE_MAP_DECODING_SQL);
    }

    /**
     * @return string
     */
    public function getCoolValueMapSearchSQL() {
        return $this->getDictionary()->getTableAttribute($this->getName(), Dictionary::TBL_ATT_VALUE_MAP_SEARCH_SQL);
    }

    /**
     * @return bool|ValueMapInterface
     */
    public function getCoolValueMap() {
        $vmapClass = $this->getCoolValueMapClass();
        if(class_exists($vmapClass)) {
            $map = new $vmapClass($this->getCoolSchema()->getName(), $this->getName());
            return $map;
        }
        return false;
    }

    /**
     * @param string $columnName
     * @return ValueMapInterface
     */
    public function getValueMapFor($columnName) {
        return CoolValueMap::getValueMapFor($this->getCoolSchema()->getName(), $this->getName(), $columnName)->getMap();
    }

    /**
     * @return boolean
     */
    public function getCoolIsEditable() {
        return $this->getDictionary()->getTableAttribute($this->getName(), Dictionary::TBL_ATT_EDITABLE);
    }

    /**
     * returns the widgets which have to be instantiated in the editor form of the given table
     * usually as dependent listers, but may as well be forms or whatever else
     */
    public function getDependantWidgets() {
        $widgets = [];
        $tableRelations = $this->getRelations();
        foreach($tableRelations as $rel) {
            switch($rel->getType()) {
                case \RelationMap::MANY_TO_ONE : {
                    break;
                }
                case \RelationMap::MANY_TO_MANY : {
                    break;
                }
                case \RelationMap::ONE_TO_MANY : {
                    $leftCols = $rel->getLeftColumns();
                    $rightCols = $rel->getRightColumns();
                    /**
                     * @var CoolTableMap $rightTable
                     */
                    $rightTable = $rel->getRightTable();

                    $widget = array(
                        'group'=>'main',
                        'slot'=>'Eulogix\Cool\Lib\Widget\WidgetSlot',
                        'widget'=> $rightTable->getCoolDefaultLister(),
                        'parameters'=>array(
                            'databaseName'=>$rightTable->getCoolSchema()->getName(),
                            'tableName'=>$rightTable->getName(),
                        ),
                        'filter'=>[]
                    );

                    foreach($leftCols as $k=>$lCol) {
                        $widget['filter'][ $rightCols[$k]->getName() ] = $lCol->getName();
                    }

                    $widgets[] = $widget;

                    break;
                }
                case \RelationMap::ONE_TO_ONE : {
                    break;
                }
            }
        }
        return $widgets;
    }

    /**
     * @inheritdoc
     * @return CoolColumnMap
     */
    public function getColumn($name, $normalize = true) {
        return parent::getColumn($name, $normalize);
    }

    /**
     * @return int
     */
    public function getCoolAuditTableId()
    {
        $id = $this->getDictionary()->getTableAttribute($this->getName(), Dictionary::TBL_ATT_AUDIT_ID);
        return $id ? $id : crc32($this->getName());
    }

    /**
     * @return FileCategory[]
     */
    public function getFileCategories() {
        $ret = [];
        if($categories = @$this->getDictionary()->getTableFilesCategories($this->getName())) {
            foreach($categories as $c) {
                $category = new FileCategory();
                $category->populate($c);
                $ret[$category->getName()] = $category;
            }
        }
        return $ret;
    }

    /**
     * @param string $categoryName
     * @return FileCategory|false
     */
    public function getFileCategory($categoryName) {
        $c = @$this->getFileCategories()[$categoryName];
        return $c ? $c : false;
    }

    /**
     * @return bool
     */
    public function hasFTSFields()
    {
        $fields = $this->getCoolFields();
        foreach($fields as $field)
            if($field->isFTSIndexable())
                return true;
        return false;
    }

    /**
     * @param string $behaviorName
     * @return bool
     */
    public function hasBehavior($behaviorName) {
        return isset( $this->getBehaviors()[ $behaviorName ] );
    }

} 
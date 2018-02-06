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

use ColumnMap;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Database\Schema;
use Eulogix\Cool\Lib\File\CoolTableFileRepository;
use Eulogix\Cool\Lib\File\FileCommand;
use Eulogix\Cool\Lib\File\FileRepositoryInterface;
use Eulogix\Cool\Lib\Form\Field\FieldInterface;
use Eulogix\Lib\File\Proxy\FileProxyInterface;
use Money\Currency;
use Money\Money;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class CoolPropelObject extends \BaseObject {

    /**
     * @var FileRepositoryInterface
     */
    private $fileRepository;

    /**
     * @var array
     */
    private $calculatedFields = null;

    /**
     * @return FileRepositoryInterface
     */
    public function getFileRepository()
    {
        if( $this->fileRepository )
            return $this->fileRepository;

        $this->fileRepository = CoolTableFileRepository::fromCoolPropelObject($this);
        return $this->fileRepository;
    }

    /**
     * Should return something fit to show the record in a selector, list, or record decodification
     * @return string
     */
    public function getHumanDescription() {
        return $this->getCoolTableMap()->getCoolValueMap()->mapValue( $this->getPrimaryKeyAsString() );
    }

    /**
     * Should return the UI-fit representation of the column value
     * @param string $fieldName
     * @return string
     */
    public function getDecodedField($fieldName) {
        try {
            $fieldValue = $this->getByName($fieldName, \BasePeer::TYPE_FIELDNAME);
        } catch(\Exception $e) {
            try {
                $fieldValue = $this->getCalculatedField($fieldName);
            } catch(\Exception $e) {
                return $fieldName.'[?]';
            }
        }

        return $this->getCoolDatabase()->decodeField( $this->_getTableName(), $fieldName, $fieldValue );
    }

    /**
     * @return Schema
     * @throws \Exception
     */
    public function getCoolDatabase() {
        return Cool::getInstance()->getSchema( $this->_getDatabaseName() );
    }

    /**
     * @return CoolTableMap
     */
    public function getCoolTableMap() {
        $dict = $this->getCoolDatabase()->getDictionary();
        return  $dict->getPropelTableMap( $this->_getTableName() );
    }

    /**
     * @return string
     */
    public function _getDatabaseName() {
        return $this->getPeer()->getTableMap()->getCoolSchemaName();
    }

    /**
     * @return string
     */
    public function _getTableName() {
        $peerClass = get_class( $this->getPeer() );
        return $peerClass::TABLE_NAME; 
    }

    /**
     * @return string
     */
    public function getPrimaryKeyAsString()
    {
        $pk = $this->getPrimaryKey();
        return is_array($pk) ? implode(';',$pk) : $pk;
    }

    /**
    * extended columns are added to the virtual columns of the object
    */
    public function populateExtendedColumns() {
        $dict = $this->getCoolDatabase()->getDictionary();
        $fields = $dict->getPropelTableMap( $this->_getTableName() )->getCoolFields();

        foreach($fields as $fieldName => $field) {
            if($field->isExtension()) {
                $containerArr = json_decode($this->getByName($field->getExtensionContainer(), $type = \BasePeer::TYPE_FIELDNAME), true);
                if($containerArr && isset($containerArr[ $fieldName ])) {
                    $this->setVirtualColumn($fieldName, $containerArr[ $fieldName ]);
                }
            }
        }
    }

    /**
     * @param $array
     * @return bool
     */
    public function extendedFromArray($array)
    {
        $ret = false;
        $tableMap = $this->getCoolTableMap();
        foreach($array as $name => $value) {
            try {
                if( ($field = $tableMap->getCoolField($name)) && $field->isExtension()) {
                    /**
                     * db extended columns must be put in the proper container column as JSON elements
                     */
                    $containerArr = json_decode($this->getByName($field->getExtensionContainer(), $type = \BasePeer::TYPE_FIELDNAME), true);

                    if( $value instanceof \DateTimeInterface )
                        $containerArr[ $name ] = $value->format('c');
                    elseif( is_numeric($value) )
                        $containerArr[ $name ] = 0 + $value;
                    else $containerArr[ $name ] = $value;

                    $this->setByName($field->getExtensionContainer(), json_encode($containerArr), $type = \BasePeer::TYPE_FIELDNAME);


                } elseif($field &&
                    //we exclude explicit sets of primary key fields to null or empty values
                    !( $tableMap->getColumn($name)->isPrimaryKey() && empty($value)) &&
                    //we also exclude NULLs and empty values if the object is new, so that default values can be inserted by the dbms
                    !(empty($value) && $this->isNew())
                ) {

                    if($field->getControl()->getType() == FieldInterface::TYPE_DATERANGE) {
                        $from = $array[$name.'_from'];
                        $to = $array[$name.'_to'];
                        $value = "[$from,$to]";
                    }

                    $this->setByName($name, $value, $type = \BasePeer::TYPE_FIELDNAME);
                }
            } catch(\Exception $e) {
                //one of the fields in $array is not a field of this bean, so skip it quietly
            }
        }
        return $ret;
    }

    /**
     * @param array $array
     */
    public function processFileAttachments($array) {
        $fileCategories = $this->getCoolTableMap()->getFileCategories();
        foreach($fileCategories as $cat) {
            if($cat->getMaxCount()==1) {
                $categoryName = $cat->getName();
                if(isset($array[$categoryName])) {
                    $value = $array[$categoryName];

                    if( $value instanceof FileProxyInterface) {

                        $this->getFileRepository()->storeFileAt($value, 'cat_'.$categoryName);

                    } elseif( $value instanceof \Eulogix\Cool\Lib\File\FileCommand) {
                        switch($value->getType()) {
                            case FileCommand::TYPE_REMOVE_STORED : {

                                $prev = $this->getFileRepository()->getChildrenOf('cat_' . $categoryName, false);
                                foreach($prev->getIterator() as $f)
                                    $this->getFileRepository()->delete($f->getId());

                                break;
                            }
                        }
                    }

                }
            }
        }
    }

    /**
     * Whether or not the record can be manually deleted by system users
     * @return bool
     */
    public function canBeDeleted() {
        return !$this->isNew();
    }

    /**
     * fetches the value of a calculated field from the <tablename>_calc view
     * @return array
     */
    public function getCalculatedFields() {
        if($this->calculatedFields === null) {
            $tm = $this->getCoolTableMap();
            $calcView = $tm->getCalcViewName();

            $sql = "SELECT * FROM $calcView WHERE TRUE";
            $parameters = [];

            $pks = $tm->getPrimaryKeys();
            foreach($pks as $k => $pk) {
                /**
                 * @var CoolColumnMap $pk
                 */
                $sql .= " AND {$pk->getName()} = :".$pk->getName();
                $parameters[':'.$pk->getName()] = $this->getByName($pk->getName(),\BasePeer::TYPE_FIELDNAME);
            }
            $this->calculatedFields = $this->getCoolDatabase()->fetch($sql, $parameters);
        }
        return $this->calculatedFields;
    }

    /**
     * updates the links of this record with another entity (assuming there is a $refTable which contains both foreign keys)
     * @param $refTable
     * @param $ids
     * @return bool
     */
    public function updateRefs($refTable, $ids) {
        if(is_array($ids)) {
            $pkFields = $this->getCoolTableMap()->getPrimaryKeys();
            $pk = array_keys($pkFields)[0];
            $refTableMap = $this->getCoolDatabase()->getDictionary()->getPropelTableMap($refTable);
            $refClass = $refTableMap->getPhpName();
            $refFQNClass = $refTableMap->getClassname();
            $refFks = $refTableMap->getForeignKeys();
            $refsGetterMethod = "get{$refClass}s";
            $alreadyLinkedIds = [];

            foreach($refFks as $refColumn => $refFk) {
                /**
                 * @var $refFk ColumnMap
                 */
                if($refColumn != $pk && $refFk->isNotNull()) {
                    //the first FK of the ref table, which is not the PK of this table
                    //and not null is assumed to be the table we have to link this record to
                    $refs = $this->$refsGetterMethod();
                    foreach($refs as $ref) {
                        /**
                         * @var CoolPropelObject $ref
                         */
                        $refId = $ref->getByName($refColumn, \BasePeer::TYPE_FIELDNAME);
                        if(!in_array($refId, $ids))
                            $ref->delete();
                        else $alreadyLinkedIds[] = $refId;
                    }
                    $idsToLink = array_diff($ids, $alreadyLinkedIds);
                    foreach($idsToLink as $id) {
                        $linkObject = new $refFQNClass();
                        $linkObject->setByName($pk, $this->getPrimaryKey(), \BasePeer::TYPE_FIELDNAME);
                        $linkObject->setByName($refColumn, $id, \BasePeer::TYPE_FIELDNAME);
                        $linkObject->save();
                    }
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * fetches the value of a calculated field from the <tablename>_calc view
     * @param string $fieldName
     * @return mixed|null
     */
    public function getCalculatedField($fieldName) {
        return $this->getCalculatedFields()[$fieldName] ?? null;
    }

    /**
     * fetches the value of a calculated field from the <tablename>_calc view
     * @param string $fieldName
     * @return Money
     */
    public function getCalculatedFieldAsMoney($fieldName, $currency = 'EUR', $precision = 2) {
        $value = $this->getCalculatedField($fieldName);
        return Money::fromDecimal($value, new Currency($currency), $precision);
    }

    /**
     * @param string $fieldName
     * @return \DateTime
     */
    public function getCalculatedFieldAsDateTime($fieldName) {
        $value = $this->getCalculatedField($fieldName);
        return $value ? new \DateTime($value) : null;
    }

    /*
     * methods that emulate the setters which may have been skipped by the custom builder
     * that strips excessive references to the core schema
     *
     * */
    public function __call($method, $args)
    {
        /**
         * @see PHP5ObjectBuilder:3427
            // Add binding for other direction of this n:n relationship.
            // If this object has already been added to the $className object, it will not be re-added.
         */
        if(preg_match('/^add+.?/sim', $method) && $args[0] instanceof \BaseObject) {
            return;
        }

        return parent::__call($method, $args);
    }

    /**
     * @return $this
     */
    public function reloadCalculatedFields() {
        //TODO modify the OM builder so that this method is called with the standard reload() method
        $this->calculatedFields = null;
        return $this;
    }

}
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

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Database\Schema;
use Eulogix\Cool\Lib\Form\Field\FieldInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class CoolCrudTableRelation {

    /**
     *
     * @var string
     */
    protected $schema;

    /**
     * when alternateTable contains a value, the FROM expressions will refer to it instead of table.
     * used for referring to audit schemas (audit trail listers..)
     * @var string
     */
    protected $table, $alternateTable;

    /**
     * the alias used in the built SQL
     * @var string
     */
    protected $alias;

    /**
     * prepended to every field name
     * @var string
     */
    protected $prefix;

    /**
     *
     * @var string
     */
    protected $join_condition;

    /**
     *  does not try to update the relation, which is used only for fetching
     * @var boolean
     */
    protected $skip_update_flag=false;

    /**
     *  if set, limits the fields that this relation adds to the DS
     *  may be used to join a table just for referencing another
     * @var boolean|string[]
     */
    protected $limit_fields=false;

    /**
     * set to true to delete the record on DS record delete
     * @var boolean
     */
    protected $delete_flag, $is_view=false;

    /**
     * set this for views
     * @var array
     */
    protected $pkFields = [];

    /**
     *
     * @var integer
     */
    protected $update_order;

    /**
     * lambda function which accepts a string as the argument, if it is set and returns false, the field is not added to the datasource
     * @var callable
     */
    protected $lambda_filter;

    /**
     * @var boolean
     */
    protected $required = false;

    /**
     * if set, adds a field with the numer of files linked to the record
     * @var boolean
     */
    protected $countFiles = true;


    /**
     * @return self
     */
    public static function build() {
        return new self();
    }

    public function getQualifier() {
        $qf = $this->getAlias() ? $this->getAlias() : $this->getTable();
        return $qf;
    }

    /**
     * @param string $alias
     * @return $this
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param boolean $delete_flag
     * @return $this
     */
    public function setDeleteFlag($delete_flag)
    {
        $this->delete_flag = $delete_flag;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getDeleteFlag()
    {
        return $this->delete_flag;
    }

    /**
     * @param string $join_condition
     * @return $this
     */
    public function setJoinCondition($join_condition)
    {
        $this->join_condition = $join_condition;

        return $this;
    }

    /**
     * @return string
     */
    public function getJoinCondition()
    {
        return $this->join_condition;
    }

    /**
     * @param string $prefix
     * @return $this
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $schema
     * @return $this
     */
    public function setSchema($schema)
    {
        $this->schema = $schema;

        return $this;
    }

    /**
     * @return string
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @returns \Eulogix\Cool\Lib\Database\Schema
     */
    public function getCoolSchema() {
        return Cool::getInstance()->getSchema($this->getSchema());
    }

    /**
     * @param boolean $skip_update_flag
     * @return $this
     */
    public function setSkipUpdateFlag($skip_update_flag)
    {
        $this->skip_update_flag = $skip_update_flag;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getSkipUpdateFlag()
    {
        return $this->skip_update_flag;
    }

    /**
     * @param string $table
     * @return $this
     */
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @return string
     */
    public function getAlternateTable()
    {
        return $this->alternateTable;
    }

    /**
     * @param string $alternateTable
     * @return $this
     */
    public function setAlternateTable($alternateTable)
    {
        $this->alternateTable = $alternateTable;
        return $this;
    }

    /**
     * @param string $view
     * @param array $pkFields
     * @return $this
     */
    public function setView($view, $pkFields=[])
    {
        $this->table = $view;
        $this->is_view = true;
        $this->pkFields = $pkFields;
        return $this;
    }

    /**
     * @return string
     */
    public function getView()
    {
        return $this->isView() ? $this->getTable() : null;
    }

    /**
     * @return boolean
     */
    public function isView()
    {
        return $this->is_view;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getDatabaseObjectTarget() {
        return $this->getAlternateTable() ? $this->getAlternateTable() : $this->getTable();
    }

    /**
     * @return string
     */
    public function getRawTableName()
    {
        if(preg_match('/^(.+?)\.(.+?)$/im', $this->table, $m))
            return $m[2];
        return $this->table;
    }

    /**
     * @param integer $update_order
     * @return $this
     */
    public function setUpdateOrder($update_order)
    {
        $this->update_order = $update_order;

        return $this;
    }

    /**
     * @param null $defaultValue
     * @return integer
     */
    public function getUpdateOrder($defaultValue=null)
    {
        return $this->update_order ? $this->update_order : $defaultValue;
    }

    /**
     * @param callable $lambda_filter
     * @return $this
     */
    public function setLambdaFilter($lambda_filter)
    {
        $this->lambda_filter = $lambda_filter;
        return $this;
    }

    /**
     * @return callable
     */
    public function getLambdaFilter()
    {
        return $this->lambda_filter;
    }

    /**
     * @param boolean $required
     * @return $this
     */
    public function setIsRequired($required)
    {
        $this->required = $required;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @return boolean
     */
    public function getCountFiles()
    {
        return $this->countFiles;
    }

    /**
     * @param boolean $countFiles
     * @return $this
     */
    public function setCountFiles($countFiles)
    {
        $this->countFiles = $countFiles;
        return $this;
    }

    /**
     * @param string[] $limit_fields
     * @return $this
     */
    public function setLimitFields($limit_fields)
    {
        $this->limit_fields = $limit_fields;
        return $this;
    }

    /**
     * @return string[]|boolean
     */
    public function getLimitFields()
    {
        return $this->limit_fields;
    }

    /**
     * @param string[] $fields
     * @return $this
     */
    public function excludeFields(array $fields)
    {
        $this->setLambdaFilter(function($fieldName) use ($fields) {
            return !in_array($fieldName, $fields);
        });
        return $this;
    }

    /**
     * remove any closure before serialization
     * @return array
     */
    public function __sleep()
    {
        $sleepVars    = get_object_vars($this);
        unset($sleepVars['lambda_filter']);
        return array_keys($sleepVars);
    }

     /**
     * @return DSField[]
     */
    public function getDSFields() {
        /** @var DSField[] $DSFields */
        $coolSchema = $this->getCoolSchema();

        $prefix = $this->getPrefix();
        $prefix = $prefix?$prefix:'';

        //TODO refactor this limit stuff
        $limit = $this->getLimitFields();
        if(is_array($limit) && empty($limit))
            $DSFields = [];
        else {
            if($this->isView()) {
                $DSFields = $coolSchema->getDSFieldsForView($this->getPhysicalViewName(), $prefix, $this->getLambdaFilter());
                foreach($DSFields as $fieldName => $DSField) {
                    if(in_array($fieldName, $this->getPKfields()))
                        $DSField->setIsPkInSource(true);
                }
            } else $DSFields = $coolSchema->getDSFieldsFor($this->getTable(), $prefix, $this->getLambdaFilter(), $this->isRequired());
        }

        if(is_array($limit) && !empty($limit)) {
            foreach($limit as &$limitName)
                $limitName = $prefix.$limitName;
            $filteredList = [];
            foreach($DSFields as $fieldName => $DSField)
                if(in_array($fieldName, $limit))
                    $filteredList[$fieldName] = $DSField;
            $DSFields = $filteredList;
        }

        if(!$this->isView() && $this->getCountFiles()) {
            $fileCountField = new DSField($this->getFileCountFieldName());
            $fileCountField->setType(\PropelColumnTypes::INTEGER)
                           ->setIsAutoGenerated(true);
            $DSFields[$this->getFileCountFieldName()] = $fileCountField;
        }

        // relations with skip update should be treated as read only
        if($this->getSkipUpdateFlag()) {
            foreach($DSFields as &$field)
                $field->setIsAutoGenerated(true);
        }

        return $DSFields;
    }

    /**
     * @return string
     */
    private function getFileCountFieldName() {
        return $this->getPrefix().($this->getAlias() ? $this->getAlias() : $this->getRawTableName()).'_files_count';
    }

    /**
     * @return string[]
     */
    public function getSqlExpressions() {
        $expressions = [];
        $db = $this->getCoolSchema();

        $tableName  = $this->getTable();
        $prefix = $this->getPrefix();
        $qualifier = $this->getQualifier();

        if($this->isView()) {

            $physicalViewName = $this->getPhysicalViewName();
            $viewFields = $db->fetchArray("SELECT column_name, data_type FROM information_schema.columns where table_schema = :schema and table_name=:table", [':schema'=>$db->getCurrentSchema(), ':table'=>$physicalViewName]);

            foreach($viewFields as $vfRecord) {
                $fieldName = $vfRecord['column_name'];
                $expressions[ $prefix.$fieldName ] = $qualifier.".".$fieldName;
            }

        } else {

            $tableMap = $db->getDictionary()->getPropelTableMap($tableName);
            $fields = $tableMap->getCoolFields();

            $limit = $this->getLimitFields();

            foreach($fields as $fieldName => $coolField)
                if(!$limit || in_array($fieldName, $limit)) {
                    //TODO: we may skip fields using other criteria too (LOBS, ...)
                    if(!$coolField->isExtension()) {
                        // we cast longvarchars to text to get around the fact that pg has no comparison functions for jsons, so they don't work in group bys
                        $cast = $coolField->getPropelColumn()->getType() == \PropelTypes::LONGVARCHAR ? '::TEXT' : '';
                        $expressions[ $prefix.$fieldName ] = $qualifier.".".$fieldName.$cast;
                    } else {
                        $expressions[ $prefix.$fieldName ] = $qualifier.".".$coolField->getExtensionContainer()."->>'$fieldName'";
                    }

                    if($coolField->getControl()->getType() == FieldInterface::TYPE_DATERANGE) {
                        $expressions[ $prefix.$fieldName.'_from' ] = "lower({$qualifier}.{$fieldName})";
                        $expressions[ $prefix.$fieldName.'_to' ] = "upper({$qualifier}.{$fieldName})";
                    }
                }

            if(!$this->isView() && $this->getCountFiles()) {
                $filesTable = $this->getCoolSchema()->getFilesIndexTableName();
                $tablePk = $this->getPKfields()[0];
                $expressions[ $this->getFileCountFieldName() ] = "(SELECT COUNT(*) FROM {$filesTable} WHERE source_table='{$this->getRawTableName()}' AND source_table_id={$qualifier}.{$tablePk})";
            }

            //and file fields
            /*$fileCategories = $tableMap->getFileCategories();
            foreach($fileCategories as $cat) {
                if($cat->getMaxCount()==1) {
                    $fieldName = $cat->getName();
                    $expressions[ $prefix.$fieldName ] = "{$prefix}$fieldName.file_id";
                }
            }*/
        }

        return $expressions;
    }

    /**
     * returns a SQL expression that can be used to fetch a stringified representation of the PK
     * @return string
     */
    public function getSQLPKExpression() {
        $tablePk = $this->getPKfields();
        $qualifier = $this->getQualifier();
        if(count($tablePk)>0) {
            $columnExpressions = [];
            foreach($tablePk as $pkCol) {
                $columnExpressions[] = "COALESCE(CAST($qualifier.$pkCol AS TEXT),'".CoolCrudDataSource::NULL_PK_SYMBOL."')";
            }
            return "(".implode(" || '".CoolCrudDataSource::PK_SEPARATOR."' || \n",$columnExpressions).")";
        }
        return null;
    }

    /**
     * returns an array of the field names that constitute the PK of this relation
     * @return string[]
     */
    public function getPKfields() {
        if(!$this->isView()) {
            $db = $this->getCoolSchema();
            $dict = $db->getDictionary();
            return $dict->getPropelTableMap($this->getTable())->getPkFields();
        } else {
            return $this->pkFields;
        }
    }

    public function isMaterialized() {
        return $this->isView() && ($this->getView() != $this->getPhysicalViewName());
    }

    /**
     * @return string
     */
    public function getPhysicalViewName() {
        /* the mat_ prefix by convention indicates a materialized version of a regular view, so we query the original
           view instead of the matview as PG information_schema does not support introspection of matviews. */

        $viewName = $this->getView();
        $physicalView = preg_match('/^mat_(.+?)$/sim', $viewName, $m) ? $m[1] : $viewName;
        return $physicalView;
    }
}

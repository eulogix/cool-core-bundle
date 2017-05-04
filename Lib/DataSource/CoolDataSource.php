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

use Eulogix\Lib\Cache\CacheShim;
use Eulogix\Lib\Cache\Shimmable;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Dictionary\Field;
use Eulogix\Cool\Lib\File\CoolTableFileRepository;
use Eulogix\Cool\Lib\Form\Field\FieldInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

abstract class CoolDataSource extends SqlDataSource implements DataSourceInterface, Shimmable {
    
    var $schemaName = "";

    /**
     * @var CacheShim
     */
    public $shim;

    /**
     * @param string $schemaName
     * @param array $parameters
     */
    public function __construct($schemaName, array $parameters = [])
    {
        $this->schemaName = $schemaName;
        $this->getParameters()->replace($parameters);
    }

    /**
     * @return string
     */
    public function getShimUID() {
        return $this->schemaName;
    }

    /**
     * @return CacheShim
     */
    public function getShim() {
        if(!$this->shim)
            $this->setShim(new CacheShim($this, Cool::getInstance()->getFactory()->getCacher(), $this->getShimUID()));
        return $this->shim;
    }

    /**
     * @param CacheShim $shim
     */
    public function setShim( $shim ) {
        $this->shim = $shim;
    }

    /**
     * returns the schema for this lister
     * @returns \Eulogix\Cool\Lib\Database\Schema
     */
    public function getCoolSchema() {
        return Cool::getInstance()->getSchema($this->schemaName);
    }

    /**
     * This method is called by DataSource.execute() for "clientExport" operations.
     * @param DSRequest $req
     * @return DSResponse
     */
    public function executeClientExport(DSRequest $req) {}

    /**
     * This method is called by DataSource.execute() for "custom" operations.
     * @param DSRequest $req
     * @return DSResponse
     */
    public function executeCustom(DSRequest $req) {}


    public function hydrateFileFields(&$row) {

    }

    /**
     * This method is called by DataSource.execute() for "fetch" operations.
     * @param DSRequest $req
     * @throws \Exception
     * @return DSResponse
     */
    public function executeFetch(DSRequest $req) {
        $success = false;
        $dsresponse = new DSResponse($this);

        if($db = $this->getCoolSchema()) {
            if($this->isInAuditMode())
                $db->setInstant($this->getInstant());

            if(array_key_exists(self::RECORD_IDENTIFIER, $req->getParameters())) {
                //single record fetch
                $recordId = $req->getParameters()[self::RECORD_IDENTIFIER];
                $sql = $this->getSql(0, 0, [], $req->getParameters(), []);
                if( $row = $db->fetch( $sql['statement'], $sql['parameters'] ) ) {

                    if($req->getIncludeDecodings()) {
                        $row = $this->addDecodedValuesToHash($row);
                    }

                    if($req->getIncludeMeta()) {
                        $row = $this->addMetaToRow($row);
                    }

                    if($req->getIncludeRecordDescriptions()) {
                        $row = $this->addDescriptionToRow($row);
                    }

                    if($req->getIncludeFiles()) {
                        $row = $this->addFilesToRow($row);
                    }

                    $this->hydrateFileFields($row);
                    $dsresponse->setTotalRows(1);
                    //we use that to distinguish this response from a multiple record fetch with 1 row!
                    $dsresponse->setAttribute(self::RECORD_IDENTIFIER, $recordId);

                    $success = true;
                } else {
                    $dsresponse->setTotalRows(0);
                    $success = false;
                }

                $dsresponse->setData($row);

            } else {
                //multiple record fetch
                $sql = $this->getSql($req->getStartRow(), $req->getEndRow(), $req->getSortByFields(), $req->getParameters(), $req->getQuery());
                $rows = $db->fetchArray( $sql['statement'], $sql['parameters'] );

                $errorCode = $db->getConnection()->errorCode();
                if($errorCode != '00000') {
                    $lastQuery = $db->getConnection()->getLastExecutedQuery();
                    $errorInfo = $db->getConnection()->errorInfo();
                    throw new \Exception($errorCode.' -> '.$lastQuery . var_export($errorInfo) );
                }

                if($req->getIncludeDecodings()) {
                    $rows = $this->addDecodedValuesToRows($rows);
                }

                if($req->getIncludeMeta()) {
                    $rows = $this->addMetaToRows($rows);
                }

                if($req->getIncludeRecordDescriptions()) {
                    $rows = $this->addDescriptionsToRows($rows);
                }

                if($req->getIncludeFiles()) {
                    $rows = $this->addFilesToRows($rows);
                }

                $dsresponse->setData($rows);
                $dsresponse->setStartRow($req->getStartRow());
                $dsresponse->setEndRow($req->getEndRow());
                $dsresponse->setTotalRows($this->getTotalRows($req->getParameters(), $req->getQuery()));

                if($sf = $req->getSummaryFields()) {
                    if( $sql = $this->getSqlSummary($sf, $req->getParameters(), $req->getQuery()) ) {
                        $summary = $db->fetchArray( $sql['statement'], $sql['parameters'] );
                        $summary = array_pop($summary);
                        //by default, summary is decoded
                        $dsresponse->setSummary( $this->getDecodedHash($summary) );
                    }
                }

                $success = true;
            }

            if($this->isInAuditMode())
                $db->exitAuditMode();

        }


        $dsresponse->setStatus($success);
        return $dsresponse;
    }

    /**
     * This method is called by DataSource.execute() for "fetch" operations.
     * @param DSRequest $req
     * @return DSResponse
     */
    public function executeCount(DSRequest $req) {
        $success = false;
        $dsresponse = new DSResponse($this);

        if($db = $this->getCoolSchema()) {
            //multiple record fetch
            $dsresponse->setTotalRows($this->getTotalRows($req->getParameters(), $req->getQuery()));

            if(!empty($req->getGroupCountFields()))
                $dsresponse->setData($this->countRowsGrouped($req->getParameters(), $req->getQuery(), $req->getGroupCountFields()));

            $success = true;
        }

        $dsresponse->setStatus($success);
        return $dsresponse;
    }

    /**
     * This method is called by DataSource.execute() for "remove" operations.
     * @param DSRequest $req
     * @return DSResponse
     */
    public function executeRemove(DSRequest $req) {
        $success = false;
        $dsresponse = new DSResponse($this);
        $dsresponse->setStatus($success);
        return $dsresponse;
    }

    /**
     * This method is called by DataSource.execute() for "update" operations.
     * @param DSRequest $req
     * @return DSResponse
     */
    public function executeUpdate(DSRequest $req) {
        $success = false;
        $dsresponse = new DSResponse($this);
        $dsresponse->setStatus($success);
        return $dsresponse;
    }

    /**
     * This method is called by DataSource.execute() for "add" operations.
     * @param DSRequest $req
     * @return DSResponse
     */
    public function executeAdd(DSRequest $req) {
        return $this->executeUpdate($req);
    }

    /**
     * builds an array of fields for a given table
     *
     * @param mixed $tableName
     * @param null $schemaName
     * @param string $prefix
     * @param callable $lambdaFilter
     * @param bool $addConstraints
     * @returns DSField[]
     */
    public function getDSFieldsFor($tableName, $schemaName=null, $prefix='', $lambdaFilter=null, $addConstraints=false) {

        if($lambdaFilter == null) {
            if( $r = $this->getShim()->callMethod(__METHOD__, func_get_args())) return $r;
        }

        $ret = [];
        $db = $schemaName ? Cool::getInstance()->getSchema($schemaName) : $this->getCoolSchema();
        if($db) {
            $dict = $db->getDictionary();
            if($tableMap = $dict->getPropelTableMap($tableName)) {
                $pk_arr = $tableMap->getPkFields();

                //add database fields
                $fields = $tableMap->getCoolFields();
                foreach($fields as $fieldName => $coolField)
                    if(!$this->getField($prefix.$fieldName) &&
                        (!is_callable($lambdaFilter) || call_user_func($lambdaFilter, $fieldName))
                    )
                    {
                        $DSfield =  new DSField($prefix.$fieldName);

                        if( in_array($fieldName, $pk_arr) ) {
                            $DSfield->setIsPkInSource(true);
                            $DSfield->setIsAutoGenerated( $tableMap->isUseIdGenerator() );
                        }

                        if( $coolField->isCalculated() || !$coolField->isEditable()) {
                            $DSfield->setIsAutoGenerated( true );
                        }

                        if(!$coolField->isExtension()) {
                            $propelColumn = $tableMap->getColumn($fieldName);
                            $DSfield->setType( $propelColumn->getType() );

                            if(($dv = $propelColumn->getDefaultValue())!==null)
                                $DSfield->setDefaultValue($dv);

                            if($addConstraints && $propelColumn->isNotNull() && !$DSfield->isPkInSource())
                                $DSfield->setIsRequired(true);
                        }

                        $DSfield->setControlType( $coolField->getControl()->getType() );

                        if($vmap = CoolValueMap::getValueMapFor($db->getName(), $tableName, $fieldName)) {
                            $DSfield->setValueMap($vmap);
                        }

                        if($coolField->getControl()->getType() == FieldInterface::TYPE_DATERANGE) {

                            $DSfield->setControlType(FieldInterface::TYPE_HIDDEN);

                            $fromDSfield =  new DSField($prefix.$fieldName."_from");
                            $fromDSfield->setType(\PropelColumnTypes::TIMESTAMP)
                                      ->setControlType(FieldInterface::TYPE_DATETIME);

                            $toDSfield =  new DSField($prefix.$fieldName."_to");
                            $toDSfield->setType(\PropelColumnTypes::TIMESTAMP)
                                      ->setControlType(FieldInterface::TYPE_DATETIME);

                            $ret[ $fromDSfield->getName() ] = $fromDSfield;
                            $ret[ $toDSfield->getName() ] = $toDSfield;
                        }

                        $ret[ $DSfield->getName() ] = $DSfield;
                    }

                //and file fields
                $fileCategories = $tableMap->getFileCategories();
                foreach($fileCategories as $cat) {
                    if($cat->getMaxCount()==1) {
                        $fieldName = $cat->getName();
                        $DSfield = new DSField($prefix.$fieldName);
                        $DSfield->setControlType( FieldInterface::TYPE_FILE );
                        $DSfield->setFileRepository( CoolTableFileRepository::fromSchemaAndTableName($db, $tableName) );
                        $ret[$prefix.$fieldName] = $DSfield;
                    }
                }
            } else return $this->getDSFieldsForView($tableName, $schemaName, $prefix, $lambdaFilter);
        }

        return $ret;
    }

    /**
     * builds an array of fields for a given view
     *
     * @param mixed $viewName
     * @param null $schemaName
     * @param string $prefix
     * @param callable $lambdaFilter
     * @returns DSField[]
     */
    public function getDSFieldsForView($viewName, $schemaName=null, $prefix='', $lambdaFilter=null) {

        $db = $schemaName ? Cool::getInstance()->getSchema($schemaName) : $this->getCoolSchema();
        $viewFields = $db->fetchArray("SELECT * FROM core.get_view_fields_origin('".$db->getCurrentSchema()."', '".$viewName."')");
        /** @var DSField[] $ret */
        $ret = [];
        $TableDSFields = [];
        $availableSchemaNames = Cool::getInstance()->getAvailableSchemaNames();
        foreach($viewFields as $viewField) {
            if($viewField['source_table'] && !isset($TableDSFields[$viewField['source_table']])) {
                $schema = in_array($viewField['source_schema'], $availableSchemaNames) ? $viewField['source_schema'] : null;
                $TableDSFields[$viewField['source_table']] = $this->getDSFieldsFor(($schema?$schema.'.':'').$viewField['source_table'], $schema, $prefix, $lambdaFilter);
            }
            /** @var DSField $DSField */
            if(!is_callable($lambdaFilter) || call_user_func($lambdaFilter, $viewField['view_column'])) {

                $DSField = @$TableDSFields[$viewField['source_table']][$prefix.$viewField['source_column']];
                if(!$DSField) {
                    $DSField = $this->getDSFieldForPGDataType($viewField['view_column'], $viewField['data_type']);
                }

                $DSField->setIsRequired(false)
                        ->setIsAutoGenerated(true);

                $ret[$prefix.$viewField['view_column']] =  $DSField;
            }

        }
        //fields returned by a view should never be treated as pks by default
        foreach($ret as &$field)
            $field->setIsPkInSource(false);

        return $ret;
    }

    /**
     * @param string $name
     * @param string $dataType
     * @return DSField
     */
    public function getDSFieldForPGDataType($name, $dataType) {
        $DSField = new DSField($name);
        switch($dataType) {
            case 'integer': { $DSField->setType(\PropelTypes::INTEGER); break;}
            case 'numeric': { $DSField->setType(\PropelTypes::DECIMAL); break;}
            case 'date':    { $DSField->setType(\PropelTypes::DATE); break;}
            case 'timestamp':
            case 'timestamp without timezone':    { $DSField->setType(\PropelTypes::TIMESTAMP); break;}
            case 'text':
            default: {
                $DSField->setType(\PropelTypes::LONGVARCHAR);
            }
        }
        $DSField->setControlType(Field::getDefaultControlType($name, $DSField->getType()));
        return $DSField;
    }

    public function getTotalRows($parameters = array(), $query=null) {
        if($db = $this->getCoolSchema()) {
            $where = $this->getSqlWhere($parameters, $query);
            return $db->fetch("SELECT COUNT(*) FROM ({$this->getSqlSelect($parameters)} {$this->getSqlFrom($parameters)} {$where['statement']} {$this->getSqlGroupBy($parameters)}) as _tmp_", $where['parameters']);
        }
        return 0;
    }

    public function countRowsGrouped($parameters = array(), $query=null, array $groupFields=[]) {
        if($db = $this->getCoolSchema()) {
            $where = $this->getSqlWhere($parameters, $query);
            $fieldsExpr = implode(',',$groupFields);
            return $db->fetchArray("SELECT {$fieldsExpr},COUNT(*) FROM ({$this->getSqlSelect($parameters)} {$this->getSqlFrom($parameters)} {$where['statement']} {$this->getSqlGroupBy($parameters)}) as _tmp_ GROUP BY {$fieldsExpr}", $where['parameters']);
        }
        return [];
    }

    //TODO: review this
    public function getFileRepository($recordid = null)
    {
        return null;
    }

}

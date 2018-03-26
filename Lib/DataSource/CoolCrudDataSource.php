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
use Eulogix\Cool\Lib\Database\Propel\CoolPropelObject;
use Eulogix\Cool\Lib\DataSource\CoolCrudTableRelation as Rel;
use Eulogix\Cool\Lib\File\FileRepositoryInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class CoolCrudDataSource extends CoolDataSource {

    const PK_SEPARATOR = ';';
    const PK_M_SEPARATOR = '§';
    const NULL_PK_SYMBOL = '*';

    const PARAM_TABLE_RELATIONS = 'table_relations';

    const PARAM_UNIONED = 'unioned';
    const PARAM_SCHEMA_LIST = 'schema_list';

    const PARAM_INSTANT = 'instant';

    const MULTITENANT_SCHEMA_PLACEHOLDER = '[_mt_]';

    const SCHEMA_PARAM_IDENTIFIER = '_schema_parameter'; //parameter that defines the expected parameter name for UNIONed DSs, has to be different than schema_identifier
    const SCHEMA_IDENTIFIER = '_schema'; //field available in UNIONed DSs

    /**
     * array of relations that participate to this datasource
     *
     * @var CoolCrudTableRelation[]
     */
    protected $tableRelations = [];

    /**
     * when this parameter contains a list of actual schema names, the DS will cycle thru them and set them
     * in the multitenant schema, building a UNIONed query
     * @var string[]
     */
    protected $schemaList = [];

    /**
     * @var boolean
     */
    protected $unioned = false;

    /**
     * @param string $schemaName
     * @param array $params
     * @return self
     */
    public function __construct($schemaName, $params=[])
    {
        parent::__construct($schemaName, $params);

        $this->tableRelations = isset($params[self::PARAM_TABLE_RELATIONS]) ? $params[self::PARAM_TABLE_RELATIONS] : [];
        foreach($this->tableRelations as $r) {
            if(!$r->getSchema())
                $r->setSchema($schemaName);

            //make sure that for DSs built around a single table, the required flag is set so that validations work as expected
            if(count($this->tableRelations)==1 && !$r->isView())
                $r->setIsRequired(true);
        }

        $this->setSchemaList( isset($params[self::PARAM_SCHEMA_LIST]) ? $params[self::PARAM_SCHEMA_LIST] : [] );

        if(@$params[self::PARAM_UNIONED])
            $this->setUnioned(true);

        if($instant = @$params[self::PARAM_INSTANT]) {
            $dateTimeInstant = new \DateTime($instant);
            $this->setInstant($dateTimeInstant);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getShimUID() {
        $session = Cool::getInstance()->getFactory()->getSession();
        return md5(implode(';',[
            get_class($this),
            $this->schemaName,
            serialize($this->tableRelations),
            serialize($this->getFieldNames()),
            $this->getCoolSchema()->getCurrentSchema(),
            $session ? $session->getLocale() : ""
        ]));
    }

    /**
     * @param CoolCrudTableRelation $relation
     * @param null $position
     */
    public function addRelation(CoolCrudTableRelation $relation, $position=null) {
        if(!$relation->getSchema())
            $relation->setSchema($this->schemaName);

        if($position!==null && is_numeric($position)) {
          array_splice( $this->tableRelations, $position, 0, [$relation] );
        } else $this->tableRelations[] = $relation;

        //force shim rebuild
        $this->setShim(null);
    }

    /**
     * @return string[]
     */
    public function getSchemaList()
    {
        return $this->schemaList;
    }

    /**
     * @param string[] $schemaList
     * @return $this
     */
    public function setSchemaList($schemaList)
    {
        $this->schemaList = $schemaList;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isUnioned()
    {
        return $this->unioned;
    }

    /**
     * @param boolean $unioned
     * @return $this
     */
    public function setUnioned($unioned)
    {
        $this->unioned = $unioned;
        return $this;
    }

    /**
     * returns a relation by qualifier (either unique table name, or alias)
     * @param string $qualifier
     * @return null|\Eulogix\Cool\Lib\DataSource\CoolCrudTableRelation
     */
    public function getRelationByQualifier($qualifier) {
        $matching = null;
        foreach($this->tableRelations as $rel) {
            if($rel->getQualifier() == $qualifier)
                return $rel;
        }
        return $matching;
    }

    /**
     * @return CoolCrudTableRelation[]
     */
    public function getTableRelations()
    {
        return $this->tableRelations;
    }

    /**
     * @param $schemaName
     * @param $tableName
     * @param string|null $instant
     * @return CoolCrudDataSource
     */
    public static function fromSchemaAndTable($schemaName, $tableName, $instant = null) {
        $ds = new self($schemaName, [
            self::PARAM_TABLE_RELATIONS => [
               Rel::build()->setTable($tableName)
                           ->setSchema($schemaName)
                           ->setDeleteFlag(true)
                           ->setIsRequired(true)
            ],
            self::PARAM_INSTANT => $instant
        ]);

        return $ds;
    }

    /**
     * convenience method that packs a Pk in a string
     * @param $pk
     * @return string
     */
    public static function stringifyPk($pk) {
        $workPk = is_array($pk) ? $pk : [$pk];
        return implode(self::PK_SEPARATOR, $workPk);
    }

    /**
     * convenience method that builds a _recordId string from an array of Pks
     * @param array $pks
     * @return string
     */
    public static function buildRecordId($pks) {
        $workPks = $pks;
        foreach($workPks as &$pk)
            $pk = self::stringifyPk($pk);
        return implode(self::PK_M_SEPARATOR, $workPks);
    }

    /**
     * convenience method that takes a stringified pk and returns an array, or null if the pk is invalid
     * @param $stringPk
     * @return array|null
     */
    public static function string2pk($stringPk)
    {
        if($stringPk==null || preg_match('/^['.self::NULL_PK_SYMBOL.self::PK_SEPARATOR.']+$/sim',$stringPk))
            return null;
        $ret = explode(self::PK_SEPARATOR, $stringPk);
        return $ret;
    }

    /**
     * base static implementation of a method that explodes a recordIds in an array of stringified pks
     * @param $recordId
     * @return array
     */
    public static function explodePks($recordId)
    {
        return explode(self::PK_M_SEPARATOR, $recordId);
    }

    /**
     * convenience method that takes a recordId and returns an array of stringified Pks, this is not static because
     * it can be overridden with methods that build the missing relations pks by means of querying
     * @param $recordId
     * @return array
     */
    public function extractRelationPks($recordId)
    {
        return self::explodePks($recordId);
    }

    /**
     * @param string $recordId
     * @param string $relationQualifier
     * @return string
     */
    public function extractPkOfRelation($recordId, $relationQualifier) {
        $tableIds = $this->extractRelationPks($recordId);
        foreach($this->tableRelations as $relationIndex => $relation) {
            if($relationQualifier == $relation->getQualifier())
                return $tableIds[$relationIndex];
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function build($parameters=[])
    {
        parent::build();

        // add an aggregated pk field
        $pkField = new DSField(self::RECORD_IDENTIFIER);
        $pkField->setIsPrimaryKey(true);
        $pkField->setIsAutoGenerated(true);
        $this->addField(self::RECORD_IDENTIFIER, $pkField);

        if($this->isUnioned()) {
            $this->initializeMultitenantSchema();
            $schemaField = new DSField(self::SCHEMA_IDENTIFIER);
            $schemaField->setIsAutoGenerated(true);
            $this->addField(self::SCHEMA_IDENTIFIER, $schemaField)->setValueMap( Cool::getInstance()->getFactory()->getValuemap('mt_schemas') );
        }

        foreach($this->tableRelations as $relation) {
            $DSFields = $relation->getDSFields();

            foreach($DSFields as $DSField)
                $DSField->setSource($relation);

            $this->addFields( $DSFields );
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function executeClientExport(DSRequest $req) {}

    /**
     * @inheritdoc
     */
    public function executeCustom(DSRequest $req) {}

    /**
     * @inheritdoc
     */
    public function executeRemove(DSRequest $req) {

        $dsresponse = new DSResponse($this);
        $success = false;

        if($db = $this->getCoolSchema()) {

            $connection = $db->getConnection();
            $connection->beginTransaction();
            try {
                $recordId = $req->getParameters()[self::RECORD_IDENTIFIER];
                $recordPks = $this->extractRelationPks($recordId);
                $pkCursor = 0;

                foreach($this->tableRelations as $relation) {
                    if (!$relation->isView()) {
                        $relationDb = $relation->getSchema() ? $relation->getCoolSchema() : $db;
                        $tableName = $relation->getTable();
                        $tablePk = self::string2pk($recordPks[ $pkCursor ]);

                        if ($tablePk && $relation->getDeleteFlag() && ( $obj = $relationDb->getPropelObject(
                                $tableName,
                                $tablePk
                            ) )
                        ) {
                            //we check here, and not directly in the delete() method of the object in order to preserve the faculty of deleting
                            //it via code elsewhere.
                            //the canBeDeleted method really just protects against manual deletion (via listers)
                            if ($obj->canBeDeleted()) {
                                $obj->delete();
                            } else {
                                throw new \Exception("OBJECT_CAN_NOT_BE_DELETED");
                            }
                            //one successful deletion is enough: if two tables in the datasource are joined, and the first table cascadedly deletes the second,
                            //the second deletion will fail, but the overall status of the operation must be a success.
                            $success = true;
                        }
                    }
                    $pkCursor++;
                }

                $connection->commit();
            } catch (\Exception $e) {
                $dsresponse->getErrorReport()->addGeneralError("EXCEPTION FROM PROPEL");
                $dsresponse->getErrorReport()->addGeneralError($e->getMessage());
                $dsresponse->setData([]);

                $connection->rollback();
                $success = false;
            }
        }

        $dsresponse->setStatus($success);
        return $dsresponse;
    }

    /**
     * template method: subclasses will use that to set Fks and other default values BEFORE currentObj has been saved
     * @param string $tableQualifier name or alias
     * @param CoolPropelObject $currentObj
     * @param CoolPropelObject[] $savedObjects
     * @param array $tableFillData
     * @return bool TRUE if the currentObj should be saved (inserted), FALSE otherwise
     */
    protected function updateHook($tableQualifier, $currentObj, $savedObjects, $tableFillData) {
        return true;
    }

    /**
     * template method: subclasses will use that to set Fks and other default values AFTER currentObj has already been saved
     * @param string $tableQualifier name or alias
     * @param CoolPropelObject $currentObj
     * @param CoolPropelObject[] $savedObjects
     * @param array $tableFillData
     */
    protected function postUpdateHook($tableQualifier, $currentObj, $savedObjects, $tableFillData) {}

    /**
     * @inheritdoc
     */
    public function executeUpdate(DSRequest $req) {

        $dsresponse = new DSResponse($this);
        if($db = $this->getCoolSchema()) {

            $connection = $db->getConnection();

            $fillData = $req->getValues();
            $errors = $this->validate( $fillData );
            if(!$errors->hasErrors()) {
                $finalHash = [];
                $recordObjects = [];
                $recordPks = [];

                if($req->getOperationType() == $req::OPERATION_TYPE_UPDATE) {
                    $recordId = $req->getParameters()[self::RECORD_IDENTIFIER];
                    $recordPks = $this->extractRelationPks($recordId);
                }

                //determine the update order
                $sort = [];
                foreach($this->tableRelations as $key=>$relation)
                    $sort[ $key ] = $relation->getUpdateOrder($key);
                asort($sort);
                $sortedKeys = array_keys( $sort );

                //cycle in order
                $success = true;
                $connection->beginTransaction();
                try {
                    foreach($sortedKeys as $relationKey) {
                        $relation = $this->tableRelations[ $relationKey ];

                        $relationDb = $relation->getSchema() ? $relation->getCoolSchema() : $db;

                        $tableName  = $relation->getTable();
                        $tableAlias = $relation->getAlias();
                        $tablePrefix = $relation->getPrefix();
                        $tableQualifier = $relation->getQualifier();

                        //if there's a prefix, we build a smaller fill array with only the values that have the same prefix
                        if($tablePrefix) {
                            $tableFillData = [];
                            foreach($fillData as $fieldName => $fieldValue) {
                                if(preg_match('/^'.$tablePrefix.'.+?$/sim', $fieldName)) {
                                    $tableFillData[substr($fieldName, strlen($tablePrefix))] = $fieldValue;
                                }
                            }
                        } else $tableFillData = $fillData;

                        $tablePk = $req->getOperationType() == $req::OPERATION_TYPE_UPDATE ? self::string2pk(@$recordPks[$relationKey]) : null;

                        if( $obj = $relationDb->getPropelObject($tableName, $tablePk) ) {

                            //keep on saving objects only if no errors have yet been raised
                            if($success && !$relation->getSkipUpdateFlag()) {

                                $obj->extendedFromArray( $tableFillData );
                                //TODO: it *may* be useful to remove the !isNew condition, to allow subclasses to skip related object saving when no modifications are done
                                $shouldSave = $this->updateHook($tableQualifier, $obj, $recordObjects, $tableFillData) || !$obj->isNew();

                                if($shouldSave) {
                                    $obj->save($connection);
                                    $obj->reload(false, $connection);
                                }

                                $obj->processFileAttachments( $tableFillData );

                                $this->postUpdateHook($tableQualifier, $obj, $recordObjects, $tableFillData);
                            }

                            $arr = $obj->toArray(\BasePeer::TYPE_FIELDNAME);

                            $finalHash = array_merge($finalHash, $arr);

                            $recordObjects[$tableQualifier] = $obj;
                            $recordPks[$relationKey] = $obj->isNew() ? self::NULL_PK_SYMBOL : $obj->getPrimaryKeyAsString();

                        } else {
                            $dsresponse->getErrorReport()->addGeneralError("UNABLE TO INSTANTIATE OBJECT");
                            //TODO clean this
                            $recordObjects[$tableQualifier] = null;
                            $recordPks[$relationKey] = null;
                        }
                    }

                    $connection->commit();
                } catch (\Exception $e) {
                    $dsresponse->getErrorReport()->addGeneralError("EXCEPTION FROM PROPEL (table:$tableName alias:$tableAlias)");
                    $dsresponse->getErrorReport()->addGeneralError($e->getMessage());
                    $dsresponse->setData([]);

                    $connection->rollback();
                    $success = false;
                }

                ksort($recordPks); //needed when the object is created new and the order of creation of the records does not follow the one of the relations
                $recordId = static::buildRecordId($recordPks);
                $dsresponse->setAttribute(self::RECORD_IDENTIFIER, $recordId);

                // only the lister asks decodings to be added
                if($req->getIncludeDecodings()) {
                    $finalHash[self::RECORD_IDENTIFIER] = $recordId;  //TODO: maybe this has to be added to the hash anyway!
                    $finalHash = $this->addDecodedValuesToHash($finalHash);
                }

                $dsresponse->setData( $finalHash );

            } else {
                $dsresponse->setErrorReport($errors);
                $dsresponse->getErrorReport()->addGeneralError("VALIDATION ERRORS");
            }       
        } else {
            $dsresponse->getErrorReport()->addGeneralError("UNABLE TO INSTANTIATE DATABASE");
        }  
        $dsresponse->setStatus($success);
        return $dsresponse;
    }

    /**
     * @inheritdoc
     */
    public function executeAdd(DSRequest $req) {
        return $this->executeUpdate($req);
    }

    public function hydrateFileFields(&$row) {
        $row = array_merge($row, $this->getRowFiles($row, true));
    }

    /**
     * returns only the SELECT portion of the query
     *
     * @param mixed $parameters
     * @param null $query
     * @return array
     */
    public function getFullSelectSql($parameters = array(), $query=null) {
        return $this->buildFullSelectSql($parameters, $query);
    }

    /**
     * returns a SELECT which only contains one fake field, used to COUNT more efficiently
     *
     * @param mixed $parameters
     * @param null $query
     * @return array
     */
    public function getStrippedCountSelectSql($parameters = array(), $query=null) {
        return $this->buildFullSelectSql($parameters, $query, 'SELECT 0 as fake ');
    }

    /**
     * @param mixed $parameters
     * @param array $query
     * @param string $selectPortion
     * @return array
     */
    private function buildFullSelectSql($parameters = array(), $query = null, $selectPortion = null) {
        $where = $this->getSqlWhere($parameters, $query);

        $select = ($selectPortion ?? $this->getSqlSelect($parameters)).' '.
            $this->getSqlFrom($parameters).' '.
            $where['statement'].' '.
            $this->getSqlGroupBy($parameters);

        if($this->isUnioned()) {
            $sql = "SELECT * FROM (\n";

            $unions = [];
            foreach($this->getSchemaList() as $actualSchema)
                $unions[] = str_replace(self::MULTITENANT_SCHEMA_PLACEHOLDER, $actualSchema, $select);

            $sql.= implode("\n UNION ALL \n", $unions).")\n AS merge_alias";

        } else {
            $sql = str_replace(self::MULTITENANT_SCHEMA_PLACEHOLDER.'.','', $select);
            $sql = str_replace(self::MULTITENANT_SCHEMA_PLACEHOLDER,'', $sql);
        }

        $ret = [
            'statement' => $sql,
            'parameters' => $where['parameters']
        ];

        return $ret;
    }

    /**
     * @param array $hash
     * @return array
     */
    public function getDecodedHash($hash) {
        if($this->isUnioned() && $hashSchema = ($hash[self::SCHEMA_IDENTIFIER] ?? null)) {
            if($this->getCoolSchema()->isSchemaNameValid($hashSchema))
                $this->getCoolSchema()->setCurrentSchema($hashSchema);
        }
        return parent::getDecodedHash($hash);
    }

    /**
     * @inheritdoc
     */
    public function getRowDescription(array $row) {
        if ($this->isUnioned() && $hashSchema = ($row[ self::SCHEMA_IDENTIFIER ] ?? null)) {
            if ($this->getCoolSchema()->isSchemaNameValid($hashSchema)) {
                $this->getCoolSchema()->setCurrentSchema($hashSchema);
            }
        }

        $recordId = $row[ self::RECORD_IDENTIFIER ];
        $recordPks = $this->extractRelationPks($recordId);

        foreach ($this->getTableRelations() as $relationKey => $relation) {
            if (!$relation->isView()) {
                $relationDb = $relation->getSchema() ? $relation->getCoolSchema() : $this->getCoolSchema();
                $tablePk = self::string2pk(@$recordPks[ $relationKey ]);
                if ($obj = $relationDb->getPropelObject($relation->getTable(), $tablePk)) {
                    if($obj instanceof CoolPropelObject)
                        return $obj->getHumanDescription();
                }
            }
        }

        return parent::getRowDescription($row);
    }

    /**
     * returns the SQL clause needed to fetch the summary (totals, averages..) from the current query
     *
     * @param string[] $summaryFields
     * @param mixed $parameters
     * @param mixed $query
     * @return mixed []
     */
    public function getSqlSummary($summaryFields, $parameters = array(), $query=null) {
        if($this->isUnioned()) {
            //TODO implement unioned summaries
        } else {
            $ret = parent::getSqlSummary($summaryFields, $parameters, $query);
            $ret['statement'] = str_replace(self::MULTITENANT_SCHEMA_PLACEHOLDER.'.','', $ret['statement']);
            $ret['statement'] = str_replace(self::MULTITENANT_SCHEMA_PLACEHOLDER,'', $ret['statement']);
            return $ret;
        }
    }

    /**
     * returns the final SQL query that can be fed to the dbms to retrieve a chunk of rows. Comes complete of ORDER BY and LIMIT clauses
     *
     * @param mixed $start
     * @param mixed $end
     * @param mixed $sort
     * @param mixed $parameters
     * @param null $query
     * @return array
     */
    public function getSql($start, $end, $sort, $parameters = array(), $query=null) {
        $select = $this->getFullSelectSql($parameters, $query);

        $ret = [
            'statement' => implode(' ',[
                $select['statement'],
                $this->getSqlSort($sort),
                $this->getSqlLimit($start, $end)
            ]),

            'parameters' => $select['parameters']
        ];

        return $ret;
    }

    public function getTotalRows($parameters = array(), $query=null) {
        if($db = $this->getCoolSchema()) {
            $select = $this->getStrippedCountSelectSql($parameters, $query);
            return $db->fetch("SELECT COUNT(*) FROM ({$select['statement']}) as _tmp_", $select['parameters']);
        }
        return 0;
    }

    public function countRowsGrouped($parameters = array(), $query=null, array $groupFields=[]) {
        if($db = $this->getCoolSchema()) {
            $select = $this->getFullSelectSql($parameters, $query);
            $fieldsExpr = implode(',',$groupFields);
            return $db->fetchArray("SELECT {$fieldsExpr},COUNT(*) as count FROM ({$select['statement']}) as _tmp_ GROUP BY {$fieldsExpr}", $select['parameters']);
        }
        return [];
    }

    /**
     * returns the base select query for this lister
     * @param mixed $parameters
     * @return string
     */
    public function getSqlSelect($parameters = array()) {
        if( $r = $this->getShim()->callMethod(__METHOD__, func_get_args())) return $r;
        $ret =  $this->getSqlBaseColumnsSelect($parameters);
        return $ret;
    }

    /**
     * returns the SQL expression that builds the row identifier
     * @return string
     */
    public function getSQLPKExpression() {
        $pkExpressions = $this->isUnioned() ? ["'".self::MULTITENANT_SCHEMA_PLACEHOLDER."'"] : [];
        foreach($this->tableRelations as $relation) {
            if($relPKExpression = $relation->getSQLPKExpression())
                $pkExpressions[] = $relPKExpression;
        }
        return implode(" || '".self::PK_M_SEPARATOR."' || \n\n", $pkExpressions);
    }

    /**
     * returns the base select query for this dataSource
     * @param mixed $parameters
     * @return string
     */
    public function getSqlBaseColumnsSelect($parameters = array()) {
            $expressions = [];

            foreach($this->tableRelations as $relation) {
                $relationExpressions = $relation->getSqlExpressions();
                //since the first relations are more likely to exist in the database, this ensures that any field that
                //has the same name in more than one relation, always refers to the first
                $expressions = array_merge($relationExpressions, $expressions );
            }

            //filter and massage expression so that only those referring to actual fields of the datasource get returned, and add AS expressions
            $filteredExpressions = $this->isUnioned() ? ["'" . self::MULTITENANT_SCHEMA_PLACEHOLDER . "' AS ".self::SCHEMA_IDENTIFIER] : [];
            foreach($expressions as $sqlAlias => $sqlExpression)
                if($this->hasField($sqlAlias) && !$this->getField($sqlAlias)->getLazyFetch())
                    $filteredExpressions[] = $sqlExpression.' AS "'.$sqlAlias.'"'; //preserve case

            $sql = "SELECT ".$this->getSQLPKExpression()." AS ".self::RECORD_IDENTIFIER.", ".implode(", \n",$filteredExpressions);
            
            return $sql;
    }

    /**
     * @param mixed $parameters
     * @return string
     */
    public function getSqlFrom($parameters = array()) {

        if( $r = $this->getShim()->callMethod(__METHOD__, func_get_args())) return $r;

        $ret = '';
        foreach($this->tableRelations as $relation) {

            $dbTarget  = $relation->getDatabaseObjectTarget();

            //allow for schema substitution for unioned DSs
            if($relation->getCoolSchema()->isMultiTenant())
                $dbTarget = self::MULTITENANT_SCHEMA_PLACEHOLDER.'.'.$dbTarget;

            $tableAlias = $relation->getAlias();
            $joinCondition = $relation->getJoinCondition();
            $alias = $tableAlias ? "AS $tableAlias " : '';

            if($joinCondition) {
                $ret.=" LEFT JOIN $dbTarget $alias ON $joinCondition\n";
            } else $ret = "FROM $dbTarget $alias\n"; //only the first table does not have join condition

            //fetch 1:1 files too
            /*
            if(!$relation->isView()) {
                $tableMap = $db->getDictionary()->getPropelTableMap($tableName);
                $pkField = $tableMap->getPkFields()[0];
                $fileCategories = $tableMap->getFileCategories();

                foreach($fileCategories as $cat) {
                    if($cat->getMaxCount()==1) {
                        $filesTableQualifier = $relation->getCoolSchema()->getCurrentSchema().'.'.$relation->getSchema().'_files';
                        $fieldName = $cat->getName();
                        $ret.="LEFT JOIN {$filesTableQualifier} {$prefix}$fieldName
                                    ON  {$prefix}$fieldName.source_table = '{$rawTableName}'
                                    AND {$prefix}$fieldName.source_table_id = $qualifier.$pkField
                                    AND {$prefix}$fieldName.category = '$fieldName'\n";
                    }
                }
            }
            */
        }
        return $ret;
    }

    /**
     * @inheritdoc
     */
    public function getRowFiles($row, $hydrate=false) {
        $ret = [];
        foreach($this->tableRelations as $relationIndex => $relation) {

            $db = $relation->getSchema() ? $relation->getCoolSchema() : $this->getCoolSchema();

            $tableName  = $relation->getTable();
            $rawTableName  = $relation->getRawTableName();
            $prefix = $relation->getPrefix();

            if(!$relation->isView()) {
                $tableMap = $db->getDictionary()->getPropelTableMap($tableName);
                $pkField = $tableMap->getPkFields()[0];
                $fileCategories = $tableMap->getFileCategories();

                foreach($fileCategories as $cat) {
                    if($cat->getMaxCount()==1) {
                        if($repo = $this->getFileRepository($row[self::RECORD_IDENTIFIER], $relationIndex)) {
                            $files = $repo->getChildrenOf('cat_' . $cat->getName(), false);
                            if($files->count() == 1)
                                $ret[$prefix.$cat->getName()] = $hydrate ? $files->fetch()[0] : $files->fetch()[0]->getId();
                        }
                    }
                }
            }

        }
        return $ret;
    }

    /**
     * returns the SQL group by portion
     * @param mixed $parameters
     * @return string[]
     */
    protected function getGroupByExpressions($parameters = array())
    {
        $filteredExpressions = [];

        if($this->isInAuditMode()) {
            //since the actual pk of the audit tables is different than the pk of the original tables, we have to group
            //by all the fields in the select clause to avoid pg complaining
            foreach($this->tableRelations as $relation) {
                $expressions = $relation->getSqlExpressions();

                foreach($expressions as $sqlAlias => $sqlExpression)
                    //TODO: filter out not visible fields, but include pks
                    //if($this->hasField($sqlAlias) && !$this->getField($sqlAlias)->getLazyFetch())
                        $filteredExpressions[] = $sqlExpression; //preserve case
            }
        }

        return $filteredExpressions;
    }

    /**
     * TODO: fix that for multiple relations!
     *
     * this is a quick&dirty template method to manage correctly extended columns
     *
     * @param string $columnName
     * @param string $paramName
     * @param $condition
     * @return string
     */
    protected function getClause($columnName, $paramName, $condition) {
        /*if($db = $this->getCoolSchema()) {
            $tableMap = $db->getDictionary()->getPropelTableMap($this->tableName);
            $coolField = $tableMap->getCoolField($columnName);
            if($coolField->isExtension()) {
                return "(".$coolField->getExtensionContainer()."->>'$columnName' = $paramName)";
            }
        }*/
        return parent::getClause($columnName, $paramName, $condition);
    }

    /**
     * returns the where portion
     * @param mixed $parameters parameters coming from the request
     * @param mixed $query
     * @returns array
     */
    public function getSqlWhere($parameters = array(), $query=null) {
         $ret = parent::getSqlWhere($parameters, $query);
         
         //this is a json object containing fields which are fixed, as the lister is filtered by the value of these fields (eg. in a one to many relation)
         if(isset($parameters['_filter'])) {
            if( $fields = json_decode($parameters['_filter']) ) {
                foreach($fields as $f=>$v)
                    if($v!==null) {
                        $paramName = preg_replace('/[^a-zA-Z]/sim','',"filter_$f");
                        $ret['statement'].=" AND $f=:$paramName";
                        $ret['parameters'][":$paramName"]=$v;
                    }
            }
         }

        $recordId = @$parameters[self::RECORD_IDENTIFIER];

        if($recordId !== null && $recordId !== '') {
            $recordPks = $this->extractRelationPks($recordId);
            $pkCursor = $this->isUnioned() ? 1 : 0; //skip the first PK which is always the schema

            foreach($this->tableRelations as $relation)
                if($relation->getSQLPKExpression()) {
                    $relationPk = @$recordPks[ $pkCursor++ ];
                    if($relationPk !== null && $relationPk !== '' && $relationPk != self::NULL_PK_SYMBOL) {
                        $qualifier = $relation->getQualifier();
                        $pkField = $relation->getPKfields()[ 0 ];
                        $variableName = ":a" . md5('record_pk' . $qualifier);

                        @$ret[ 'statement' ] .= " AND ({$qualifier}.{$pkField} = {$variableName}) ";
                        @$ret[ 'parameters' ][ $variableName ] = $relationPk;
                    }
                }
         }

         return $ret;
    }

    /**
     * returns the default file repository instance that retrieves and stores files
     * if a relation is specified, it returns its repo
     *
     * @param mixed $recordid
     * @param int $relationIndex
     * @return FileRepositoryInterface
     */
    public function getFileRepository($recordid = null, $relationIndex=null)
    {
        $pks = $this->extractRelationPks($recordid);
        foreach($this->tableRelations as $key => $relation)
            if($relationIndex===null || $key==$relationIndex) {
                $tableName  = $relation->getTable();
                $pk = self::string2pk(@$pks[$key]);
                $db = $relation->getSchema() ? $relation->getCoolSchema() : $this->getCoolSchema();
                if( ($obj = $db->getPropelObject($tableName, $pk)) && !$obj->isNew()) {
                    return $obj->getFileRepository();
                }
            }
    }

    /**
     * //gridx enumerates columns starting from 1, and the first one is reserved for tools.
     * TODO: refactor this, having the client resolve column offsets to actual field names
     * TODO: get rid of the dependancy with gridx and cool lister (offset 2)
     *
     * @param $colName
     * @return string
     */
    public function _sqlExpression($colName) {

        if( $r = $this->getShim()->callMethod(__METHOD__, func_get_args())) return $r;

        $db = $this->getCoolSchema();

        foreach($this->tableRelations as $relation) {
            $tableName  = $relation->getTable();
            $prefix = $relation->getPrefix();
            $qualifier = $relation->getQualifier();


            if($tableMap = $db->getDictionary()->getPropelTableMap($tableName)) {
                $fields = $tableMap->getCoolFields();
                foreach($fields as $fieldName => $coolField) {
                    if(!$coolField->isExtension()) {

                        if( ($prefix && $colName=="{$prefix}$fieldName") ||
                            (!$prefix && $fieldName==$colName) )
                            return "$qualifier.$fieldName";

                    }
                }
            }

            return $colName;
        }

    }

    /**
     * For UNIONed DSs, assign the first schema of the pool to the multitenant schema, so that operations that explore
     * the schema metadata can work as usual
     * @throws \Exception
     */
    private function initializeMultitenantSchema() {
        $schema = $this->getCoolSchema();
        if($schema->isMultiTenant()) {
            $schema->setCurrentSchema($this->getCoolSchema()->getSiblingSchemas()[0]);
        }
    }

}

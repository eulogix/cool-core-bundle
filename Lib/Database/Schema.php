<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Database;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AsyncJob;
use Eulogix\Cool\Lib\Database\Propel\Behaviors\NotifierBehavior;
use Eulogix\Cool\Lib\DataSource\CoolValueMap;
use Eulogix\Cool\Lib\DataSource\DataSourceInterface;
use Eulogix\Cool\Lib\DataSource\DSField;
use Eulogix\Cool\Lib\Dictionary\Field;
use Eulogix\Cool\Lib\File\CoolTableFileRepository;
use Eulogix\Cool\Lib\Form\Field\FieldInterface;
use Eulogix\Cool\Lib\Traits\CoolCacheShimmed;
use Eulogix\Lib\Cache\CacheDecorator;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Lib\Cache\Shimmable;
use Eulogix\Lib\Database\Postgres\PgUtils;
use Eulogix\Cool\Lib\Database\Propel\CoolPropelObject;
use Eulogix\Cool\Lib\DataSource\CoolCrudDataSource;
use Eulogix\Cool\Lib\Dictionary\Dictionary;
use \DateTime;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */
class Schema implements Shimmable
{

    use CoolCacheShimmed;

    /**
     * @var string The propel database name
     */
    protected $name;

    /**
     * @var string
     */
    protected $currentSchema = 'public';

    /**
     * @var Dictionary The dictionary
     */
    protected $dictionary;

    /**
     * @var \DateTime
     */
    private $instant;

    /**
     * @var Dictionary The dictionary
     */
    protected $propelModelNamespace;

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * pool of pre-built DataSources used for field decodification
     * @var DataSourceInterface[]
     */
    private $dataSourcePool = [];


    /**
     * @var DateTime[]
     */
    private $matViewsRefreshLog = [];

    /**
     * @param string $name
     * @param string $propelModelNamespace
     */
    function __construct($name, $propelModelNamespace)
    {
        $this->name = $name;
        $this->propelModelNamespace = $propelModelNamespace;
    }

    /**
     * @return string
     */
    public function getShimUID() {
        $session = Cool::getInstance()->getFactory()->getSession();
        return md5(implode(';',[
            get_class($this),
            ($this->isMultiTenant() ? $this->getCurrentSchema() : ""),
            ($session && $session->getDebugLookups() ? "_DL_" : "_NODL_"),
            ($session ? $session->getLocale() : "")
        ]));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the schema bound to the current connection.
     * @return string
     */
    public function getCurrentSchema()
    {
        return $this->currentSchema;
    }

    /**
     * @param string $schemaName
     * @throws \Exception
     */
    public function setCurrentSchema($schemaName)
    {
        if($attachedTo = $this->getAttachedToSchema())
            return $attachedTo->setCurrentSchema($schemaName);

        if (!$this->isSchemaNameValid($schemaName)) {
            throw new \Exception("$schemaName is not a valid schema name for " . $this->name);
        }
        $this->currentSchema = $schemaName;
        $this->dataSourcePool = [];
        Cool::getInstance()->refreshSearchPaths();
    }

    /**
     * @param string $schemaName
     * @return bool
     */
    public function isSchemaNameValid($schemaName)
    {
        return in_array($schemaName, array_merge($this->getSiblingSchemas(), ['public']));
    }

    /**
     * Called on instantiation
     */
    public function init()
    {
    }

    /**
     * @param \DateTime $instant
     * @return $this
     */
    public function setInstant(\DateTime $instant)
    {
        $this->instant = $instant;
        $schema = $this->getCurrentSchema();
        $this->query("SELECT $schema.enter_audit_mode(:instant)", [":instant" => $instant->format('c')]);

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getInstant()
    {
        return $this->instant;
    }

    /**
     * @return boolean
     */
    public function isInAuditMode()
    {
        return $this->getInstant() instanceof \DateTime;
    }

    public function exitAuditMode()
    {
        $schema = $this->getCurrentSchema();
        $this->query("SELECT $schema.exit_audit_mode()");
    }

    /**
     * @return Dictionary
     */
    function getDictionary()
    {
        if (!$this->dictionary) {
            $dictionaryClass = $this->propelModelNamespace . "\\Dictionary";
            $this->dictionary = new CacheDecorator(
                new $dictionaryClass($this->getCurrentSchema()),
                Cool::getInstance()->getFactory()->getCacher()
            );
        }

        return $this->dictionary;
    }

    /**
     * @param string $sql
     * @param array $bindParams
     * @param bool $purgeParams
     * @param int $cacheDurationSeconds
     * @return array
     */
    public function fetch($sql, $bindParams = null, $purgeParams = false, $cacheDurationSeconds = 0)
    {

        if ($cacheDurationSeconds) {
            $cacher = Cool::getInstance()->getFactory()->getCacher();
            $cacheToken = $cacher->tokenize(func_get_args());
            if ($cacher->exists($cacheToken)) {
                return $cacher->fetch($cacheToken);
            }
        }

        $sth = $this->query($sql, $bindParams, $purgeParams);
        $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
        //if we issued a query like select count(*) from...the raw value is returned
        if ($sth->columnCount() == 1 && $sth->rowCount() == 1) {
            $ret = array_pop($result[ 0 ]);
        } //if we have only 1 record returned, we return it instead of an array of arrays
        elseif ($sth->rowCount() == 1) {
            $ret = $result[ 0 ];
        } elseif ($result == []) {
            $ret = null;
        } else {
            $ret = $result;
        }

        if ($cacheDurationSeconds) {
            $cacher->store($cacheToken, $ret);
        }

        return $ret;
    }

    /**
     * @param string $sql
     * @param array $bindParams
     * @param bool $purgeParams
     * @param int $cacheDurationSeconds
     * @return array
     */
    public function fetchArray($sql, $bindParams = null, $purgeParams = false, $cacheDurationSeconds = 0)
    {

        if ($cacheDurationSeconds) {
            $cacher = Cool::getInstance()->getFactory()->getCacher();
            $cacheToken = $cacher->tokenize(func_get_args());
            if ($cacher->exists($cacheToken)) {
                return $cacher->fetch($cacheToken);
            }
        }

        $sth = $this->query($sql, $bindParams, $purgeParams);
        $ret = $sth->fetchAll(\PDO::FETCH_ASSOC);

        if ($cacheDurationSeconds) {
            $cacher->store($cacheToken, $ret);
        }

        return $ret;
    }

    /**
     * @param string $sql
     * @param string $keyField
     * @param bool $omitKeyFieldInResult
     * @param array $bindParams
     * @param bool $purgeParams
     * @param int $cacheDurationSeconds
     * @return array|mixed
     */
    public function fetchKeyedArray(
        $sql,
        $keyField,
        $omitKeyFieldInResult = false,
        $bindParams = null,
        $purgeParams = false,
        $cacheDurationSeconds = 0
    ) {

        if ($cacheDurationSeconds) {
            $cacher = Cool::getInstance()->getFactory()->getCacher();
            $cacheToken = $cacher->tokenize(func_get_args());
            if ($cacher->exists($cacheToken)) {
                return $cacher->fetch($cacheToken);
            }
        }

        $sth = $this->query($sql, $bindParams, $purgeParams);
        $tmp = $sth->fetchAll(\PDO::FETCH_ASSOC);
        $ret = [];
        foreach ($tmp as $row) {
            $rowKey = $row[ $keyField ];
            if ($omitKeyFieldInResult) {
                unset( $row[ $keyField ] );
            }
            $ret[ $rowKey ] = $row;
        }

        if ($cacheDurationSeconds) {
            $cacher->store($cacheToken, $ret);
        }

        return $ret;
    }

    /**
     * @param string $sql
     * @param array $bindParams
     * @param bool $purgeParams
     * @param int $cacheDurationSeconds
     * @return array|mixed
     */
    public function fetchArrayWithNumericKeys($sql, $bindParams = null, $purgeParams = false, $cacheDurationSeconds = 0)
    {

        if ($cacheDurationSeconds) {
            $cacher = Cool::getInstance()->getFactory()->getCacher();
            $cacheToken = $cacher->tokenize(func_get_args());
            if ($cacher->exists($cacheToken)) {
                return $cacher->fetch($cacheToken);
            }
        }

        $sth = $this->query($sql, $bindParams, $purgeParams);
        $ret = $sth->fetchAll(\PDO::FETCH_NUM);

        //if the result is an array of arrays with a single element, we collapse it
        if (count($ret) > 0 && count($ret[ 0 ]) == 1) {
            $newRet = [];
            foreach ($ret as $row) {
                $newRet[] = $row[ 0 ];
            }
            $ret = &$newRet;
        }

        if ($cacheDurationSeconds) {
            $cacher->store($cacheToken, $ret);
        }

        return $ret;
    }

    /**
     * @param string $sql
     * @param array $bindParams
     * @param bool $purgeParams
     * @return int
     */
    public function countRows($sql, $bindParams = null, $purgeParams = false)
    {
        $sth = $this->query($sql, $bindParams, $purgeParams);

        return $sth->rowCount();
    }

    /**
     * @param string $sql
     * @param null $bindParams
     * @param bool $purgeParams
     * @return \PDOStatement
     */
    public function query($sql, $bindParams = null, $purgeParams = false)
    {
        $con = $this->getConnection();
        $con->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $sth = $con->prepare($sql);

        $bindParams = @count($bindParams) > 0 ? $bindParams : null;

        //purge $bindParams from unneeded parameters (which cause an error) : the default is false, but may be switched to true. It is not done to prevent performance hits on the queries
        $p = [];
        if ($bindParams) {
            if ($purgeParams) {
                foreach ($bindParams as $param => $value) {
                    if (strpos($sql, $param) !== false) {
                        $p[ $param ] = $value;
                    }
                }
            } else {
                $p = $bindParams;
            }
        }

        //now bind params with their correct type
        foreach ($p as $paramName => $paramValue) {
            if ($paramValue instanceof \DateTime) {
                $type = \PDO::PARAM_STR;
                $paramValue = $paramValue->format('c');
            } elseif (is_bool($paramValue)) {
                $type = \PDO::PARAM_BOOL;
            } elseif (is_null($paramValue)) {
                $type = \PDO::PARAM_NULL;
            } elseif (is_int($paramValue)) {
                $type = \PDO::PARAM_INT;
            } else {
                $type = \PDO::PARAM_STR;
            }
            $sth->bindValue($paramName, $paramValue, $type);
        }

        if ($this->getDebug()) {
            $sth->debugDumpParams();
        }

        $sth->execute();

        return $sth;
    }

    /**
     * @param string $tableName
     * @param mixed $pk
     * @return CoolPropelObject|bool
     */
    public function getPropelObject($tableName, $pk = null)
    {
        if ($pk) {
            $queryClass = $this->getDictionary()->getPropelQueryNamespace($tableName);
            if (!class_exists($queryClass))
                $queryClass = $this->getDictionary()->getPropelQueryNamespace($this->getName().'.'.$tableName);

            if (class_exists($queryClass)) {
                return $queryClass::create()->findPk(
                    is_array($pk) && count($pk) == 1 ? $pk[ 0 ] : $pk,
                    $this->getConnection()
                );
            }
        } else {
            $modelClass = $this->getDictionary()->getPropelModelClassNamespace($tableName);
            if (!class_exists($modelClass))
                $modelClass = $this->getDictionary()->getPropelModelClassNamespace($this->getName().'.'.$tableName);

            if (class_exists($modelClass)) {
                return new $modelClass();
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isMultiTenant()
    {
        return count($this->getSiblingSchemas()) > 1;
    }

    /**
     * in a multi-tenancy environment, you will have several schemas in the same database, each schema having the same set of tables
     * (defined by the propel xml schema). In this case, you should make this function return an array of schema names for this database.
     * By default, only the default 'public' schema is returned
     *
     * @return array
     */
    public function getSiblingSchemas()
    {
        return array($this->getCurrentSchema());
    }

    /**
     * return the physical database name
     *
     * @return string
     */
    public function getDatabaseName()
    {
        $p = $this->getConnectionParameters();
        return $p[ 'dsn_info' ][ 'dbname' ] ?? null;
    }

    /**
     * @param string $siblingName
     * @return string
     */
    public function getAuditSchemaNameForSibling($siblingName)
    {
        return $siblingName . '_audit';
    }

    /**
     * @return string
     */
    public function getCurrentAuditSchemaName()
    {
        return $this->getAuditSchemaNameForSibling($this->getCurrentSchema());
    }

    /**
     * @return array
     * @throws \PropelException
     */
    public function getConnectionParameters()
    {
        $p = $this->getConnection()->getConfiguration()->getParameters();
        $defaultConnection = $p[ 'datasources' ][ 'default' ];
        $pp = $p[ 'datasources' ][ $defaultConnection ][ 'connection' ];

        //extract further info from dsn
        $dsn = [];
        preg_match_all('/([a-z]+)=([^;]+)/im', $pp[ 'dsn' ], $mm, PREG_SET_ORDER);
        foreach ($mm as $m) {
            $dsn[ $m[ 1 ] ] = $m[ 2 ];
        }
        $pp[ 'dsn_info' ] = $dsn;

        return $pp;
    }

    /**
     * returns the translation domain for the base database objects such as fields, validations, messages
     *
     * @return string
     */
    public function getTranslationDomain($tableName = null)
    {
        return 'COOL_DB_' . $this->name . ( $tableName ? '_TABLE_' . $tableName : '' );
    }

    /**
     * @return \PropelPDO
     */
    public function getConnection()
    {
        return Cool::getInstance()->getConnection();
    }

    /**
     * helper that quickly decodes a field using its valueMap
     * @param string $tableName
     * @param string $fieldName
     * @param string $value
     * @param null $recordId
     * @return mixed
     */
    public function decodeField($tableName, $fieldName, $value, $recordId = null)
    {
        if(!isset($this->dataSourcePool[$tableName])) {
            $this->dataSourcePool[$tableName] = CoolCrudDataSource::fromSchemaAndTable($this->getName(), $tableName);
            $this->dataSourcePool[$tableName]->build();
        }
        return $this->dataSourcePool[$tableName]->getDecodedValue($fieldName, $value, $recordId);
    }

    /**
     * @param boolean $debug
     * @return $this
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * basic implementation of valuemap table creation.
     * Can be extended in other schemas to apply filters and further processing
     * @param $domainName
     * @return array|mixed
     */
    public function getValueMapTable($domainName)
    {
        $ret = $this->getLookupTable($domainName);
        if (Cool::getInstance()->getFactory()->getSession()->getDebugLookups()) {
            foreach ($ret as &$mapItem) {
                $mapItem[ 'label' ] .= " ('{$mapItem['value']}')";
            }
            array_unshift($ret, ['value' => '', 'label' => "*** $domainName ***"]);
        }

        return $ret;
    }

    /**
     * basic implementation of OTLT lookup table fetching, points to the core schema without filters.
     * Can be extended in other schemas to apply filters and further processing
     *
     * @param $domainName
     * @return array|mixed
     */
    public function getOTLTLookupTable($domainName)
    {
        $cdb = Cool::getInstance()->getCoreSchema();
        $currentLocale = Cool::getInstance()->getFactory()->getSession()->getLocale();

        $stmt = "SELECT value, dec_{$currentLocale} AS label, sort_order, ext, filter FROM lookup WHERE domain_name=:domain_name";
        if ($filter = $this->getSchemaFilter()) {
            $stmt .= " AND (schema_filter IS NULL OR ('{$filter}' = ANY(schema_filter)))";
        }
        $stmt .= " ORDER BY sort_order ASC, label ASC";

        $ret = $cdb->fetchArray($stmt, array(":domain_name" => $domainName), false, 60 * 5);
        foreach ($ret as &$row) {
            foreach ($row as $k => $v) {
                if (PgUtils::isPGArray($v)) {
                    $row[ $k ] = PgUtils::fromPGArray($v);
                }
            }
        }
        
        return $ret;
    }

    /**
     * basic implementation of TABLE lookup retrieving. Retrieves the full lookup table. EN is the safe fallback
     * Can be extended in other schemas to apply filters and further processing
     * @param $domainName
     * @return array|mixed
     */
    public function getLookupTable($domainName)
    {
        $cdb = Cool::getInstance()->getCoreSchema();
        $tableName = strtolower($domainName);
        $currentLocale = Cool::getInstance()->getFactory()->getSession()->getLocale();

        $stmt = "SELECT *, COALESCE(dec_{$currentLocale}, '*' || dec_en) AS label FROM lookups.{$tableName}";
        if ($filter = $this->getSchemaFilter()) {
            $stmt .= " WHERE (schema_filter IS NULL AND NOT ('{$filter}' = ANY(schema_filter_inv)) ) OR  
            (schema_filter IS NULL AND schema_filter_inv IS NULL) 
            OR ('{$filter}' = ANY(schema_filter))";
        }
        $stmt .= " ORDER BY sort_order ASC, label ASC";

        $ret = $cdb->fetchArray($stmt, [], false, 60 * 5);
        foreach ($ret as &$row) {
            foreach ($row as $k => $v) {
                if (PgUtils::isPGArray($v)) {
                    $row[ $k ] = PgUtils::fromPGArray($v);
                }
            }
        }

        return $ret;
    }

    /**
     * @param string $value
     * @param string $domainName
     * @return bool
     */
    public function lookupValueExists($value, $domainName) {
        return in_array($value, $this->getLookupValues($domainName));
    }

    /**
     * use this to avoid exceptions when you need to add a lookup that may not exist
     * @param $value
     * @param $domainName
     * @return \PDOStatement
     */
    public function insertLookupValue($value, $domainName) {
        $tableName = strtolower($domainName);
        $locales = Cool::getInstance()->getFactory()->getLocaleManager()->getAvailableLocales();

        $localeFields = array_map(function($locale){
            return "\"dec_{$locale}\"";
        }, $locales);

        $localePlaceHolders = array_map(function($locale){
            return ":{$locale}";
        }, $locales);

        $sql = "INSERT INTO lookups.{$tableName} (\"value\", ".implode(',',$localeFields).") VALUES (:value, ".implode(',',$localePlaceHolders).")";
        $parameters = [':value' => $value];
        foreach($localePlaceHolders as $pc)
            $parameters[$pc] = $value;

        return $this->query($sql, $parameters);
    }

    /**
     * returns a string if a filter has to be applied when retrieving lookup tables, null otherwise
     * @return null|string
     */
    protected function getSchemaFilter()
    {
        return $this->isMultiTenant() ? $this->getCurrentSchema() : null;
    }

    /**
     * retrieves only the lookup Values
     * @param $domainName
     * @return array
     */
    public function getLookupValues($domainName)
    {
        return array_column($this->getLookupTable($domainName), 'value');
    }

    /**
     * When lookup tables are extended with custom attributes, the distinct value of these
     * attributes can be used to group and filter values and so on.
     * This helper returns a list of all the unique values of the custom column.
     * If the column is an ARRAY type, it returns the distinct values IN the arrays, and not the arrays themselves
     * @param string $domainName
     * @param string $columnName
     * @return array
     */
    public function getDistinctValuesFromAdditionalLookupColumn($domainName, $columnName)
    {
        $ret = [];
        $lkTable = $this->getLookupTable($domainName);
        foreach ($lkTable as $row) {
            if ($v = $row[ $columnName ] ?? null) {
                if (is_array($v)) {
                    foreach ($v as $av) {
                        $ret[ $av ] = 1;
                    }
                } else {
                    $ret[ $v ] = 1;
                }
            }
        }
        $temp = array_keys($ret);
        sort($temp);

        return $temp;
    }

    /**
     * fetches the value of a column in the lookup table for a given domain/value
     * @param string $domainName
     * @param string $value
     * @param string $column
     * @return mixed
     */
    public function getLookupColumn($domainName, $value, $column)
    {
        $lkTable = $this->getLookupTable($domainName);
        foreach ($lkTable as $row) {
            if ($value == $row[ 'value' ] ?? null) {
                return $row[ $column ] ?? null;
            }
        }
        return null;
    }

    /**
     * builds a JOIN condition for a lookup table
     * @param string $domainName
     * @param string $alias
     * @param string $field
     * @return string
     */
    public function buildLookupJoin($domainName, $alias, $field)
    {
        return "LEFT JOIN lookups.{$domainName} {$alias} ON {$field} = {$alias}.value";
    }


    /**
     * builds an array of fields for a given table
     *
     * @param mixed $tableName
     * @param string $prefix
     * @param callable $lambdaFilter
     * @param bool $addConstraints
     * @return DSField[]
     * @throws \PropelException
     */
    public function getDSFieldsFor($tableName, $prefix='', $lambdaFilter=null, $addConstraints=false) {

        if($lambdaFilter == null) {
            if( $r = $this->getShim()->callMethod(__METHOD__, func_get_args())) return array_copy($r);
        }

        $ret = [];
        $dict = $this->getDictionary();
        if($tableMap = $dict->getPropelTableMap($tableName)) {
            $pk_arr = $tableMap->getPkFields();

            //add database fields
            $fields = $tableMap->getCoolFields();
            foreach($fields as $fieldName => $coolField)
                if(!is_callable($lambdaFilter) || call_user_func($lambdaFilter, $fieldName)) {
                    $DSfield = new DSField($prefix.$fieldName);

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

                    if($vmap = CoolValueMap::getValueMapFor($this->getName(), $tableName, $fieldName)) {
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
                    $DSfield->setFileRepository( CoolTableFileRepository::fromSchemaAndTableName($this, $tableName) );
                    $ret[$prefix.$fieldName] = $DSfield;
                }
            }
        } elseif($tableName == $this->getFilesIndexTableName())
             return $this->getDSFieldsForFilesIndex($prefix);
        else return $this->getDSFieldsForView($tableName, $prefix, $lambdaFilter);

        return $ret;
    }

    /**
     * builds an array of fields for a given view
     *
     * @param mixed $viewName
     * @param string $prefix
     * @param callable $lambdaFilter
     * @returns DSField[]
     */
    public function getDSFieldsForView($viewName, $prefix='', $lambdaFilter=null) {

        if($lambdaFilter == null) {
            if( $r = $this->getShim()->callMethod(__METHOD__, func_get_args())) return array_copy($r);
        }

        $viewFields = $this->fetchArray("SELECT * FROM core.get_view_fields_origin('".$this->getCurrentSchema()."', '".$viewName."')");
        /** @var DSField[] $ret */
        $ret = [];
        $TableDSFields = [];
        $availableSchemaNames = Cool::getInstance()->getAvailableSchemaNames();
        foreach($viewFields as $viewField) {
            if($viewField['source_table'] && !isset($TableDSFields[$viewField['source_table']])) {
                $sourceSchemaName = in_array($viewField['source_schema'], $availableSchemaNames) ? $viewField['source_schema'] : null;
                $sourceSchema = $sourceSchemaName ? Cool::getInstance()->getSchema($sourceSchemaName) : $this;
                $TableDSFields[$viewField['source_table']] = $sourceSchema->getDSFieldsFor($viewField['source_table'], $prefix, $lambdaFilter);
            }
            /** @var DSField $DSField */
            if(!is_callable($lambdaFilter) || call_user_func($lambdaFilter, $viewField['view_column'])) {

                $DSField =  $TableDSFields[$viewField['source_table']][$prefix.$viewField['source_column']] ??
                            $this->getDSFieldForPGDataType($prefix.$viewField['view_column'], $viewField['data_type']);

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
     * builds an array of fields for the schema files index
     *
     * @param string $prefix
     * @returns DSField[]
     */
    public function getDSFieldsForFilesIndex($prefix='') {
        $ret = [];

        $fileId         = new DSField($n = $prefix.'file_id');                  $fileId->setType(\PropelTypes::INTEGER);            $ret[$n] = $fileId;
        $fileSize       = new DSField($n = $prefix.'file_size');                $fileSize->setType(\PropelTypes::INTEGER);          $ret[$n] = $fileSize;
        $fileName       = new DSField($n = $prefix.'file_name');                $fileName->setType(\PropelTypes::LONGVARCHAR);      $ret[$n] = $fileName;
        $uploadDate     = new DSField($n = $prefix.'upload_date');              $uploadDate->setType(\PropelTypes::TIMESTAMP);      $ret[$n] = $uploadDate;
        $lastModDate    = new DSField($n = $prefix.'last_modification_date');   $lastModDate->setType(\PropelTypes::TIMESTAMP);     $ret[$n] = $lastModDate;

        $uploadedBy     = new DSField($n = $prefix.'uploaded_by_user');         $uploadedBy->setType(\PropelTypes::INTEGER);        $ret[$n] = $uploadedBy;
        $uploadedBy->setValueMap(CoolValueMap::getValueMapForTable('core', 'account'));

        $sha1           = new DSField($n = $prefix.'checksum_sha1');            $sha1->setType(\PropelTypes::LONGVARCHAR);          $ret[$n] = $sha1;
        $sourceTable    = new DSField($n = $prefix.'source_table');             $sourceTable->setType(\PropelTypes::LONGVARCHAR);   $ret[$n] = $sourceTable;
        $category       = new DSField($n = $prefix.'category');                 $category->setType(\PropelTypes::LONGVARCHAR);      $ret[$n] = $category;

        return $ret;
    }

    /**
     * @return string
     */
    public function getFilesIndexTableName() {
        if($attachedTo = $this->getAttachedToSchema())
            return $attachedTo->getFilesIndexTableName();
        return $this->getName().'_files';
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

    /**
     * @return string[]
     */
    public function getNotificationChannels() {
        $ret = [];
        $tmaps = $this->getDictionary()->getPropelTableMaps();
        foreach($tmaps as $tmap) {
            if($tmap->hasBehavior('notifier')) {
                $settings = $tmap->getBehaviors()['notifier'];
                $baseChannel = NotifierBehavior::cleanChannelName( $settings['channel'] ? $settings['channel'] : 'c_'.$tmap->getCoolRawName() );
                $ret[ $baseChannel ] = 1;
                foreach($this->getSiblingSchemas() as $sibling)
                    $ret[ $sibling.';'.$baseChannel ] = 1;
            }
        }
        return array_keys($ret);
    }

    /**
     * @param string $viewName
     * @param int $maxIntervalSeconds if defined, limits the rate of refresh
     * @param bool $concurrently
     * @param bool $asyncJob
     */
    public function refreshMaterializedViewInAllSchemas($viewName, $maxIntervalSeconds = 0, $concurrently = true, $asyncJob = false) {
        $schemas = $this->getSiblingSchemas();
        foreach($schemas as $schema) {
            $this->refreshMaterializedView($viewName, $schema, $maxIntervalSeconds, $concurrently, $asyncJob);
        }
    }

    /**
     * @param string $viewName
     * @param null $schema one of the siblings schemas
     * @param int $maxIntervalSeconds if defined, limits the rate of refresh
     * @param bool $concurrently
     * @param bool $asyncJob
     */
    public function refreshMaterializedView($viewName, $schema = null, $maxIntervalSeconds = 0, $concurrently = true, $asyncJob = false) {
        $schema = $schema ?? $this->getCurrentSchema();
        if($this->isSchemaNameValid($schema)) {
            $token = $schema.$viewName;
            $now = new DateTime();
            if( !$maxIntervalSeconds
                || !isset($this->matViewsRefreshLog[$token])
                || ($now->getTimestamp() - $this->matViewsRefreshLog[$token]->getTimestamp() >= $maxIntervalSeconds) ) {

                $this->matViewsRefreshLog[ $token ] = $now;
                if(!$asyncJob) {
                    try {
                        $this->query("REFRESH MATERIALIZED VIEW ".($concurrently ? "CONCURRENTLY" : '')." $schema.$viewName");
                    } catch(\Throwable $e) {
                        //do nothing for now
                    }
                } else {
                    $job = new AsyncJob();

                    $job->setExecutorType(AsyncJob::EXECUTOR_RUNDECK)
                        ->setJobPath("cool:database:refreshMaterializedView")
                        ->setParametersArray([
                            'schema_name'           => $this->getName(),
                            'actual_schema_name'    => $schema,
                            'view_name'             => $viewName
                        ])->execute(false);
                }
            }
        }
    }

    /**
     * @return string|bool
     */
    public function getAttachedToSchemaName() {
        return Cool::getInstance()->getAttachedToSchemaName($this->getName());
    }

    /**
     * @return bool|Schema
     * @throws \Exception
     */
    public function getAttachedToSchema() {
        if($schemaName = $this->getAttachedToSchemaName())
            return Cool::getInstance()->getSchema($schemaName);
        return false;
    }

    /**
     * @return \string[]
     */
    public function getAttachedSchemas() {
        return Cool::getInstance()->getSchemaNamesAttachedTo($this->getName());
    }
}

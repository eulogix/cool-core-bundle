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

use Eulogix\Lib\Cache\CacheDecorator;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Lib\Database\Postgres\PgUtils;
use Eulogix\Cool\Lib\Database\Propel\CoolPropelObject;
use Eulogix\Cool\Lib\DataSource\CoolCrudDataSource;
use Eulogix\Cool\Lib\Dictionary\Dictionary;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */
class Schema
{

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
        if (!$this->isSchemaNameValid($schemaName)) {
            throw new \Exception("$schemaName is not a valid schema name for " . $this->name);
        }
        $this->currentSchema = $schemaName;
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
            $queryClass = $this->getDictionary()->getTableAttribute(
                $tableName,
                Dictionary::TBL_ATT_PROPEL_QUERY_NAMESPACE
            );
            if (!$queryClass) {
                $queryClass = $this->getDictionary()->getTableAttribute(
                    $this->getName() . '.' . $tableName,
                    Dictionary::TBL_ATT_PROPEL_QUERY_NAMESPACE
                );
            }
            $ret = $queryClass::create()->findPk(
                is_array($pk) && count($pk) == 1 ? $pk[ 0 ] : $pk,
                $this->getConnection()
            );

            return $ret;
        } else {
            $modelClass = $this->getDictionary()->getTableAttribute(
                $tableName,
                Dictionary::TBL_ATT_PROPEL_MODEL_NAMESPACE
            );
            if (!$modelClass) {
                $modelClass = $this->getDictionary()->getTableAttribute(
                    $tableName,
                    Dictionary::TBL_ATT_PROPEL_MODEL_NAMESPACE
                );
            }
            if ($modelClass) {
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

        return @$p[ 'dsn_info' ][ 'dbname' ];
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
     * TODO: kinda slow, maybe pool the prebuilt datasources
     * @param string $tableName
     * @param string $fieldName
     * @param string $value
     * @param null $recordId
     * @return mixed
     */
    public function decodeField($tableName, $fieldName, $value, $recordId = null)
    {
        $ds = CoolCrudDataSource::fromSchemaAndTable($this->getName(), $tableName);

        return $ds->build()->getDecodedValue($fieldName, $value, $recordId);
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

        $stmt = "SELECT value AS value, dec_{$currentLocale} AS label FROM lookup WHERE domain_name=:domain_name";
        if ($filter = $this->getSchemaFilter()) {
            $stmt .= " AND (schema_filter IS NULL OR ('{$filter}' = ANY(schema_filter)))";
        }
        $stmt .= " ORDER BY sort_order ASC, label ASC";

        $ret = $cdb->fetchArray($stmt, array(":domain_name" => $domainName), false, 60 * 5);

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
            $stmt .= " WHERE schema_filter IS NULL OR ('{$filter}' = ANY(schema_filter))";
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
            if ($v = @$row[ $columnName ]) {
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
     */
    public function getLookupColumn($domainName, $value, $column)
    {
        $lkTable = $this->getLookupTable($domainName);
        foreach ($lkTable as $row) {
            if ($value == @$row[ 'value' ]) {
                return @$row[ $column ];
            }
        }
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

}

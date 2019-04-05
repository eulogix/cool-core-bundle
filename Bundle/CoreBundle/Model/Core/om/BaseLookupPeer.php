<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core\om;

use \BasePeer;
use \Criteria;
use \Exception;
use \PDO;
use \PDOStatement;
use \Propel;
use \PropelException;
use \PropelPDO;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\Lookup;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\LookupPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\map\LookupTableMap;

abstract class BaseLookupPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'cool_db';

    /** the table name for this class */
    const TABLE_NAME = 'core.lookup';

    /** the related Propel class for this table */
    const OM_CLASS = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\Lookup';

    /** the related TableMap class for this table */
    const TM_CLASS = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\map\\LookupTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 12;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 12;

    /** the column name for the lookup_id field */
    const LOOKUP_ID = 'core.lookup.lookup_id';

    /** the column name for the domain_name field */
    const DOMAIN_NAME = 'core.lookup.domain_name';

    /** the column name for the value field */
    const VALUE = 'core.lookup.value';

    /** the column name for the dec_it field */
    const DEC_IT = 'core.lookup.dec_it';

    /** the column name for the dec_en field */
    const DEC_EN = 'core.lookup.dec_en';

    /** the column name for the dec_es field */
    const DEC_ES = 'core.lookup.dec_es';

    /** the column name for the dec_pt field */
    const DEC_PT = 'core.lookup.dec_pt';

    /** the column name for the dec_el field */
    const DEC_EL = 'core.lookup.dec_el';

    /** the column name for the sort_order field */
    const SORT_ORDER = 'core.lookup.sort_order';

    /** the column name for the schema_filter field */
    const SCHEMA_FILTER = 'core.lookup.schema_filter';

    /** the column name for the filter field */
    const FILTER = 'core.lookup.filter';

    /** the column name for the ext field */
    const EXT = 'core.lookup.ext';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identity map to hold any loaded instances of Lookup objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array Lookup[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. LookupPeer::$fieldNames[LookupPeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('LookupId', 'DomainName', 'Value', 'DecIt', 'DecEn', 'DecEs', 'DecPt', 'DecEl', 'SortOrder', 'SchemaFilter', 'Filter', 'Ext', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('lookupId', 'domainName', 'value', 'decIt', 'decEn', 'decEs', 'decPt', 'decEl', 'sortOrder', 'schemaFilter', 'filter', 'ext', ),
        BasePeer::TYPE_COLNAME => array (LookupPeer::LOOKUP_ID, LookupPeer::DOMAIN_NAME, LookupPeer::VALUE, LookupPeer::DEC_IT, LookupPeer::DEC_EN, LookupPeer::DEC_ES, LookupPeer::DEC_PT, LookupPeer::DEC_EL, LookupPeer::SORT_ORDER, LookupPeer::SCHEMA_FILTER, LookupPeer::FILTER, LookupPeer::EXT, ),
        BasePeer::TYPE_RAW_COLNAME => array ('LOOKUP_ID', 'DOMAIN_NAME', 'VALUE', 'DEC_IT', 'DEC_EN', 'DEC_ES', 'DEC_PT', 'DEC_EL', 'SORT_ORDER', 'SCHEMA_FILTER', 'FILTER', 'EXT', ),
        BasePeer::TYPE_FIELDNAME => array ('lookup_id', 'domain_name', 'value', 'dec_it', 'dec_en', 'dec_es', 'dec_pt', 'dec_el', 'sort_order', 'schema_filter', 'filter', 'ext', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. LookupPeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('LookupId' => 0, 'DomainName' => 1, 'Value' => 2, 'DecIt' => 3, 'DecEn' => 4, 'DecEs' => 5, 'DecPt' => 6, 'DecEl' => 7, 'SortOrder' => 8, 'SchemaFilter' => 9, 'Filter' => 10, 'Ext' => 11, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('lookupId' => 0, 'domainName' => 1, 'value' => 2, 'decIt' => 3, 'decEn' => 4, 'decEs' => 5, 'decPt' => 6, 'decEl' => 7, 'sortOrder' => 8, 'schemaFilter' => 9, 'filter' => 10, 'ext' => 11, ),
        BasePeer::TYPE_COLNAME => array (LookupPeer::LOOKUP_ID => 0, LookupPeer::DOMAIN_NAME => 1, LookupPeer::VALUE => 2, LookupPeer::DEC_IT => 3, LookupPeer::DEC_EN => 4, LookupPeer::DEC_ES => 5, LookupPeer::DEC_PT => 6, LookupPeer::DEC_EL => 7, LookupPeer::SORT_ORDER => 8, LookupPeer::SCHEMA_FILTER => 9, LookupPeer::FILTER => 10, LookupPeer::EXT => 11, ),
        BasePeer::TYPE_RAW_COLNAME => array ('LOOKUP_ID' => 0, 'DOMAIN_NAME' => 1, 'VALUE' => 2, 'DEC_IT' => 3, 'DEC_EN' => 4, 'DEC_ES' => 5, 'DEC_PT' => 6, 'DEC_EL' => 7, 'SORT_ORDER' => 8, 'SCHEMA_FILTER' => 9, 'FILTER' => 10, 'EXT' => 11, ),
        BasePeer::TYPE_FIELDNAME => array ('lookup_id' => 0, 'domain_name' => 1, 'value' => 2, 'dec_it' => 3, 'dec_en' => 4, 'dec_es' => 5, 'dec_pt' => 6, 'dec_el' => 7, 'sort_order' => 8, 'schema_filter' => 9, 'filter' => 10, 'ext' => 11, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, )
    );

    /**
     * Translates a fieldname to another type
     *
     * @param      string $name field name
     * @param      string $fromType One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                         BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
     * @param      string $toType   One of the class type constants
     * @return string          translated name of the field.
     * @throws PropelException - if the specified name could not be found in the fieldname mappings.
     */
    public static function translateFieldName($name, $fromType, $toType)
    {
        $toNames = LookupPeer::getFieldNames($toType);
        $key = isset(LookupPeer::$fieldKeys[$fromType][$name]) ? LookupPeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(LookupPeer::$fieldKeys[$fromType], true));
        }

        return $toNames[$key];
    }

    /**
     * Returns an array of field names.
     *
     * @param      string $type The type of fieldnames to return:
     *                      One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                      BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
     * @return array           A list of field names
     * @throws PropelException - if the type is not valid.
     */
    public static function getFieldNames($type = BasePeer::TYPE_PHPNAME)
    {
        if (!array_key_exists($type, LookupPeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return LookupPeer::$fieldNames[$type];
    }

    /**
     * Convenience method which changes table.column to alias.column.
     *
     * Using this method you can maintain SQL abstraction while using column aliases.
     * <code>
     *		$c->addAlias("alias1", TablePeer::TABLE_NAME);
     *		$c->addJoin(TablePeer::alias("alias1", TablePeer::PRIMARY_KEY_COLUMN), TablePeer::PRIMARY_KEY_COLUMN);
     * </code>
     * @param      string $alias The alias for the current table.
     * @param      string $column The column name for current table. (i.e. LookupPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(LookupPeer::TABLE_NAME.'.', $alias.'.', $column);
    }

    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param      Criteria $criteria object containing the columns to add.
     * @param      string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(LookupPeer::LOOKUP_ID);
            $criteria->addSelectColumn(LookupPeer::DOMAIN_NAME);
            $criteria->addSelectColumn(LookupPeer::VALUE);
            $criteria->addSelectColumn(LookupPeer::DEC_IT);
            $criteria->addSelectColumn(LookupPeer::DEC_EN);
            $criteria->addSelectColumn(LookupPeer::DEC_ES);
            $criteria->addSelectColumn(LookupPeer::DEC_PT);
            $criteria->addSelectColumn(LookupPeer::DEC_EL);
            $criteria->addSelectColumn(LookupPeer::SORT_ORDER);
            $criteria->addSelectColumn(LookupPeer::SCHEMA_FILTER);
            $criteria->addSelectColumn(LookupPeer::FILTER);
            $criteria->addSelectColumn(LookupPeer::EXT);
        } else {
            $criteria->addSelectColumn($alias . '.lookup_id');
            $criteria->addSelectColumn($alias . '.domain_name');
            $criteria->addSelectColumn($alias . '.value');
            $criteria->addSelectColumn($alias . '.dec_it');
            $criteria->addSelectColumn($alias . '.dec_en');
            $criteria->addSelectColumn($alias . '.dec_es');
            $criteria->addSelectColumn($alias . '.dec_pt');
            $criteria->addSelectColumn($alias . '.dec_el');
            $criteria->addSelectColumn($alias . '.sort_order');
            $criteria->addSelectColumn($alias . '.schema_filter');
            $criteria->addSelectColumn($alias . '.filter');
            $criteria->addSelectColumn($alias . '.ext');
        }
    }

    /**
     * Returns the number of rows matching criteria.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @return int Number of matching rows.
     */
    public static function doCount(Criteria $criteria, $distinct = false, PropelPDO $con = null)
    {
        // we may modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(LookupPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            LookupPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(LookupPeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(LookupPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        // BasePeer returns a PDOStatement
        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }
    /**
     * Selects one object from the DB.
     *
     * @param      Criteria $criteria object used to create the SELECT statement.
     * @param      PropelPDO $con
     * @return Lookup
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = LookupPeer::doSelect($critcopy, $con);
        if ($objects) {
            return $objects[0];
        }

        return null;
    }
    /**
     * Selects several row from the DB.
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      PropelPDO $con
     * @return array           Array of selected Objects
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelect(Criteria $criteria, PropelPDO $con = null)
    {
        return LookupPeer::populateObjects(LookupPeer::doSelectStmt($criteria, $con));
    }
    /**
     * Prepares the Criteria object and uses the parent doSelect() method to execute a PDOStatement.
     *
     * Use this method directly if you want to work with an executed statement directly (for example
     * to perform your own object hydration).
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      PropelPDO $con The connection to use
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     * @return PDOStatement The executed PDOStatement object.
     * @see        BasePeer::doSelect()
     */
    public static function doSelectStmt(Criteria $criteria, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(LookupPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            LookupPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(LookupPeer::DATABASE_NAME);

        // BasePeer returns a PDOStatement
        return BasePeer::doSelect($criteria, $con);
    }
    /**
     * Adds an object to the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database.  In some cases -- especially when you override doSelect*()
     * methods in your stub classes -- you may need to explicitly add objects
     * to the cache in order to ensure that the same objects are always returned by doSelect*()
     * and retrieveByPK*() calls.
     *
     * @param Lookup $obj A Lookup object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getLookupId();
            } // if key === null
            LookupPeer::$instances[$key] = $obj;
        }
    }

    /**
     * Removes an object from the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database.  In some cases -- especially when you override doDelete
     * methods in your stub classes -- you may need to explicitly remove objects
     * from the cache in order to prevent returning objects that no longer exist.
     *
     * @param      mixed $value A Lookup object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof Lookup) {
                $key = (string) $value->getLookupId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or Lookup object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(LookupPeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return Lookup Found object or null if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(LookupPeer::$instances[$key])) {
                return LookupPeer::$instances[$key];
            }
        }

        return null; // just to be explicit
    }

    /**
     * Clear the instance pool.
     *
     * @return void
     */
    public static function clearInstancePool($and_clear_all_references = false)
    {
      if ($and_clear_all_references) {
        foreach (LookupPeer::$instances as $instance) {
          $instance->clearAllReferences(true);
        }
      }
        LookupPeer::$instances = array();
    }

    /**
     * Method to invalidate the instance pool of all tables related to core.lookup
     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
    }

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @return string A string version of PK or null if the components of primary key in result array are all null.
     */
    public static function getPrimaryKeyHashFromRow($row, $startcol = 0)
    {
        // If the PK cannot be derived from the row, return null.
        if ($row[$startcol] === null) {
            return null;
        }

        return (string) $row[$startcol];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $startcol = 0)
    {

        return (int) $row[$startcol];
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function populateObjects(PDOStatement $stmt)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = LookupPeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = LookupPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = LookupPeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                LookupPeer::addInstanceToPool($obj, $key);
            } // if key exists
        }
        $stmt->closeCursor();

        return $results;
    }
    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     * @return array (Lookup object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = LookupPeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = LookupPeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + LookupPeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = LookupPeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            LookupPeer::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }

    /**
     * Returns the TableMap related to this peer.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getDatabaseMap(LookupPeer::DATABASE_NAME)->getTable(LookupPeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseLookupPeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseLookupPeer::TABLE_NAME)) {
        $dbMap->addTableObject(new \Eulogix\Cool\Bundle\CoreBundle\Model\Core\map\LookupTableMap());
      }
    }

    /**
     * The class that the Peer will make instances of.
     *
     *
     * @return string ClassName
     */
    public static function getOMClass($row = 0, $colnum = 0)
    {
        return LookupPeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a Lookup or Criteria object.
     *
     * @param      mixed $values Criteria or Lookup object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(LookupPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from Lookup object
        }

        if ($criteria->containsKey(LookupPeer::LOOKUP_ID) && $criteria->keyContainsValue(LookupPeer::LOOKUP_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.LookupPeer::LOOKUP_ID.')');
        }


        // Set the correct dbName
        $criteria->setDbName(LookupPeer::DATABASE_NAME);

        try {
            // use transaction because $criteria could contain info
            // for more than one table (I guess, conceivably)
            $con->beginTransaction();
            $pk = BasePeer::doInsert($criteria, $con);
            $con->commit();
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }

        return $pk;
    }

    /**
     * Performs an UPDATE on the database, given a Lookup or Criteria object.
     *
     * @param      mixed $values Criteria or Lookup object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(LookupPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(LookupPeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(LookupPeer::LOOKUP_ID);
            $value = $criteria->remove(LookupPeer::LOOKUP_ID);
            if ($value) {
                $selectCriteria->add(LookupPeer::LOOKUP_ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(LookupPeer::TABLE_NAME);
            }

        } else { // $values is Lookup object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(LookupPeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the core.lookup table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(LookupPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(LookupPeer::TABLE_NAME, $con, LookupPeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            LookupPeer::clearInstancePool();
            LookupPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a Lookup or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or Lookup object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param      PropelPDO $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *				if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, PropelPDO $con = null)
     {
        if ($con === null) {
            $con = Propel::getConnection(LookupPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            LookupPeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof Lookup) { // it's a model object
            // invalidate the cache for this single object
            LookupPeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(LookupPeer::DATABASE_NAME);
            $criteria->add(LookupPeer::LOOKUP_ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                LookupPeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(LookupPeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            LookupPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given Lookup object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param Lookup $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(LookupPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(LookupPeer::TABLE_NAME);

            if (! is_array($cols)) {
                $cols = array($cols);
            }

            foreach ($cols as $colName) {
                if ($tableMap->hasColumn($colName)) {
                    $get = 'get' . $tableMap->getColumn($colName)->getPhpName();
                    $columns[$colName] = $obj->$get();
                }
            }
        } else {

        }

        return BasePeer::doValidate(LookupPeer::DATABASE_NAME, LookupPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return Lookup
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = LookupPeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(LookupPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(LookupPeer::DATABASE_NAME);
        $criteria->add(LookupPeer::LOOKUP_ID, $pk);

        $v = LookupPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return Lookup[]
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(LookupPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(LookupPeer::DATABASE_NAME);
            $criteria->add(LookupPeer::LOOKUP_ID, $pks, Criteria::IN);
            $objs = LookupPeer::doSelect($criteria, $con);
        }

        return $objs;
    }

} // BaseLookupPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseLookupPeer::buildTableMap();


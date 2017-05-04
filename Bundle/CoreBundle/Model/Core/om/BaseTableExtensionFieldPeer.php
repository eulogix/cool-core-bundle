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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\FieldDefinitionPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtensionField;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtensionFieldPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtensionPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\map\TableExtensionFieldTableMap;

abstract class BaseTableExtensionFieldPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'cool_db';

    /** the table name for this class */
    const TABLE_NAME = 'core.table_extension_field';

    /** the related Propel class for this table */
    const OM_CLASS = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\TableExtensionField';

    /** the related TableMap class for this table */
    const TM_CLASS = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\map\\TableExtensionFieldTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 5;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 5;

    /** the column name for the table_extension_field_id field */
    const TABLE_EXTENSION_FIELD_ID = 'core.table_extension_field.table_extension_field_id';

    /** the column name for the table_extension_id field */
    const TABLE_EXTENSION_ID = 'core.table_extension_field.table_extension_id';

    /** the column name for the field_definition_id field */
    const FIELD_DEFINITION_ID = 'core.table_extension_field.field_definition_id';

    /** the column name for the require_index field */
    const REQUIRE_INDEX = 'core.table_extension_field.require_index';

    /** the column name for the active_flag field */
    const ACTIVE_FLAG = 'core.table_extension_field.active_flag';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identity map to hold any loaded instances of TableExtensionField objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array TableExtensionField[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. TableExtensionFieldPeer::$fieldNames[TableExtensionFieldPeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('TableExtensionFieldId', 'TableExtensionId', 'FieldDefinitionId', 'RequireIndex', 'ActiveFlag', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('tableExtensionFieldId', 'tableExtensionId', 'fieldDefinitionId', 'requireIndex', 'activeFlag', ),
        BasePeer::TYPE_COLNAME => array (TableExtensionFieldPeer::TABLE_EXTENSION_FIELD_ID, TableExtensionFieldPeer::TABLE_EXTENSION_ID, TableExtensionFieldPeer::FIELD_DEFINITION_ID, TableExtensionFieldPeer::REQUIRE_INDEX, TableExtensionFieldPeer::ACTIVE_FLAG, ),
        BasePeer::TYPE_RAW_COLNAME => array ('TABLE_EXTENSION_FIELD_ID', 'TABLE_EXTENSION_ID', 'FIELD_DEFINITION_ID', 'REQUIRE_INDEX', 'ACTIVE_FLAG', ),
        BasePeer::TYPE_FIELDNAME => array ('table_extension_field_id', 'table_extension_id', 'field_definition_id', 'require_index', 'active_flag', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. TableExtensionFieldPeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('TableExtensionFieldId' => 0, 'TableExtensionId' => 1, 'FieldDefinitionId' => 2, 'RequireIndex' => 3, 'ActiveFlag' => 4, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('tableExtensionFieldId' => 0, 'tableExtensionId' => 1, 'fieldDefinitionId' => 2, 'requireIndex' => 3, 'activeFlag' => 4, ),
        BasePeer::TYPE_COLNAME => array (TableExtensionFieldPeer::TABLE_EXTENSION_FIELD_ID => 0, TableExtensionFieldPeer::TABLE_EXTENSION_ID => 1, TableExtensionFieldPeer::FIELD_DEFINITION_ID => 2, TableExtensionFieldPeer::REQUIRE_INDEX => 3, TableExtensionFieldPeer::ACTIVE_FLAG => 4, ),
        BasePeer::TYPE_RAW_COLNAME => array ('TABLE_EXTENSION_FIELD_ID' => 0, 'TABLE_EXTENSION_ID' => 1, 'FIELD_DEFINITION_ID' => 2, 'REQUIRE_INDEX' => 3, 'ACTIVE_FLAG' => 4, ),
        BasePeer::TYPE_FIELDNAME => array ('table_extension_field_id' => 0, 'table_extension_id' => 1, 'field_definition_id' => 2, 'require_index' => 3, 'active_flag' => 4, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, )
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
        $toNames = TableExtensionFieldPeer::getFieldNames($toType);
        $key = isset(TableExtensionFieldPeer::$fieldKeys[$fromType][$name]) ? TableExtensionFieldPeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(TableExtensionFieldPeer::$fieldKeys[$fromType], true));
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
        if (!array_key_exists($type, TableExtensionFieldPeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return TableExtensionFieldPeer::$fieldNames[$type];
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
     * @param      string $column The column name for current table. (i.e. TableExtensionFieldPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(TableExtensionFieldPeer::TABLE_NAME.'.', $alias.'.', $column);
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
            $criteria->addSelectColumn(TableExtensionFieldPeer::TABLE_EXTENSION_FIELD_ID);
            $criteria->addSelectColumn(TableExtensionFieldPeer::TABLE_EXTENSION_ID);
            $criteria->addSelectColumn(TableExtensionFieldPeer::FIELD_DEFINITION_ID);
            $criteria->addSelectColumn(TableExtensionFieldPeer::REQUIRE_INDEX);
            $criteria->addSelectColumn(TableExtensionFieldPeer::ACTIVE_FLAG);
        } else {
            $criteria->addSelectColumn($alias . '.table_extension_field_id');
            $criteria->addSelectColumn($alias . '.table_extension_id');
            $criteria->addSelectColumn($alias . '.field_definition_id');
            $criteria->addSelectColumn($alias . '.require_index');
            $criteria->addSelectColumn($alias . '.active_flag');
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
        $criteria->setPrimaryTableName(TableExtensionFieldPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            TableExtensionFieldPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(TableExtensionFieldPeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(TableExtensionFieldPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return TableExtensionField
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = TableExtensionFieldPeer::doSelect($critcopy, $con);
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
        return TableExtensionFieldPeer::populateObjects(TableExtensionFieldPeer::doSelectStmt($criteria, $con));
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
            $con = Propel::getConnection(TableExtensionFieldPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            TableExtensionFieldPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(TableExtensionFieldPeer::DATABASE_NAME);

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
     * @param TableExtensionField $obj A TableExtensionField object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getTableExtensionFieldId();
            } // if key === null
            TableExtensionFieldPeer::$instances[$key] = $obj;
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
     * @param      mixed $value A TableExtensionField object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof TableExtensionField) {
                $key = (string) $value->getTableExtensionFieldId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or TableExtensionField object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(TableExtensionFieldPeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return TableExtensionField Found object or null if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(TableExtensionFieldPeer::$instances[$key])) {
                return TableExtensionFieldPeer::$instances[$key];
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
        foreach (TableExtensionFieldPeer::$instances as $instance) {
          $instance->clearAllReferences(true);
        }
      }
        TableExtensionFieldPeer::$instances = array();
    }

    /**
     * Method to invalidate the instance pool of all tables related to core.table_extension_field
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
        $cls = TableExtensionFieldPeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = TableExtensionFieldPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = TableExtensionFieldPeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                TableExtensionFieldPeer::addInstanceToPool($obj, $key);
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
     * @return array (TableExtensionField object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = TableExtensionFieldPeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = TableExtensionFieldPeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + TableExtensionFieldPeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = TableExtensionFieldPeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            TableExtensionFieldPeer::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }


    /**
     * Returns the number of rows matching criteria, joining the related TableExtension table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinTableExtension(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(TableExtensionFieldPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            TableExtensionFieldPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(TableExtensionFieldPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(TableExtensionFieldPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(TableExtensionFieldPeer::TABLE_EXTENSION_ID, TableExtensionPeer::TABLE_EXTENSION_ID, $join_behavior);

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
     * Returns the number of rows matching criteria, joining the related FieldDefinition table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinFieldDefinition(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(TableExtensionFieldPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            TableExtensionFieldPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(TableExtensionFieldPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(TableExtensionFieldPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(TableExtensionFieldPeer::FIELD_DEFINITION_ID, FieldDefinitionPeer::FIELD_DEFINITION_ID, $join_behavior);

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
     * Selects a collection of TableExtensionField objects pre-filled with their TableExtension objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of TableExtensionField objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinTableExtension(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(TableExtensionFieldPeer::DATABASE_NAME);
        }

        TableExtensionFieldPeer::addSelectColumns($criteria);
        $startcol = TableExtensionFieldPeer::NUM_HYDRATE_COLUMNS;
        TableExtensionPeer::addSelectColumns($criteria);

        $criteria->addJoin(TableExtensionFieldPeer::TABLE_EXTENSION_ID, TableExtensionPeer::TABLE_EXTENSION_ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = TableExtensionFieldPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = TableExtensionFieldPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = TableExtensionFieldPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                TableExtensionFieldPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = TableExtensionPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = TableExtensionPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = TableExtensionPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    TableExtensionPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (TableExtensionField) to $obj2 (TableExtension)
                $obj2->addTableExtensionField($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of TableExtensionField objects pre-filled with their FieldDefinition objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of TableExtensionField objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinFieldDefinition(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(TableExtensionFieldPeer::DATABASE_NAME);
        }

        TableExtensionFieldPeer::addSelectColumns($criteria);
        $startcol = TableExtensionFieldPeer::NUM_HYDRATE_COLUMNS;
        FieldDefinitionPeer::addSelectColumns($criteria);

        $criteria->addJoin(TableExtensionFieldPeer::FIELD_DEFINITION_ID, FieldDefinitionPeer::FIELD_DEFINITION_ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = TableExtensionFieldPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = TableExtensionFieldPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = TableExtensionFieldPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                TableExtensionFieldPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = FieldDefinitionPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = FieldDefinitionPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = FieldDefinitionPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    FieldDefinitionPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (TableExtensionField) to $obj2 (FieldDefinition)
                $obj2->addTableExtensionField($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Returns the number of rows matching criteria, joining all related tables
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAll(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(TableExtensionFieldPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            TableExtensionFieldPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(TableExtensionFieldPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(TableExtensionFieldPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(TableExtensionFieldPeer::TABLE_EXTENSION_ID, TableExtensionPeer::TABLE_EXTENSION_ID, $join_behavior);

        $criteria->addJoin(TableExtensionFieldPeer::FIELD_DEFINITION_ID, FieldDefinitionPeer::FIELD_DEFINITION_ID, $join_behavior);

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
     * Selects a collection of TableExtensionField objects pre-filled with all related objects.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of TableExtensionField objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(TableExtensionFieldPeer::DATABASE_NAME);
        }

        TableExtensionFieldPeer::addSelectColumns($criteria);
        $startcol2 = TableExtensionFieldPeer::NUM_HYDRATE_COLUMNS;

        TableExtensionPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + TableExtensionPeer::NUM_HYDRATE_COLUMNS;

        FieldDefinitionPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + FieldDefinitionPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(TableExtensionFieldPeer::TABLE_EXTENSION_ID, TableExtensionPeer::TABLE_EXTENSION_ID, $join_behavior);

        $criteria->addJoin(TableExtensionFieldPeer::FIELD_DEFINITION_ID, FieldDefinitionPeer::FIELD_DEFINITION_ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = TableExtensionFieldPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = TableExtensionFieldPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = TableExtensionFieldPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                TableExtensionFieldPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

            // Add objects for joined TableExtension rows

            $key2 = TableExtensionPeer::getPrimaryKeyHashFromRow($row, $startcol2);
            if ($key2 !== null) {
                $obj2 = TableExtensionPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = TableExtensionPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    TableExtensionPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 loaded

                // Add the $obj1 (TableExtensionField) to the collection in $obj2 (TableExtension)
                $obj2->addTableExtensionField($obj1);
            } // if joined row not null

            // Add objects for joined FieldDefinition rows

            $key3 = FieldDefinitionPeer::getPrimaryKeyHashFromRow($row, $startcol3);
            if ($key3 !== null) {
                $obj3 = FieldDefinitionPeer::getInstanceFromPool($key3);
                if (!$obj3) {

                    $cls = FieldDefinitionPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    FieldDefinitionPeer::addInstanceToPool($obj3, $key3);
                } // if obj3 loaded

                // Add the $obj1 (TableExtensionField) to the collection in $obj3 (FieldDefinition)
                $obj3->addTableExtensionField($obj1);
            } // if joined row not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Returns the number of rows matching criteria, joining the related TableExtension table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptTableExtension(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(TableExtensionFieldPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            TableExtensionFieldPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(TableExtensionFieldPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(TableExtensionFieldPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(TableExtensionFieldPeer::FIELD_DEFINITION_ID, FieldDefinitionPeer::FIELD_DEFINITION_ID, $join_behavior);

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
     * Returns the number of rows matching criteria, joining the related FieldDefinition table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptFieldDefinition(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(TableExtensionFieldPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            TableExtensionFieldPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(TableExtensionFieldPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(TableExtensionFieldPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(TableExtensionFieldPeer::TABLE_EXTENSION_ID, TableExtensionPeer::TABLE_EXTENSION_ID, $join_behavior);

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
     * Selects a collection of TableExtensionField objects pre-filled with all related objects except TableExtension.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of TableExtensionField objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptTableExtension(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(TableExtensionFieldPeer::DATABASE_NAME);
        }

        TableExtensionFieldPeer::addSelectColumns($criteria);
        $startcol2 = TableExtensionFieldPeer::NUM_HYDRATE_COLUMNS;

        FieldDefinitionPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + FieldDefinitionPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(TableExtensionFieldPeer::FIELD_DEFINITION_ID, FieldDefinitionPeer::FIELD_DEFINITION_ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = TableExtensionFieldPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = TableExtensionFieldPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = TableExtensionFieldPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                TableExtensionFieldPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined FieldDefinition rows

                $key2 = FieldDefinitionPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = FieldDefinitionPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = FieldDefinitionPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    FieldDefinitionPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (TableExtensionField) to the collection in $obj2 (FieldDefinition)
                $obj2->addTableExtensionField($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of TableExtensionField objects pre-filled with all related objects except FieldDefinition.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of TableExtensionField objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptFieldDefinition(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(TableExtensionFieldPeer::DATABASE_NAME);
        }

        TableExtensionFieldPeer::addSelectColumns($criteria);
        $startcol2 = TableExtensionFieldPeer::NUM_HYDRATE_COLUMNS;

        TableExtensionPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + TableExtensionPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(TableExtensionFieldPeer::TABLE_EXTENSION_ID, TableExtensionPeer::TABLE_EXTENSION_ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = TableExtensionFieldPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = TableExtensionFieldPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = TableExtensionFieldPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                TableExtensionFieldPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined TableExtension rows

                $key2 = TableExtensionPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = TableExtensionPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = TableExtensionPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    TableExtensionPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (TableExtensionField) to the collection in $obj2 (TableExtension)
                $obj2->addTableExtensionField($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
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
        return Propel::getDatabaseMap(TableExtensionFieldPeer::DATABASE_NAME)->getTable(TableExtensionFieldPeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseTableExtensionFieldPeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseTableExtensionFieldPeer::TABLE_NAME)) {
        $dbMap->addTableObject(new \Eulogix\Cool\Bundle\CoreBundle\Model\Core\map\TableExtensionFieldTableMap());
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
        return TableExtensionFieldPeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a TableExtensionField or Criteria object.
     *
     * @param      mixed $values Criteria or TableExtensionField object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(TableExtensionFieldPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from TableExtensionField object
        }

        if ($criteria->containsKey(TableExtensionFieldPeer::TABLE_EXTENSION_FIELD_ID) && $criteria->keyContainsValue(TableExtensionFieldPeer::TABLE_EXTENSION_FIELD_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.TableExtensionFieldPeer::TABLE_EXTENSION_FIELD_ID.')');
        }


        // Set the correct dbName
        $criteria->setDbName(TableExtensionFieldPeer::DATABASE_NAME);

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
     * Performs an UPDATE on the database, given a TableExtensionField or Criteria object.
     *
     * @param      mixed $values Criteria or TableExtensionField object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(TableExtensionFieldPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(TableExtensionFieldPeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(TableExtensionFieldPeer::TABLE_EXTENSION_FIELD_ID);
            $value = $criteria->remove(TableExtensionFieldPeer::TABLE_EXTENSION_FIELD_ID);
            if ($value) {
                $selectCriteria->add(TableExtensionFieldPeer::TABLE_EXTENSION_FIELD_ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(TableExtensionFieldPeer::TABLE_NAME);
            }

        } else { // $values is TableExtensionField object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(TableExtensionFieldPeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the core.table_extension_field table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(TableExtensionFieldPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(TableExtensionFieldPeer::TABLE_NAME, $con, TableExtensionFieldPeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            TableExtensionFieldPeer::clearInstancePool();
            TableExtensionFieldPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a TableExtensionField or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or TableExtensionField object or primary key or array of primary keys
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
            $con = Propel::getConnection(TableExtensionFieldPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            TableExtensionFieldPeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof TableExtensionField) { // it's a model object
            // invalidate the cache for this single object
            TableExtensionFieldPeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(TableExtensionFieldPeer::DATABASE_NAME);
            $criteria->add(TableExtensionFieldPeer::TABLE_EXTENSION_FIELD_ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                TableExtensionFieldPeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(TableExtensionFieldPeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            TableExtensionFieldPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given TableExtensionField object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param TableExtensionField $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(TableExtensionFieldPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(TableExtensionFieldPeer::TABLE_NAME);

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

        return BasePeer::doValidate(TableExtensionFieldPeer::DATABASE_NAME, TableExtensionFieldPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return TableExtensionField
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = TableExtensionFieldPeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(TableExtensionFieldPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(TableExtensionFieldPeer::DATABASE_NAME);
        $criteria->add(TableExtensionFieldPeer::TABLE_EXTENSION_FIELD_ID, $pk);

        $v = TableExtensionFieldPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return TableExtensionField[]
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(TableExtensionFieldPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(TableExtensionFieldPeer::DATABASE_NAME);
            $criteria->add(TableExtensionFieldPeer::TABLE_EXTENSION_FIELD_ID, $pks, Criteria::IN);
            $objs = TableExtensionFieldPeer::doSelect($criteria, $con);
        }

        return $objs;
    }

} // BaseTableExtensionFieldPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseTableExtensionFieldPeer::buildTableMap();


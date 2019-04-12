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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserReminder;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserReminderPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\map\UserReminderTableMap;

abstract class BaseUserReminderPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'cool_db';

    /** the table name for this class */
    const TABLE_NAME = 'core.user_reminder';

    /** the related Propel class for this table */
    const OM_CLASS = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\UserReminder';

    /** the related TableMap class for this table */
    const TM_CLASS = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\map\\UserReminderTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 11;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 11;

    /** the column name for the user_reminder_id field */
    const USER_REMINDER_ID = 'core.user_reminder.user_reminder_id';

    /** the column name for the name field */
    const NAME = 'core.user_reminder.name';

    /** the column name for the category field */
    const CATEGORY = 'core.user_reminder.category';

    /** the column name for the lister field */
    const LISTER = 'core.user_reminder.lister';

    /** the column name for the lister_translation_domain field */
    const LISTER_TRANSLATION_DOMAIN = 'core.user_reminder.lister_translation_domain';

    /** the column name for the parent_tables field */
    const PARENT_TABLES = 'core.user_reminder.parent_tables';

    /** the column name for the context_schema field */
    const CONTEXT_SCHEMA = 'core.user_reminder.context_schema';

    /** the column name for the sql_query field */
    const SQL_QUERY = 'core.user_reminder.sql_query';

    /** the column name for the type field */
    const TYPE = 'core.user_reminder.type';

    /** the column name for the sort_order field */
    const SORT_ORDER = 'core.user_reminder.sort_order';

    /** the column name for the count_sql_query field */
    const COUNT_SQL_QUERY = 'core.user_reminder.count_sql_query';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identity map to hold any loaded instances of UserReminder objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array UserReminder[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. UserReminderPeer::$fieldNames[UserReminderPeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('UserReminderId', 'Name', 'Category', 'Lister', 'ListerTranslationDomain', 'ParentTables', 'ContextSchema', 'SqlQuery', 'Type', 'SortOrder', 'CountSqlQuery', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('userReminderId', 'name', 'category', 'lister', 'listerTranslationDomain', 'parentTables', 'contextSchema', 'sqlQuery', 'type', 'sortOrder', 'countSqlQuery', ),
        BasePeer::TYPE_COLNAME => array (UserReminderPeer::USER_REMINDER_ID, UserReminderPeer::NAME, UserReminderPeer::CATEGORY, UserReminderPeer::LISTER, UserReminderPeer::LISTER_TRANSLATION_DOMAIN, UserReminderPeer::PARENT_TABLES, UserReminderPeer::CONTEXT_SCHEMA, UserReminderPeer::SQL_QUERY, UserReminderPeer::TYPE, UserReminderPeer::SORT_ORDER, UserReminderPeer::COUNT_SQL_QUERY, ),
        BasePeer::TYPE_RAW_COLNAME => array ('USER_REMINDER_ID', 'NAME', 'CATEGORY', 'LISTER', 'LISTER_TRANSLATION_DOMAIN', 'PARENT_TABLES', 'CONTEXT_SCHEMA', 'SQL_QUERY', 'TYPE', 'SORT_ORDER', 'COUNT_SQL_QUERY', ),
        BasePeer::TYPE_FIELDNAME => array ('user_reminder_id', 'name', 'category', 'lister', 'lister_translation_domain', 'parent_tables', 'context_schema', 'sql_query', 'type', 'sort_order', 'count_sql_query', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. UserReminderPeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('UserReminderId' => 0, 'Name' => 1, 'Category' => 2, 'Lister' => 3, 'ListerTranslationDomain' => 4, 'ParentTables' => 5, 'ContextSchema' => 6, 'SqlQuery' => 7, 'Type' => 8, 'SortOrder' => 9, 'CountSqlQuery' => 10, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('userReminderId' => 0, 'name' => 1, 'category' => 2, 'lister' => 3, 'listerTranslationDomain' => 4, 'parentTables' => 5, 'contextSchema' => 6, 'sqlQuery' => 7, 'type' => 8, 'sortOrder' => 9, 'countSqlQuery' => 10, ),
        BasePeer::TYPE_COLNAME => array (UserReminderPeer::USER_REMINDER_ID => 0, UserReminderPeer::NAME => 1, UserReminderPeer::CATEGORY => 2, UserReminderPeer::LISTER => 3, UserReminderPeer::LISTER_TRANSLATION_DOMAIN => 4, UserReminderPeer::PARENT_TABLES => 5, UserReminderPeer::CONTEXT_SCHEMA => 6, UserReminderPeer::SQL_QUERY => 7, UserReminderPeer::TYPE => 8, UserReminderPeer::SORT_ORDER => 9, UserReminderPeer::COUNT_SQL_QUERY => 10, ),
        BasePeer::TYPE_RAW_COLNAME => array ('USER_REMINDER_ID' => 0, 'NAME' => 1, 'CATEGORY' => 2, 'LISTER' => 3, 'LISTER_TRANSLATION_DOMAIN' => 4, 'PARENT_TABLES' => 5, 'CONTEXT_SCHEMA' => 6, 'SQL_QUERY' => 7, 'TYPE' => 8, 'SORT_ORDER' => 9, 'COUNT_SQL_QUERY' => 10, ),
        BasePeer::TYPE_FIELDNAME => array ('user_reminder_id' => 0, 'name' => 1, 'category' => 2, 'lister' => 3, 'lister_translation_domain' => 4, 'parent_tables' => 5, 'context_schema' => 6, 'sql_query' => 7, 'type' => 8, 'sort_order' => 9, 'count_sql_query' => 10, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, )
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
        $toNames = UserReminderPeer::getFieldNames($toType);
        $key = isset(UserReminderPeer::$fieldKeys[$fromType][$name]) ? UserReminderPeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(UserReminderPeer::$fieldKeys[$fromType], true));
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
        if (!array_key_exists($type, UserReminderPeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return UserReminderPeer::$fieldNames[$type];
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
     * @param      string $column The column name for current table. (i.e. UserReminderPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(UserReminderPeer::TABLE_NAME.'.', $alias.'.', $column);
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
            $criteria->addSelectColumn(UserReminderPeer::USER_REMINDER_ID);
            $criteria->addSelectColumn(UserReminderPeer::NAME);
            $criteria->addSelectColumn(UserReminderPeer::CATEGORY);
            $criteria->addSelectColumn(UserReminderPeer::LISTER);
            $criteria->addSelectColumn(UserReminderPeer::LISTER_TRANSLATION_DOMAIN);
            $criteria->addSelectColumn(UserReminderPeer::PARENT_TABLES);
            $criteria->addSelectColumn(UserReminderPeer::CONTEXT_SCHEMA);
            $criteria->addSelectColumn(UserReminderPeer::SQL_QUERY);
            $criteria->addSelectColumn(UserReminderPeer::TYPE);
            $criteria->addSelectColumn(UserReminderPeer::SORT_ORDER);
            $criteria->addSelectColumn(UserReminderPeer::COUNT_SQL_QUERY);
        } else {
            $criteria->addSelectColumn($alias . '.user_reminder_id');
            $criteria->addSelectColumn($alias . '.name');
            $criteria->addSelectColumn($alias . '.category');
            $criteria->addSelectColumn($alias . '.lister');
            $criteria->addSelectColumn($alias . '.lister_translation_domain');
            $criteria->addSelectColumn($alias . '.parent_tables');
            $criteria->addSelectColumn($alias . '.context_schema');
            $criteria->addSelectColumn($alias . '.sql_query');
            $criteria->addSelectColumn($alias . '.type');
            $criteria->addSelectColumn($alias . '.sort_order');
            $criteria->addSelectColumn($alias . '.count_sql_query');
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
        $criteria->setPrimaryTableName(UserReminderPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            UserReminderPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(UserReminderPeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(UserReminderPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return UserReminder
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = UserReminderPeer::doSelect($critcopy, $con);
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
        return UserReminderPeer::populateObjects(UserReminderPeer::doSelectStmt($criteria, $con));
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
            $con = Propel::getConnection(UserReminderPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            UserReminderPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(UserReminderPeer::DATABASE_NAME);

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
     * @param UserReminder $obj A UserReminder object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getUserReminderId();
            } // if key === null
            UserReminderPeer::$instances[$key] = $obj;
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
     * @param      mixed $value A UserReminder object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof UserReminder) {
                $key = (string) $value->getUserReminderId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or UserReminder object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(UserReminderPeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return UserReminder Found object or null if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(UserReminderPeer::$instances[$key])) {
                return UserReminderPeer::$instances[$key];
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
        foreach (UserReminderPeer::$instances as $instance) {
          $instance->clearAllReferences(true);
        }
      }
        UserReminderPeer::$instances = array();
    }

    /**
     * Method to invalidate the instance pool of all tables related to core.user_reminder
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
        $cls = UserReminderPeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = UserReminderPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = UserReminderPeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                UserReminderPeer::addInstanceToPool($obj, $key);
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
     * @return array (UserReminder object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = UserReminderPeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = UserReminderPeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + UserReminderPeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = UserReminderPeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            UserReminderPeer::addInstanceToPool($obj, $key);
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
        return Propel::getDatabaseMap(UserReminderPeer::DATABASE_NAME)->getTable(UserReminderPeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseUserReminderPeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseUserReminderPeer::TABLE_NAME)) {
        $dbMap->addTableObject(new \Eulogix\Cool\Bundle\CoreBundle\Model\Core\map\UserReminderTableMap());
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
        return UserReminderPeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a UserReminder or Criteria object.
     *
     * @param      mixed $values Criteria or UserReminder object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(UserReminderPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from UserReminder object
        }

        if ($criteria->containsKey(UserReminderPeer::USER_REMINDER_ID) && $criteria->keyContainsValue(UserReminderPeer::USER_REMINDER_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.UserReminderPeer::USER_REMINDER_ID.')');
        }


        // Set the correct dbName
        $criteria->setDbName(UserReminderPeer::DATABASE_NAME);

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
     * Performs an UPDATE on the database, given a UserReminder or Criteria object.
     *
     * @param      mixed $values Criteria or UserReminder object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(UserReminderPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(UserReminderPeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(UserReminderPeer::USER_REMINDER_ID);
            $value = $criteria->remove(UserReminderPeer::USER_REMINDER_ID);
            if ($value) {
                $selectCriteria->add(UserReminderPeer::USER_REMINDER_ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(UserReminderPeer::TABLE_NAME);
            }

        } else { // $values is UserReminder object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(UserReminderPeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the core.user_reminder table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(UserReminderPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(UserReminderPeer::TABLE_NAME, $con, UserReminderPeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            UserReminderPeer::clearInstancePool();
            UserReminderPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a UserReminder or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or UserReminder object or primary key or array of primary keys
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
            $con = Propel::getConnection(UserReminderPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            UserReminderPeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof UserReminder) { // it's a model object
            // invalidate the cache for this single object
            UserReminderPeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(UserReminderPeer::DATABASE_NAME);
            $criteria->add(UserReminderPeer::USER_REMINDER_ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                UserReminderPeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(UserReminderPeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            UserReminderPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given UserReminder object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param UserReminder $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(UserReminderPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(UserReminderPeer::TABLE_NAME);

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

        return BasePeer::doValidate(UserReminderPeer::DATABASE_NAME, UserReminderPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return UserReminder
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = UserReminderPeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(UserReminderPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(UserReminderPeer::DATABASE_NAME);
        $criteria->add(UserReminderPeer::USER_REMINDER_ID, $pk);

        $v = UserReminderPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return UserReminder[]
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(UserReminderPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(UserReminderPeer::DATABASE_NAME);
            $criteria->add(UserReminderPeer::USER_REMINDER_ID, $pks, Criteria::IN);
            $objs = UserReminderPeer::doSelect($criteria, $con);
        }

        return $objs;
    }

} // BaseUserReminderPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseUserReminderPeer::buildTableMap();


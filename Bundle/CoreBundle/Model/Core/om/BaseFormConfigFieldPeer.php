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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\FormConfigField;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\FormConfigFieldPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\FormConfigPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\map\FormConfigFieldTableMap;

abstract class BaseFormConfigFieldPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'cool_db';

    /** the table name for this class */
    const TABLE_NAME = 'core.form_config_field';

    /** the related Propel class for this table */
    const OM_CLASS = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\FormConfigField';

    /** the related TableMap class for this table */
    const TM_CLASS = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\map\\FormConfigFieldTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 7;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 7;

    /** the column name for the form_config_field_id field */
    const FORM_CONFIG_FIELD_ID = 'core.form_config_field.form_config_field_id';

    /** the column name for the form_config_id field */
    const FORM_CONFIG_ID = 'core.form_config_field.form_config_id';

    /** the column name for the name field */
    const NAME = 'core.form_config_field.name';

    /** the column name for the read_only_flag field */
    const READ_ONLY_FLAG = 'core.form_config_field.read_only_flag';

    /** the column name for the hidden_flag field */
    const HIDDEN_FLAG = 'core.form_config_field.hidden_flag';

    /** the column name for the width field */
    const WIDTH = 'core.form_config_field.width';

    /** the column name for the height field */
    const HEIGHT = 'core.form_config_field.height';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identity map to hold any loaded instances of FormConfigField objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array FormConfigField[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. FormConfigFieldPeer::$fieldNames[FormConfigFieldPeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('FormConfigFieldId', 'FormConfigId', 'Name', 'ReadOnlyFlag', 'HiddenFlag', 'Width', 'Height', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('formConfigFieldId', 'formConfigId', 'name', 'readOnlyFlag', 'hiddenFlag', 'width', 'height', ),
        BasePeer::TYPE_COLNAME => array (FormConfigFieldPeer::FORM_CONFIG_FIELD_ID, FormConfigFieldPeer::FORM_CONFIG_ID, FormConfigFieldPeer::NAME, FormConfigFieldPeer::READ_ONLY_FLAG, FormConfigFieldPeer::HIDDEN_FLAG, FormConfigFieldPeer::WIDTH, FormConfigFieldPeer::HEIGHT, ),
        BasePeer::TYPE_RAW_COLNAME => array ('FORM_CONFIG_FIELD_ID', 'FORM_CONFIG_ID', 'NAME', 'READ_ONLY_FLAG', 'HIDDEN_FLAG', 'WIDTH', 'HEIGHT', ),
        BasePeer::TYPE_FIELDNAME => array ('form_config_field_id', 'form_config_id', 'name', 'read_only_flag', 'hidden_flag', 'width', 'height', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. FormConfigFieldPeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('FormConfigFieldId' => 0, 'FormConfigId' => 1, 'Name' => 2, 'ReadOnlyFlag' => 3, 'HiddenFlag' => 4, 'Width' => 5, 'Height' => 6, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('formConfigFieldId' => 0, 'formConfigId' => 1, 'name' => 2, 'readOnlyFlag' => 3, 'hiddenFlag' => 4, 'width' => 5, 'height' => 6, ),
        BasePeer::TYPE_COLNAME => array (FormConfigFieldPeer::FORM_CONFIG_FIELD_ID => 0, FormConfigFieldPeer::FORM_CONFIG_ID => 1, FormConfigFieldPeer::NAME => 2, FormConfigFieldPeer::READ_ONLY_FLAG => 3, FormConfigFieldPeer::HIDDEN_FLAG => 4, FormConfigFieldPeer::WIDTH => 5, FormConfigFieldPeer::HEIGHT => 6, ),
        BasePeer::TYPE_RAW_COLNAME => array ('FORM_CONFIG_FIELD_ID' => 0, 'FORM_CONFIG_ID' => 1, 'NAME' => 2, 'READ_ONLY_FLAG' => 3, 'HIDDEN_FLAG' => 4, 'WIDTH' => 5, 'HEIGHT' => 6, ),
        BasePeer::TYPE_FIELDNAME => array ('form_config_field_id' => 0, 'form_config_id' => 1, 'name' => 2, 'read_only_flag' => 3, 'hidden_flag' => 4, 'width' => 5, 'height' => 6, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, )
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
        $toNames = FormConfigFieldPeer::getFieldNames($toType);
        $key = isset(FormConfigFieldPeer::$fieldKeys[$fromType][$name]) ? FormConfigFieldPeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(FormConfigFieldPeer::$fieldKeys[$fromType], true));
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
        if (!array_key_exists($type, FormConfigFieldPeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return FormConfigFieldPeer::$fieldNames[$type];
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
     * @param      string $column The column name for current table. (i.e. FormConfigFieldPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(FormConfigFieldPeer::TABLE_NAME.'.', $alias.'.', $column);
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
            $criteria->addSelectColumn(FormConfigFieldPeer::FORM_CONFIG_FIELD_ID);
            $criteria->addSelectColumn(FormConfigFieldPeer::FORM_CONFIG_ID);
            $criteria->addSelectColumn(FormConfigFieldPeer::NAME);
            $criteria->addSelectColumn(FormConfigFieldPeer::READ_ONLY_FLAG);
            $criteria->addSelectColumn(FormConfigFieldPeer::HIDDEN_FLAG);
            $criteria->addSelectColumn(FormConfigFieldPeer::WIDTH);
            $criteria->addSelectColumn(FormConfigFieldPeer::HEIGHT);
        } else {
            $criteria->addSelectColumn($alias . '.form_config_field_id');
            $criteria->addSelectColumn($alias . '.form_config_id');
            $criteria->addSelectColumn($alias . '.name');
            $criteria->addSelectColumn($alias . '.read_only_flag');
            $criteria->addSelectColumn($alias . '.hidden_flag');
            $criteria->addSelectColumn($alias . '.width');
            $criteria->addSelectColumn($alias . '.height');
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
        $criteria->setPrimaryTableName(FormConfigFieldPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            FormConfigFieldPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(FormConfigFieldPeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(FormConfigFieldPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return FormConfigField
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = FormConfigFieldPeer::doSelect($critcopy, $con);
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
        return FormConfigFieldPeer::populateObjects(FormConfigFieldPeer::doSelectStmt($criteria, $con));
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
            $con = Propel::getConnection(FormConfigFieldPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            FormConfigFieldPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(FormConfigFieldPeer::DATABASE_NAME);

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
     * @param FormConfigField $obj A FormConfigField object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getFormConfigFieldId();
            } // if key === null
            FormConfigFieldPeer::$instances[$key] = $obj;
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
     * @param      mixed $value A FormConfigField object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof FormConfigField) {
                $key = (string) $value->getFormConfigFieldId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or FormConfigField object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(FormConfigFieldPeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return FormConfigField Found object or null if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(FormConfigFieldPeer::$instances[$key])) {
                return FormConfigFieldPeer::$instances[$key];
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
        foreach (FormConfigFieldPeer::$instances as $instance) {
          $instance->clearAllReferences(true);
        }
      }
        FormConfigFieldPeer::$instances = array();
    }

    /**
     * Method to invalidate the instance pool of all tables related to core.form_config_field
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
        $cls = FormConfigFieldPeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = FormConfigFieldPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = FormConfigFieldPeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                FormConfigFieldPeer::addInstanceToPool($obj, $key);
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
     * @return array (FormConfigField object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = FormConfigFieldPeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = FormConfigFieldPeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + FormConfigFieldPeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = FormConfigFieldPeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            FormConfigFieldPeer::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }


    /**
     * Returns the number of rows matching criteria, joining the related FormConfig table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinFormConfig(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(FormConfigFieldPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            FormConfigFieldPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(FormConfigFieldPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(FormConfigFieldPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(FormConfigFieldPeer::FORM_CONFIG_ID, FormConfigPeer::FORM_CONFIG_ID, $join_behavior);

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
     * Selects a collection of FormConfigField objects pre-filled with their FormConfig objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of FormConfigField objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinFormConfig(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(FormConfigFieldPeer::DATABASE_NAME);
        }

        FormConfigFieldPeer::addSelectColumns($criteria);
        $startcol = FormConfigFieldPeer::NUM_HYDRATE_COLUMNS;
        FormConfigPeer::addSelectColumns($criteria);

        $criteria->addJoin(FormConfigFieldPeer::FORM_CONFIG_ID, FormConfigPeer::FORM_CONFIG_ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = FormConfigFieldPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = FormConfigFieldPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = FormConfigFieldPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                FormConfigFieldPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = FormConfigPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = FormConfigPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = FormConfigPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    FormConfigPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (FormConfigField) to $obj2 (FormConfig)
                $obj2->addFormConfigField($obj1);

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
        $criteria->setPrimaryTableName(FormConfigFieldPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            FormConfigFieldPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(FormConfigFieldPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(FormConfigFieldPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(FormConfigFieldPeer::FORM_CONFIG_ID, FormConfigPeer::FORM_CONFIG_ID, $join_behavior);

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
     * Selects a collection of FormConfigField objects pre-filled with all related objects.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of FormConfigField objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(FormConfigFieldPeer::DATABASE_NAME);
        }

        FormConfigFieldPeer::addSelectColumns($criteria);
        $startcol2 = FormConfigFieldPeer::NUM_HYDRATE_COLUMNS;

        FormConfigPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + FormConfigPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(FormConfigFieldPeer::FORM_CONFIG_ID, FormConfigPeer::FORM_CONFIG_ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = FormConfigFieldPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = FormConfigFieldPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = FormConfigFieldPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                FormConfigFieldPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

            // Add objects for joined FormConfig rows

            $key2 = FormConfigPeer::getPrimaryKeyHashFromRow($row, $startcol2);
            if ($key2 !== null) {
                $obj2 = FormConfigPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = FormConfigPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    FormConfigPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 loaded

                // Add the $obj1 (FormConfigField) to the collection in $obj2 (FormConfig)
                $obj2->addFormConfigField($obj1);
            } // if joined row not null

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
        return Propel::getDatabaseMap(FormConfigFieldPeer::DATABASE_NAME)->getTable(FormConfigFieldPeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseFormConfigFieldPeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseFormConfigFieldPeer::TABLE_NAME)) {
        $dbMap->addTableObject(new \Eulogix\Cool\Bundle\CoreBundle\Model\Core\map\FormConfigFieldTableMap());
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
        return FormConfigFieldPeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a FormConfigField or Criteria object.
     *
     * @param      mixed $values Criteria or FormConfigField object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(FormConfigFieldPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from FormConfigField object
        }

        if ($criteria->containsKey(FormConfigFieldPeer::FORM_CONFIG_FIELD_ID) && $criteria->keyContainsValue(FormConfigFieldPeer::FORM_CONFIG_FIELD_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.FormConfigFieldPeer::FORM_CONFIG_FIELD_ID.')');
        }


        // Set the correct dbName
        $criteria->setDbName(FormConfigFieldPeer::DATABASE_NAME);

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
     * Performs an UPDATE on the database, given a FormConfigField or Criteria object.
     *
     * @param      mixed $values Criteria or FormConfigField object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(FormConfigFieldPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(FormConfigFieldPeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(FormConfigFieldPeer::FORM_CONFIG_FIELD_ID);
            $value = $criteria->remove(FormConfigFieldPeer::FORM_CONFIG_FIELD_ID);
            if ($value) {
                $selectCriteria->add(FormConfigFieldPeer::FORM_CONFIG_FIELD_ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(FormConfigFieldPeer::TABLE_NAME);
            }

        } else { // $values is FormConfigField object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(FormConfigFieldPeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the core.form_config_field table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(FormConfigFieldPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(FormConfigFieldPeer::TABLE_NAME, $con, FormConfigFieldPeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            FormConfigFieldPeer::clearInstancePool();
            FormConfigFieldPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a FormConfigField or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or FormConfigField object or primary key or array of primary keys
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
            $con = Propel::getConnection(FormConfigFieldPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            FormConfigFieldPeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof FormConfigField) { // it's a model object
            // invalidate the cache for this single object
            FormConfigFieldPeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(FormConfigFieldPeer::DATABASE_NAME);
            $criteria->add(FormConfigFieldPeer::FORM_CONFIG_FIELD_ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                FormConfigFieldPeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(FormConfigFieldPeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            FormConfigFieldPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given FormConfigField object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param FormConfigField $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(FormConfigFieldPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(FormConfigFieldPeer::TABLE_NAME);

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

        return BasePeer::doValidate(FormConfigFieldPeer::DATABASE_NAME, FormConfigFieldPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return FormConfigField
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = FormConfigFieldPeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(FormConfigFieldPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(FormConfigFieldPeer::DATABASE_NAME);
        $criteria->add(FormConfigFieldPeer::FORM_CONFIG_FIELD_ID, $pk);

        $v = FormConfigFieldPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return FormConfigField[]
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(FormConfigFieldPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(FormConfigFieldPeer::DATABASE_NAME);
            $criteria->add(FormConfigFieldPeer::FORM_CONFIG_FIELD_ID, $pks, Criteria::IN);
            $objs = FormConfigFieldPeer::doSelect($criteria, $con);
        }

        return $objs;
    }

} // BaseFormConfigFieldPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseFormConfigFieldPeer::buildTableMap();


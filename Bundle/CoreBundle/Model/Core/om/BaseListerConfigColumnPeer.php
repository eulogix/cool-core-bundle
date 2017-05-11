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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\ListerConfigColumn;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\ListerConfigColumnPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\ListerConfigPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\map\ListerConfigColumnTableMap;

abstract class BaseListerConfigColumnPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'cool_db';

    /** the table name for this class */
    const TABLE_NAME = 'core.lister_config_column';

    /** the related Propel class for this table */
    const OM_CLASS = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\ListerConfigColumn';

    /** the related TableMap class for this table */
    const TM_CLASS = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\map\\ListerConfigColumnTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 15;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 15;

    /** the column name for the lister_config_column_id field */
    const LISTER_CONFIG_COLUMN_ID = 'core.lister_config_column.lister_config_column_id';

    /** the column name for the lister_config_id field */
    const LISTER_CONFIG_ID = 'core.lister_config_column.lister_config_id';

    /** the column name for the name field */
    const NAME = 'core.lister_config_column.name';

    /** the column name for the sortable_flag field */
    const SORTABLE_FLAG = 'core.lister_config_column.sortable_flag';

    /** the column name for the editable_flag field */
    const EDITABLE_FLAG = 'core.lister_config_column.editable_flag';

    /** the column name for the show_summary_flag field */
    const SHOW_SUMMARY_FLAG = 'core.lister_config_column.show_summary_flag';

    /** the column name for the width field */
    const WIDTH = 'core.lister_config_column.width';

    /** the column name for the cell_template field */
    const CELL_TEMPLATE = 'core.lister_config_column.cell_template';

    /** the column name for the cell_template_js field */
    const CELL_TEMPLATE_JS = 'core.lister_config_column.cell_template_js';

    /** the column name for the column_style_css field */
    const COLUMN_STYLE_CSS = 'core.lister_config_column.column_style_css';

    /** the column name for the sort_order field */
    const SORT_ORDER = 'core.lister_config_column.sort_order';

    /** the column name for the sortby_order field */
    const SORTBY_ORDER = 'core.lister_config_column.sortby_order';

    /** the column name for the sortby_direction field */
    const SORTBY_DIRECTION = 'core.lister_config_column.sortby_direction';

    /** the column name for the truncate_chars field */
    const TRUNCATE_CHARS = 'core.lister_config_column.truncate_chars';

    /** the column name for the tooltip_max_width field */
    const TOOLTIP_MAX_WIDTH = 'core.lister_config_column.tooltip_max_width';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identity map to hold any loaded instances of ListerConfigColumn objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array ListerConfigColumn[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. ListerConfigColumnPeer::$fieldNames[ListerConfigColumnPeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('ListerConfigColumnId', 'ListerConfigId', 'Name', 'SortableFlag', 'EditableFlag', 'ShowSummaryFlag', 'Width', 'CellTemplate', 'CellTemplateJs', 'ColumnStyleCss', 'SortOrder', 'SortbyOrder', 'SortbyDirection', 'TruncateChars', 'TooltipMaxWidth', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('listerConfigColumnId', 'listerConfigId', 'name', 'sortableFlag', 'editableFlag', 'showSummaryFlag', 'width', 'cellTemplate', 'cellTemplateJs', 'columnStyleCss', 'sortOrder', 'sortbyOrder', 'sortbyDirection', 'truncateChars', 'tooltipMaxWidth', ),
        BasePeer::TYPE_COLNAME => array (ListerConfigColumnPeer::LISTER_CONFIG_COLUMN_ID, ListerConfigColumnPeer::LISTER_CONFIG_ID, ListerConfigColumnPeer::NAME, ListerConfigColumnPeer::SORTABLE_FLAG, ListerConfigColumnPeer::EDITABLE_FLAG, ListerConfigColumnPeer::SHOW_SUMMARY_FLAG, ListerConfigColumnPeer::WIDTH, ListerConfigColumnPeer::CELL_TEMPLATE, ListerConfigColumnPeer::CELL_TEMPLATE_JS, ListerConfigColumnPeer::COLUMN_STYLE_CSS, ListerConfigColumnPeer::SORT_ORDER, ListerConfigColumnPeer::SORTBY_ORDER, ListerConfigColumnPeer::SORTBY_DIRECTION, ListerConfigColumnPeer::TRUNCATE_CHARS, ListerConfigColumnPeer::TOOLTIP_MAX_WIDTH, ),
        BasePeer::TYPE_RAW_COLNAME => array ('LISTER_CONFIG_COLUMN_ID', 'LISTER_CONFIG_ID', 'NAME', 'SORTABLE_FLAG', 'EDITABLE_FLAG', 'SHOW_SUMMARY_FLAG', 'WIDTH', 'CELL_TEMPLATE', 'CELL_TEMPLATE_JS', 'COLUMN_STYLE_CSS', 'SORT_ORDER', 'SORTBY_ORDER', 'SORTBY_DIRECTION', 'TRUNCATE_CHARS', 'TOOLTIP_MAX_WIDTH', ),
        BasePeer::TYPE_FIELDNAME => array ('lister_config_column_id', 'lister_config_id', 'name', 'sortable_flag', 'editable_flag', 'show_summary_flag', 'width', 'cell_template', 'cell_template_js', 'column_style_css', 'sort_order', 'sortby_order', 'sortby_direction', 'truncate_chars', 'tooltip_max_width', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. ListerConfigColumnPeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('ListerConfigColumnId' => 0, 'ListerConfigId' => 1, 'Name' => 2, 'SortableFlag' => 3, 'EditableFlag' => 4, 'ShowSummaryFlag' => 5, 'Width' => 6, 'CellTemplate' => 7, 'CellTemplateJs' => 8, 'ColumnStyleCss' => 9, 'SortOrder' => 10, 'SortbyOrder' => 11, 'SortbyDirection' => 12, 'TruncateChars' => 13, 'TooltipMaxWidth' => 14, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('listerConfigColumnId' => 0, 'listerConfigId' => 1, 'name' => 2, 'sortableFlag' => 3, 'editableFlag' => 4, 'showSummaryFlag' => 5, 'width' => 6, 'cellTemplate' => 7, 'cellTemplateJs' => 8, 'columnStyleCss' => 9, 'sortOrder' => 10, 'sortbyOrder' => 11, 'sortbyDirection' => 12, 'truncateChars' => 13, 'tooltipMaxWidth' => 14, ),
        BasePeer::TYPE_COLNAME => array (ListerConfigColumnPeer::LISTER_CONFIG_COLUMN_ID => 0, ListerConfigColumnPeer::LISTER_CONFIG_ID => 1, ListerConfigColumnPeer::NAME => 2, ListerConfigColumnPeer::SORTABLE_FLAG => 3, ListerConfigColumnPeer::EDITABLE_FLAG => 4, ListerConfigColumnPeer::SHOW_SUMMARY_FLAG => 5, ListerConfigColumnPeer::WIDTH => 6, ListerConfigColumnPeer::CELL_TEMPLATE => 7, ListerConfigColumnPeer::CELL_TEMPLATE_JS => 8, ListerConfigColumnPeer::COLUMN_STYLE_CSS => 9, ListerConfigColumnPeer::SORT_ORDER => 10, ListerConfigColumnPeer::SORTBY_ORDER => 11, ListerConfigColumnPeer::SORTBY_DIRECTION => 12, ListerConfigColumnPeer::TRUNCATE_CHARS => 13, ListerConfigColumnPeer::TOOLTIP_MAX_WIDTH => 14, ),
        BasePeer::TYPE_RAW_COLNAME => array ('LISTER_CONFIG_COLUMN_ID' => 0, 'LISTER_CONFIG_ID' => 1, 'NAME' => 2, 'SORTABLE_FLAG' => 3, 'EDITABLE_FLAG' => 4, 'SHOW_SUMMARY_FLAG' => 5, 'WIDTH' => 6, 'CELL_TEMPLATE' => 7, 'CELL_TEMPLATE_JS' => 8, 'COLUMN_STYLE_CSS' => 9, 'SORT_ORDER' => 10, 'SORTBY_ORDER' => 11, 'SORTBY_DIRECTION' => 12, 'TRUNCATE_CHARS' => 13, 'TOOLTIP_MAX_WIDTH' => 14, ),
        BasePeer::TYPE_FIELDNAME => array ('lister_config_column_id' => 0, 'lister_config_id' => 1, 'name' => 2, 'sortable_flag' => 3, 'editable_flag' => 4, 'show_summary_flag' => 5, 'width' => 6, 'cell_template' => 7, 'cell_template_js' => 8, 'column_style_css' => 9, 'sort_order' => 10, 'sortby_order' => 11, 'sortby_direction' => 12, 'truncate_chars' => 13, 'tooltip_max_width' => 14, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, )
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
        $toNames = ListerConfigColumnPeer::getFieldNames($toType);
        $key = isset(ListerConfigColumnPeer::$fieldKeys[$fromType][$name]) ? ListerConfigColumnPeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(ListerConfigColumnPeer::$fieldKeys[$fromType], true));
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
        if (!array_key_exists($type, ListerConfigColumnPeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return ListerConfigColumnPeer::$fieldNames[$type];
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
     * @param      string $column The column name for current table. (i.e. ListerConfigColumnPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(ListerConfigColumnPeer::TABLE_NAME.'.', $alias.'.', $column);
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
            $criteria->addSelectColumn(ListerConfigColumnPeer::LISTER_CONFIG_COLUMN_ID);
            $criteria->addSelectColumn(ListerConfigColumnPeer::LISTER_CONFIG_ID);
            $criteria->addSelectColumn(ListerConfigColumnPeer::NAME);
            $criteria->addSelectColumn(ListerConfigColumnPeer::SORTABLE_FLAG);
            $criteria->addSelectColumn(ListerConfigColumnPeer::EDITABLE_FLAG);
            $criteria->addSelectColumn(ListerConfigColumnPeer::SHOW_SUMMARY_FLAG);
            $criteria->addSelectColumn(ListerConfigColumnPeer::WIDTH);
            $criteria->addSelectColumn(ListerConfigColumnPeer::CELL_TEMPLATE);
            $criteria->addSelectColumn(ListerConfigColumnPeer::CELL_TEMPLATE_JS);
            $criteria->addSelectColumn(ListerConfigColumnPeer::COLUMN_STYLE_CSS);
            $criteria->addSelectColumn(ListerConfigColumnPeer::SORT_ORDER);
            $criteria->addSelectColumn(ListerConfigColumnPeer::SORTBY_ORDER);
            $criteria->addSelectColumn(ListerConfigColumnPeer::SORTBY_DIRECTION);
            $criteria->addSelectColumn(ListerConfigColumnPeer::TRUNCATE_CHARS);
            $criteria->addSelectColumn(ListerConfigColumnPeer::TOOLTIP_MAX_WIDTH);
        } else {
            $criteria->addSelectColumn($alias . '.lister_config_column_id');
            $criteria->addSelectColumn($alias . '.lister_config_id');
            $criteria->addSelectColumn($alias . '.name');
            $criteria->addSelectColumn($alias . '.sortable_flag');
            $criteria->addSelectColumn($alias . '.editable_flag');
            $criteria->addSelectColumn($alias . '.show_summary_flag');
            $criteria->addSelectColumn($alias . '.width');
            $criteria->addSelectColumn($alias . '.cell_template');
            $criteria->addSelectColumn($alias . '.cell_template_js');
            $criteria->addSelectColumn($alias . '.column_style_css');
            $criteria->addSelectColumn($alias . '.sort_order');
            $criteria->addSelectColumn($alias . '.sortby_order');
            $criteria->addSelectColumn($alias . '.sortby_direction');
            $criteria->addSelectColumn($alias . '.truncate_chars');
            $criteria->addSelectColumn($alias . '.tooltip_max_width');
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
        $criteria->setPrimaryTableName(ListerConfigColumnPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            ListerConfigColumnPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(ListerConfigColumnPeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(ListerConfigColumnPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return ListerConfigColumn
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = ListerConfigColumnPeer::doSelect($critcopy, $con);
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
        return ListerConfigColumnPeer::populateObjects(ListerConfigColumnPeer::doSelectStmt($criteria, $con));
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
            $con = Propel::getConnection(ListerConfigColumnPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            ListerConfigColumnPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(ListerConfigColumnPeer::DATABASE_NAME);

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
     * @param ListerConfigColumn $obj A ListerConfigColumn object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getListerConfigColumnId();
            } // if key === null
            ListerConfigColumnPeer::$instances[$key] = $obj;
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
     * @param      mixed $value A ListerConfigColumn object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof ListerConfigColumn) {
                $key = (string) $value->getListerConfigColumnId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or ListerConfigColumn object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(ListerConfigColumnPeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return ListerConfigColumn Found object or null if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(ListerConfigColumnPeer::$instances[$key])) {
                return ListerConfigColumnPeer::$instances[$key];
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
        foreach (ListerConfigColumnPeer::$instances as $instance) {
          $instance->clearAllReferences(true);
        }
      }
        ListerConfigColumnPeer::$instances = array();
    }

    /**
     * Method to invalidate the instance pool of all tables related to core.lister_config_column
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
        $cls = ListerConfigColumnPeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = ListerConfigColumnPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = ListerConfigColumnPeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                ListerConfigColumnPeer::addInstanceToPool($obj, $key);
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
     * @return array (ListerConfigColumn object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = ListerConfigColumnPeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = ListerConfigColumnPeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + ListerConfigColumnPeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = ListerConfigColumnPeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            ListerConfigColumnPeer::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }


    /**
     * Returns the number of rows matching criteria, joining the related ListerConfig table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinListerConfig(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(ListerConfigColumnPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            ListerConfigColumnPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(ListerConfigColumnPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(ListerConfigColumnPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(ListerConfigColumnPeer::LISTER_CONFIG_ID, ListerConfigPeer::LISTER_CONFIG_ID, $join_behavior);

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
     * Selects a collection of ListerConfigColumn objects pre-filled with their ListerConfig objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of ListerConfigColumn objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinListerConfig(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(ListerConfigColumnPeer::DATABASE_NAME);
        }

        ListerConfigColumnPeer::addSelectColumns($criteria);
        $startcol = ListerConfigColumnPeer::NUM_HYDRATE_COLUMNS;
        ListerConfigPeer::addSelectColumns($criteria);

        $criteria->addJoin(ListerConfigColumnPeer::LISTER_CONFIG_ID, ListerConfigPeer::LISTER_CONFIG_ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = ListerConfigColumnPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = ListerConfigColumnPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = ListerConfigColumnPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                ListerConfigColumnPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = ListerConfigPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = ListerConfigPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = ListerConfigPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    ListerConfigPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (ListerConfigColumn) to $obj2 (ListerConfig)
                $obj2->addListerConfigColumn($obj1);

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
        $criteria->setPrimaryTableName(ListerConfigColumnPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            ListerConfigColumnPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(ListerConfigColumnPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(ListerConfigColumnPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(ListerConfigColumnPeer::LISTER_CONFIG_ID, ListerConfigPeer::LISTER_CONFIG_ID, $join_behavior);

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
     * Selects a collection of ListerConfigColumn objects pre-filled with all related objects.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of ListerConfigColumn objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(ListerConfigColumnPeer::DATABASE_NAME);
        }

        ListerConfigColumnPeer::addSelectColumns($criteria);
        $startcol2 = ListerConfigColumnPeer::NUM_HYDRATE_COLUMNS;

        ListerConfigPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + ListerConfigPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(ListerConfigColumnPeer::LISTER_CONFIG_ID, ListerConfigPeer::LISTER_CONFIG_ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = ListerConfigColumnPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = ListerConfigColumnPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = ListerConfigColumnPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                ListerConfigColumnPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

            // Add objects for joined ListerConfig rows

            $key2 = ListerConfigPeer::getPrimaryKeyHashFromRow($row, $startcol2);
            if ($key2 !== null) {
                $obj2 = ListerConfigPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = ListerConfigPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    ListerConfigPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 loaded

                // Add the $obj1 (ListerConfigColumn) to the collection in $obj2 (ListerConfig)
                $obj2->addListerConfigColumn($obj1);
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
        return Propel::getDatabaseMap(ListerConfigColumnPeer::DATABASE_NAME)->getTable(ListerConfigColumnPeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseListerConfigColumnPeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseListerConfigColumnPeer::TABLE_NAME)) {
        $dbMap->addTableObject(new \Eulogix\Cool\Bundle\CoreBundle\Model\Core\map\ListerConfigColumnTableMap());
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
        return ListerConfigColumnPeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a ListerConfigColumn or Criteria object.
     *
     * @param      mixed $values Criteria or ListerConfigColumn object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(ListerConfigColumnPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from ListerConfigColumn object
        }

        if ($criteria->containsKey(ListerConfigColumnPeer::LISTER_CONFIG_COLUMN_ID) && $criteria->keyContainsValue(ListerConfigColumnPeer::LISTER_CONFIG_COLUMN_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.ListerConfigColumnPeer::LISTER_CONFIG_COLUMN_ID.')');
        }


        // Set the correct dbName
        $criteria->setDbName(ListerConfigColumnPeer::DATABASE_NAME);

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
     * Performs an UPDATE on the database, given a ListerConfigColumn or Criteria object.
     *
     * @param      mixed $values Criteria or ListerConfigColumn object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(ListerConfigColumnPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(ListerConfigColumnPeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(ListerConfigColumnPeer::LISTER_CONFIG_COLUMN_ID);
            $value = $criteria->remove(ListerConfigColumnPeer::LISTER_CONFIG_COLUMN_ID);
            if ($value) {
                $selectCriteria->add(ListerConfigColumnPeer::LISTER_CONFIG_COLUMN_ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(ListerConfigColumnPeer::TABLE_NAME);
            }

        } else { // $values is ListerConfigColumn object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(ListerConfigColumnPeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the core.lister_config_column table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(ListerConfigColumnPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(ListerConfigColumnPeer::TABLE_NAME, $con, ListerConfigColumnPeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            ListerConfigColumnPeer::clearInstancePool();
            ListerConfigColumnPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a ListerConfigColumn or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or ListerConfigColumn object or primary key or array of primary keys
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
            $con = Propel::getConnection(ListerConfigColumnPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            ListerConfigColumnPeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof ListerConfigColumn) { // it's a model object
            // invalidate the cache for this single object
            ListerConfigColumnPeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(ListerConfigColumnPeer::DATABASE_NAME);
            $criteria->add(ListerConfigColumnPeer::LISTER_CONFIG_COLUMN_ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                ListerConfigColumnPeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(ListerConfigColumnPeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            ListerConfigColumnPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given ListerConfigColumn object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param ListerConfigColumn $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(ListerConfigColumnPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(ListerConfigColumnPeer::TABLE_NAME);

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

        return BasePeer::doValidate(ListerConfigColumnPeer::DATABASE_NAME, ListerConfigColumnPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return ListerConfigColumn
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = ListerConfigColumnPeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(ListerConfigColumnPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(ListerConfigColumnPeer::DATABASE_NAME);
        $criteria->add(ListerConfigColumnPeer::LISTER_CONFIG_COLUMN_ID, $pk);

        $v = ListerConfigColumnPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return ListerConfigColumn[]
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(ListerConfigColumnPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(ListerConfigColumnPeer::DATABASE_NAME);
            $criteria->add(ListerConfigColumnPeer::LISTER_CONFIG_COLUMN_ID, $pks, Criteria::IN);
            $objs = ListerConfigColumnPeer::doSelect($criteria, $con);
        }

        return $objs;
    }

} // BaseListerConfigColumnPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseListerConfigColumnPeer::buildTableMap();


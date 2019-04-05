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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\Account;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountGroupRefPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountProfileRefPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountSettingPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\map\AccountTableMap;

abstract class BaseAccountPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'cool_db';

    /** the table name for this class */
    const TABLE_NAME = 'core.account';

    /** the related Propel class for this table */
    const OM_CLASS = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\Account';

    /** the related TableMap class for this table */
    const TM_CLASS = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\map\\AccountTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 16;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 16;

    /** the column name for the account_id field */
    const ACCOUNT_ID = 'core.account.account_id';

    /** the column name for the login_name field */
    const LOGIN_NAME = 'core.account.login_name';

    /** the column name for the hashed_password field */
    const HASHED_PASSWORD = 'core.account.hashed_password';

    /** the column name for the type field */
    const TYPE = 'core.account.type';

    /** the column name for the first_name field */
    const FIRST_NAME = 'core.account.first_name';

    /** the column name for the last_name field */
    const LAST_NAME = 'core.account.last_name';

    /** the column name for the sex field */
    const SEX = 'core.account.sex';

    /** the column name for the email field */
    const EMAIL = 'core.account.email';

    /** the column name for the telephone field */
    const TELEPHONE = 'core.account.telephone';

    /** the column name for the mobile field */
    const MOBILE = 'core.account.mobile';

    /** the column name for the default_locale field */
    const DEFAULT_LOCALE = 'core.account.default_locale';

    /** the column name for the company_name field */
    const COMPANY_NAME = 'core.account.company_name';

    /** the column name for the validity field */
    const VALIDITY = 'core.account.validity';

    /** the column name for the roles field */
    const ROLES = 'core.account.roles';

    /** the column name for the last_password_update field */
    const LAST_PASSWORD_UPDATE = 'core.account.last_password_update';

    /** the column name for the validate_method field */
    const VALIDATE_METHOD = 'core.account.validate_method';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identity map to hold any loaded instances of Account objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array Account[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. AccountPeer::$fieldNames[AccountPeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('AccountId', 'LoginName', 'HashedPassword', 'Type', 'FirstName', 'LastName', 'Sex', 'Email', 'Telephone', 'Mobile', 'DefaultLocale', 'CompanyName', 'Validity', 'Roles', 'LastPasswordUpdate', 'ValidateMethod', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('accountId', 'loginName', 'hashedPassword', 'type', 'firstName', 'lastName', 'sex', 'email', 'telephone', 'mobile', 'defaultLocale', 'companyName', 'validity', 'roles', 'lastPasswordUpdate', 'validateMethod', ),
        BasePeer::TYPE_COLNAME => array (AccountPeer::ACCOUNT_ID, AccountPeer::LOGIN_NAME, AccountPeer::HASHED_PASSWORD, AccountPeer::TYPE, AccountPeer::FIRST_NAME, AccountPeer::LAST_NAME, AccountPeer::SEX, AccountPeer::EMAIL, AccountPeer::TELEPHONE, AccountPeer::MOBILE, AccountPeer::DEFAULT_LOCALE, AccountPeer::COMPANY_NAME, AccountPeer::VALIDITY, AccountPeer::ROLES, AccountPeer::LAST_PASSWORD_UPDATE, AccountPeer::VALIDATE_METHOD, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ACCOUNT_ID', 'LOGIN_NAME', 'HASHED_PASSWORD', 'TYPE', 'FIRST_NAME', 'LAST_NAME', 'SEX', 'EMAIL', 'TELEPHONE', 'MOBILE', 'DEFAULT_LOCALE', 'COMPANY_NAME', 'VALIDITY', 'ROLES', 'LAST_PASSWORD_UPDATE', 'VALIDATE_METHOD', ),
        BasePeer::TYPE_FIELDNAME => array ('account_id', 'login_name', 'hashed_password', 'type', 'first_name', 'last_name', 'sex', 'email', 'telephone', 'mobile', 'default_locale', 'company_name', 'validity', 'roles', 'last_password_update', 'validate_method', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. AccountPeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('AccountId' => 0, 'LoginName' => 1, 'HashedPassword' => 2, 'Type' => 3, 'FirstName' => 4, 'LastName' => 5, 'Sex' => 6, 'Email' => 7, 'Telephone' => 8, 'Mobile' => 9, 'DefaultLocale' => 10, 'CompanyName' => 11, 'Validity' => 12, 'Roles' => 13, 'LastPasswordUpdate' => 14, 'ValidateMethod' => 15, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('accountId' => 0, 'loginName' => 1, 'hashedPassword' => 2, 'type' => 3, 'firstName' => 4, 'lastName' => 5, 'sex' => 6, 'email' => 7, 'telephone' => 8, 'mobile' => 9, 'defaultLocale' => 10, 'companyName' => 11, 'validity' => 12, 'roles' => 13, 'lastPasswordUpdate' => 14, 'validateMethod' => 15, ),
        BasePeer::TYPE_COLNAME => array (AccountPeer::ACCOUNT_ID => 0, AccountPeer::LOGIN_NAME => 1, AccountPeer::HASHED_PASSWORD => 2, AccountPeer::TYPE => 3, AccountPeer::FIRST_NAME => 4, AccountPeer::LAST_NAME => 5, AccountPeer::SEX => 6, AccountPeer::EMAIL => 7, AccountPeer::TELEPHONE => 8, AccountPeer::MOBILE => 9, AccountPeer::DEFAULT_LOCALE => 10, AccountPeer::COMPANY_NAME => 11, AccountPeer::VALIDITY => 12, AccountPeer::ROLES => 13, AccountPeer::LAST_PASSWORD_UPDATE => 14, AccountPeer::VALIDATE_METHOD => 15, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ACCOUNT_ID' => 0, 'LOGIN_NAME' => 1, 'HASHED_PASSWORD' => 2, 'TYPE' => 3, 'FIRST_NAME' => 4, 'LAST_NAME' => 5, 'SEX' => 6, 'EMAIL' => 7, 'TELEPHONE' => 8, 'MOBILE' => 9, 'DEFAULT_LOCALE' => 10, 'COMPANY_NAME' => 11, 'VALIDITY' => 12, 'ROLES' => 13, 'LAST_PASSWORD_UPDATE' => 14, 'VALIDATE_METHOD' => 15, ),
        BasePeer::TYPE_FIELDNAME => array ('account_id' => 0, 'login_name' => 1, 'hashed_password' => 2, 'type' => 3, 'first_name' => 4, 'last_name' => 5, 'sex' => 6, 'email' => 7, 'telephone' => 8, 'mobile' => 9, 'default_locale' => 10, 'company_name' => 11, 'validity' => 12, 'roles' => 13, 'last_password_update' => 14, 'validate_method' => 15, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, )
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
        $toNames = AccountPeer::getFieldNames($toType);
        $key = isset(AccountPeer::$fieldKeys[$fromType][$name]) ? AccountPeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(AccountPeer::$fieldKeys[$fromType], true));
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
        if (!array_key_exists($type, AccountPeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return AccountPeer::$fieldNames[$type];
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
     * @param      string $column The column name for current table. (i.e. AccountPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(AccountPeer::TABLE_NAME.'.', $alias.'.', $column);
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
            $criteria->addSelectColumn(AccountPeer::ACCOUNT_ID);
            $criteria->addSelectColumn(AccountPeer::LOGIN_NAME);
            $criteria->addSelectColumn(AccountPeer::HASHED_PASSWORD);
            $criteria->addSelectColumn(AccountPeer::TYPE);
            $criteria->addSelectColumn(AccountPeer::FIRST_NAME);
            $criteria->addSelectColumn(AccountPeer::LAST_NAME);
            $criteria->addSelectColumn(AccountPeer::SEX);
            $criteria->addSelectColumn(AccountPeer::EMAIL);
            $criteria->addSelectColumn(AccountPeer::TELEPHONE);
            $criteria->addSelectColumn(AccountPeer::MOBILE);
            $criteria->addSelectColumn(AccountPeer::DEFAULT_LOCALE);
            $criteria->addSelectColumn(AccountPeer::COMPANY_NAME);
            $criteria->addSelectColumn(AccountPeer::VALIDITY);
            $criteria->addSelectColumn(AccountPeer::ROLES);
            $criteria->addSelectColumn(AccountPeer::LAST_PASSWORD_UPDATE);
            $criteria->addSelectColumn(AccountPeer::VALIDATE_METHOD);
        } else {
            $criteria->addSelectColumn($alias . '.account_id');
            $criteria->addSelectColumn($alias . '.login_name');
            $criteria->addSelectColumn($alias . '.hashed_password');
            $criteria->addSelectColumn($alias . '.type');
            $criteria->addSelectColumn($alias . '.first_name');
            $criteria->addSelectColumn($alias . '.last_name');
            $criteria->addSelectColumn($alias . '.sex');
            $criteria->addSelectColumn($alias . '.email');
            $criteria->addSelectColumn($alias . '.telephone');
            $criteria->addSelectColumn($alias . '.mobile');
            $criteria->addSelectColumn($alias . '.default_locale');
            $criteria->addSelectColumn($alias . '.company_name');
            $criteria->addSelectColumn($alias . '.validity');
            $criteria->addSelectColumn($alias . '.roles');
            $criteria->addSelectColumn($alias . '.last_password_update');
            $criteria->addSelectColumn($alias . '.validate_method');
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
        $criteria->setPrimaryTableName(AccountPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            AccountPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(AccountPeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(AccountPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return Account
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = AccountPeer::doSelect($critcopy, $con);
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
        return AccountPeer::populateObjects(AccountPeer::doSelectStmt($criteria, $con));
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
            $con = Propel::getConnection(AccountPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            AccountPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(AccountPeer::DATABASE_NAME);

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
     * @param Account $obj A Account object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getAccountId();
            } // if key === null
            AccountPeer::$instances[$key] = $obj;
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
     * @param      mixed $value A Account object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof Account) {
                $key = (string) $value->getAccountId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or Account object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(AccountPeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return Account Found object or null if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(AccountPeer::$instances[$key])) {
                return AccountPeer::$instances[$key];
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
        foreach (AccountPeer::$instances as $instance) {
          $instance->clearAllReferences(true);
        }
      }
        AccountPeer::$instances = array();
    }

    /**
     * Method to invalidate the instance pool of all tables related to core.account
     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in AccountSettingPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        AccountSettingPeer::clearInstancePool();
        // Invalidate objects in AccountProfileRefPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        AccountProfileRefPeer::clearInstancePool();
        // Invalidate objects in AccountGroupRefPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        AccountGroupRefPeer::clearInstancePool();
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
        $cls = AccountPeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = AccountPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = AccountPeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                AccountPeer::addInstanceToPool($obj, $key);
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
     * @return array (Account object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = AccountPeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = AccountPeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + AccountPeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = AccountPeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            AccountPeer::addInstanceToPool($obj, $key);
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
        return Propel::getDatabaseMap(AccountPeer::DATABASE_NAME)->getTable(AccountPeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseAccountPeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseAccountPeer::TABLE_NAME)) {
        $dbMap->addTableObject(new \Eulogix\Cool\Bundle\CoreBundle\Model\Core\map\AccountTableMap());
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
        return AccountPeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a Account or Criteria object.
     *
     * @param      mixed $values Criteria or Account object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(AccountPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from Account object
        }

        if ($criteria->containsKey(AccountPeer::ACCOUNT_ID) && $criteria->keyContainsValue(AccountPeer::ACCOUNT_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.AccountPeer::ACCOUNT_ID.')');
        }


        // Set the correct dbName
        $criteria->setDbName(AccountPeer::DATABASE_NAME);

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
     * Performs an UPDATE on the database, given a Account or Criteria object.
     *
     * @param      mixed $values Criteria or Account object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(AccountPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(AccountPeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(AccountPeer::ACCOUNT_ID);
            $value = $criteria->remove(AccountPeer::ACCOUNT_ID);
            if ($value) {
                $selectCriteria->add(AccountPeer::ACCOUNT_ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(AccountPeer::TABLE_NAME);
            }

        } else { // $values is Account object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(AccountPeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the core.account table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(AccountPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(AccountPeer::TABLE_NAME, $con, AccountPeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            AccountPeer::clearInstancePool();
            AccountPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a Account or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or Account object or primary key or array of primary keys
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
            $con = Propel::getConnection(AccountPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            AccountPeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof Account) { // it's a model object
            // invalidate the cache for this single object
            AccountPeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(AccountPeer::DATABASE_NAME);
            $criteria->add(AccountPeer::ACCOUNT_ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                AccountPeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(AccountPeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            AccountPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given Account object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param Account $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(AccountPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(AccountPeer::TABLE_NAME);

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

        return BasePeer::doValidate(AccountPeer::DATABASE_NAME, AccountPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return Account
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = AccountPeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(AccountPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(AccountPeer::DATABASE_NAME);
        $criteria->add(AccountPeer::ACCOUNT_ID, $pk);

        $v = AccountPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return Account[]
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(AccountPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(AccountPeer::DATABASE_NAME);
            $criteria->add(AccountPeer::ACCOUNT_ID, $pks, Criteria::IN);
            $objs = AccountPeer::doSelect($criteria, $con);
        }

        return $objs;
    }

} // BaseAccountPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseAccountPeer::buildTableMap();


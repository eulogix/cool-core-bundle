<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core\om;

use \BasePeer;
use \Criteria;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelException;
use \PropelPDO;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserReminder;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserReminderPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserReminderQuery;
use Eulogix\Cool\Lib\Database\Propel\CoolPropelObject;

abstract class BaseUserReminder extends CoolPropelObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\UserReminderPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        UserReminderPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the user_reminder_id field.
     * @var        int
     */
    protected $user_reminder_id;

    /**
     * The value for the name field.
     * @var        string
     */
    protected $name;

    /**
     * The value for the category field.
     * @var        string
     */
    protected $category;

    /**
     * The value for the lister field.
     * @var        string
     */
    protected $lister;

    /**
     * The value for the lister_translation_domain field.
     * @var        string
     */
    protected $lister_translation_domain;

    /**
     * The value for the parent_tables field.
     * @var        string
     */
    protected $parent_tables;

    /**
     * The value for the context_schema field.
     * Note: this column has a database default value of: 'core'
     * @var        string
     */
    protected $context_schema;

    /**
     * The value for the sql_query field.
     * @var        string
     */
    protected $sql_query;

    /**
     * The value for the type field.
     * Note: this column has a database default value of: 'SIMPLE'
     * @var        string
     */
    protected $type;

    /**
     * The value for the sort_order field.
     * @var        int
     */
    protected $sort_order;

    /**
     * The value for the count_sql_query field.
     * @var        string
     */
    protected $count_sql_query;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInSave = false;

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * Flag to prevent endless clearAllReferences($deep=true) loop, if this object is referenced
     * @var        boolean
     */
    protected $alreadyInClearAllReferencesDeep = false;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->context_schema = 'core';
        $this->type = 'SIMPLE';
    }

    /**
     * Initializes internal state of BaseUserReminder object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
    }

    /**
     * Get the [user_reminder_id] column value.
     *
     * @return int
     */
    public function getUserReminderId()
    {

        return $this->user_reminder_id;
    }

    /**
     * Get the [name] column value.
     *
     * @return string
     */
    public function getName()
    {

        return $this->name;
    }

    /**
     * Get the [category] column value.
     *
     * @return string
     */
    public function getCategory()
    {

        return $this->category;
    }

    /**
     * Get the [lister] column value.
     * fill this with a widgetId if you don't want the default one
     * @return string
     */
    public function getLister()
    {

        return $this->lister;
    }

    /**
     * Get the [lister_translation_domain] column value.
     * set this with an existing translation domain to reuse translations already in place somewhere else
     * @return string
     */
    public function getListerTranslationDomain()
    {

        return $this->lister_translation_domain;
    }

    /**
     * Get the [parent_tables] column value.
     * use a comma separated list of tables here. Their fields will be visible in the lister
     * @return string
     */
    public function getParentTables()
    {

        return $this->parent_tables;
    }

    /**
     * Get the [context_schema] column value.
     *
     * @return string
     */
    public function getContextSchema()
    {

        return $this->context_schema;
    }

    /**
     * Get the [sql_query] column value.
     *
     * @return string
     */
    public function getSqlQuery()
    {

        return $this->sql_query;
    }

    /**
     * Get the [type] column value.
     * SIMPLE: the result is a simple count. DATED: the query is partitioned on the _date column
     * @return string
     */
    public function getType()
    {

        return $this->type;
    }

    /**
     * Get the [sort_order] column value.
     *
     * @return int
     */
    public function getSortOrder()
    {

        return $this->sort_order;
    }

    /**
     * Get the [count_sql_query] column value.
     * alternate query for counting, used as is instead of the rewritten query for complex cases
     * @return string
     */
    public function getCountSqlQuery()
    {

        return $this->count_sql_query;
    }

    /**
     * Set the value of [user_reminder_id] column.
     *
     * @param  int $v new value
     * @return UserReminder The current object (for fluent API support)
     */
    public function setUserReminderId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->user_reminder_id !== $v) {
            $this->user_reminder_id = $v;
            $this->modifiedColumns[] = UserReminderPeer::USER_REMINDER_ID;
        }


        return $this;
    } // setUserReminderId()

    /**
     * Set the value of [name] column.
     *
     * @param  string $v new value
     * @return UserReminder The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[] = UserReminderPeer::NAME;
        }


        return $this;
    } // setName()

    /**
     * Set the value of [category] column.
     *
     * @param  string $v new value
     * @return UserReminder The current object (for fluent API support)
     */
    public function setCategory($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->category !== $v) {
            $this->category = $v;
            $this->modifiedColumns[] = UserReminderPeer::CATEGORY;
        }


        return $this;
    } // setCategory()

    /**
     * Set the value of [lister] column.
     * fill this with a widgetId if you don't want the default one
     * @param  string $v new value
     * @return UserReminder The current object (for fluent API support)
     */
    public function setLister($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->lister !== $v) {
            $this->lister = $v;
            $this->modifiedColumns[] = UserReminderPeer::LISTER;
        }


        return $this;
    } // setLister()

    /**
     * Set the value of [lister_translation_domain] column.
     * set this with an existing translation domain to reuse translations already in place somewhere else
     * @param  string $v new value
     * @return UserReminder The current object (for fluent API support)
     */
    public function setListerTranslationDomain($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->lister_translation_domain !== $v) {
            $this->lister_translation_domain = $v;
            $this->modifiedColumns[] = UserReminderPeer::LISTER_TRANSLATION_DOMAIN;
        }


        return $this;
    } // setListerTranslationDomain()

    /**
     * Set the value of [parent_tables] column.
     * use a comma separated list of tables here. Their fields will be visible in the lister
     * @param  string $v new value
     * @return UserReminder The current object (for fluent API support)
     */
    public function setParentTables($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->parent_tables !== $v) {
            $this->parent_tables = $v;
            $this->modifiedColumns[] = UserReminderPeer::PARENT_TABLES;
        }


        return $this;
    } // setParentTables()

    /**
     * Set the value of [context_schema] column.
     *
     * @param  string $v new value
     * @return UserReminder The current object (for fluent API support)
     */
    public function setContextSchema($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->context_schema !== $v) {
            $this->context_schema = $v;
            $this->modifiedColumns[] = UserReminderPeer::CONTEXT_SCHEMA;
        }


        return $this;
    } // setContextSchema()

    /**
     * Set the value of [sql_query] column.
     *
     * @param  string $v new value
     * @return UserReminder The current object (for fluent API support)
     */
    public function setSqlQuery($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->sql_query !== $v) {
            $this->sql_query = $v;
            $this->modifiedColumns[] = UserReminderPeer::SQL_QUERY;
        }


        return $this;
    } // setSqlQuery()

    /**
     * Set the value of [type] column.
     * SIMPLE: the result is a simple count. DATED: the query is partitioned on the _date column
     * @param  string $v new value
     * @return UserReminder The current object (for fluent API support)
     */
    public function setType($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->type !== $v) {
            $this->type = $v;
            $this->modifiedColumns[] = UserReminderPeer::TYPE;
        }


        return $this;
    } // setType()

    /**
     * Set the value of [sort_order] column.
     *
     * @param  int $v new value
     * @return UserReminder The current object (for fluent API support)
     */
    public function setSortOrder($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->sort_order !== $v) {
            $this->sort_order = $v;
            $this->modifiedColumns[] = UserReminderPeer::SORT_ORDER;
        }


        return $this;
    } // setSortOrder()

    /**
     * Set the value of [count_sql_query] column.
     * alternate query for counting, used as is instead of the rewritten query for complex cases
     * @param  string $v new value
     * @return UserReminder The current object (for fluent API support)
     */
    public function setCountSqlQuery($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->count_sql_query !== $v) {
            $this->count_sql_query = $v;
            $this->modifiedColumns[] = UserReminderPeer::COUNT_SQL_QUERY;
        }


        return $this;
    } // setCountSqlQuery()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
            if ($this->context_schema !== 'core') {
                return false;
            }

            if ($this->type !== 'SIMPLE') {
                return false;
            }

        // otherwise, everything was equal, so return true
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
     * @param int $startcol 0-based offset column which indicates which resultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false)
    {
        try {

            $this->user_reminder_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->name = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->category = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->lister = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->lister_translation_domain = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->parent_tables = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->context_schema = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->sql_query = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->type = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->sort_order = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
            $this->count_sql_query = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 11; // 11 = UserReminderPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating UserReminder object", $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {

    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param boolean $deep (optional) Whether to also de-associated any related objects.
     * @param PropelPDO $con (optional) The PropelPDO connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getConnection(UserReminderPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = UserReminderPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

        } // if (deep)

        $this->reloadCalculatedFields();

    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param PropelPDO $con
     * @return void
     * @throws PropelException
     * @throws Exception
     * @see        BaseObject::setDeleted()
     * @see        BaseObject::isDeleted()
     */
    public function delete(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(UserReminderPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = UserReminderQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @throws Exception
     * @see        doSave()
     */
    public function save(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(UserReminderPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                UserReminderPeer::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see        save()
     */
    protected function doSave(PropelPDO $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param PropelPDO $con
     *
     * @throws PropelException
     * @see        doSave()
     */
    protected function doInsert(PropelPDO $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[] = UserReminderPeer::USER_REMINDER_ID;
        if (null !== $this->user_reminder_id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . UserReminderPeer::USER_REMINDER_ID . ')');
        }
        if (null === $this->user_reminder_id) {
            try {
                $stmt = $con->query("SELECT nextval('core.user_reminder_user_reminder_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->user_reminder_id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(UserReminderPeer::USER_REMINDER_ID)) {
            $modifiedColumns[':p' . $index++]  = 'user_reminder_id';
        }
        if ($this->isColumnModified(UserReminderPeer::NAME)) {
            $modifiedColumns[':p' . $index++]  = 'name';
        }
        if ($this->isColumnModified(UserReminderPeer::CATEGORY)) {
            $modifiedColumns[':p' . $index++]  = 'category';
        }
        if ($this->isColumnModified(UserReminderPeer::LISTER)) {
            $modifiedColumns[':p' . $index++]  = 'lister';
        }
        if ($this->isColumnModified(UserReminderPeer::LISTER_TRANSLATION_DOMAIN)) {
            $modifiedColumns[':p' . $index++]  = 'lister_translation_domain';
        }
        if ($this->isColumnModified(UserReminderPeer::PARENT_TABLES)) {
            $modifiedColumns[':p' . $index++]  = 'parent_tables';
        }
        if ($this->isColumnModified(UserReminderPeer::CONTEXT_SCHEMA)) {
            $modifiedColumns[':p' . $index++]  = 'context_schema';
        }
        if ($this->isColumnModified(UserReminderPeer::SQL_QUERY)) {
            $modifiedColumns[':p' . $index++]  = 'sql_query';
        }
        if ($this->isColumnModified(UserReminderPeer::TYPE)) {
            $modifiedColumns[':p' . $index++]  = 'type';
        }
        if ($this->isColumnModified(UserReminderPeer::SORT_ORDER)) {
            $modifiedColumns[':p' . $index++]  = 'sort_order';
        }
        if ($this->isColumnModified(UserReminderPeer::COUNT_SQL_QUERY)) {
            $modifiedColumns[':p' . $index++]  = 'count_sql_query';
        }

        $sql = sprintf(
            'INSERT INTO core.user_reminder (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'user_reminder_id':
                        $stmt->bindValue($identifier, $this->user_reminder_id, PDO::PARAM_INT);
                        break;
                    case 'name':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case 'category':
                        $stmt->bindValue($identifier, $this->category, PDO::PARAM_STR);
                        break;
                    case 'lister':
                        $stmt->bindValue($identifier, $this->lister, PDO::PARAM_STR);
                        break;
                    case 'lister_translation_domain':
                        $stmt->bindValue($identifier, $this->lister_translation_domain, PDO::PARAM_STR);
                        break;
                    case 'parent_tables':
                        $stmt->bindValue($identifier, $this->parent_tables, PDO::PARAM_STR);
                        break;
                    case 'context_schema':
                        $stmt->bindValue($identifier, $this->context_schema, PDO::PARAM_STR);
                        break;
                    case 'sql_query':
                        $stmt->bindValue($identifier, $this->sql_query, PDO::PARAM_STR);
                        break;
                    case 'type':
                        $stmt->bindValue($identifier, $this->type, PDO::PARAM_STR);
                        break;
                    case 'sort_order':
                        $stmt->bindValue($identifier, $this->sort_order, PDO::PARAM_INT);
                        break;
                    case 'count_sql_query':
                        $stmt->bindValue($identifier, $this->count_sql_query, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
        }

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param PropelPDO $con
     *
     * @see        doSave()
     */
    protected function doUpdate(PropelPDO $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();
        BasePeer::doUpdate($selectCriteria, $valuesCriteria, $con);
    }

    /**
     * Array of ValidationFailed objects.
     * @var        array ValidationFailed[]
     */
    protected $validationFailures = array();

    /**
     * Gets any ValidationFailed objects that resulted from last call to validate().
     *
     *
     * @return array ValidationFailed[]
     * @see        validate()
     */
    public function getValidationFailures()
    {
        return $this->validationFailures;
    }

    /**
     * Validates the objects modified field values and all objects related to this table.
     *
     * If $columns is either a column name or an array of column names
     * only those columns are validated.
     *
     * @param mixed $columns Column name or an array of column names.
     * @return boolean Whether all columns pass validation.
     * @see        doValidate()
     * @see        getValidationFailures()
     */
    public function validate($columns = null)
    {
        $res = $this->doValidate($columns);
        if ($res === true) {
            $this->validationFailures = array();

            return true;
        }

        $this->validationFailures = $res;

        return false;
    }

    /**
     * This function performs the validation work for complex object models.
     *
     * In addition to checking the current object, all related objects will
     * also be validated.  If all pass then <code>true</code> is returned; otherwise
     * an aggregated array of ValidationFailed objects will be returned.
     *
     * @param array $columns Array of column names to validate.
     * @return mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objects otherwise.
     */
    protected function doValidate($columns = null)
    {
        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            $failureMap = array();


            if (($retval = UserReminderPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }



            $this->alreadyInValidation = false;
        }

        return (!empty($failureMap) ? $failureMap : true);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param string $name name
     * @param string $type The type of fieldname the $name is of:
     *               one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *               BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *               Defaults to BasePeer::TYPE_PHPNAME
     * @return mixed Value of field.
     */
    public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = UserReminderPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getUserReminderId();
                break;
            case 1:
                return $this->getName();
                break;
            case 2:
                return $this->getCategory();
                break;
            case 3:
                return $this->getLister();
                break;
            case 4:
                return $this->getListerTranslationDomain();
                break;
            case 5:
                return $this->getParentTables();
                break;
            case 6:
                return $this->getContextSchema();
                break;
            case 7:
                return $this->getSqlQuery();
                break;
            case 8:
                return $this->getType();
                break;
            case 9:
                return $this->getSortOrder();
                break;
            case 10:
                return $this->getCountSqlQuery();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     *                    BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                    Defaults to BasePeer::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to true.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array())
    {
        if (isset($alreadyDumpedObjects['UserReminder'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['UserReminder'][$this->getPrimaryKey()] = true;
        $keys = UserReminderPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getUserReminderId(),
            $keys[1] => $this->getName(),
            $keys[2] => $this->getCategory(),
            $keys[3] => $this->getLister(),
            $keys[4] => $this->getListerTranslationDomain(),
            $keys[5] => $this->getParentTables(),
            $keys[6] => $this->getContextSchema(),
            $keys[7] => $this->getSqlQuery(),
            $keys[8] => $this->getType(),
            $keys[9] => $this->getSortOrder(),
            $keys[10] => $this->getCountSqlQuery(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }


        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param string $name peer name
     * @param mixed $value field value
     * @param string $type The type of fieldname the $name is of:
     *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                     Defaults to BasePeer::TYPE_PHPNAME
     * @return void
     */
    public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = UserReminderPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

        $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @param mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setUserReminderId($value);
                break;
            case 1:
                $this->setName($value);
                break;
            case 2:
                $this->setCategory($value);
                break;
            case 3:
                $this->setLister($value);
                break;
            case 4:
                $this->setListerTranslationDomain($value);
                break;
            case 5:
                $this->setParentTables($value);
                break;
            case 6:
                $this->setContextSchema($value);
                break;
            case 7:
                $this->setSqlQuery($value);
                break;
            case 8:
                $this->setType($value);
                break;
            case 9:
                $this->setSortOrder($value);
                break;
            case 10:
                $this->setCountSqlQuery($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     * BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     * The default key type is the column's BasePeer::TYPE_PHPNAME
     *
     * @param array  $arr     An array to populate the object from.
     * @param string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
    {
        $keys = UserReminderPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setUserReminderId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setName($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setCategory($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setLister($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setListerTranslationDomain($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setParentTables($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setContextSchema($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setSqlQuery($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setType($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setSortOrder($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setCountSqlQuery($arr[$keys[10]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(UserReminderPeer::DATABASE_NAME);

        if ($this->isColumnModified(UserReminderPeer::USER_REMINDER_ID)) $criteria->add(UserReminderPeer::USER_REMINDER_ID, $this->user_reminder_id);
        if ($this->isColumnModified(UserReminderPeer::NAME)) $criteria->add(UserReminderPeer::NAME, $this->name);
        if ($this->isColumnModified(UserReminderPeer::CATEGORY)) $criteria->add(UserReminderPeer::CATEGORY, $this->category);
        if ($this->isColumnModified(UserReminderPeer::LISTER)) $criteria->add(UserReminderPeer::LISTER, $this->lister);
        if ($this->isColumnModified(UserReminderPeer::LISTER_TRANSLATION_DOMAIN)) $criteria->add(UserReminderPeer::LISTER_TRANSLATION_DOMAIN, $this->lister_translation_domain);
        if ($this->isColumnModified(UserReminderPeer::PARENT_TABLES)) $criteria->add(UserReminderPeer::PARENT_TABLES, $this->parent_tables);
        if ($this->isColumnModified(UserReminderPeer::CONTEXT_SCHEMA)) $criteria->add(UserReminderPeer::CONTEXT_SCHEMA, $this->context_schema);
        if ($this->isColumnModified(UserReminderPeer::SQL_QUERY)) $criteria->add(UserReminderPeer::SQL_QUERY, $this->sql_query);
        if ($this->isColumnModified(UserReminderPeer::TYPE)) $criteria->add(UserReminderPeer::TYPE, $this->type);
        if ($this->isColumnModified(UserReminderPeer::SORT_ORDER)) $criteria->add(UserReminderPeer::SORT_ORDER, $this->sort_order);
        if ($this->isColumnModified(UserReminderPeer::COUNT_SQL_QUERY)) $criteria->add(UserReminderPeer::COUNT_SQL_QUERY, $this->count_sql_query);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(UserReminderPeer::DATABASE_NAME);
        $criteria->add(UserReminderPeer::USER_REMINDER_ID, $this->user_reminder_id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getUserReminderId();
    }

    /**
     * Generic method to set the primary key (user_reminder_id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setUserReminderId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getUserReminderId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of UserReminder (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setName($this->getName());
        $copyObj->setCategory($this->getCategory());
        $copyObj->setLister($this->getLister());
        $copyObj->setListerTranslationDomain($this->getListerTranslationDomain());
        $copyObj->setParentTables($this->getParentTables());
        $copyObj->setContextSchema($this->getContextSchema());
        $copyObj->setSqlQuery($this->getSqlQuery());
        $copyObj->setType($this->getType());
        $copyObj->setSortOrder($this->getSortOrder());
        $copyObj->setCountSqlQuery($this->getCountSqlQuery());
        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setUserReminderId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return UserReminder Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Returns a peer instance associated with this om.
     *
     * Since Peer classes are not to have any instance attributes, this method returns the
     * same instance for all member of this class. The method could therefore
     * be static, but this would prevent one from overriding the behavior.
     *
     * @return UserReminderPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new UserReminderPeer();
        }

        return self::$peer;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->user_reminder_id = null;
        $this->name = null;
        $this->category = null;
        $this->lister = null;
        $this->lister_translation_domain = null;
        $this->parent_tables = null;
        $this->context_schema = null;
        $this->sql_query = null;
        $this->type = null;
        $this->sort_order = null;
        $this->count_sql_query = null;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
        $this->alreadyInClearAllReferencesDeep = false;
        $this->clearAllReferences();
        $this->applyDefaultValues();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volume/high-memory operations.
     *
     * @param boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep && !$this->alreadyInClearAllReferencesDeep) {
            $this->alreadyInClearAllReferencesDeep = true;

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(UserReminderPeer::DEFAULT_STRING_FORMAT);
    }

    /**
     * return true is the object is in saving state
     *
     * @return boolean
     */
    public function isAlreadyInSave()
    {
        return $this->alreadyInSave;
    }

}

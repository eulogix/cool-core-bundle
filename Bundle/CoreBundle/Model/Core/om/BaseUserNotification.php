<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core\om;

use \BasePeer;
use \Criteria;
use \DateTime;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelDateTime;
use \PropelException;
use \PropelPDO;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\Account;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountQuery;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserNotification;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserNotificationPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserNotificationQuery;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Database\Propel\CoolPropelObject;

abstract class BaseUserNotification extends CoolPropelObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\UserNotificationPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        UserNotificationPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the user_notification_id field.
     * @var        int
     */
    protected $user_notification_id;

    /**
     * The value for the user_id field.
     * @var        int
     */
    protected $user_id;

    /**
     * The value for the context field.
     * @var        string
     */
    protected $context;

    /**
     * The value for the title field.
     * @var        string
     */
    protected $title;

    /**
     * The value for the notification field.
     * @var        string
     */
    protected $notification;

    /**
     * The value for the notification_data field.
     * @var        string
     */
    protected $notification_data;

    /**
     * The value for the creation_date field.
     * @var        string
     */
    protected $creation_date;

    /**
     * The value for the update_date field.
     * @var        string
     */
    protected $update_date;

    /**
     * The value for the creation_user_id field.
     * @var        int
     */
    protected $creation_user_id;

    /**
     * The value for the update_user_id field.
     * @var        int
     */
    protected $update_user_id;

    /**
     * The value for the record_version field.
     * @var        int
     */
    protected $record_version;

    /**
     * @var        Account
     */
    protected $aAccountRelatedByUserId;

    /**
     * @var        Account
     */
    protected $aAccountRelatedByCreationUserId;

    /**
     * @var        Account
     */
    protected $aAccountRelatedByUpdateUserId;

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
     * Get the [user_notification_id] column value.
     *
     * @return int
     */
    public function getUserNotificationId()
    {

        return $this->user_notification_id;
    }

    /**
     * Get the [user_id] column value.
     *
     * @return int
     */
    public function getUserId()
    {

        return $this->user_id;
    }

    /**
     * Get the [context] column value.
     * used to group notifications and to only show them in specific contexts (such as projects)
     * @return string
     */
    public function getContext()
    {

        return $this->context;
    }

    /**
     * Get the [title] column value.
     * the notification title
     * @return string
     */
    public function getTitle()
    {

        return $this->title;
    }

    /**
     * Get the [notification] column value.
     * the notification text
     * @return string
     */
    public function getNotification()
    {

        return $this->notification;
    }

    /**
     * Get the [notification_data] column value.
     * container for additional data used in UI rendering
     * @return string
     */
    public function getNotificationData()
    {

        return $this->notification_data;
    }

    /**
     * Get the [optionally formatted] temporal [creation_date] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreationDate($format = null)
    {
        if ($this->creation_date === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->creation_date);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->creation_date, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Get the [optionally formatted] temporal [update_date] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getUpdateDate($format = null)
    {
        if ($this->update_date === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->update_date);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->update_date, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Get the [creation_user_id] column value.
     *
     * @return int
     */
    public function getCreationUserId()
    {

        return $this->creation_user_id;
    }

    /**
     * Get the [update_user_id] column value.
     *
     * @return int
     */
    public function getUpdateUserId()
    {

        return $this->update_user_id;
    }

    /**
     * Get the [record_version] column value.
     *
     * @return int
     */
    public function getRecordVersion()
    {

        return $this->record_version;
    }

    /**
     * Set the value of [user_notification_id] column.
     *
     * @param  int $v new value
     * @return UserNotification The current object (for fluent API support)
     */
    public function setUserNotificationId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->user_notification_id !== $v) {
            $this->user_notification_id = $v;
            $this->modifiedColumns[] = UserNotificationPeer::USER_NOTIFICATION_ID;
        }


        return $this;
    } // setUserNotificationId()

    /**
     * Set the value of [user_id] column.
     *
     * @param  int $v new value
     * @return UserNotification The current object (for fluent API support)
     */
    public function setUserId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->user_id !== $v) {
            $this->user_id = $v;
            $this->modifiedColumns[] = UserNotificationPeer::USER_ID;
        }

        if ($this->aAccountRelatedByUserId !== null && $this->aAccountRelatedByUserId->getAccountId() !== $v) {
            $this->aAccountRelatedByUserId = null;
        }


        return $this;
    } // setUserId()

    /**
     * Set the value of [context] column.
     * used to group notifications and to only show them in specific contexts (such as projects)
     * @param  string $v new value
     * @return UserNotification The current object (for fluent API support)
     */
    public function setContext($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->context !== $v) {
            $this->context = $v;
            $this->modifiedColumns[] = UserNotificationPeer::CONTEXT;
        }


        return $this;
    } // setContext()

    /**
     * Set the value of [title] column.
     * the notification title
     * @param  string $v new value
     * @return UserNotification The current object (for fluent API support)
     */
    public function setTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[] = UserNotificationPeer::TITLE;
        }


        return $this;
    } // setTitle()

    /**
     * Set the value of [notification] column.
     * the notification text
     * @param  string $v new value
     * @return UserNotification The current object (for fluent API support)
     */
    public function setNotification($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->notification !== $v) {
            $this->notification = $v;
            $this->modifiedColumns[] = UserNotificationPeer::NOTIFICATION;
        }


        return $this;
    } // setNotification()

    /**
     * Set the value of [notification_data] column.
     * container for additional data used in UI rendering
     * @param  string $v new value
     * @return UserNotification The current object (for fluent API support)
     */
    public function setNotificationData($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->notification_data !== $v) {
            $this->notification_data = $v;
            $this->modifiedColumns[] = UserNotificationPeer::NOTIFICATION_DATA;
        }


        return $this;
    } // setNotificationData()

    /**
     * Sets the value of [creation_date] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return UserNotification The current object (for fluent API support)
     */
    public function setCreationDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->creation_date !== null || $dt !== null) {
            $currentDateAsString = ($this->creation_date !== null && $tmpDt = new DateTime($this->creation_date)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->creation_date = $newDateAsString;
                $this->modifiedColumns[] = UserNotificationPeer::CREATION_DATE;
            }
        } // if either are not null


        return $this;
    } // setCreationDate()

    /**
     * Sets the value of [update_date] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return UserNotification The current object (for fluent API support)
     */
    public function setUpdateDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->update_date !== null || $dt !== null) {
            $currentDateAsString = ($this->update_date !== null && $tmpDt = new DateTime($this->update_date)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->update_date = $newDateAsString;
                $this->modifiedColumns[] = UserNotificationPeer::UPDATE_DATE;
            }
        } // if either are not null


        return $this;
    } // setUpdateDate()

    /**
     * Set the value of [creation_user_id] column.
     *
     * @param  int $v new value
     * @return UserNotification The current object (for fluent API support)
     */
    public function setCreationUserId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->creation_user_id !== $v) {
            $this->creation_user_id = $v;
            $this->modifiedColumns[] = UserNotificationPeer::CREATION_USER_ID;
        }

        if ($this->aAccountRelatedByCreationUserId !== null && $this->aAccountRelatedByCreationUserId->getAccountId() !== $v) {
            $this->aAccountRelatedByCreationUserId = null;
        }


        return $this;
    } // setCreationUserId()

    /**
     * Set the value of [update_user_id] column.
     *
     * @param  int $v new value
     * @return UserNotification The current object (for fluent API support)
     */
    public function setUpdateUserId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->update_user_id !== $v) {
            $this->update_user_id = $v;
            $this->modifiedColumns[] = UserNotificationPeer::UPDATE_USER_ID;
        }

        if ($this->aAccountRelatedByUpdateUserId !== null && $this->aAccountRelatedByUpdateUserId->getAccountId() !== $v) {
            $this->aAccountRelatedByUpdateUserId = null;
        }


        return $this;
    } // setUpdateUserId()

    /**
     * Set the value of [record_version] column.
     *
     * @param  int $v new value
     * @return UserNotification The current object (for fluent API support)
     */
    public function setRecordVersion($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->record_version !== $v) {
            $this->record_version = $v;
            $this->modifiedColumns[] = UserNotificationPeer::RECORD_VERSION;
        }


        return $this;
    } // setRecordVersion()

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

            $this->user_notification_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->user_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->context = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->title = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->notification = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->notification_data = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->creation_date = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->update_date = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->creation_user_id = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
            $this->update_user_id = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
            $this->record_version = ($row[$startcol + 10] !== null) ? (int) $row[$startcol + 10] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 11; // 11 = UserNotificationPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating UserNotification object", $e);
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

        if ($this->aAccountRelatedByUserId !== null && $this->user_id !== $this->aAccountRelatedByUserId->getAccountId()) {
            $this->aAccountRelatedByUserId = null;
        }
        if ($this->aAccountRelatedByCreationUserId !== null && $this->creation_user_id !== $this->aAccountRelatedByCreationUserId->getAccountId()) {
            $this->aAccountRelatedByCreationUserId = null;
        }
        if ($this->aAccountRelatedByUpdateUserId !== null && $this->update_user_id !== $this->aAccountRelatedByUpdateUserId->getAccountId()) {
            $this->aAccountRelatedByUpdateUserId = null;
        }
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
            $con = Propel::getConnection(UserNotificationPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = UserNotificationPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aAccountRelatedByUserId = null;
            $this->aAccountRelatedByCreationUserId = null;
            $this->aAccountRelatedByUpdateUserId = null;
        } // if (deep)
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
            $con = Propel::getConnection(UserNotificationPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = UserNotificationQuery::create()
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
            $con = Propel::getConnection(UserNotificationPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                UserNotificationPeer::addInstanceToPool($this);
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

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aAccountRelatedByUserId !== null) {
                if ($this->aAccountRelatedByUserId->isModified() || $this->aAccountRelatedByUserId->isNew()) {
                    $affectedRows += $this->aAccountRelatedByUserId->save($con);
                }
                $this->setAccountRelatedByUserId($this->aAccountRelatedByUserId);
            }

            if ($this->aAccountRelatedByCreationUserId !== null) {
                if ($this->aAccountRelatedByCreationUserId->isModified() || $this->aAccountRelatedByCreationUserId->isNew()) {
                    $affectedRows += $this->aAccountRelatedByCreationUserId->save($con);
                }
                $this->setAccountRelatedByCreationUserId($this->aAccountRelatedByCreationUserId);
            }

            if ($this->aAccountRelatedByUpdateUserId !== null) {
                if ($this->aAccountRelatedByUpdateUserId->isModified() || $this->aAccountRelatedByUpdateUserId->isNew()) {
                    $affectedRows += $this->aAccountRelatedByUpdateUserId->save($con);
                }
                $this->setAccountRelatedByUpdateUserId($this->aAccountRelatedByUpdateUserId);
            }

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

        $this->modifiedColumns[] = UserNotificationPeer::USER_NOTIFICATION_ID;
        if (null !== $this->user_notification_id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . UserNotificationPeer::USER_NOTIFICATION_ID . ')');
        }
        if (null === $this->user_notification_id) {
            try {
                $stmt = $con->query("SELECT nextval('core.user_notification_user_notification_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->user_notification_id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(UserNotificationPeer::USER_NOTIFICATION_ID)) {
            $modifiedColumns[':p' . $index++]  = 'user_notification_id';
        }
        if ($this->isColumnModified(UserNotificationPeer::USER_ID)) {
            $modifiedColumns[':p' . $index++]  = 'user_id';
        }
        if ($this->isColumnModified(UserNotificationPeer::CONTEXT)) {
            $modifiedColumns[':p' . $index++]  = 'context';
        }
        if ($this->isColumnModified(UserNotificationPeer::TITLE)) {
            $modifiedColumns[':p' . $index++]  = 'title';
        }
        if ($this->isColumnModified(UserNotificationPeer::NOTIFICATION)) {
            $modifiedColumns[':p' . $index++]  = 'notification';
        }
        if ($this->isColumnModified(UserNotificationPeer::NOTIFICATION_DATA)) {
            $modifiedColumns[':p' . $index++]  = 'notification_data';
        }
        if ($this->isColumnModified(UserNotificationPeer::CREATION_DATE)) {
            $modifiedColumns[':p' . $index++]  = 'creation_date';
        }
        if ($this->isColumnModified(UserNotificationPeer::UPDATE_DATE)) {
            $modifiedColumns[':p' . $index++]  = 'update_date';
        }
        if ($this->isColumnModified(UserNotificationPeer::CREATION_USER_ID)) {
            $modifiedColumns[':p' . $index++]  = 'creation_user_id';
        }
        if ($this->isColumnModified(UserNotificationPeer::UPDATE_USER_ID)) {
            $modifiedColumns[':p' . $index++]  = 'update_user_id';
        }
        if ($this->isColumnModified(UserNotificationPeer::RECORD_VERSION)) {
            $modifiedColumns[':p' . $index++]  = 'record_version';
        }

        $sql = sprintf(
            'INSERT INTO core.user_notification (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'user_notification_id':
                        $stmt->bindValue($identifier, $this->user_notification_id, PDO::PARAM_INT);
                        break;
                    case 'user_id':
                        $stmt->bindValue($identifier, $this->user_id, PDO::PARAM_INT);
                        break;
                    case 'context':
                        $stmt->bindValue($identifier, $this->context, PDO::PARAM_STR);
                        break;
                    case 'title':
                        $stmt->bindValue($identifier, $this->title, PDO::PARAM_STR);
                        break;
                    case 'notification':
                        $stmt->bindValue($identifier, $this->notification, PDO::PARAM_STR);
                        break;
                    case 'notification_data':
                        $stmt->bindValue($identifier, $this->notification_data, PDO::PARAM_STR);
                        break;
                    case 'creation_date':
                        $stmt->bindValue($identifier, $this->creation_date, PDO::PARAM_STR);
                        break;
                    case 'update_date':
                        $stmt->bindValue($identifier, $this->update_date, PDO::PARAM_STR);
                        break;
                    case 'creation_user_id':
                        $stmt->bindValue($identifier, $this->creation_user_id, PDO::PARAM_INT);
                        break;
                    case 'update_user_id':
                        $stmt->bindValue($identifier, $this->update_user_id, PDO::PARAM_INT);
                        break;
                    case 'record_version':
                        $stmt->bindValue($identifier, $this->record_version, PDO::PARAM_INT);
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


            // We call the validate method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aAccountRelatedByUserId !== null) {
                if (!$this->aAccountRelatedByUserId->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aAccountRelatedByUserId->getValidationFailures());
                }
            }

            if ($this->aAccountRelatedByCreationUserId !== null) {
                if (!$this->aAccountRelatedByCreationUserId->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aAccountRelatedByCreationUserId->getValidationFailures());
                }
            }

            if ($this->aAccountRelatedByUpdateUserId !== null) {
                if (!$this->aAccountRelatedByUpdateUserId->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aAccountRelatedByUpdateUserId->getValidationFailures());
                }
            }


            if (($retval = UserNotificationPeer::doValidate($this, $columns)) !== true) {
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
        $pos = UserNotificationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getUserNotificationId();
                break;
            case 1:
                return $this->getUserId();
                break;
            case 2:
                return $this->getContext();
                break;
            case 3:
                return $this->getTitle();
                break;
            case 4:
                return $this->getNotification();
                break;
            case 5:
                return $this->getNotificationData();
                break;
            case 6:
                return $this->getCreationDate();
                break;
            case 7:
                return $this->getUpdateDate();
                break;
            case 8:
                return $this->getCreationUserId();
                break;
            case 9:
                return $this->getUpdateUserId();
                break;
            case 10:
                return $this->getRecordVersion();
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
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['UserNotification'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['UserNotification'][$this->getPrimaryKey()] = true;
        $keys = UserNotificationPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getUserNotificationId(),
            $keys[1] => $this->getUserId(),
            $keys[2] => $this->getContext(),
            $keys[3] => $this->getTitle(),
            $keys[4] => $this->getNotification(),
            $keys[5] => $this->getNotificationData(),
            $keys[6] => $this->getCreationDate(),
            $keys[7] => $this->getUpdateDate(),
            $keys[8] => $this->getCreationUserId(),
            $keys[9] => $this->getUpdateUserId(),
            $keys[10] => $this->getRecordVersion(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aAccountRelatedByUserId) {
                $result['AccountRelatedByUserId'] = $this->aAccountRelatedByUserId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aAccountRelatedByCreationUserId) {
                $result['AccountRelatedByCreationUserId'] = $this->aAccountRelatedByCreationUserId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aAccountRelatedByUpdateUserId) {
                $result['AccountRelatedByUpdateUserId'] = $this->aAccountRelatedByUpdateUserId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
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
        $pos = UserNotificationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setUserNotificationId($value);
                break;
            case 1:
                $this->setUserId($value);
                break;
            case 2:
                $this->setContext($value);
                break;
            case 3:
                $this->setTitle($value);
                break;
            case 4:
                $this->setNotification($value);
                break;
            case 5:
                $this->setNotificationData($value);
                break;
            case 6:
                $this->setCreationDate($value);
                break;
            case 7:
                $this->setUpdateDate($value);
                break;
            case 8:
                $this->setCreationUserId($value);
                break;
            case 9:
                $this->setUpdateUserId($value);
                break;
            case 10:
                $this->setRecordVersion($value);
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
        $keys = UserNotificationPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setUserNotificationId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setUserId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setContext($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setTitle($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setNotification($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setNotificationData($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setCreationDate($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setUpdateDate($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setCreationUserId($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setUpdateUserId($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setRecordVersion($arr[$keys[10]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(UserNotificationPeer::DATABASE_NAME);

        if ($this->isColumnModified(UserNotificationPeer::USER_NOTIFICATION_ID)) $criteria->add(UserNotificationPeer::USER_NOTIFICATION_ID, $this->user_notification_id);
        if ($this->isColumnModified(UserNotificationPeer::USER_ID)) $criteria->add(UserNotificationPeer::USER_ID, $this->user_id);
        if ($this->isColumnModified(UserNotificationPeer::CONTEXT)) $criteria->add(UserNotificationPeer::CONTEXT, $this->context);
        if ($this->isColumnModified(UserNotificationPeer::TITLE)) $criteria->add(UserNotificationPeer::TITLE, $this->title);
        if ($this->isColumnModified(UserNotificationPeer::NOTIFICATION)) $criteria->add(UserNotificationPeer::NOTIFICATION, $this->notification);
        if ($this->isColumnModified(UserNotificationPeer::NOTIFICATION_DATA)) $criteria->add(UserNotificationPeer::NOTIFICATION_DATA, $this->notification_data);
        if ($this->isColumnModified(UserNotificationPeer::CREATION_DATE)) $criteria->add(UserNotificationPeer::CREATION_DATE, $this->creation_date);
        if ($this->isColumnModified(UserNotificationPeer::UPDATE_DATE)) $criteria->add(UserNotificationPeer::UPDATE_DATE, $this->update_date);
        if ($this->isColumnModified(UserNotificationPeer::CREATION_USER_ID)) $criteria->add(UserNotificationPeer::CREATION_USER_ID, $this->creation_user_id);
        if ($this->isColumnModified(UserNotificationPeer::UPDATE_USER_ID)) $criteria->add(UserNotificationPeer::UPDATE_USER_ID, $this->update_user_id);
        if ($this->isColumnModified(UserNotificationPeer::RECORD_VERSION)) $criteria->add(UserNotificationPeer::RECORD_VERSION, $this->record_version);

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
        $criteria = new Criteria(UserNotificationPeer::DATABASE_NAME);
        $criteria->add(UserNotificationPeer::USER_NOTIFICATION_ID, $this->user_notification_id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getUserNotificationId();
    }

    /**
     * Generic method to set the primary key (user_notification_id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setUserNotificationId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getUserNotificationId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of UserNotification (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setUserId($this->getUserId());
        $copyObj->setContext($this->getContext());
        $copyObj->setTitle($this->getTitle());
        $copyObj->setNotification($this->getNotification());
        $copyObj->setNotificationData($this->getNotificationData());
        $copyObj->setCreationDate($this->getCreationDate());
        $copyObj->setUpdateDate($this->getUpdateDate());
        $copyObj->setCreationUserId($this->getCreationUserId());
        $copyObj->setUpdateUserId($this->getUpdateUserId());
        $copyObj->setRecordVersion($this->getRecordVersion());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setUserNotificationId(NULL); // this is a auto-increment column, so set to default value
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
     * @return UserNotification Clone of current object.
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
     * @return UserNotificationPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new UserNotificationPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Account object.
     *
     * @param                  Account $v
     * @return UserNotification The current object (for fluent API support)
     * @throws PropelException
     */
    public function setAccountRelatedByUserId(Account $v = null)
    {
        if ($v === null) {
            $this->setUserId(NULL);
        } else {
            $this->setUserId($v->getAccountId());
        }

        $this->aAccountRelatedByUserId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Account object, it will not be re-added.
        if ($v !== null) {
            $v->addUserNotificationRelatedByUserId($this);
        }


        return $this;
    }


    /**
     * Get the associated Account object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Account The associated Account object.
     * @throws PropelException
     */
    public function getAccountRelatedByUserId(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aAccountRelatedByUserId === null && ($this->user_id !== null) && $doQuery) {
            $this->aAccountRelatedByUserId = AccountQuery::create()->findPk($this->user_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aAccountRelatedByUserId->addUserNotificationsRelatedByUserId($this);
             */
        }

        return $this->aAccountRelatedByUserId;
    }

    /**
     * Declares an association between this object and a Account object.
     *
     * @param                  Account $v
     * @return UserNotification The current object (for fluent API support)
     * @throws PropelException
     */
    public function setAccountRelatedByCreationUserId(Account $v = null)
    {
        if ($v === null) {
            $this->setCreationUserId(NULL);
        } else {
            $this->setCreationUserId($v->getAccountId());
        }

        $this->aAccountRelatedByCreationUserId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Account object, it will not be re-added.
        if ($v !== null) {
            $v->addUserNotificationRelatedByCreationUserId($this);
        }


        return $this;
    }


    /**
     * Get the associated Account object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Account The associated Account object.
     * @throws PropelException
     */
    public function getAccountRelatedByCreationUserId(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aAccountRelatedByCreationUserId === null && ($this->creation_user_id !== null) && $doQuery) {
            $this->aAccountRelatedByCreationUserId = AccountQuery::create()->findPk($this->creation_user_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aAccountRelatedByCreationUserId->addUserNotificationsRelatedByCreationUserId($this);
             */
        }

        return $this->aAccountRelatedByCreationUserId;
    }

    /**
     * Declares an association between this object and a Account object.
     *
     * @param                  Account $v
     * @return UserNotification The current object (for fluent API support)
     * @throws PropelException
     */
    public function setAccountRelatedByUpdateUserId(Account $v = null)
    {
        if ($v === null) {
            $this->setUpdateUserId(NULL);
        } else {
            $this->setUpdateUserId($v->getAccountId());
        }

        $this->aAccountRelatedByUpdateUserId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Account object, it will not be re-added.
        if ($v !== null) {
            $v->addUserNotificationRelatedByUpdateUserId($this);
        }


        return $this;
    }


    /**
     * Get the associated Account object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Account The associated Account object.
     * @throws PropelException
     */
    public function getAccountRelatedByUpdateUserId(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aAccountRelatedByUpdateUserId === null && ($this->update_user_id !== null) && $doQuery) {
            $this->aAccountRelatedByUpdateUserId = AccountQuery::create()->findPk($this->update_user_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aAccountRelatedByUpdateUserId->addUserNotificationsRelatedByUpdateUserId($this);
             */
        }

        return $this->aAccountRelatedByUpdateUserId;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->user_notification_id = null;
        $this->user_id = null;
        $this->context = null;
        $this->title = null;
        $this->notification = null;
        $this->notification_data = null;
        $this->creation_date = null;
        $this->update_date = null;
        $this->creation_user_id = null;
        $this->update_user_id = null;
        $this->record_version = null;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
        $this->alreadyInClearAllReferencesDeep = false;
        $this->clearAllReferences();
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
            if ($this->aAccountRelatedByUserId instanceof Persistent) {
              $this->aAccountRelatedByUserId->clearAllReferences($deep);
            }
            if ($this->aAccountRelatedByCreationUserId instanceof Persistent) {
              $this->aAccountRelatedByCreationUserId->clearAllReferences($deep);
            }
            if ($this->aAccountRelatedByUpdateUserId instanceof Persistent) {
              $this->aAccountRelatedByUpdateUserId->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        $this->aAccountRelatedByUserId = null;
        $this->aAccountRelatedByCreationUserId = null;
        $this->aAccountRelatedByUpdateUserId = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(UserNotificationPeer::DEFAULT_STRING_FORMAT);
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

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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\PendingCall;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\PendingCallPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\PendingCallQuery;
use Eulogix\Cool\Lib\Database\Propel\CoolPropelObject;

abstract class BasePendingCall extends CoolPropelObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\PendingCallPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        PendingCallPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the pending_call_id field.
     * @var        int
     */
    protected $pending_call_id;

    /**
     * The value for the sid field.
     * @var        string
     */
    protected $sid;

    /**
     * The value for the recording_url field.
     * @var        string
     */
    protected $recording_url;

    /**
     * The value for the client_sid field.
     * @var        string
     */
    protected $client_sid;

    /**
     * The value for the creation_date field.
     * @var        string
     */
    protected $creation_date;

    /**
     * The value for the caller_user_id field.
     * @var        int
     */
    protected $caller_user_id;

    /**
     * The value for the target field.
     * @var        string
     */
    protected $target;

    /**
     * The value for the serialized_call field.
     * @var        string
     */
    protected $serialized_call;

    /**
     * The value for the properties field.
     * @var        string
     */
    protected $properties;

    /**
     * @var        Account
     */
    protected $aAccount;

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
     * Get the [pending_call_id] column value.
     *
     * @return int
     */
    public function getPendingCallId()
    {

        return $this->pending_call_id;
    }

    /**
     * Get the [sid] column value.
     * twilio call SID
     * @return string
     */
    public function getSid()
    {

        return $this->sid;
    }

    /**
     * Get the [recording_url] column value.
     * twilio recording URL
     * @return string
     */
    public function getRecordingUrl()
    {

        return $this->recording_url;
    }

    /**
     * Get the [client_sid] column value.
     * session id of the client that initiated the call
     * @return string
     */
    public function getClientSid()
    {

        return $this->client_sid;
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
     * Get the [caller_user_id] column value.
     *
     * @return int
     */
    public function getCallerUserId()
    {

        return $this->caller_user_id;
    }

    /**
     * Get the [target] column value.
     *
     * @return string
     */
    public function getTarget()
    {

        return $this->target;
    }

    /**
     * Get the [serialized_call] column value.
     *
     * @return string
     */
    public function getSerializedCall()
    {

        return $this->serialized_call;
    }

    /**
     * Get the [properties] column value.
     *
     * @return string
     */
    public function getProperties()
    {

        return $this->properties;
    }

    /**
     * Set the value of [pending_call_id] column.
     *
     * @param  int $v new value
     * @return PendingCall The current object (for fluent API support)
     */
    public function setPendingCallId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->pending_call_id !== $v) {
            $this->pending_call_id = $v;
            $this->modifiedColumns[] = PendingCallPeer::PENDING_CALL_ID;
        }


        return $this;
    } // setPendingCallId()

    /**
     * Set the value of [sid] column.
     * twilio call SID
     * @param  string $v new value
     * @return PendingCall The current object (for fluent API support)
     */
    public function setSid($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->sid !== $v) {
            $this->sid = $v;
            $this->modifiedColumns[] = PendingCallPeer::SID;
        }


        return $this;
    } // setSid()

    /**
     * Set the value of [recording_url] column.
     * twilio recording URL
     * @param  string $v new value
     * @return PendingCall The current object (for fluent API support)
     */
    public function setRecordingUrl($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->recording_url !== $v) {
            $this->recording_url = $v;
            $this->modifiedColumns[] = PendingCallPeer::RECORDING_URL;
        }


        return $this;
    } // setRecordingUrl()

    /**
     * Set the value of [client_sid] column.
     * session id of the client that initiated the call
     * @param  string $v new value
     * @return PendingCall The current object (for fluent API support)
     */
    public function setClientSid($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->client_sid !== $v) {
            $this->client_sid = $v;
            $this->modifiedColumns[] = PendingCallPeer::CLIENT_SID;
        }


        return $this;
    } // setClientSid()

    /**
     * Sets the value of [creation_date] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return PendingCall The current object (for fluent API support)
     */
    public function setCreationDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->creation_date !== null || $dt !== null) {
            $currentDateAsString = ($this->creation_date !== null && $tmpDt = new DateTime($this->creation_date)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->creation_date = $newDateAsString;
                $this->modifiedColumns[] = PendingCallPeer::CREATION_DATE;
            }
        } // if either are not null


        return $this;
    } // setCreationDate()

    /**
     * Set the value of [caller_user_id] column.
     *
     * @param  int $v new value
     * @return PendingCall The current object (for fluent API support)
     */
    public function setCallerUserId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->caller_user_id !== $v) {
            $this->caller_user_id = $v;
            $this->modifiedColumns[] = PendingCallPeer::CALLER_USER_ID;
        }

        if ($this->aAccount !== null && $this->aAccount->getAccountId() !== $v) {
            $this->aAccount = null;
        }


        return $this;
    } // setCallerUserId()

    /**
     * Set the value of [target] column.
     *
     * @param  string $v new value
     * @return PendingCall The current object (for fluent API support)
     */
    public function setTarget($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->target !== $v) {
            $this->target = $v;
            $this->modifiedColumns[] = PendingCallPeer::TARGET;
        }


        return $this;
    } // setTarget()

    /**
     * Set the value of [serialized_call] column.
     *
     * @param  string $v new value
     * @return PendingCall The current object (for fluent API support)
     */
    public function setSerializedCall($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->serialized_call !== $v) {
            $this->serialized_call = $v;
            $this->modifiedColumns[] = PendingCallPeer::SERIALIZED_CALL;
        }


        return $this;
    } // setSerializedCall()

    /**
     * Set the value of [properties] column.
     *
     * @param  string $v new value
     * @return PendingCall The current object (for fluent API support)
     */
    public function setProperties($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->properties !== $v) {
            $this->properties = $v;
            $this->modifiedColumns[] = PendingCallPeer::PROPERTIES;
        }


        return $this;
    } // setProperties()

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

            $this->pending_call_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->sid = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->recording_url = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->client_sid = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->creation_date = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->caller_user_id = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
            $this->target = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->serialized_call = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->properties = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 9; // 9 = PendingCallPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating PendingCall object", $e);
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

        if ($this->aAccount !== null && $this->caller_user_id !== $this->aAccount->getAccountId()) {
            $this->aAccount = null;
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
            $con = Propel::getConnection(PendingCallPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = PendingCallPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aAccount = null;
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
            $con = Propel::getConnection(PendingCallPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = PendingCallQuery::create()
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
            $con = Propel::getConnection(PendingCallPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                PendingCallPeer::addInstanceToPool($this);
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

            if ($this->aAccount !== null) {
                if ($this->aAccount->isModified() || $this->aAccount->isNew()) {
                    $affectedRows += $this->aAccount->save($con);
                }
                $this->setAccount($this->aAccount);
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

        $this->modifiedColumns[] = PendingCallPeer::PENDING_CALL_ID;
        if (null !== $this->pending_call_id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . PendingCallPeer::PENDING_CALL_ID . ')');
        }
        if (null === $this->pending_call_id) {
            try {
                $stmt = $con->query("SELECT nextval('core.pending_call_pending_call_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->pending_call_id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(PendingCallPeer::PENDING_CALL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'pending_call_id';
        }
        if ($this->isColumnModified(PendingCallPeer::SID)) {
            $modifiedColumns[':p' . $index++]  = 'sid';
        }
        if ($this->isColumnModified(PendingCallPeer::RECORDING_URL)) {
            $modifiedColumns[':p' . $index++]  = 'recording_url';
        }
        if ($this->isColumnModified(PendingCallPeer::CLIENT_SID)) {
            $modifiedColumns[':p' . $index++]  = 'client_sid';
        }
        if ($this->isColumnModified(PendingCallPeer::CREATION_DATE)) {
            $modifiedColumns[':p' . $index++]  = 'creation_date';
        }
        if ($this->isColumnModified(PendingCallPeer::CALLER_USER_ID)) {
            $modifiedColumns[':p' . $index++]  = 'caller_user_id';
        }
        if ($this->isColumnModified(PendingCallPeer::TARGET)) {
            $modifiedColumns[':p' . $index++]  = 'target';
        }
        if ($this->isColumnModified(PendingCallPeer::SERIALIZED_CALL)) {
            $modifiedColumns[':p' . $index++]  = 'serialized_call';
        }
        if ($this->isColumnModified(PendingCallPeer::PROPERTIES)) {
            $modifiedColumns[':p' . $index++]  = 'properties';
        }

        $sql = sprintf(
            'INSERT INTO core.pending_call (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'pending_call_id':
                        $stmt->bindValue($identifier, $this->pending_call_id, PDO::PARAM_INT);
                        break;
                    case 'sid':
                        $stmt->bindValue($identifier, $this->sid, PDO::PARAM_STR);
                        break;
                    case 'recording_url':
                        $stmt->bindValue($identifier, $this->recording_url, PDO::PARAM_STR);
                        break;
                    case 'client_sid':
                        $stmt->bindValue($identifier, $this->client_sid, PDO::PARAM_STR);
                        break;
                    case 'creation_date':
                        $stmt->bindValue($identifier, $this->creation_date, PDO::PARAM_STR);
                        break;
                    case 'caller_user_id':
                        $stmt->bindValue($identifier, $this->caller_user_id, PDO::PARAM_INT);
                        break;
                    case 'target':
                        $stmt->bindValue($identifier, $this->target, PDO::PARAM_STR);
                        break;
                    case 'serialized_call':
                        $stmt->bindValue($identifier, $this->serialized_call, PDO::PARAM_STR);
                        break;
                    case 'properties':
                        $stmt->bindValue($identifier, $this->properties, PDO::PARAM_STR);
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

            if ($this->aAccount !== null) {
                if (!$this->aAccount->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aAccount->getValidationFailures());
                }
            }


            if (($retval = PendingCallPeer::doValidate($this, $columns)) !== true) {
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
        $pos = PendingCallPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getPendingCallId();
                break;
            case 1:
                return $this->getSid();
                break;
            case 2:
                return $this->getRecordingUrl();
                break;
            case 3:
                return $this->getClientSid();
                break;
            case 4:
                return $this->getCreationDate();
                break;
            case 5:
                return $this->getCallerUserId();
                break;
            case 6:
                return $this->getTarget();
                break;
            case 7:
                return $this->getSerializedCall();
                break;
            case 8:
                return $this->getProperties();
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
        if (isset($alreadyDumpedObjects['PendingCall'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['PendingCall'][$this->getPrimaryKey()] = true;
        $keys = PendingCallPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getPendingCallId(),
            $keys[1] => $this->getSid(),
            $keys[2] => $this->getRecordingUrl(),
            $keys[3] => $this->getClientSid(),
            $keys[4] => $this->getCreationDate(),
            $keys[5] => $this->getCallerUserId(),
            $keys[6] => $this->getTarget(),
            $keys[7] => $this->getSerializedCall(),
            $keys[8] => $this->getProperties(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aAccount) {
                $result['Account'] = $this->aAccount->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
        $pos = PendingCallPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setPendingCallId($value);
                break;
            case 1:
                $this->setSid($value);
                break;
            case 2:
                $this->setRecordingUrl($value);
                break;
            case 3:
                $this->setClientSid($value);
                break;
            case 4:
                $this->setCreationDate($value);
                break;
            case 5:
                $this->setCallerUserId($value);
                break;
            case 6:
                $this->setTarget($value);
                break;
            case 7:
                $this->setSerializedCall($value);
                break;
            case 8:
                $this->setProperties($value);
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
        $keys = PendingCallPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setPendingCallId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setSid($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setRecordingUrl($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setClientSid($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setCreationDate($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setCallerUserId($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setTarget($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setSerializedCall($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setProperties($arr[$keys[8]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(PendingCallPeer::DATABASE_NAME);

        if ($this->isColumnModified(PendingCallPeer::PENDING_CALL_ID)) $criteria->add(PendingCallPeer::PENDING_CALL_ID, $this->pending_call_id);
        if ($this->isColumnModified(PendingCallPeer::SID)) $criteria->add(PendingCallPeer::SID, $this->sid);
        if ($this->isColumnModified(PendingCallPeer::RECORDING_URL)) $criteria->add(PendingCallPeer::RECORDING_URL, $this->recording_url);
        if ($this->isColumnModified(PendingCallPeer::CLIENT_SID)) $criteria->add(PendingCallPeer::CLIENT_SID, $this->client_sid);
        if ($this->isColumnModified(PendingCallPeer::CREATION_DATE)) $criteria->add(PendingCallPeer::CREATION_DATE, $this->creation_date);
        if ($this->isColumnModified(PendingCallPeer::CALLER_USER_ID)) $criteria->add(PendingCallPeer::CALLER_USER_ID, $this->caller_user_id);
        if ($this->isColumnModified(PendingCallPeer::TARGET)) $criteria->add(PendingCallPeer::TARGET, $this->target);
        if ($this->isColumnModified(PendingCallPeer::SERIALIZED_CALL)) $criteria->add(PendingCallPeer::SERIALIZED_CALL, $this->serialized_call);
        if ($this->isColumnModified(PendingCallPeer::PROPERTIES)) $criteria->add(PendingCallPeer::PROPERTIES, $this->properties);

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
        $criteria = new Criteria(PendingCallPeer::DATABASE_NAME);
        $criteria->add(PendingCallPeer::PENDING_CALL_ID, $this->pending_call_id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getPendingCallId();
    }

    /**
     * Generic method to set the primary key (pending_call_id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setPendingCallId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getPendingCallId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of PendingCall (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setSid($this->getSid());
        $copyObj->setRecordingUrl($this->getRecordingUrl());
        $copyObj->setClientSid($this->getClientSid());
        $copyObj->setCreationDate($this->getCreationDate());
        $copyObj->setCallerUserId($this->getCallerUserId());
        $copyObj->setTarget($this->getTarget());
        $copyObj->setSerializedCall($this->getSerializedCall());
        $copyObj->setProperties($this->getProperties());

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
            $copyObj->setPendingCallId(NULL); // this is a auto-increment column, so set to default value
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
     * @return PendingCall Clone of current object.
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
     * @return PendingCallPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new PendingCallPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Account object.
     *
     * @param                  Account $v
     * @return PendingCall The current object (for fluent API support)
     * @throws PropelException
     */
    public function setAccount(Account $v = null)
    {
        if ($v === null) {
            $this->setCallerUserId(NULL);
        } else {
            $this->setCallerUserId($v->getAccountId());
        }

        $this->aAccount = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Account object, it will not be re-added.
        if ($v !== null) {
            $v->addPendingCall($this);
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
    public function getAccount(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aAccount === null && ($this->caller_user_id !== null) && $doQuery) {
            $this->aAccount = AccountQuery::create()->findPk($this->caller_user_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aAccount->addPendingCalls($this);
             */
        }

        return $this->aAccount;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->pending_call_id = null;
        $this->sid = null;
        $this->recording_url = null;
        $this->client_sid = null;
        $this->creation_date = null;
        $this->caller_user_id = null;
        $this->target = null;
        $this->serialized_call = null;
        $this->properties = null;
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
            if ($this->aAccount instanceof Persistent) {
              $this->aAccount->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        $this->aAccount = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(PendingCallPeer::DEFAULT_STRING_FORMAT);
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

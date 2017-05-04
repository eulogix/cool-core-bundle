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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\Translation;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\TranslationPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\TranslationQuery;
use Eulogix\Cool\Lib\Database\Propel\CoolPropelObject;

abstract class BaseTranslation extends CoolPropelObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\TranslationPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        TranslationPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the translation_id field.
     * @var        int
     */
    protected $translation_id;

    /**
     * The value for the domain_name field.
     * Note: this column has a database default value of: ''
     * @var        string
     */
    protected $domain_name;

    /**
     * The value for the locale field.
     * @var        string
     */
    protected $locale;

    /**
     * The value for the token field.
     * Note: this column has a database default value of: ''
     * @var        string
     */
    protected $token;

    /**
     * The value for the value field.
     * @var        string
     */
    protected $value;

    /**
     * The value for the used_flag field.
     * @var        boolean
     */
    protected $used_flag;

    /**
     * The value for the active_flag field.
     * @var        boolean
     */
    protected $active_flag;

    /**
     * The value for the expose_flag field.
     * @var        boolean
     */
    protected $expose_flag;

    /**
     * The value for the last_usage_date field.
     * @var        string
     */
    protected $last_usage_date;

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
        $this->domain_name = '';
        $this->token = '';
    }

    /**
     * Initializes internal state of BaseTranslation object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
    }

    /**
     * Get the [translation_id] column value.
     *
     * @return int
     */
    public function getTranslationId()
    {

        return $this->translation_id;
    }

    /**
     * Get the [domain_name] column value.
     *
     * @return string
     */
    public function getDomainName()
    {

        return $this->domain_name;
    }

    /**
     * Get the [locale] column value.
     *
     * @return string
     */
    public function getLocale()
    {

        return $this->locale;
    }

    /**
     * Get the [token] column value.
     *
     * @return string
     */
    public function getToken()
    {

        return $this->token;
    }

    /**
     * Get the [value] column value.
     *
     * @return string
     */
    public function getValue()
    {

        return $this->value;
    }

    /**
     * Get the [used_flag] column value.
     *
     * @return boolean
     */
    public function getUsedFlag()
    {

        return $this->used_flag;
    }

    /**
     * Get the [active_flag] column value.
     *
     * @return boolean
     */
    public function getActiveFlag()
    {

        return $this->active_flag;
    }

    /**
     * Get the [expose_flag] column value.
     *
     * @return boolean
     */
    public function getExposeFlag()
    {

        return $this->expose_flag;
    }

    /**
     * Get the [optionally formatted] temporal [last_usage_date] column value.
     * in debug mode, this column gets populated by the translator
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getLastUsageDate($format = null)
    {
        if ($this->last_usage_date === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->last_usage_date);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->last_usage_date, true), $x);
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
     * Set the value of [translation_id] column.
     *
     * @param  int $v new value
     * @return Translation The current object (for fluent API support)
     */
    public function setTranslationId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->translation_id !== $v) {
            $this->translation_id = $v;
            $this->modifiedColumns[] = TranslationPeer::TRANSLATION_ID;
        }


        return $this;
    } // setTranslationId()

    /**
     * Set the value of [domain_name] column.
     *
     * @param  string $v new value
     * @return Translation The current object (for fluent API support)
     */
    public function setDomainName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->domain_name !== $v) {
            $this->domain_name = $v;
            $this->modifiedColumns[] = TranslationPeer::DOMAIN_NAME;
        }


        return $this;
    } // setDomainName()

    /**
     * Set the value of [locale] column.
     *
     * @param  string $v new value
     * @return Translation The current object (for fluent API support)
     */
    public function setLocale($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->locale !== $v) {
            $this->locale = $v;
            $this->modifiedColumns[] = TranslationPeer::LOCALE;
        }


        return $this;
    } // setLocale()

    /**
     * Set the value of [token] column.
     *
     * @param  string $v new value
     * @return Translation The current object (for fluent API support)
     */
    public function setToken($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->token !== $v) {
            $this->token = $v;
            $this->modifiedColumns[] = TranslationPeer::TOKEN;
        }


        return $this;
    } // setToken()

    /**
     * Set the value of [value] column.
     *
     * @param  string $v new value
     * @return Translation The current object (for fluent API support)
     */
    public function setValue($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->value !== $v) {
            $this->value = $v;
            $this->modifiedColumns[] = TranslationPeer::VALUE;
        }


        return $this;
    } // setValue()

    /**
     * Sets the value of the [used_flag] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return Translation The current object (for fluent API support)
     */
    public function setUsedFlag($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->used_flag !== $v) {
            $this->used_flag = $v;
            $this->modifiedColumns[] = TranslationPeer::USED_FLAG;
        }


        return $this;
    } // setUsedFlag()

    /**
     * Sets the value of the [active_flag] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return Translation The current object (for fluent API support)
     */
    public function setActiveFlag($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->active_flag !== $v) {
            $this->active_flag = $v;
            $this->modifiedColumns[] = TranslationPeer::ACTIVE_FLAG;
        }


        return $this;
    } // setActiveFlag()

    /**
     * Sets the value of the [expose_flag] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return Translation The current object (for fluent API support)
     */
    public function setExposeFlag($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->expose_flag !== $v) {
            $this->expose_flag = $v;
            $this->modifiedColumns[] = TranslationPeer::EXPOSE_FLAG;
        }


        return $this;
    } // setExposeFlag()

    /**
     * Sets the value of [last_usage_date] column to a normalized version of the date/time value specified.
     * in debug mode, this column gets populated by the translator
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Translation The current object (for fluent API support)
     */
    public function setLastUsageDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->last_usage_date !== null || $dt !== null) {
            $currentDateAsString = ($this->last_usage_date !== null && $tmpDt = new DateTime($this->last_usage_date)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->last_usage_date = $newDateAsString;
                $this->modifiedColumns[] = TranslationPeer::LAST_USAGE_DATE;
            }
        } // if either are not null


        return $this;
    } // setLastUsageDate()

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
            if ($this->domain_name !== '') {
                return false;
            }

            if ($this->token !== '') {
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

            $this->translation_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->domain_name = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->locale = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->token = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->value = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->used_flag = ($row[$startcol + 5] !== null) ? (boolean) $row[$startcol + 5] : null;
            $this->active_flag = ($row[$startcol + 6] !== null) ? (boolean) $row[$startcol + 6] : null;
            $this->expose_flag = ($row[$startcol + 7] !== null) ? (boolean) $row[$startcol + 7] : null;
            $this->last_usage_date = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 9; // 9 = TranslationPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Translation object", $e);
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
            $con = Propel::getConnection(TranslationPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = TranslationPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

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
            $con = Propel::getConnection(TranslationPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = TranslationQuery::create()
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
            $con = Propel::getConnection(TranslationPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                TranslationPeer::addInstanceToPool($this);
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

        $this->modifiedColumns[] = TranslationPeer::TRANSLATION_ID;
        if (null !== $this->translation_id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . TranslationPeer::TRANSLATION_ID . ')');
        }
        if (null === $this->translation_id) {
            try {
                $stmt = $con->query("SELECT nextval('core.translation_translation_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->translation_id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(TranslationPeer::TRANSLATION_ID)) {
            $modifiedColumns[':p' . $index++]  = 'translation_id';
        }
        if ($this->isColumnModified(TranslationPeer::DOMAIN_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'domain_name';
        }
        if ($this->isColumnModified(TranslationPeer::LOCALE)) {
            $modifiedColumns[':p' . $index++]  = 'locale';
        }
        if ($this->isColumnModified(TranslationPeer::TOKEN)) {
            $modifiedColumns[':p' . $index++]  = 'token';
        }
        if ($this->isColumnModified(TranslationPeer::VALUE)) {
            $modifiedColumns[':p' . $index++]  = 'value';
        }
        if ($this->isColumnModified(TranslationPeer::USED_FLAG)) {
            $modifiedColumns[':p' . $index++]  = 'used_flag';
        }
        if ($this->isColumnModified(TranslationPeer::ACTIVE_FLAG)) {
            $modifiedColumns[':p' . $index++]  = 'active_flag';
        }
        if ($this->isColumnModified(TranslationPeer::EXPOSE_FLAG)) {
            $modifiedColumns[':p' . $index++]  = 'expose_flag';
        }
        if ($this->isColumnModified(TranslationPeer::LAST_USAGE_DATE)) {
            $modifiedColumns[':p' . $index++]  = 'last_usage_date';
        }

        $sql = sprintf(
            'INSERT INTO core.translation (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'translation_id':
                        $stmt->bindValue($identifier, $this->translation_id, PDO::PARAM_INT);
                        break;
                    case 'domain_name':
                        $stmt->bindValue($identifier, $this->domain_name, PDO::PARAM_STR);
                        break;
                    case 'locale':
                        $stmt->bindValue($identifier, $this->locale, PDO::PARAM_STR);
                        break;
                    case 'token':
                        $stmt->bindValue($identifier, $this->token, PDO::PARAM_STR);
                        break;
                    case 'value':
                        $stmt->bindValue($identifier, $this->value, PDO::PARAM_STR);
                        break;
                    case 'used_flag':
                        $stmt->bindValue($identifier, $this->used_flag, PDO::PARAM_BOOL);
                        break;
                    case 'active_flag':
                        $stmt->bindValue($identifier, $this->active_flag, PDO::PARAM_BOOL);
                        break;
                    case 'expose_flag':
                        $stmt->bindValue($identifier, $this->expose_flag, PDO::PARAM_BOOL);
                        break;
                    case 'last_usage_date':
                        $stmt->bindValue($identifier, $this->last_usage_date, PDO::PARAM_STR);
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


            if (($retval = TranslationPeer::doValidate($this, $columns)) !== true) {
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
        $pos = TranslationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getTranslationId();
                break;
            case 1:
                return $this->getDomainName();
                break;
            case 2:
                return $this->getLocale();
                break;
            case 3:
                return $this->getToken();
                break;
            case 4:
                return $this->getValue();
                break;
            case 5:
                return $this->getUsedFlag();
                break;
            case 6:
                return $this->getActiveFlag();
                break;
            case 7:
                return $this->getExposeFlag();
                break;
            case 8:
                return $this->getLastUsageDate();
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
        if (isset($alreadyDumpedObjects['Translation'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Translation'][$this->getPrimaryKey()] = true;
        $keys = TranslationPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getTranslationId(),
            $keys[1] => $this->getDomainName(),
            $keys[2] => $this->getLocale(),
            $keys[3] => $this->getToken(),
            $keys[4] => $this->getValue(),
            $keys[5] => $this->getUsedFlag(),
            $keys[6] => $this->getActiveFlag(),
            $keys[7] => $this->getExposeFlag(),
            $keys[8] => $this->getLastUsageDate(),
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
        $pos = TranslationPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setTranslationId($value);
                break;
            case 1:
                $this->setDomainName($value);
                break;
            case 2:
                $this->setLocale($value);
                break;
            case 3:
                $this->setToken($value);
                break;
            case 4:
                $this->setValue($value);
                break;
            case 5:
                $this->setUsedFlag($value);
                break;
            case 6:
                $this->setActiveFlag($value);
                break;
            case 7:
                $this->setExposeFlag($value);
                break;
            case 8:
                $this->setLastUsageDate($value);
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
        $keys = TranslationPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setTranslationId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setDomainName($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setLocale($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setToken($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setValue($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setUsedFlag($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setActiveFlag($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setExposeFlag($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setLastUsageDate($arr[$keys[8]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(TranslationPeer::DATABASE_NAME);

        if ($this->isColumnModified(TranslationPeer::TRANSLATION_ID)) $criteria->add(TranslationPeer::TRANSLATION_ID, $this->translation_id);
        if ($this->isColumnModified(TranslationPeer::DOMAIN_NAME)) $criteria->add(TranslationPeer::DOMAIN_NAME, $this->domain_name);
        if ($this->isColumnModified(TranslationPeer::LOCALE)) $criteria->add(TranslationPeer::LOCALE, $this->locale);
        if ($this->isColumnModified(TranslationPeer::TOKEN)) $criteria->add(TranslationPeer::TOKEN, $this->token);
        if ($this->isColumnModified(TranslationPeer::VALUE)) $criteria->add(TranslationPeer::VALUE, $this->value);
        if ($this->isColumnModified(TranslationPeer::USED_FLAG)) $criteria->add(TranslationPeer::USED_FLAG, $this->used_flag);
        if ($this->isColumnModified(TranslationPeer::ACTIVE_FLAG)) $criteria->add(TranslationPeer::ACTIVE_FLAG, $this->active_flag);
        if ($this->isColumnModified(TranslationPeer::EXPOSE_FLAG)) $criteria->add(TranslationPeer::EXPOSE_FLAG, $this->expose_flag);
        if ($this->isColumnModified(TranslationPeer::LAST_USAGE_DATE)) $criteria->add(TranslationPeer::LAST_USAGE_DATE, $this->last_usage_date);

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
        $criteria = new Criteria(TranslationPeer::DATABASE_NAME);
        $criteria->add(TranslationPeer::TRANSLATION_ID, $this->translation_id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getTranslationId();
    }

    /**
     * Generic method to set the primary key (translation_id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setTranslationId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getTranslationId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of Translation (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setDomainName($this->getDomainName());
        $copyObj->setLocale($this->getLocale());
        $copyObj->setToken($this->getToken());
        $copyObj->setValue($this->getValue());
        $copyObj->setUsedFlag($this->getUsedFlag());
        $copyObj->setActiveFlag($this->getActiveFlag());
        $copyObj->setExposeFlag($this->getExposeFlag());
        $copyObj->setLastUsageDate($this->getLastUsageDate());
        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setTranslationId(NULL); // this is a auto-increment column, so set to default value
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
     * @return Translation Clone of current object.
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
     * @return TranslationPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new TranslationPeer();
        }

        return self::$peer;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->translation_id = null;
        $this->domain_name = null;
        $this->locale = null;
        $this->token = null;
        $this->value = null;
        $this->used_flag = null;
        $this->active_flag = null;
        $this->expose_flag = null;
        $this->last_usage_date = null;
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
        return (string) $this->exportTo(TranslationPeer::DEFAULT_STRING_FORMAT);
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

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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AsyncJob;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AsyncJobPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AsyncJobQuery;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Database\Propel\CoolPropelObject;

abstract class BaseAsyncJob extends CoolPropelObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AsyncJobPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        AsyncJobPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the async_job_id field.
     * @var        int
     */
    protected $async_job_id;

    /**
     * The value for the issuer_user_id field.
     * @var        int
     */
    protected $issuer_user_id;

    /**
     * The value for the context field.
     * @var        string
     */
    protected $context;

    /**
     * The value for the executor_type field.
     * @var        string
     */
    protected $executor_type;

    /**
     * The value for the execution_id field.
     * @var        string
     */
    protected $execution_id;

    /**
     * The value for the job_path field.
     * @var        string
     */
    protected $job_path;

    /**
     * The value for the parameters field.
     * @var        string
     */
    protected $parameters;

    /**
     * The value for the start_date field.
     * @var        string
     */
    protected $start_date;

    /**
     * The value for the completion_date field.
     * @var        string
     */
    protected $completion_date;

    /**
     * The value for the completion_percentage field.
     * @var        int
     */
    protected $completion_percentage;

    /**
     * The value for the outcome field.
     * @var        string
     */
    protected $outcome;

    /**
     * The value for the job_output field.
     * @var        string
     */
    protected $job_output;

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
    protected $aAccountRelatedByIssuerUserId;

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
     * Get the [async_job_id] column value.
     *
     * @return int
     */
    public function getAsyncJobId()
    {

        return $this->async_job_id;
    }

    /**
     * Get the [issuer_user_id] column value.
     * the user who started the job, requesting the operation
     * @return int
     */
    public function getIssuerUserId()
    {

        return $this->issuer_user_id;
    }

    /**
     * Get the [context] column value.
     * used to group jobs and to only show them in specific contexts (such as projects)
     * @return string
     */
    public function getContext()
    {

        return $this->context;
    }

    /**
     * Get the [executor_type] column value.
     *
     * @return string
     */
    public function getExecutorType()
    {

        return $this->executor_type;
    }

    /**
     * Get the [execution_id] column value.
     * may contain a reference to the execution instance of the executor
     * @return string
     */
    public function getExecutionId()
    {

        return $this->execution_id;
    }

    /**
     * Get the [job_path] column value.
     * string pointer to the job
     * @return string
     */
    public function getJobPath()
    {

        return $this->job_path;
    }

    /**
     * Get the [parameters] column value.
     * generic container for all the parameters that must be passed to the job
     * @return string
     */
    public function getParameters()
    {

        return $this->parameters;
    }

    /**
     * Get the [optionally formatted] temporal [start_date] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getStartDate($format = null)
    {
        if ($this->start_date === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->start_date);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->start_date, true), $x);
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
     * Get the [optionally formatted] temporal [completion_date] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCompletionDate($format = null)
    {
        if ($this->completion_date === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->completion_date);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->completion_date, true), $x);
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
     * Get the [completion_percentage] column value.
     *
     * @return int
     */
    public function getCompletionPercentage()
    {

        return $this->completion_percentage;
    }

    /**
     * Get the [outcome] column value.
     *
     * @return string
     */
    public function getOutcome()
    {

        return $this->outcome;
    }

    /**
     * Get the [job_output] column value.
     * container for the ouptut of the job, typically links to generated objects/files
     * @return string
     */
    public function getJobOutput()
    {

        return $this->job_output;
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
     * Set the value of [async_job_id] column.
     *
     * @param  int $v new value
     * @return AsyncJob The current object (for fluent API support)
     */
    public function setAsyncJobId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->async_job_id !== $v) {
            $this->async_job_id = $v;
            $this->modifiedColumns[] = AsyncJobPeer::ASYNC_JOB_ID;
        }


        return $this;
    } // setAsyncJobId()

    /**
     * Set the value of [issuer_user_id] column.
     * the user who started the job, requesting the operation
     * @param  int $v new value
     * @return AsyncJob The current object (for fluent API support)
     */
    public function setIssuerUserId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->issuer_user_id !== $v) {
            $this->issuer_user_id = $v;
            $this->modifiedColumns[] = AsyncJobPeer::ISSUER_USER_ID;
        }

        if ($this->aAccountRelatedByIssuerUserId !== null && $this->aAccountRelatedByIssuerUserId->getAccountId() !== $v) {
            $this->aAccountRelatedByIssuerUserId = null;
        }


        return $this;
    } // setIssuerUserId()

    /**
     * Set the value of [context] column.
     * used to group jobs and to only show them in specific contexts (such as projects)
     * @param  string $v new value
     * @return AsyncJob The current object (for fluent API support)
     */
    public function setContext($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->context !== $v) {
            $this->context = $v;
            $this->modifiedColumns[] = AsyncJobPeer::CONTEXT;
        }


        return $this;
    } // setContext()

    /**
     * Set the value of [executor_type] column.
     *
     * @param  string $v new value
     * @return AsyncJob The current object (for fluent API support)
     */
    public function setExecutorType($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->executor_type !== $v) {
            $this->executor_type = $v;
            $this->modifiedColumns[] = AsyncJobPeer::EXECUTOR_TYPE;
        }


        return $this;
    } // setExecutorType()

    /**
     * Set the value of [execution_id] column.
     * may contain a reference to the execution instance of the executor
     * @param  string $v new value
     * @return AsyncJob The current object (for fluent API support)
     */
    public function setExecutionId($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->execution_id !== $v) {
            $this->execution_id = $v;
            $this->modifiedColumns[] = AsyncJobPeer::EXECUTION_ID;
        }


        return $this;
    } // setExecutionId()

    /**
     * Set the value of [job_path] column.
     * string pointer to the job
     * @param  string $v new value
     * @return AsyncJob The current object (for fluent API support)
     */
    public function setJobPath($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->job_path !== $v) {
            $this->job_path = $v;
            $this->modifiedColumns[] = AsyncJobPeer::JOB_PATH;
        }


        return $this;
    } // setJobPath()

    /**
     * Set the value of [parameters] column.
     * generic container for all the parameters that must be passed to the job
     * @param  string $v new value
     * @return AsyncJob The current object (for fluent API support)
     */
    public function setParameters($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->parameters !== $v) {
            $this->parameters = $v;
            $this->modifiedColumns[] = AsyncJobPeer::PARAMETERS;
        }


        return $this;
    } // setParameters()

    /**
     * Sets the value of [start_date] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return AsyncJob The current object (for fluent API support)
     */
    public function setStartDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->start_date !== null || $dt !== null) {
            $currentDateAsString = ($this->start_date !== null && $tmpDt = new DateTime($this->start_date)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->start_date = $newDateAsString;
                $this->modifiedColumns[] = AsyncJobPeer::START_DATE;
            }
        } // if either are not null


        return $this;
    } // setStartDate()

    /**
     * Sets the value of [completion_date] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return AsyncJob The current object (for fluent API support)
     */
    public function setCompletionDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->completion_date !== null || $dt !== null) {
            $currentDateAsString = ($this->completion_date !== null && $tmpDt = new DateTime($this->completion_date)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->completion_date = $newDateAsString;
                $this->modifiedColumns[] = AsyncJobPeer::COMPLETION_DATE;
            }
        } // if either are not null


        return $this;
    } // setCompletionDate()

    /**
     * Set the value of [completion_percentage] column.
     *
     * @param  int $v new value
     * @return AsyncJob The current object (for fluent API support)
     */
    public function setCompletionPercentage($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->completion_percentage !== $v) {
            $this->completion_percentage = $v;
            $this->modifiedColumns[] = AsyncJobPeer::COMPLETION_PERCENTAGE;
        }


        return $this;
    } // setCompletionPercentage()

    /**
     * Set the value of [outcome] column.
     *
     * @param  string $v new value
     * @return AsyncJob The current object (for fluent API support)
     */
    public function setOutcome($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->outcome !== $v) {
            $this->outcome = $v;
            $this->modifiedColumns[] = AsyncJobPeer::OUTCOME;
        }


        return $this;
    } // setOutcome()

    /**
     * Set the value of [job_output] column.
     * container for the ouptut of the job, typically links to generated objects/files
     * @param  string $v new value
     * @return AsyncJob The current object (for fluent API support)
     */
    public function setJobOutput($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->job_output !== $v) {
            $this->job_output = $v;
            $this->modifiedColumns[] = AsyncJobPeer::JOB_OUTPUT;
        }


        return $this;
    } // setJobOutput()

    /**
     * Sets the value of [creation_date] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return AsyncJob The current object (for fluent API support)
     */
    public function setCreationDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->creation_date !== null || $dt !== null) {
            $currentDateAsString = ($this->creation_date !== null && $tmpDt = new DateTime($this->creation_date)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->creation_date = $newDateAsString;
                $this->modifiedColumns[] = AsyncJobPeer::CREATION_DATE;
            }
        } // if either are not null


        return $this;
    } // setCreationDate()

    /**
     * Sets the value of [update_date] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return AsyncJob The current object (for fluent API support)
     */
    public function setUpdateDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->update_date !== null || $dt !== null) {
            $currentDateAsString = ($this->update_date !== null && $tmpDt = new DateTime($this->update_date)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->update_date = $newDateAsString;
                $this->modifiedColumns[] = AsyncJobPeer::UPDATE_DATE;
            }
        } // if either are not null


        return $this;
    } // setUpdateDate()

    /**
     * Set the value of [creation_user_id] column.
     *
     * @param  int $v new value
     * @return AsyncJob The current object (for fluent API support)
     */
    public function setCreationUserId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->creation_user_id !== $v) {
            $this->creation_user_id = $v;
            $this->modifiedColumns[] = AsyncJobPeer::CREATION_USER_ID;
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
     * @return AsyncJob The current object (for fluent API support)
     */
    public function setUpdateUserId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->update_user_id !== $v) {
            $this->update_user_id = $v;
            $this->modifiedColumns[] = AsyncJobPeer::UPDATE_USER_ID;
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
     * @return AsyncJob The current object (for fluent API support)
     */
    public function setRecordVersion($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->record_version !== $v) {
            $this->record_version = $v;
            $this->modifiedColumns[] = AsyncJobPeer::RECORD_VERSION;
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

            $this->async_job_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->issuer_user_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->context = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->executor_type = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->execution_id = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->job_path = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->parameters = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->start_date = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->completion_date = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->completion_percentage = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
            $this->outcome = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
            $this->job_output = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
            $this->creation_date = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
            $this->update_date = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
            $this->creation_user_id = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
            $this->update_user_id = ($row[$startcol + 15] !== null) ? (int) $row[$startcol + 15] : null;
            $this->record_version = ($row[$startcol + 16] !== null) ? (int) $row[$startcol + 16] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 17; // 17 = AsyncJobPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating AsyncJob object", $e);
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

        if ($this->aAccountRelatedByIssuerUserId !== null && $this->issuer_user_id !== $this->aAccountRelatedByIssuerUserId->getAccountId()) {
            $this->aAccountRelatedByIssuerUserId = null;
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
            $con = Propel::getConnection(AsyncJobPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = AsyncJobPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aAccountRelatedByIssuerUserId = null;
            $this->aAccountRelatedByCreationUserId = null;
            $this->aAccountRelatedByUpdateUserId = null;
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
            $con = Propel::getConnection(AsyncJobPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = AsyncJobQuery::create()
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
            $con = Propel::getConnection(AsyncJobPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                AsyncJobPeer::addInstanceToPool($this);
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

            if ($this->aAccountRelatedByIssuerUserId !== null) {
                if ($this->aAccountRelatedByIssuerUserId->isModified() || $this->aAccountRelatedByIssuerUserId->isNew()) {
                    $affectedRows += $this->aAccountRelatedByIssuerUserId->save($con);
                }
                $this->setAccountRelatedByIssuerUserId($this->aAccountRelatedByIssuerUserId);
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

        $this->modifiedColumns[] = AsyncJobPeer::ASYNC_JOB_ID;
        if (null !== $this->async_job_id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . AsyncJobPeer::ASYNC_JOB_ID . ')');
        }
        if (null === $this->async_job_id) {
            try {
                $stmt = $con->query("SELECT nextval('core.async_job_async_job_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->async_job_id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(AsyncJobPeer::ASYNC_JOB_ID)) {
            $modifiedColumns[':p' . $index++]  = 'async_job_id';
        }
        if ($this->isColumnModified(AsyncJobPeer::ISSUER_USER_ID)) {
            $modifiedColumns[':p' . $index++]  = 'issuer_user_id';
        }
        if ($this->isColumnModified(AsyncJobPeer::CONTEXT)) {
            $modifiedColumns[':p' . $index++]  = 'context';
        }
        if ($this->isColumnModified(AsyncJobPeer::EXECUTOR_TYPE)) {
            $modifiedColumns[':p' . $index++]  = 'executor_type';
        }
        if ($this->isColumnModified(AsyncJobPeer::EXECUTION_ID)) {
            $modifiedColumns[':p' . $index++]  = 'execution_id';
        }
        if ($this->isColumnModified(AsyncJobPeer::JOB_PATH)) {
            $modifiedColumns[':p' . $index++]  = 'job_path';
        }
        if ($this->isColumnModified(AsyncJobPeer::PARAMETERS)) {
            $modifiedColumns[':p' . $index++]  = 'parameters';
        }
        if ($this->isColumnModified(AsyncJobPeer::START_DATE)) {
            $modifiedColumns[':p' . $index++]  = 'start_date';
        }
        if ($this->isColumnModified(AsyncJobPeer::COMPLETION_DATE)) {
            $modifiedColumns[':p' . $index++]  = 'completion_date';
        }
        if ($this->isColumnModified(AsyncJobPeer::COMPLETION_PERCENTAGE)) {
            $modifiedColumns[':p' . $index++]  = 'completion_percentage';
        }
        if ($this->isColumnModified(AsyncJobPeer::OUTCOME)) {
            $modifiedColumns[':p' . $index++]  = 'outcome';
        }
        if ($this->isColumnModified(AsyncJobPeer::JOB_OUTPUT)) {
            $modifiedColumns[':p' . $index++]  = 'job_output';
        }
        if ($this->isColumnModified(AsyncJobPeer::CREATION_DATE)) {
            $modifiedColumns[':p' . $index++]  = 'creation_date';
        }
        if ($this->isColumnModified(AsyncJobPeer::UPDATE_DATE)) {
            $modifiedColumns[':p' . $index++]  = 'update_date';
        }
        if ($this->isColumnModified(AsyncJobPeer::CREATION_USER_ID)) {
            $modifiedColumns[':p' . $index++]  = 'creation_user_id';
        }
        if ($this->isColumnModified(AsyncJobPeer::UPDATE_USER_ID)) {
            $modifiedColumns[':p' . $index++]  = 'update_user_id';
        }
        if ($this->isColumnModified(AsyncJobPeer::RECORD_VERSION)) {
            $modifiedColumns[':p' . $index++]  = 'record_version';
        }

        $sql = sprintf(
            'INSERT INTO core.async_job (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'async_job_id':
                        $stmt->bindValue($identifier, $this->async_job_id, PDO::PARAM_INT);
                        break;
                    case 'issuer_user_id':
                        $stmt->bindValue($identifier, $this->issuer_user_id, PDO::PARAM_INT);
                        break;
                    case 'context':
                        $stmt->bindValue($identifier, $this->context, PDO::PARAM_STR);
                        break;
                    case 'executor_type':
                        $stmt->bindValue($identifier, $this->executor_type, PDO::PARAM_STR);
                        break;
                    case 'execution_id':
                        $stmt->bindValue($identifier, $this->execution_id, PDO::PARAM_STR);
                        break;
                    case 'job_path':
                        $stmt->bindValue($identifier, $this->job_path, PDO::PARAM_STR);
                        break;
                    case 'parameters':
                        $stmt->bindValue($identifier, $this->parameters, PDO::PARAM_STR);
                        break;
                    case 'start_date':
                        $stmt->bindValue($identifier, $this->start_date, PDO::PARAM_STR);
                        break;
                    case 'completion_date':
                        $stmt->bindValue($identifier, $this->completion_date, PDO::PARAM_STR);
                        break;
                    case 'completion_percentage':
                        $stmt->bindValue($identifier, $this->completion_percentage, PDO::PARAM_INT);
                        break;
                    case 'outcome':
                        $stmt->bindValue($identifier, $this->outcome, PDO::PARAM_STR);
                        break;
                    case 'job_output':
                        $stmt->bindValue($identifier, $this->job_output, PDO::PARAM_STR);
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

            if ($this->aAccountRelatedByIssuerUserId !== null) {
                if (!$this->aAccountRelatedByIssuerUserId->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aAccountRelatedByIssuerUserId->getValidationFailures());
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


            if (($retval = AsyncJobPeer::doValidate($this, $columns)) !== true) {
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
        $pos = AsyncJobPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getAsyncJobId();
                break;
            case 1:
                return $this->getIssuerUserId();
                break;
            case 2:
                return $this->getContext();
                break;
            case 3:
                return $this->getExecutorType();
                break;
            case 4:
                return $this->getExecutionId();
                break;
            case 5:
                return $this->getJobPath();
                break;
            case 6:
                return $this->getParameters();
                break;
            case 7:
                return $this->getStartDate();
                break;
            case 8:
                return $this->getCompletionDate();
                break;
            case 9:
                return $this->getCompletionPercentage();
                break;
            case 10:
                return $this->getOutcome();
                break;
            case 11:
                return $this->getJobOutput();
                break;
            case 12:
                return $this->getCreationDate();
                break;
            case 13:
                return $this->getUpdateDate();
                break;
            case 14:
                return $this->getCreationUserId();
                break;
            case 15:
                return $this->getUpdateUserId();
                break;
            case 16:
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
        if (isset($alreadyDumpedObjects['AsyncJob'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['AsyncJob'][$this->getPrimaryKey()] = true;
        $keys = AsyncJobPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getAsyncJobId(),
            $keys[1] => $this->getIssuerUserId(),
            $keys[2] => $this->getContext(),
            $keys[3] => $this->getExecutorType(),
            $keys[4] => $this->getExecutionId(),
            $keys[5] => $this->getJobPath(),
            $keys[6] => $this->getParameters(),
            $keys[7] => $this->getStartDate(),
            $keys[8] => $this->getCompletionDate(),
            $keys[9] => $this->getCompletionPercentage(),
            $keys[10] => $this->getOutcome(),
            $keys[11] => $this->getJobOutput(),
            $keys[12] => $this->getCreationDate(),
            $keys[13] => $this->getUpdateDate(),
            $keys[14] => $this->getCreationUserId(),
            $keys[15] => $this->getUpdateUserId(),
            $keys[16] => $this->getRecordVersion(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aAccountRelatedByIssuerUserId) {
                $result['AccountRelatedByIssuerUserId'] = $this->aAccountRelatedByIssuerUserId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
        $pos = AsyncJobPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setAsyncJobId($value);
                break;
            case 1:
                $this->setIssuerUserId($value);
                break;
            case 2:
                $this->setContext($value);
                break;
            case 3:
                $this->setExecutorType($value);
                break;
            case 4:
                $this->setExecutionId($value);
                break;
            case 5:
                $this->setJobPath($value);
                break;
            case 6:
                $this->setParameters($value);
                break;
            case 7:
                $this->setStartDate($value);
                break;
            case 8:
                $this->setCompletionDate($value);
                break;
            case 9:
                $this->setCompletionPercentage($value);
                break;
            case 10:
                $this->setOutcome($value);
                break;
            case 11:
                $this->setJobOutput($value);
                break;
            case 12:
                $this->setCreationDate($value);
                break;
            case 13:
                $this->setUpdateDate($value);
                break;
            case 14:
                $this->setCreationUserId($value);
                break;
            case 15:
                $this->setUpdateUserId($value);
                break;
            case 16:
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
        $keys = AsyncJobPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setAsyncJobId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setIssuerUserId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setContext($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setExecutorType($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setExecutionId($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setJobPath($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setParameters($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setStartDate($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setCompletionDate($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setCompletionPercentage($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setOutcome($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setJobOutput($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setCreationDate($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setUpdateDate($arr[$keys[13]]);
        if (array_key_exists($keys[14], $arr)) $this->setCreationUserId($arr[$keys[14]]);
        if (array_key_exists($keys[15], $arr)) $this->setUpdateUserId($arr[$keys[15]]);
        if (array_key_exists($keys[16], $arr)) $this->setRecordVersion($arr[$keys[16]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(AsyncJobPeer::DATABASE_NAME);

        if ($this->isColumnModified(AsyncJobPeer::ASYNC_JOB_ID)) $criteria->add(AsyncJobPeer::ASYNC_JOB_ID, $this->async_job_id);
        if ($this->isColumnModified(AsyncJobPeer::ISSUER_USER_ID)) $criteria->add(AsyncJobPeer::ISSUER_USER_ID, $this->issuer_user_id);
        if ($this->isColumnModified(AsyncJobPeer::CONTEXT)) $criteria->add(AsyncJobPeer::CONTEXT, $this->context);
        if ($this->isColumnModified(AsyncJobPeer::EXECUTOR_TYPE)) $criteria->add(AsyncJobPeer::EXECUTOR_TYPE, $this->executor_type);
        if ($this->isColumnModified(AsyncJobPeer::EXECUTION_ID)) $criteria->add(AsyncJobPeer::EXECUTION_ID, $this->execution_id);
        if ($this->isColumnModified(AsyncJobPeer::JOB_PATH)) $criteria->add(AsyncJobPeer::JOB_PATH, $this->job_path);
        if ($this->isColumnModified(AsyncJobPeer::PARAMETERS)) $criteria->add(AsyncJobPeer::PARAMETERS, $this->parameters);
        if ($this->isColumnModified(AsyncJobPeer::START_DATE)) $criteria->add(AsyncJobPeer::START_DATE, $this->start_date);
        if ($this->isColumnModified(AsyncJobPeer::COMPLETION_DATE)) $criteria->add(AsyncJobPeer::COMPLETION_DATE, $this->completion_date);
        if ($this->isColumnModified(AsyncJobPeer::COMPLETION_PERCENTAGE)) $criteria->add(AsyncJobPeer::COMPLETION_PERCENTAGE, $this->completion_percentage);
        if ($this->isColumnModified(AsyncJobPeer::OUTCOME)) $criteria->add(AsyncJobPeer::OUTCOME, $this->outcome);
        if ($this->isColumnModified(AsyncJobPeer::JOB_OUTPUT)) $criteria->add(AsyncJobPeer::JOB_OUTPUT, $this->job_output);
        if ($this->isColumnModified(AsyncJobPeer::CREATION_DATE)) $criteria->add(AsyncJobPeer::CREATION_DATE, $this->creation_date);
        if ($this->isColumnModified(AsyncJobPeer::UPDATE_DATE)) $criteria->add(AsyncJobPeer::UPDATE_DATE, $this->update_date);
        if ($this->isColumnModified(AsyncJobPeer::CREATION_USER_ID)) $criteria->add(AsyncJobPeer::CREATION_USER_ID, $this->creation_user_id);
        if ($this->isColumnModified(AsyncJobPeer::UPDATE_USER_ID)) $criteria->add(AsyncJobPeer::UPDATE_USER_ID, $this->update_user_id);
        if ($this->isColumnModified(AsyncJobPeer::RECORD_VERSION)) $criteria->add(AsyncJobPeer::RECORD_VERSION, $this->record_version);

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
        $criteria = new Criteria(AsyncJobPeer::DATABASE_NAME);
        $criteria->add(AsyncJobPeer::ASYNC_JOB_ID, $this->async_job_id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getAsyncJobId();
    }

    /**
     * Generic method to set the primary key (async_job_id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setAsyncJobId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getAsyncJobId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of AsyncJob (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setIssuerUserId($this->getIssuerUserId());
        $copyObj->setContext($this->getContext());
        $copyObj->setExecutorType($this->getExecutorType());
        $copyObj->setExecutionId($this->getExecutionId());
        $copyObj->setJobPath($this->getJobPath());
        $copyObj->setParameters($this->getParameters());
        $copyObj->setStartDate($this->getStartDate());
        $copyObj->setCompletionDate($this->getCompletionDate());
        $copyObj->setCompletionPercentage($this->getCompletionPercentage());
        $copyObj->setOutcome($this->getOutcome());
        $copyObj->setJobOutput($this->getJobOutput());
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
            $copyObj->setAsyncJobId(NULL); // this is a auto-increment column, so set to default value
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
     * @return AsyncJob Clone of current object.
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
     * @return AsyncJobPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new AsyncJobPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Account object.
     *
     * @param                  Account $v
     * @return AsyncJob The current object (for fluent API support)
     * @throws PropelException
     */
    public function setAccountRelatedByIssuerUserId(Account $v = null)
    {
        if ($v === null) {
            $this->setIssuerUserId(NULL);
        } else {
            $this->setIssuerUserId($v->getAccountId());
        }

        $this->aAccountRelatedByIssuerUserId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Account object, it will not be re-added.
        if ($v !== null) {
            $v->addAsyncJobRelatedByIssuerUserId($this);
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
    public function getAccountRelatedByIssuerUserId(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aAccountRelatedByIssuerUserId === null && ($this->issuer_user_id !== null) && $doQuery) {
            $this->aAccountRelatedByIssuerUserId = AccountQuery::create()->findPk($this->issuer_user_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aAccountRelatedByIssuerUserId->addAsyncJobsRelatedByIssuerUserId($this);
             */
        }

        return $this->aAccountRelatedByIssuerUserId;
    }

    /**
     * Declares an association between this object and a Account object.
     *
     * @param                  Account $v
     * @return AsyncJob The current object (for fluent API support)
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
            $v->addAsyncJobRelatedByCreationUserId($this);
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
                $this->aAccountRelatedByCreationUserId->addAsyncJobsRelatedByCreationUserId($this);
             */
        }

        return $this->aAccountRelatedByCreationUserId;
    }

    /**
     * Declares an association between this object and a Account object.
     *
     * @param                  Account $v
     * @return AsyncJob The current object (for fluent API support)
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
            $v->addAsyncJobRelatedByUpdateUserId($this);
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
                $this->aAccountRelatedByUpdateUserId->addAsyncJobsRelatedByUpdateUserId($this);
             */
        }

        return $this->aAccountRelatedByUpdateUserId;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->async_job_id = null;
        $this->issuer_user_id = null;
        $this->context = null;
        $this->executor_type = null;
        $this->execution_id = null;
        $this->job_path = null;
        $this->parameters = null;
        $this->start_date = null;
        $this->completion_date = null;
        $this->completion_percentage = null;
        $this->outcome = null;
        $this->job_output = null;
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
            if ($this->aAccountRelatedByIssuerUserId instanceof Persistent) {
              $this->aAccountRelatedByIssuerUserId->clearAllReferences($deep);
            }
            if ($this->aAccountRelatedByCreationUserId instanceof Persistent) {
              $this->aAccountRelatedByCreationUserId->clearAllReferences($deep);
            }
            if ($this->aAccountRelatedByUpdateUserId instanceof Persistent) {
              $this->aAccountRelatedByUpdateUserId->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        $this->aAccountRelatedByIssuerUserId = null;
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
        return (string) $this->exportTo(AsyncJobPeer::DEFAULT_STRING_FORMAT);
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

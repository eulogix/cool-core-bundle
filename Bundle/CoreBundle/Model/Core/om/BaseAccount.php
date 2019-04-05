<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core\om;

use \BasePeer;
use \Criteria;
use \DateTime;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelCollection;
use \PropelDateTime;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\Account;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountGroupRef;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountGroupRefQuery;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountProfileRef;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountProfileRefQuery;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountQuery;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountSetting;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountSettingQuery;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AsyncJob;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AsyncJobQuery;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\PendingCall;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\PendingCallQuery;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserNotification;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserNotificationQuery;
use Eulogix\Cool\Lib\Database\Propel\CoolPropelObject;

abstract class BaseAccount extends CoolPropelObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        AccountPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the account_id field.
     * @var        int
     */
    protected $account_id;

    /**
     * The value for the login_name field.
     * @var        string
     */
    protected $login_name;

    /**
     * The value for the hashed_password field.
     * @var        string
     */
    protected $hashed_password;

    /**
     * The value for the type field.
     * @var        string
     */
    protected $type;

    /**
     * The value for the first_name field.
     * @var        string
     */
    protected $first_name;

    /**
     * The value for the last_name field.
     * @var        string
     */
    protected $last_name;

    /**
     * The value for the sex field.
     * @var        string
     */
    protected $sex;

    /**
     * The value for the email field.
     * @var        string
     */
    protected $email;

    /**
     * The value for the telephone field.
     * @var        string
     */
    protected $telephone;

    /**
     * The value for the mobile field.
     * @var        string
     */
    protected $mobile;

    /**
     * The value for the default_locale field.
     * @var        string
     */
    protected $default_locale;

    /**
     * The value for the company_name field.
     * @var        string
     */
    protected $company_name;

    /**
     * The value for the validity field.
     * @var        string
     */
    protected $validity;

    /**
     * The value for the roles field.
     * @var        string
     */
    protected $roles;

    /**
     * The value for the last_password_update field.
     * Note: this column has a database default value of: (expression) CURRENT_TIMESTAMP
     * @var        string
     */
    protected $last_password_update;

    /**
     * The value for the validate_method field.
     * Note: this column has a database default value of: 'LOCAL'
     * @var        string
     */
    protected $validate_method;

    /**
     * @var        PropelObjectCollection|AccountSetting[] Collection to store aggregation of AccountSetting objects.
     */
    protected $collAccountSettings;
    protected $collAccountSettingsPartial;

    /**
     * @var        PropelObjectCollection|AccountProfileRef[] Collection to store aggregation of AccountProfileRef objects.
     */
    protected $collAccountProfileRefs;
    protected $collAccountProfileRefsPartial;

    /**
     * @var        PropelObjectCollection|AccountGroupRef[] Collection to store aggregation of AccountGroupRef objects.
     */
    protected $collAccountGroupRefs;
    protected $collAccountGroupRefsPartial;

    /**
     * @var        PropelObjectCollection|PendingCall[] Collection to store aggregation of PendingCall objects.
     */
    protected $collPendingCalls;
    protected $collPendingCallsPartial;

    /**
     * @var        PropelObjectCollection|AsyncJob[] Collection to store aggregation of AsyncJob objects.
     */
    protected $collAsyncJobsRelatedByIssuerUserId;
    protected $collAsyncJobsRelatedByIssuerUserIdPartial;

    /**
     * @var        PropelObjectCollection|UserNotification[] Collection to store aggregation of UserNotification objects.
     */
    protected $collUserNotificationsRelatedByUserId;
    protected $collUserNotificationsRelatedByUserIdPartial;

    /**
     * @var        PropelObjectCollection|AsyncJob[] Collection to store aggregation of AsyncJob objects.
     */
    protected $collAsyncJobsRelatedByCreationUserId;
    protected $collAsyncJobsRelatedByCreationUserIdPartial;

    /**
     * @var        PropelObjectCollection|AsyncJob[] Collection to store aggregation of AsyncJob objects.
     */
    protected $collAsyncJobsRelatedByUpdateUserId;
    protected $collAsyncJobsRelatedByUpdateUserIdPartial;

    /**
     * @var        PropelObjectCollection|UserNotification[] Collection to store aggregation of UserNotification objects.
     */
    protected $collUserNotificationsRelatedByCreationUserId;
    protected $collUserNotificationsRelatedByCreationUserIdPartial;

    /**
     * @var        PropelObjectCollection|UserNotification[] Collection to store aggregation of UserNotification objects.
     */
    protected $collUserNotificationsRelatedByUpdateUserId;
    protected $collUserNotificationsRelatedByUpdateUserIdPartial;

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
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $accountSettingsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $accountProfileRefsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $accountGroupRefsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $pendingCallsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $asyncJobsRelatedByIssuerUserIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $userNotificationsRelatedByUserIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $asyncJobsRelatedByCreationUserIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $asyncJobsRelatedByUpdateUserIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $userNotificationsRelatedByCreationUserIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $userNotificationsRelatedByUpdateUserIdScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->validate_method = 'LOCAL';
    }

    /**
     * Initializes internal state of BaseAccount object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
    }

    /**
     * Get the [account_id] column value.
     *
     * @return int
     */
    public function getAccountId()
    {

        return $this->account_id;
    }

    /**
     * Get the [login_name] column value.
     *
     * @return string
     */
    public function getLoginName()
    {

        return $this->login_name;
    }

    /**
     * Get the [hashed_password] column value.
     *
     * @return string
     */
    public function getHashedPassword()
    {

        return $this->hashed_password;
    }

    /**
     * Get the [type] column value.
     * each app may use this field to cluster users as it likes
     * @return string
     */
    public function getType()
    {

        return $this->type;
    }

    /**
     * Get the [first_name] column value.
     *
     * @return string
     */
    public function getFirstName()
    {

        return $this->first_name;
    }

    /**
     * Get the [last_name] column value.
     *
     * @return string
     */
    public function getLastName()
    {

        return $this->last_name;
    }

    /**
     * Get the [sex] column value.
     *
     * @return string
     */
    public function getSex()
    {

        return $this->sex;
    }

    /**
     * Get the [email] column value.
     *
     * @return string
     */
    public function getEmail()
    {

        return $this->email;
    }

    /**
     * Get the [telephone] column value.
     *
     * @return string
     */
    public function getTelephone()
    {

        return $this->telephone;
    }

    /**
     * Get the [mobile] column value.
     *
     * @return string
     */
    public function getMobile()
    {

        return $this->mobile;
    }

    /**
     * Get the [default_locale] column value.
     *
     * @return string
     */
    public function getDefaultLocale()
    {

        return $this->default_locale;
    }

    /**
     * Get the [company_name] column value.
     *
     * @return string
     */
    public function getCompanyName()
    {

        return $this->company_name;
    }

    /**
     * Get the [validity] column value.
     *
     * @return string
     */
    public function getValidity()
    {

        return $this->validity;
    }

    /**
     * Get the [roles] column value.
     * SF security roles
     * @return string
     */
    public function getRoles()
    {

        return $this->roles;
    }

    /**
     * Get the [optionally formatted] temporal [last_password_update] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getLastPasswordUpdate($format = null)
    {
        if ($this->last_password_update === null) {
            return null;
        }


        try {
            $dt = new DateTime($this->last_password_update);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->last_password_update, true), $x);
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
     * Get the [validate_method] column value.
     * identifies the type of login to be used for the user
     * @return string
     */
    public function getValidateMethod()
    {

        return $this->validate_method;
    }

    /**
     * Set the value of [account_id] column.
     *
     * @param  int $v new value
     * @return Account The current object (for fluent API support)
     */
    public function setAccountId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->account_id !== $v) {
            $this->account_id = $v;
            $this->modifiedColumns[] = AccountPeer::ACCOUNT_ID;
        }


        return $this;
    } // setAccountId()

    /**
     * Set the value of [login_name] column.
     *
     * @param  string $v new value
     * @return Account The current object (for fluent API support)
     */
    public function setLoginName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->login_name !== $v) {
            $this->login_name = $v;
            $this->modifiedColumns[] = AccountPeer::LOGIN_NAME;
        }


        return $this;
    } // setLoginName()

    /**
     * Set the value of [hashed_password] column.
     *
     * @param  string $v new value
     * @return Account The current object (for fluent API support)
     */
    public function setHashedPassword($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->hashed_password !== $v) {
            $this->hashed_password = $v;
            $this->modifiedColumns[] = AccountPeer::HASHED_PASSWORD;
        }


        return $this;
    } // setHashedPassword()

    /**
     * Set the value of [type] column.
     * each app may use this field to cluster users as it likes
     * @param  string $v new value
     * @return Account The current object (for fluent API support)
     */
    public function setType($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->type !== $v) {
            $this->type = $v;
            $this->modifiedColumns[] = AccountPeer::TYPE;
        }


        return $this;
    } // setType()

    /**
     * Set the value of [first_name] column.
     *
     * @param  string $v new value
     * @return Account The current object (for fluent API support)
     */
    public function setFirstName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->first_name !== $v) {
            $this->first_name = $v;
            $this->modifiedColumns[] = AccountPeer::FIRST_NAME;
        }


        return $this;
    } // setFirstName()

    /**
     * Set the value of [last_name] column.
     *
     * @param  string $v new value
     * @return Account The current object (for fluent API support)
     */
    public function setLastName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->last_name !== $v) {
            $this->last_name = $v;
            $this->modifiedColumns[] = AccountPeer::LAST_NAME;
        }


        return $this;
    } // setLastName()

    /**
     * Set the value of [sex] column.
     *
     * @param  string $v new value
     * @return Account The current object (for fluent API support)
     */
    public function setSex($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->sex !== $v) {
            $this->sex = $v;
            $this->modifiedColumns[] = AccountPeer::SEX;
        }


        return $this;
    } // setSex()

    /**
     * Set the value of [email] column.
     *
     * @param  string $v new value
     * @return Account The current object (for fluent API support)
     */
    public function setEmail($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->email !== $v) {
            $this->email = $v;
            $this->modifiedColumns[] = AccountPeer::EMAIL;
        }


        return $this;
    } // setEmail()

    /**
     * Set the value of [telephone] column.
     *
     * @param  string $v new value
     * @return Account The current object (for fluent API support)
     */
    public function setTelephone($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->telephone !== $v) {
            $this->telephone = $v;
            $this->modifiedColumns[] = AccountPeer::TELEPHONE;
        }


        return $this;
    } // setTelephone()

    /**
     * Set the value of [mobile] column.
     *
     * @param  string $v new value
     * @return Account The current object (for fluent API support)
     */
    public function setMobile($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->mobile !== $v) {
            $this->mobile = $v;
            $this->modifiedColumns[] = AccountPeer::MOBILE;
        }


        return $this;
    } // setMobile()

    /**
     * Set the value of [default_locale] column.
     *
     * @param  string $v new value
     * @return Account The current object (for fluent API support)
     */
    public function setDefaultLocale($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->default_locale !== $v) {
            $this->default_locale = $v;
            $this->modifiedColumns[] = AccountPeer::DEFAULT_LOCALE;
        }


        return $this;
    } // setDefaultLocale()

    /**
     * Set the value of [company_name] column.
     *
     * @param  string $v new value
     * @return Account The current object (for fluent API support)
     */
    public function setCompanyName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->company_name !== $v) {
            $this->company_name = $v;
            $this->modifiedColumns[] = AccountPeer::COMPANY_NAME;
        }


        return $this;
    } // setCompanyName()

    /**
     * Set the value of [validity] column.
     *
     * @param  string $v new value
     * @return Account The current object (for fluent API support)
     */
    public function setValidity($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->validity !== $v) {
            $this->validity = $v;
            $this->modifiedColumns[] = AccountPeer::VALIDITY;
        }


        return $this;
    } // setValidity()

    /**
     * Set the value of [roles] column.
     * SF security roles
     * @param  string $v new value
     * @return Account The current object (for fluent API support)
     */
    public function setRoles($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->roles !== $v) {
            $this->roles = $v;
            $this->modifiedColumns[] = AccountPeer::ROLES;
        }


        return $this;
    } // setRoles()

    /**
     * Sets the value of [last_password_update] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Account The current object (for fluent API support)
     */
    public function setLastPasswordUpdate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->last_password_update !== null || $dt !== null) {
            $currentDateAsString = ($this->last_password_update !== null && $tmpDt = new DateTime($this->last_password_update)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->last_password_update = $newDateAsString;
                $this->modifiedColumns[] = AccountPeer::LAST_PASSWORD_UPDATE;
            }
        } // if either are not null


        return $this;
    } // setLastPasswordUpdate()

    /**
     * Set the value of [validate_method] column.
     * identifies the type of login to be used for the user
     * @param  string $v new value
     * @return Account The current object (for fluent API support)
     */
    public function setValidateMethod($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->validate_method !== $v) {
            $this->validate_method = $v;
            $this->modifiedColumns[] = AccountPeer::VALIDATE_METHOD;
        }


        return $this;
    } // setValidateMethod()

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
            if ($this->validate_method !== 'LOCAL') {
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

            $this->account_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->login_name = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->hashed_password = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->type = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->first_name = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->last_name = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->sex = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->email = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->telephone = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->mobile = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
            $this->default_locale = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
            $this->company_name = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
            $this->validity = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
            $this->roles = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
            $this->last_password_update = ($row[$startcol + 14] !== null) ? (string) $row[$startcol + 14] : null;
            $this->validate_method = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 16; // 16 = AccountPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Account object", $e);
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
            $con = Propel::getConnection(AccountPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = AccountPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collAccountSettings = null;

            $this->collAccountProfileRefs = null;

            $this->collAccountGroupRefs = null;

            $this->collPendingCalls = null;

            $this->collAsyncJobsRelatedByIssuerUserId = null;

            $this->collUserNotificationsRelatedByUserId = null;

            $this->collAsyncJobsRelatedByCreationUserId = null;

            $this->collAsyncJobsRelatedByUpdateUserId = null;

            $this->collUserNotificationsRelatedByCreationUserId = null;

            $this->collUserNotificationsRelatedByUpdateUserId = null;

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
            $con = Propel::getConnection(AccountPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = AccountQuery::create()
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
            $con = Propel::getConnection(AccountPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                AccountPeer::addInstanceToPool($this);
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

            if ($this->accountSettingsScheduledForDeletion !== null) {
                if (!$this->accountSettingsScheduledForDeletion->isEmpty()) {
                    AccountSettingQuery::create()
                        ->filterByPrimaryKeys($this->accountSettingsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->accountSettingsScheduledForDeletion = null;
                }
            }

            if ($this->collAccountSettings !== null) {
                foreach ($this->collAccountSettings as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->accountProfileRefsScheduledForDeletion !== null) {
                if (!$this->accountProfileRefsScheduledForDeletion->isEmpty()) {
                    AccountProfileRefQuery::create()
                        ->filterByPrimaryKeys($this->accountProfileRefsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->accountProfileRefsScheduledForDeletion = null;
                }
            }

            if ($this->collAccountProfileRefs !== null) {
                foreach ($this->collAccountProfileRefs as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->accountGroupRefsScheduledForDeletion !== null) {
                if (!$this->accountGroupRefsScheduledForDeletion->isEmpty()) {
                    AccountGroupRefQuery::create()
                        ->filterByPrimaryKeys($this->accountGroupRefsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->accountGroupRefsScheduledForDeletion = null;
                }
            }

            if ($this->collAccountGroupRefs !== null) {
                foreach ($this->collAccountGroupRefs as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->pendingCallsScheduledForDeletion !== null) {
                if (!$this->pendingCallsScheduledForDeletion->isEmpty()) {
                    foreach ($this->pendingCallsScheduledForDeletion as $pendingCall) {
                        // need to save related object because we set the relation to null
                        $pendingCall->save($con);
                    }
                    $this->pendingCallsScheduledForDeletion = null;
                }
            }

            if ($this->collPendingCalls !== null) {
                foreach ($this->collPendingCalls as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->asyncJobsRelatedByIssuerUserIdScheduledForDeletion !== null) {
                if (!$this->asyncJobsRelatedByIssuerUserIdScheduledForDeletion->isEmpty()) {
                    foreach ($this->asyncJobsRelatedByIssuerUserIdScheduledForDeletion as $asyncJobRelatedByIssuerUserId) {
                        // need to save related object because we set the relation to null
                        $asyncJobRelatedByIssuerUserId->save($con);
                    }
                    $this->asyncJobsRelatedByIssuerUserIdScheduledForDeletion = null;
                }
            }

            if ($this->collAsyncJobsRelatedByIssuerUserId !== null) {
                foreach ($this->collAsyncJobsRelatedByIssuerUserId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->userNotificationsRelatedByUserIdScheduledForDeletion !== null) {
                if (!$this->userNotificationsRelatedByUserIdScheduledForDeletion->isEmpty()) {
                    foreach ($this->userNotificationsRelatedByUserIdScheduledForDeletion as $userNotificationRelatedByUserId) {
                        // need to save related object because we set the relation to null
                        $userNotificationRelatedByUserId->save($con);
                    }
                    $this->userNotificationsRelatedByUserIdScheduledForDeletion = null;
                }
            }

            if ($this->collUserNotificationsRelatedByUserId !== null) {
                foreach ($this->collUserNotificationsRelatedByUserId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->asyncJobsRelatedByCreationUserIdScheduledForDeletion !== null) {
                if (!$this->asyncJobsRelatedByCreationUserIdScheduledForDeletion->isEmpty()) {
                    foreach ($this->asyncJobsRelatedByCreationUserIdScheduledForDeletion as $asyncJobRelatedByCreationUserId) {
                        // need to save related object because we set the relation to null
                        $asyncJobRelatedByCreationUserId->save($con);
                    }
                    $this->asyncJobsRelatedByCreationUserIdScheduledForDeletion = null;
                }
            }

            if ($this->collAsyncJobsRelatedByCreationUserId !== null) {
                foreach ($this->collAsyncJobsRelatedByCreationUserId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->asyncJobsRelatedByUpdateUserIdScheduledForDeletion !== null) {
                if (!$this->asyncJobsRelatedByUpdateUserIdScheduledForDeletion->isEmpty()) {
                    foreach ($this->asyncJobsRelatedByUpdateUserIdScheduledForDeletion as $asyncJobRelatedByUpdateUserId) {
                        // need to save related object because we set the relation to null
                        $asyncJobRelatedByUpdateUserId->save($con);
                    }
                    $this->asyncJobsRelatedByUpdateUserIdScheduledForDeletion = null;
                }
            }

            if ($this->collAsyncJobsRelatedByUpdateUserId !== null) {
                foreach ($this->collAsyncJobsRelatedByUpdateUserId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->userNotificationsRelatedByCreationUserIdScheduledForDeletion !== null) {
                if (!$this->userNotificationsRelatedByCreationUserIdScheduledForDeletion->isEmpty()) {
                    foreach ($this->userNotificationsRelatedByCreationUserIdScheduledForDeletion as $userNotificationRelatedByCreationUserId) {
                        // need to save related object because we set the relation to null
                        $userNotificationRelatedByCreationUserId->save($con);
                    }
                    $this->userNotificationsRelatedByCreationUserIdScheduledForDeletion = null;
                }
            }

            if ($this->collUserNotificationsRelatedByCreationUserId !== null) {
                foreach ($this->collUserNotificationsRelatedByCreationUserId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->userNotificationsRelatedByUpdateUserIdScheduledForDeletion !== null) {
                if (!$this->userNotificationsRelatedByUpdateUserIdScheduledForDeletion->isEmpty()) {
                    foreach ($this->userNotificationsRelatedByUpdateUserIdScheduledForDeletion as $userNotificationRelatedByUpdateUserId) {
                        // need to save related object because we set the relation to null
                        $userNotificationRelatedByUpdateUserId->save($con);
                    }
                    $this->userNotificationsRelatedByUpdateUserIdScheduledForDeletion = null;
                }
            }

            if ($this->collUserNotificationsRelatedByUpdateUserId !== null) {
                foreach ($this->collUserNotificationsRelatedByUpdateUserId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
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

        $this->modifiedColumns[] = AccountPeer::ACCOUNT_ID;
        if (null !== $this->account_id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . AccountPeer::ACCOUNT_ID . ')');
        }
        if (null === $this->account_id) {
            try {
                $stmt = $con->query("SELECT nextval('core.account_account_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->account_id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(AccountPeer::ACCOUNT_ID)) {
            $modifiedColumns[':p' . $index++]  = 'account_id';
        }
        if ($this->isColumnModified(AccountPeer::LOGIN_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'login_name';
        }
        if ($this->isColumnModified(AccountPeer::HASHED_PASSWORD)) {
            $modifiedColumns[':p' . $index++]  = 'hashed_password';
        }
        if ($this->isColumnModified(AccountPeer::TYPE)) {
            $modifiedColumns[':p' . $index++]  = 'type';
        }
        if ($this->isColumnModified(AccountPeer::FIRST_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'first_name';
        }
        if ($this->isColumnModified(AccountPeer::LAST_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'last_name';
        }
        if ($this->isColumnModified(AccountPeer::SEX)) {
            $modifiedColumns[':p' . $index++]  = 'sex';
        }
        if ($this->isColumnModified(AccountPeer::EMAIL)) {
            $modifiedColumns[':p' . $index++]  = 'email';
        }
        if ($this->isColumnModified(AccountPeer::TELEPHONE)) {
            $modifiedColumns[':p' . $index++]  = 'telephone';
        }
        if ($this->isColumnModified(AccountPeer::MOBILE)) {
            $modifiedColumns[':p' . $index++]  = 'mobile';
        }
        if ($this->isColumnModified(AccountPeer::DEFAULT_LOCALE)) {
            $modifiedColumns[':p' . $index++]  = 'default_locale';
        }
        if ($this->isColumnModified(AccountPeer::COMPANY_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'company_name';
        }
        if ($this->isColumnModified(AccountPeer::VALIDITY)) {
            $modifiedColumns[':p' . $index++]  = 'validity';
        }
        if ($this->isColumnModified(AccountPeer::ROLES)) {
            $modifiedColumns[':p' . $index++]  = 'roles';
        }
        if ($this->isColumnModified(AccountPeer::LAST_PASSWORD_UPDATE)) {
            $modifiedColumns[':p' . $index++]  = 'last_password_update';
        }
        if ($this->isColumnModified(AccountPeer::VALIDATE_METHOD)) {
            $modifiedColumns[':p' . $index++]  = 'validate_method';
        }

        $sql = sprintf(
            'INSERT INTO core.account (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'account_id':
                        $stmt->bindValue($identifier, $this->account_id, PDO::PARAM_INT);
                        break;
                    case 'login_name':
                        $stmt->bindValue($identifier, $this->login_name, PDO::PARAM_STR);
                        break;
                    case 'hashed_password':
                        $stmt->bindValue($identifier, $this->hashed_password, PDO::PARAM_STR);
                        break;
                    case 'type':
                        $stmt->bindValue($identifier, $this->type, PDO::PARAM_STR);
                        break;
                    case 'first_name':
                        $stmt->bindValue($identifier, $this->first_name, PDO::PARAM_STR);
                        break;
                    case 'last_name':
                        $stmt->bindValue($identifier, $this->last_name, PDO::PARAM_STR);
                        break;
                    case 'sex':
                        $stmt->bindValue($identifier, $this->sex, PDO::PARAM_STR);
                        break;
                    case 'email':
                        $stmt->bindValue($identifier, $this->email, PDO::PARAM_STR);
                        break;
                    case 'telephone':
                        $stmt->bindValue($identifier, $this->telephone, PDO::PARAM_STR);
                        break;
                    case 'mobile':
                        $stmt->bindValue($identifier, $this->mobile, PDO::PARAM_STR);
                        break;
                    case 'default_locale':
                        $stmt->bindValue($identifier, $this->default_locale, PDO::PARAM_STR);
                        break;
                    case 'company_name':
                        $stmt->bindValue($identifier, $this->company_name, PDO::PARAM_STR);
                        break;
                    case 'validity':
                        $stmt->bindValue($identifier, $this->validity, PDO::PARAM_STR);
                        break;
                    case 'roles':
                        $stmt->bindValue($identifier, $this->roles, PDO::PARAM_STR);
                        break;
                    case 'last_password_update':
                        $stmt->bindValue($identifier, $this->last_password_update, PDO::PARAM_STR);
                        break;
                    case 'validate_method':
                        $stmt->bindValue($identifier, $this->validate_method, PDO::PARAM_STR);
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


            if (($retval = AccountPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collAccountSettings !== null) {
                    foreach ($this->collAccountSettings as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collAccountProfileRefs !== null) {
                    foreach ($this->collAccountProfileRefs as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collAccountGroupRefs !== null) {
                    foreach ($this->collAccountGroupRefs as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collPendingCalls !== null) {
                    foreach ($this->collPendingCalls as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collAsyncJobsRelatedByIssuerUserId !== null) {
                    foreach ($this->collAsyncJobsRelatedByIssuerUserId as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collUserNotificationsRelatedByUserId !== null) {
                    foreach ($this->collUserNotificationsRelatedByUserId as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collAsyncJobsRelatedByCreationUserId !== null) {
                    foreach ($this->collAsyncJobsRelatedByCreationUserId as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collAsyncJobsRelatedByUpdateUserId !== null) {
                    foreach ($this->collAsyncJobsRelatedByUpdateUserId as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collUserNotificationsRelatedByCreationUserId !== null) {
                    foreach ($this->collUserNotificationsRelatedByCreationUserId as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collUserNotificationsRelatedByUpdateUserId !== null) {
                    foreach ($this->collUserNotificationsRelatedByUpdateUserId as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
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
        $pos = AccountPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getAccountId();
                break;
            case 1:
                return $this->getLoginName();
                break;
            case 2:
                return $this->getHashedPassword();
                break;
            case 3:
                return $this->getType();
                break;
            case 4:
                return $this->getFirstName();
                break;
            case 5:
                return $this->getLastName();
                break;
            case 6:
                return $this->getSex();
                break;
            case 7:
                return $this->getEmail();
                break;
            case 8:
                return $this->getTelephone();
                break;
            case 9:
                return $this->getMobile();
                break;
            case 10:
                return $this->getDefaultLocale();
                break;
            case 11:
                return $this->getCompanyName();
                break;
            case 12:
                return $this->getValidity();
                break;
            case 13:
                return $this->getRoles();
                break;
            case 14:
                return $this->getLastPasswordUpdate();
                break;
            case 15:
                return $this->getValidateMethod();
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
        if (isset($alreadyDumpedObjects['Account'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Account'][$this->getPrimaryKey()] = true;
        $keys = AccountPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getAccountId(),
            $keys[1] => $this->getLoginName(),
            $keys[2] => $this->getHashedPassword(),
            $keys[3] => $this->getType(),
            $keys[4] => $this->getFirstName(),
            $keys[5] => $this->getLastName(),
            $keys[6] => $this->getSex(),
            $keys[7] => $this->getEmail(),
            $keys[8] => $this->getTelephone(),
            $keys[9] => $this->getMobile(),
            $keys[10] => $this->getDefaultLocale(),
            $keys[11] => $this->getCompanyName(),
            $keys[12] => $this->getValidity(),
            $keys[13] => $this->getRoles(),
            $keys[14] => $this->getLastPasswordUpdate(),
            $keys[15] => $this->getValidateMethod(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collAccountSettings) {
                $result['AccountSettings'] = $this->collAccountSettings->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collAccountProfileRefs) {
                $result['AccountProfileRefs'] = $this->collAccountProfileRefs->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collAccountGroupRefs) {
                $result['AccountGroupRefs'] = $this->collAccountGroupRefs->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collPendingCalls) {
                $result['PendingCalls'] = $this->collPendingCalls->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collAsyncJobsRelatedByIssuerUserId) {
                $result['AsyncJobsRelatedByIssuerUserId'] = $this->collAsyncJobsRelatedByIssuerUserId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collUserNotificationsRelatedByUserId) {
                $result['UserNotificationsRelatedByUserId'] = $this->collUserNotificationsRelatedByUserId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collAsyncJobsRelatedByCreationUserId) {
                $result['AsyncJobsRelatedByCreationUserId'] = $this->collAsyncJobsRelatedByCreationUserId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collAsyncJobsRelatedByUpdateUserId) {
                $result['AsyncJobsRelatedByUpdateUserId'] = $this->collAsyncJobsRelatedByUpdateUserId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collUserNotificationsRelatedByCreationUserId) {
                $result['UserNotificationsRelatedByCreationUserId'] = $this->collUserNotificationsRelatedByCreationUserId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collUserNotificationsRelatedByUpdateUserId) {
                $result['UserNotificationsRelatedByUpdateUserId'] = $this->collUserNotificationsRelatedByUpdateUserId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = AccountPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setAccountId($value);
                break;
            case 1:
                $this->setLoginName($value);
                break;
            case 2:
                $this->setHashedPassword($value);
                break;
            case 3:
                $this->setType($value);
                break;
            case 4:
                $this->setFirstName($value);
                break;
            case 5:
                $this->setLastName($value);
                break;
            case 6:
                $this->setSex($value);
                break;
            case 7:
                $this->setEmail($value);
                break;
            case 8:
                $this->setTelephone($value);
                break;
            case 9:
                $this->setMobile($value);
                break;
            case 10:
                $this->setDefaultLocale($value);
                break;
            case 11:
                $this->setCompanyName($value);
                break;
            case 12:
                $this->setValidity($value);
                break;
            case 13:
                $this->setRoles($value);
                break;
            case 14:
                $this->setLastPasswordUpdate($value);
                break;
            case 15:
                $this->setValidateMethod($value);
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
        $keys = AccountPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setAccountId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setLoginName($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setHashedPassword($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setType($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setFirstName($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setLastName($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setSex($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setEmail($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setTelephone($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setMobile($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setDefaultLocale($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setCompanyName($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setValidity($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setRoles($arr[$keys[13]]);
        if (array_key_exists($keys[14], $arr)) $this->setLastPasswordUpdate($arr[$keys[14]]);
        if (array_key_exists($keys[15], $arr)) $this->setValidateMethod($arr[$keys[15]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(AccountPeer::DATABASE_NAME);

        if ($this->isColumnModified(AccountPeer::ACCOUNT_ID)) $criteria->add(AccountPeer::ACCOUNT_ID, $this->account_id);
        if ($this->isColumnModified(AccountPeer::LOGIN_NAME)) $criteria->add(AccountPeer::LOGIN_NAME, $this->login_name);
        if ($this->isColumnModified(AccountPeer::HASHED_PASSWORD)) $criteria->add(AccountPeer::HASHED_PASSWORD, $this->hashed_password);
        if ($this->isColumnModified(AccountPeer::TYPE)) $criteria->add(AccountPeer::TYPE, $this->type);
        if ($this->isColumnModified(AccountPeer::FIRST_NAME)) $criteria->add(AccountPeer::FIRST_NAME, $this->first_name);
        if ($this->isColumnModified(AccountPeer::LAST_NAME)) $criteria->add(AccountPeer::LAST_NAME, $this->last_name);
        if ($this->isColumnModified(AccountPeer::SEX)) $criteria->add(AccountPeer::SEX, $this->sex);
        if ($this->isColumnModified(AccountPeer::EMAIL)) $criteria->add(AccountPeer::EMAIL, $this->email);
        if ($this->isColumnModified(AccountPeer::TELEPHONE)) $criteria->add(AccountPeer::TELEPHONE, $this->telephone);
        if ($this->isColumnModified(AccountPeer::MOBILE)) $criteria->add(AccountPeer::MOBILE, $this->mobile);
        if ($this->isColumnModified(AccountPeer::DEFAULT_LOCALE)) $criteria->add(AccountPeer::DEFAULT_LOCALE, $this->default_locale);
        if ($this->isColumnModified(AccountPeer::COMPANY_NAME)) $criteria->add(AccountPeer::COMPANY_NAME, $this->company_name);
        if ($this->isColumnModified(AccountPeer::VALIDITY)) $criteria->add(AccountPeer::VALIDITY, $this->validity);
        if ($this->isColumnModified(AccountPeer::ROLES)) $criteria->add(AccountPeer::ROLES, $this->roles);
        if ($this->isColumnModified(AccountPeer::LAST_PASSWORD_UPDATE)) $criteria->add(AccountPeer::LAST_PASSWORD_UPDATE, $this->last_password_update);
        if ($this->isColumnModified(AccountPeer::VALIDATE_METHOD)) $criteria->add(AccountPeer::VALIDATE_METHOD, $this->validate_method);

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
        $criteria = new Criteria(AccountPeer::DATABASE_NAME);
        $criteria->add(AccountPeer::ACCOUNT_ID, $this->account_id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getAccountId();
    }

    /**
     * Generic method to set the primary key (account_id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setAccountId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getAccountId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of Account (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setLoginName($this->getLoginName());
        $copyObj->setHashedPassword($this->getHashedPassword());
        $copyObj->setType($this->getType());
        $copyObj->setFirstName($this->getFirstName());
        $copyObj->setLastName($this->getLastName());
        $copyObj->setSex($this->getSex());
        $copyObj->setEmail($this->getEmail());
        $copyObj->setTelephone($this->getTelephone());
        $copyObj->setMobile($this->getMobile());
        $copyObj->setDefaultLocale($this->getDefaultLocale());
        $copyObj->setCompanyName($this->getCompanyName());
        $copyObj->setValidity($this->getValidity());
        $copyObj->setRoles($this->getRoles());
        $copyObj->setLastPasswordUpdate($this->getLastPasswordUpdate());
        $copyObj->setValidateMethod($this->getValidateMethod());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getAccountSettings() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addAccountSetting($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getAccountProfileRefs() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addAccountProfileRef($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getAccountGroupRefs() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addAccountGroupRef($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getPendingCalls() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addPendingCall($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getAsyncJobsRelatedByIssuerUserId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addAsyncJobRelatedByIssuerUserId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getUserNotificationsRelatedByUserId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addUserNotificationRelatedByUserId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getAsyncJobsRelatedByCreationUserId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addAsyncJobRelatedByCreationUserId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getAsyncJobsRelatedByUpdateUserId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addAsyncJobRelatedByUpdateUserId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getUserNotificationsRelatedByCreationUserId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addUserNotificationRelatedByCreationUserId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getUserNotificationsRelatedByUpdateUserId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addUserNotificationRelatedByUpdateUserId($relObj->copy($deepCopy));
                }
            }

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setAccountId(NULL); // this is a auto-increment column, so set to default value
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
     * @return Account Clone of current object.
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
     * @return AccountPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new AccountPeer();
        }

        return self::$peer;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('AccountSetting' == $relationName) {
            $this->initAccountSettings();
        }
        if ('AccountProfileRef' == $relationName) {
            $this->initAccountProfileRefs();
        }
        if ('AccountGroupRef' == $relationName) {
            $this->initAccountGroupRefs();
        }
        if ('PendingCall' == $relationName) {
            $this->initPendingCalls();
        }
        if ('AsyncJobRelatedByIssuerUserId' == $relationName) {
            $this->initAsyncJobsRelatedByIssuerUserId();
        }
        if ('UserNotificationRelatedByUserId' == $relationName) {
            $this->initUserNotificationsRelatedByUserId();
        }
        if ('AsyncJobRelatedByCreationUserId' == $relationName) {
            $this->initAsyncJobsRelatedByCreationUserId();
        }
        if ('AsyncJobRelatedByUpdateUserId' == $relationName) {
            $this->initAsyncJobsRelatedByUpdateUserId();
        }
        if ('UserNotificationRelatedByCreationUserId' == $relationName) {
            $this->initUserNotificationsRelatedByCreationUserId();
        }
        if ('UserNotificationRelatedByUpdateUserId' == $relationName) {
            $this->initUserNotificationsRelatedByUpdateUserId();
        }
    }

    /**
     * Clears out the collAccountSettings collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Account The current object (for fluent API support)
     * @see        addAccountSettings()
     */
    public function clearAccountSettings()
    {
        $this->collAccountSettings = null; // important to set this to null since that means it is uninitialized
        $this->collAccountSettingsPartial = null;

        return $this;
    }

    /**
     * reset is the collAccountSettings collection loaded partially
     *
     * @return void
     */
    public function resetPartialAccountSettings($v = true)
    {
        $this->collAccountSettingsPartial = $v;
    }

    /**
     * Initializes the collAccountSettings collection.
     *
     * By default this just sets the collAccountSettings collection to an empty array (like clearcollAccountSettings());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initAccountSettings($overrideExisting = true)
    {
        if (null !== $this->collAccountSettings && !$overrideExisting) {
            return;
        }
        $this->collAccountSettings = new PropelObjectCollection();
        $this->collAccountSettings->setModel('AccountSetting');
    }

    /**
     * Gets an array of AccountSetting objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Account is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|AccountSetting[] List of AccountSetting objects
     * @throws PropelException
     */
    public function getAccountSettings($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collAccountSettingsPartial && !$this->isNew();
        if (null === $this->collAccountSettings || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collAccountSettings) {
                // return empty collection
                $this->initAccountSettings();
            } else {
                $collAccountSettings = AccountSettingQuery::create(null, $criteria)
                    ->filterByAccount($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collAccountSettingsPartial && count($collAccountSettings)) {
                      $this->initAccountSettings(false);

                      foreach ($collAccountSettings as $obj) {
                        if (false == $this->collAccountSettings->contains($obj)) {
                          $this->collAccountSettings->append($obj);
                        }
                      }

                      $this->collAccountSettingsPartial = true;
                    }

                    $collAccountSettings->getInternalIterator()->rewind();

                    return $collAccountSettings;
                }

                if ($partial && $this->collAccountSettings) {
                    foreach ($this->collAccountSettings as $obj) {
                        if ($obj->isNew()) {
                            $collAccountSettings[] = $obj;
                        }
                    }
                }

                $this->collAccountSettings = $collAccountSettings;
                $this->collAccountSettingsPartial = false;
            }
        }

        return $this->collAccountSettings;
    }

    /**
     * Sets a collection of AccountSetting objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $accountSettings A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Account The current object (for fluent API support)
     */
    public function setAccountSettings(PropelCollection $accountSettings, PropelPDO $con = null)
    {
        $accountSettingsToDelete = $this->getAccountSettings(new Criteria(), $con)->diff($accountSettings);


        $this->accountSettingsScheduledForDeletion = $accountSettingsToDelete;

        foreach ($accountSettingsToDelete as $accountSettingRemoved) {
            $accountSettingRemoved->setAccount(null);
        }

        $this->collAccountSettings = null;
        foreach ($accountSettings as $accountSetting) {
            $this->addAccountSetting($accountSetting);
        }

        $this->collAccountSettings = $accountSettings;
        $this->collAccountSettingsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related AccountSetting objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related AccountSetting objects.
     * @throws PropelException
     */
    public function countAccountSettings(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collAccountSettingsPartial && !$this->isNew();
        if (null === $this->collAccountSettings || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collAccountSettings) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getAccountSettings());
            }
            $query = AccountSettingQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByAccount($this)
                ->count($con);
        }

        return count($this->collAccountSettings);
    }

    /**
     * Method called to associate a AccountSetting object to this object
     * through the AccountSetting foreign key attribute.
     *
     * @param    AccountSetting $l AccountSetting
     * @return Account The current object (for fluent API support)
     */
    public function addAccountSetting(AccountSetting $l)
    {
        if ($this->collAccountSettings === null) {
            $this->initAccountSettings();
            $this->collAccountSettingsPartial = true;
        }

        if (!in_array($l, $this->collAccountSettings->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddAccountSetting($l);

            if ($this->accountSettingsScheduledForDeletion and $this->accountSettingsScheduledForDeletion->contains($l)) {
                $this->accountSettingsScheduledForDeletion->remove($this->accountSettingsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	AccountSetting $accountSetting The accountSetting object to add.
     */
    protected function doAddAccountSetting($accountSetting)
    {
        $this->collAccountSettings[]= $accountSetting;
        $accountSetting->setAccount($this);
    }

    /**
     * @param	AccountSetting $accountSetting The accountSetting object to remove.
     * @return Account The current object (for fluent API support)
     */
    public function removeAccountSetting($accountSetting)
    {
        if ($this->getAccountSettings()->contains($accountSetting)) {
            $this->collAccountSettings->remove($this->collAccountSettings->search($accountSetting));
            if (null === $this->accountSettingsScheduledForDeletion) {
                $this->accountSettingsScheduledForDeletion = clone $this->collAccountSettings;
                $this->accountSettingsScheduledForDeletion->clear();
            }
            $this->accountSettingsScheduledForDeletion[]= clone $accountSetting;
            $accountSetting->setAccount(null);
        }

        return $this;
    }

    /**
     * Clears out the collAccountProfileRefs collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Account The current object (for fluent API support)
     * @see        addAccountProfileRefs()
     */
    public function clearAccountProfileRefs()
    {
        $this->collAccountProfileRefs = null; // important to set this to null since that means it is uninitialized
        $this->collAccountProfileRefsPartial = null;

        return $this;
    }

    /**
     * reset is the collAccountProfileRefs collection loaded partially
     *
     * @return void
     */
    public function resetPartialAccountProfileRefs($v = true)
    {
        $this->collAccountProfileRefsPartial = $v;
    }

    /**
     * Initializes the collAccountProfileRefs collection.
     *
     * By default this just sets the collAccountProfileRefs collection to an empty array (like clearcollAccountProfileRefs());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initAccountProfileRefs($overrideExisting = true)
    {
        if (null !== $this->collAccountProfileRefs && !$overrideExisting) {
            return;
        }
        $this->collAccountProfileRefs = new PropelObjectCollection();
        $this->collAccountProfileRefs->setModel('AccountProfileRef');
    }

    /**
     * Gets an array of AccountProfileRef objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Account is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|AccountProfileRef[] List of AccountProfileRef objects
     * @throws PropelException
     */
    public function getAccountProfileRefs($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collAccountProfileRefsPartial && !$this->isNew();
        if (null === $this->collAccountProfileRefs || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collAccountProfileRefs) {
                // return empty collection
                $this->initAccountProfileRefs();
            } else {
                $collAccountProfileRefs = AccountProfileRefQuery::create(null, $criteria)
                    ->filterByAccount($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collAccountProfileRefsPartial && count($collAccountProfileRefs)) {
                      $this->initAccountProfileRefs(false);

                      foreach ($collAccountProfileRefs as $obj) {
                        if (false == $this->collAccountProfileRefs->contains($obj)) {
                          $this->collAccountProfileRefs->append($obj);
                        }
                      }

                      $this->collAccountProfileRefsPartial = true;
                    }

                    $collAccountProfileRefs->getInternalIterator()->rewind();

                    return $collAccountProfileRefs;
                }

                if ($partial && $this->collAccountProfileRefs) {
                    foreach ($this->collAccountProfileRefs as $obj) {
                        if ($obj->isNew()) {
                            $collAccountProfileRefs[] = $obj;
                        }
                    }
                }

                $this->collAccountProfileRefs = $collAccountProfileRefs;
                $this->collAccountProfileRefsPartial = false;
            }
        }

        return $this->collAccountProfileRefs;
    }

    /**
     * Sets a collection of AccountProfileRef objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $accountProfileRefs A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Account The current object (for fluent API support)
     */
    public function setAccountProfileRefs(PropelCollection $accountProfileRefs, PropelPDO $con = null)
    {
        $accountProfileRefsToDelete = $this->getAccountProfileRefs(new Criteria(), $con)->diff($accountProfileRefs);


        $this->accountProfileRefsScheduledForDeletion = $accountProfileRefsToDelete;

        foreach ($accountProfileRefsToDelete as $accountProfileRefRemoved) {
            $accountProfileRefRemoved->setAccount(null);
        }

        $this->collAccountProfileRefs = null;
        foreach ($accountProfileRefs as $accountProfileRef) {
            $this->addAccountProfileRef($accountProfileRef);
        }

        $this->collAccountProfileRefs = $accountProfileRefs;
        $this->collAccountProfileRefsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related AccountProfileRef objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related AccountProfileRef objects.
     * @throws PropelException
     */
    public function countAccountProfileRefs(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collAccountProfileRefsPartial && !$this->isNew();
        if (null === $this->collAccountProfileRefs || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collAccountProfileRefs) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getAccountProfileRefs());
            }
            $query = AccountProfileRefQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByAccount($this)
                ->count($con);
        }

        return count($this->collAccountProfileRefs);
    }

    /**
     * Method called to associate a AccountProfileRef object to this object
     * through the AccountProfileRef foreign key attribute.
     *
     * @param    AccountProfileRef $l AccountProfileRef
     * @return Account The current object (for fluent API support)
     */
    public function addAccountProfileRef(AccountProfileRef $l)
    {
        if ($this->collAccountProfileRefs === null) {
            $this->initAccountProfileRefs();
            $this->collAccountProfileRefsPartial = true;
        }

        if (!in_array($l, $this->collAccountProfileRefs->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddAccountProfileRef($l);

            if ($this->accountProfileRefsScheduledForDeletion and $this->accountProfileRefsScheduledForDeletion->contains($l)) {
                $this->accountProfileRefsScheduledForDeletion->remove($this->accountProfileRefsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	AccountProfileRef $accountProfileRef The accountProfileRef object to add.
     */
    protected function doAddAccountProfileRef($accountProfileRef)
    {
        $this->collAccountProfileRefs[]= $accountProfileRef;
        $accountProfileRef->setAccount($this);
    }

    /**
     * @param	AccountProfileRef $accountProfileRef The accountProfileRef object to remove.
     * @return Account The current object (for fluent API support)
     */
    public function removeAccountProfileRef($accountProfileRef)
    {
        if ($this->getAccountProfileRefs()->contains($accountProfileRef)) {
            $this->collAccountProfileRefs->remove($this->collAccountProfileRefs->search($accountProfileRef));
            if (null === $this->accountProfileRefsScheduledForDeletion) {
                $this->accountProfileRefsScheduledForDeletion = clone $this->collAccountProfileRefs;
                $this->accountProfileRefsScheduledForDeletion->clear();
            }
            $this->accountProfileRefsScheduledForDeletion[]= clone $accountProfileRef;
            $accountProfileRef->setAccount(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Account is new, it will return
     * an empty collection; or if this Account has previously
     * been saved, it will retrieve related AccountProfileRefs from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Account.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|AccountProfileRef[] List of AccountProfileRef objects
     */
    public function getAccountProfileRefsJoinAccountProfile($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = AccountProfileRefQuery::create(null, $criteria);
        $query->joinWith('AccountProfile', $join_behavior);

        return $this->getAccountProfileRefs($query, $con);
    }

    /**
     * Clears out the collAccountGroupRefs collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Account The current object (for fluent API support)
     * @see        addAccountGroupRefs()
     */
    public function clearAccountGroupRefs()
    {
        $this->collAccountGroupRefs = null; // important to set this to null since that means it is uninitialized
        $this->collAccountGroupRefsPartial = null;

        return $this;
    }

    /**
     * reset is the collAccountGroupRefs collection loaded partially
     *
     * @return void
     */
    public function resetPartialAccountGroupRefs($v = true)
    {
        $this->collAccountGroupRefsPartial = $v;
    }

    /**
     * Initializes the collAccountGroupRefs collection.
     *
     * By default this just sets the collAccountGroupRefs collection to an empty array (like clearcollAccountGroupRefs());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initAccountGroupRefs($overrideExisting = true)
    {
        if (null !== $this->collAccountGroupRefs && !$overrideExisting) {
            return;
        }
        $this->collAccountGroupRefs = new PropelObjectCollection();
        $this->collAccountGroupRefs->setModel('AccountGroupRef');
    }

    /**
     * Gets an array of AccountGroupRef objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Account is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|AccountGroupRef[] List of AccountGroupRef objects
     * @throws PropelException
     */
    public function getAccountGroupRefs($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collAccountGroupRefsPartial && !$this->isNew();
        if (null === $this->collAccountGroupRefs || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collAccountGroupRefs) {
                // return empty collection
                $this->initAccountGroupRefs();
            } else {
                $collAccountGroupRefs = AccountGroupRefQuery::create(null, $criteria)
                    ->filterByAccount($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collAccountGroupRefsPartial && count($collAccountGroupRefs)) {
                      $this->initAccountGroupRefs(false);

                      foreach ($collAccountGroupRefs as $obj) {
                        if (false == $this->collAccountGroupRefs->contains($obj)) {
                          $this->collAccountGroupRefs->append($obj);
                        }
                      }

                      $this->collAccountGroupRefsPartial = true;
                    }

                    $collAccountGroupRefs->getInternalIterator()->rewind();

                    return $collAccountGroupRefs;
                }

                if ($partial && $this->collAccountGroupRefs) {
                    foreach ($this->collAccountGroupRefs as $obj) {
                        if ($obj->isNew()) {
                            $collAccountGroupRefs[] = $obj;
                        }
                    }
                }

                $this->collAccountGroupRefs = $collAccountGroupRefs;
                $this->collAccountGroupRefsPartial = false;
            }
        }

        return $this->collAccountGroupRefs;
    }

    /**
     * Sets a collection of AccountGroupRef objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $accountGroupRefs A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Account The current object (for fluent API support)
     */
    public function setAccountGroupRefs(PropelCollection $accountGroupRefs, PropelPDO $con = null)
    {
        $accountGroupRefsToDelete = $this->getAccountGroupRefs(new Criteria(), $con)->diff($accountGroupRefs);


        $this->accountGroupRefsScheduledForDeletion = $accountGroupRefsToDelete;

        foreach ($accountGroupRefsToDelete as $accountGroupRefRemoved) {
            $accountGroupRefRemoved->setAccount(null);
        }

        $this->collAccountGroupRefs = null;
        foreach ($accountGroupRefs as $accountGroupRef) {
            $this->addAccountGroupRef($accountGroupRef);
        }

        $this->collAccountGroupRefs = $accountGroupRefs;
        $this->collAccountGroupRefsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related AccountGroupRef objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related AccountGroupRef objects.
     * @throws PropelException
     */
    public function countAccountGroupRefs(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collAccountGroupRefsPartial && !$this->isNew();
        if (null === $this->collAccountGroupRefs || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collAccountGroupRefs) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getAccountGroupRefs());
            }
            $query = AccountGroupRefQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByAccount($this)
                ->count($con);
        }

        return count($this->collAccountGroupRefs);
    }

    /**
     * Method called to associate a AccountGroupRef object to this object
     * through the AccountGroupRef foreign key attribute.
     *
     * @param    AccountGroupRef $l AccountGroupRef
     * @return Account The current object (for fluent API support)
     */
    public function addAccountGroupRef(AccountGroupRef $l)
    {
        if ($this->collAccountGroupRefs === null) {
            $this->initAccountGroupRefs();
            $this->collAccountGroupRefsPartial = true;
        }

        if (!in_array($l, $this->collAccountGroupRefs->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddAccountGroupRef($l);

            if ($this->accountGroupRefsScheduledForDeletion and $this->accountGroupRefsScheduledForDeletion->contains($l)) {
                $this->accountGroupRefsScheduledForDeletion->remove($this->accountGroupRefsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	AccountGroupRef $accountGroupRef The accountGroupRef object to add.
     */
    protected function doAddAccountGroupRef($accountGroupRef)
    {
        $this->collAccountGroupRefs[]= $accountGroupRef;
        $accountGroupRef->setAccount($this);
    }

    /**
     * @param	AccountGroupRef $accountGroupRef The accountGroupRef object to remove.
     * @return Account The current object (for fluent API support)
     */
    public function removeAccountGroupRef($accountGroupRef)
    {
        if ($this->getAccountGroupRefs()->contains($accountGroupRef)) {
            $this->collAccountGroupRefs->remove($this->collAccountGroupRefs->search($accountGroupRef));
            if (null === $this->accountGroupRefsScheduledForDeletion) {
                $this->accountGroupRefsScheduledForDeletion = clone $this->collAccountGroupRefs;
                $this->accountGroupRefsScheduledForDeletion->clear();
            }
            $this->accountGroupRefsScheduledForDeletion[]= clone $accountGroupRef;
            $accountGroupRef->setAccount(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Account is new, it will return
     * an empty collection; or if this Account has previously
     * been saved, it will retrieve related AccountGroupRefs from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Account.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|AccountGroupRef[] List of AccountGroupRef objects
     */
    public function getAccountGroupRefsJoinAccountGroup($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = AccountGroupRefQuery::create(null, $criteria);
        $query->joinWith('AccountGroup', $join_behavior);

        return $this->getAccountGroupRefs($query, $con);
    }

    /**
     * Clears out the collPendingCalls collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Account The current object (for fluent API support)
     * @see        addPendingCalls()
     */
    public function clearPendingCalls()
    {
        $this->collPendingCalls = null; // important to set this to null since that means it is uninitialized
        $this->collPendingCallsPartial = null;

        return $this;
    }

    /**
     * reset is the collPendingCalls collection loaded partially
     *
     * @return void
     */
    public function resetPartialPendingCalls($v = true)
    {
        $this->collPendingCallsPartial = $v;
    }

    /**
     * Initializes the collPendingCalls collection.
     *
     * By default this just sets the collPendingCalls collection to an empty array (like clearcollPendingCalls());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initPendingCalls($overrideExisting = true)
    {
        if (null !== $this->collPendingCalls && !$overrideExisting) {
            return;
        }
        $this->collPendingCalls = new PropelObjectCollection();
        $this->collPendingCalls->setModel('PendingCall');
    }

    /**
     * Gets an array of PendingCall objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Account is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|PendingCall[] List of PendingCall objects
     * @throws PropelException
     */
    public function getPendingCalls($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collPendingCallsPartial && !$this->isNew();
        if (null === $this->collPendingCalls || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collPendingCalls) {
                // return empty collection
                $this->initPendingCalls();
            } else {
                $collPendingCalls = PendingCallQuery::create(null, $criteria)
                    ->filterByAccount($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collPendingCallsPartial && count($collPendingCalls)) {
                      $this->initPendingCalls(false);

                      foreach ($collPendingCalls as $obj) {
                        if (false == $this->collPendingCalls->contains($obj)) {
                          $this->collPendingCalls->append($obj);
                        }
                      }

                      $this->collPendingCallsPartial = true;
                    }

                    $collPendingCalls->getInternalIterator()->rewind();

                    return $collPendingCalls;
                }

                if ($partial && $this->collPendingCalls) {
                    foreach ($this->collPendingCalls as $obj) {
                        if ($obj->isNew()) {
                            $collPendingCalls[] = $obj;
                        }
                    }
                }

                $this->collPendingCalls = $collPendingCalls;
                $this->collPendingCallsPartial = false;
            }
        }

        return $this->collPendingCalls;
    }

    /**
     * Sets a collection of PendingCall objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $pendingCalls A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Account The current object (for fluent API support)
     */
    public function setPendingCalls(PropelCollection $pendingCalls, PropelPDO $con = null)
    {
        $pendingCallsToDelete = $this->getPendingCalls(new Criteria(), $con)->diff($pendingCalls);


        $this->pendingCallsScheduledForDeletion = $pendingCallsToDelete;

        foreach ($pendingCallsToDelete as $pendingCallRemoved) {
            $pendingCallRemoved->setAccount(null);
        }

        $this->collPendingCalls = null;
        foreach ($pendingCalls as $pendingCall) {
            $this->addPendingCall($pendingCall);
        }

        $this->collPendingCalls = $pendingCalls;
        $this->collPendingCallsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related PendingCall objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related PendingCall objects.
     * @throws PropelException
     */
    public function countPendingCalls(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collPendingCallsPartial && !$this->isNew();
        if (null === $this->collPendingCalls || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collPendingCalls) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getPendingCalls());
            }
            $query = PendingCallQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByAccount($this)
                ->count($con);
        }

        return count($this->collPendingCalls);
    }

    /**
     * Method called to associate a PendingCall object to this object
     * through the PendingCall foreign key attribute.
     *
     * @param    PendingCall $l PendingCall
     * @return Account The current object (for fluent API support)
     */
    public function addPendingCall(PendingCall $l)
    {
        if ($this->collPendingCalls === null) {
            $this->initPendingCalls();
            $this->collPendingCallsPartial = true;
        }

        if (!in_array($l, $this->collPendingCalls->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddPendingCall($l);

            if ($this->pendingCallsScheduledForDeletion and $this->pendingCallsScheduledForDeletion->contains($l)) {
                $this->pendingCallsScheduledForDeletion->remove($this->pendingCallsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	PendingCall $pendingCall The pendingCall object to add.
     */
    protected function doAddPendingCall($pendingCall)
    {
        $this->collPendingCalls[]= $pendingCall;
        $pendingCall->setAccount($this);
    }

    /**
     * @param	PendingCall $pendingCall The pendingCall object to remove.
     * @return Account The current object (for fluent API support)
     */
    public function removePendingCall($pendingCall)
    {
        if ($this->getPendingCalls()->contains($pendingCall)) {
            $this->collPendingCalls->remove($this->collPendingCalls->search($pendingCall));
            if (null === $this->pendingCallsScheduledForDeletion) {
                $this->pendingCallsScheduledForDeletion = clone $this->collPendingCalls;
                $this->pendingCallsScheduledForDeletion->clear();
            }
            $this->pendingCallsScheduledForDeletion[]= $pendingCall;
            $pendingCall->setAccount(null);
        }

        return $this;
    }

    /**
     * Clears out the collAsyncJobsRelatedByIssuerUserId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Account The current object (for fluent API support)
     * @see        addAsyncJobsRelatedByIssuerUserId()
     */
    public function clearAsyncJobsRelatedByIssuerUserId()
    {
        $this->collAsyncJobsRelatedByIssuerUserId = null; // important to set this to null since that means it is uninitialized
        $this->collAsyncJobsRelatedByIssuerUserIdPartial = null;

        return $this;
    }

    /**
     * reset is the collAsyncJobsRelatedByIssuerUserId collection loaded partially
     *
     * @return void
     */
    public function resetPartialAsyncJobsRelatedByIssuerUserId($v = true)
    {
        $this->collAsyncJobsRelatedByIssuerUserIdPartial = $v;
    }

    /**
     * Initializes the collAsyncJobsRelatedByIssuerUserId collection.
     *
     * By default this just sets the collAsyncJobsRelatedByIssuerUserId collection to an empty array (like clearcollAsyncJobsRelatedByIssuerUserId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initAsyncJobsRelatedByIssuerUserId($overrideExisting = true)
    {
        if (null !== $this->collAsyncJobsRelatedByIssuerUserId && !$overrideExisting) {
            return;
        }
        $this->collAsyncJobsRelatedByIssuerUserId = new PropelObjectCollection();
        $this->collAsyncJobsRelatedByIssuerUserId->setModel('AsyncJob');
    }

    /**
     * Gets an array of AsyncJob objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Account is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|AsyncJob[] List of AsyncJob objects
     * @throws PropelException
     */
    public function getAsyncJobsRelatedByIssuerUserId($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collAsyncJobsRelatedByIssuerUserIdPartial && !$this->isNew();
        if (null === $this->collAsyncJobsRelatedByIssuerUserId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collAsyncJobsRelatedByIssuerUserId) {
                // return empty collection
                $this->initAsyncJobsRelatedByIssuerUserId();
            } else {
                $collAsyncJobsRelatedByIssuerUserId = AsyncJobQuery::create(null, $criteria)
                    ->filterByAccountRelatedByIssuerUserId($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collAsyncJobsRelatedByIssuerUserIdPartial && count($collAsyncJobsRelatedByIssuerUserId)) {
                      $this->initAsyncJobsRelatedByIssuerUserId(false);

                      foreach ($collAsyncJobsRelatedByIssuerUserId as $obj) {
                        if (false == $this->collAsyncJobsRelatedByIssuerUserId->contains($obj)) {
                          $this->collAsyncJobsRelatedByIssuerUserId->append($obj);
                        }
                      }

                      $this->collAsyncJobsRelatedByIssuerUserIdPartial = true;
                    }

                    $collAsyncJobsRelatedByIssuerUserId->getInternalIterator()->rewind();

                    return $collAsyncJobsRelatedByIssuerUserId;
                }

                if ($partial && $this->collAsyncJobsRelatedByIssuerUserId) {
                    foreach ($this->collAsyncJobsRelatedByIssuerUserId as $obj) {
                        if ($obj->isNew()) {
                            $collAsyncJobsRelatedByIssuerUserId[] = $obj;
                        }
                    }
                }

                $this->collAsyncJobsRelatedByIssuerUserId = $collAsyncJobsRelatedByIssuerUserId;
                $this->collAsyncJobsRelatedByIssuerUserIdPartial = false;
            }
        }

        return $this->collAsyncJobsRelatedByIssuerUserId;
    }

    /**
     * Sets a collection of AsyncJobRelatedByIssuerUserId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $asyncJobsRelatedByIssuerUserId A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Account The current object (for fluent API support)
     */
    public function setAsyncJobsRelatedByIssuerUserId(PropelCollection $asyncJobsRelatedByIssuerUserId, PropelPDO $con = null)
    {
        $asyncJobsRelatedByIssuerUserIdToDelete = $this->getAsyncJobsRelatedByIssuerUserId(new Criteria(), $con)->diff($asyncJobsRelatedByIssuerUserId);


        $this->asyncJobsRelatedByIssuerUserIdScheduledForDeletion = $asyncJobsRelatedByIssuerUserIdToDelete;

        foreach ($asyncJobsRelatedByIssuerUserIdToDelete as $asyncJobRelatedByIssuerUserIdRemoved) {
            $asyncJobRelatedByIssuerUserIdRemoved->setAccountRelatedByIssuerUserId(null);
        }

        $this->collAsyncJobsRelatedByIssuerUserId = null;
        foreach ($asyncJobsRelatedByIssuerUserId as $asyncJobRelatedByIssuerUserId) {
            $this->addAsyncJobRelatedByIssuerUserId($asyncJobRelatedByIssuerUserId);
        }

        $this->collAsyncJobsRelatedByIssuerUserId = $asyncJobsRelatedByIssuerUserId;
        $this->collAsyncJobsRelatedByIssuerUserIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related AsyncJob objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related AsyncJob objects.
     * @throws PropelException
     */
    public function countAsyncJobsRelatedByIssuerUserId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collAsyncJobsRelatedByIssuerUserIdPartial && !$this->isNew();
        if (null === $this->collAsyncJobsRelatedByIssuerUserId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collAsyncJobsRelatedByIssuerUserId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getAsyncJobsRelatedByIssuerUserId());
            }
            $query = AsyncJobQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByAccountRelatedByIssuerUserId($this)
                ->count($con);
        }

        return count($this->collAsyncJobsRelatedByIssuerUserId);
    }

    /**
     * Method called to associate a AsyncJob object to this object
     * through the AsyncJob foreign key attribute.
     *
     * @param    AsyncJob $l AsyncJob
     * @return Account The current object (for fluent API support)
     */
    public function addAsyncJobRelatedByIssuerUserId(AsyncJob $l)
    {
        if ($this->collAsyncJobsRelatedByIssuerUserId === null) {
            $this->initAsyncJobsRelatedByIssuerUserId();
            $this->collAsyncJobsRelatedByIssuerUserIdPartial = true;
        }

        if (!in_array($l, $this->collAsyncJobsRelatedByIssuerUserId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddAsyncJobRelatedByIssuerUserId($l);

            if ($this->asyncJobsRelatedByIssuerUserIdScheduledForDeletion and $this->asyncJobsRelatedByIssuerUserIdScheduledForDeletion->contains($l)) {
                $this->asyncJobsRelatedByIssuerUserIdScheduledForDeletion->remove($this->asyncJobsRelatedByIssuerUserIdScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	AsyncJobRelatedByIssuerUserId $asyncJobRelatedByIssuerUserId The asyncJobRelatedByIssuerUserId object to add.
     */
    protected function doAddAsyncJobRelatedByIssuerUserId($asyncJobRelatedByIssuerUserId)
    {
        $this->collAsyncJobsRelatedByIssuerUserId[]= $asyncJobRelatedByIssuerUserId;
        $asyncJobRelatedByIssuerUserId->setAccountRelatedByIssuerUserId($this);
    }

    /**
     * @param	AsyncJobRelatedByIssuerUserId $asyncJobRelatedByIssuerUserId The asyncJobRelatedByIssuerUserId object to remove.
     * @return Account The current object (for fluent API support)
     */
    public function removeAsyncJobRelatedByIssuerUserId($asyncJobRelatedByIssuerUserId)
    {
        if ($this->getAsyncJobsRelatedByIssuerUserId()->contains($asyncJobRelatedByIssuerUserId)) {
            $this->collAsyncJobsRelatedByIssuerUserId->remove($this->collAsyncJobsRelatedByIssuerUserId->search($asyncJobRelatedByIssuerUserId));
            if (null === $this->asyncJobsRelatedByIssuerUserIdScheduledForDeletion) {
                $this->asyncJobsRelatedByIssuerUserIdScheduledForDeletion = clone $this->collAsyncJobsRelatedByIssuerUserId;
                $this->asyncJobsRelatedByIssuerUserIdScheduledForDeletion->clear();
            }
            $this->asyncJobsRelatedByIssuerUserIdScheduledForDeletion[]= $asyncJobRelatedByIssuerUserId;
            $asyncJobRelatedByIssuerUserId->setAccountRelatedByIssuerUserId(null);
        }

        return $this;
    }

    /**
     * Clears out the collUserNotificationsRelatedByUserId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Account The current object (for fluent API support)
     * @see        addUserNotificationsRelatedByUserId()
     */
    public function clearUserNotificationsRelatedByUserId()
    {
        $this->collUserNotificationsRelatedByUserId = null; // important to set this to null since that means it is uninitialized
        $this->collUserNotificationsRelatedByUserIdPartial = null;

        return $this;
    }

    /**
     * reset is the collUserNotificationsRelatedByUserId collection loaded partially
     *
     * @return void
     */
    public function resetPartialUserNotificationsRelatedByUserId($v = true)
    {
        $this->collUserNotificationsRelatedByUserIdPartial = $v;
    }

    /**
     * Initializes the collUserNotificationsRelatedByUserId collection.
     *
     * By default this just sets the collUserNotificationsRelatedByUserId collection to an empty array (like clearcollUserNotificationsRelatedByUserId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initUserNotificationsRelatedByUserId($overrideExisting = true)
    {
        if (null !== $this->collUserNotificationsRelatedByUserId && !$overrideExisting) {
            return;
        }
        $this->collUserNotificationsRelatedByUserId = new PropelObjectCollection();
        $this->collUserNotificationsRelatedByUserId->setModel('UserNotification');
    }

    /**
     * Gets an array of UserNotification objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Account is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|UserNotification[] List of UserNotification objects
     * @throws PropelException
     */
    public function getUserNotificationsRelatedByUserId($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collUserNotificationsRelatedByUserIdPartial && !$this->isNew();
        if (null === $this->collUserNotificationsRelatedByUserId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collUserNotificationsRelatedByUserId) {
                // return empty collection
                $this->initUserNotificationsRelatedByUserId();
            } else {
                $collUserNotificationsRelatedByUserId = UserNotificationQuery::create(null, $criteria)
                    ->filterByAccountRelatedByUserId($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collUserNotificationsRelatedByUserIdPartial && count($collUserNotificationsRelatedByUserId)) {
                      $this->initUserNotificationsRelatedByUserId(false);

                      foreach ($collUserNotificationsRelatedByUserId as $obj) {
                        if (false == $this->collUserNotificationsRelatedByUserId->contains($obj)) {
                          $this->collUserNotificationsRelatedByUserId->append($obj);
                        }
                      }

                      $this->collUserNotificationsRelatedByUserIdPartial = true;
                    }

                    $collUserNotificationsRelatedByUserId->getInternalIterator()->rewind();

                    return $collUserNotificationsRelatedByUserId;
                }

                if ($partial && $this->collUserNotificationsRelatedByUserId) {
                    foreach ($this->collUserNotificationsRelatedByUserId as $obj) {
                        if ($obj->isNew()) {
                            $collUserNotificationsRelatedByUserId[] = $obj;
                        }
                    }
                }

                $this->collUserNotificationsRelatedByUserId = $collUserNotificationsRelatedByUserId;
                $this->collUserNotificationsRelatedByUserIdPartial = false;
            }
        }

        return $this->collUserNotificationsRelatedByUserId;
    }

    /**
     * Sets a collection of UserNotificationRelatedByUserId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $userNotificationsRelatedByUserId A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Account The current object (for fluent API support)
     */
    public function setUserNotificationsRelatedByUserId(PropelCollection $userNotificationsRelatedByUserId, PropelPDO $con = null)
    {
        $userNotificationsRelatedByUserIdToDelete = $this->getUserNotificationsRelatedByUserId(new Criteria(), $con)->diff($userNotificationsRelatedByUserId);


        $this->userNotificationsRelatedByUserIdScheduledForDeletion = $userNotificationsRelatedByUserIdToDelete;

        foreach ($userNotificationsRelatedByUserIdToDelete as $userNotificationRelatedByUserIdRemoved) {
            $userNotificationRelatedByUserIdRemoved->setAccountRelatedByUserId(null);
        }

        $this->collUserNotificationsRelatedByUserId = null;
        foreach ($userNotificationsRelatedByUserId as $userNotificationRelatedByUserId) {
            $this->addUserNotificationRelatedByUserId($userNotificationRelatedByUserId);
        }

        $this->collUserNotificationsRelatedByUserId = $userNotificationsRelatedByUserId;
        $this->collUserNotificationsRelatedByUserIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related UserNotification objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related UserNotification objects.
     * @throws PropelException
     */
    public function countUserNotificationsRelatedByUserId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collUserNotificationsRelatedByUserIdPartial && !$this->isNew();
        if (null === $this->collUserNotificationsRelatedByUserId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collUserNotificationsRelatedByUserId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getUserNotificationsRelatedByUserId());
            }
            $query = UserNotificationQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByAccountRelatedByUserId($this)
                ->count($con);
        }

        return count($this->collUserNotificationsRelatedByUserId);
    }

    /**
     * Method called to associate a UserNotification object to this object
     * through the UserNotification foreign key attribute.
     *
     * @param    UserNotification $l UserNotification
     * @return Account The current object (for fluent API support)
     */
    public function addUserNotificationRelatedByUserId(UserNotification $l)
    {
        if ($this->collUserNotificationsRelatedByUserId === null) {
            $this->initUserNotificationsRelatedByUserId();
            $this->collUserNotificationsRelatedByUserIdPartial = true;
        }

        if (!in_array($l, $this->collUserNotificationsRelatedByUserId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddUserNotificationRelatedByUserId($l);

            if ($this->userNotificationsRelatedByUserIdScheduledForDeletion and $this->userNotificationsRelatedByUserIdScheduledForDeletion->contains($l)) {
                $this->userNotificationsRelatedByUserIdScheduledForDeletion->remove($this->userNotificationsRelatedByUserIdScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	UserNotificationRelatedByUserId $userNotificationRelatedByUserId The userNotificationRelatedByUserId object to add.
     */
    protected function doAddUserNotificationRelatedByUserId($userNotificationRelatedByUserId)
    {
        $this->collUserNotificationsRelatedByUserId[]= $userNotificationRelatedByUserId;
        $userNotificationRelatedByUserId->setAccountRelatedByUserId($this);
    }

    /**
     * @param	UserNotificationRelatedByUserId $userNotificationRelatedByUserId The userNotificationRelatedByUserId object to remove.
     * @return Account The current object (for fluent API support)
     */
    public function removeUserNotificationRelatedByUserId($userNotificationRelatedByUserId)
    {
        if ($this->getUserNotificationsRelatedByUserId()->contains($userNotificationRelatedByUserId)) {
            $this->collUserNotificationsRelatedByUserId->remove($this->collUserNotificationsRelatedByUserId->search($userNotificationRelatedByUserId));
            if (null === $this->userNotificationsRelatedByUserIdScheduledForDeletion) {
                $this->userNotificationsRelatedByUserIdScheduledForDeletion = clone $this->collUserNotificationsRelatedByUserId;
                $this->userNotificationsRelatedByUserIdScheduledForDeletion->clear();
            }
            $this->userNotificationsRelatedByUserIdScheduledForDeletion[]= $userNotificationRelatedByUserId;
            $userNotificationRelatedByUserId->setAccountRelatedByUserId(null);
        }

        return $this;
    }

    /**
     * Clears out the collAsyncJobsRelatedByCreationUserId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Account The current object (for fluent API support)
     * @see        addAsyncJobsRelatedByCreationUserId()
     */
    public function clearAsyncJobsRelatedByCreationUserId()
    {
        $this->collAsyncJobsRelatedByCreationUserId = null; // important to set this to null since that means it is uninitialized
        $this->collAsyncJobsRelatedByCreationUserIdPartial = null;

        return $this;
    }

    /**
     * reset is the collAsyncJobsRelatedByCreationUserId collection loaded partially
     *
     * @return void
     */
    public function resetPartialAsyncJobsRelatedByCreationUserId($v = true)
    {
        $this->collAsyncJobsRelatedByCreationUserIdPartial = $v;
    }

    /**
     * Initializes the collAsyncJobsRelatedByCreationUserId collection.
     *
     * By default this just sets the collAsyncJobsRelatedByCreationUserId collection to an empty array (like clearcollAsyncJobsRelatedByCreationUserId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initAsyncJobsRelatedByCreationUserId($overrideExisting = true)
    {
        if (null !== $this->collAsyncJobsRelatedByCreationUserId && !$overrideExisting) {
            return;
        }
        $this->collAsyncJobsRelatedByCreationUserId = new PropelObjectCollection();
        $this->collAsyncJobsRelatedByCreationUserId->setModel('AsyncJob');
    }

    /**
     * Gets an array of AsyncJob objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Account is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|AsyncJob[] List of AsyncJob objects
     * @throws PropelException
     */
    public function getAsyncJobsRelatedByCreationUserId($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collAsyncJobsRelatedByCreationUserIdPartial && !$this->isNew();
        if (null === $this->collAsyncJobsRelatedByCreationUserId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collAsyncJobsRelatedByCreationUserId) {
                // return empty collection
                $this->initAsyncJobsRelatedByCreationUserId();
            } else {
                $collAsyncJobsRelatedByCreationUserId = AsyncJobQuery::create(null, $criteria)
                    ->filterByAccountRelatedByCreationUserId($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collAsyncJobsRelatedByCreationUserIdPartial && count($collAsyncJobsRelatedByCreationUserId)) {
                      $this->initAsyncJobsRelatedByCreationUserId(false);

                      foreach ($collAsyncJobsRelatedByCreationUserId as $obj) {
                        if (false == $this->collAsyncJobsRelatedByCreationUserId->contains($obj)) {
                          $this->collAsyncJobsRelatedByCreationUserId->append($obj);
                        }
                      }

                      $this->collAsyncJobsRelatedByCreationUserIdPartial = true;
                    }

                    $collAsyncJobsRelatedByCreationUserId->getInternalIterator()->rewind();

                    return $collAsyncJobsRelatedByCreationUserId;
                }

                if ($partial && $this->collAsyncJobsRelatedByCreationUserId) {
                    foreach ($this->collAsyncJobsRelatedByCreationUserId as $obj) {
                        if ($obj->isNew()) {
                            $collAsyncJobsRelatedByCreationUserId[] = $obj;
                        }
                    }
                }

                $this->collAsyncJobsRelatedByCreationUserId = $collAsyncJobsRelatedByCreationUserId;
                $this->collAsyncJobsRelatedByCreationUserIdPartial = false;
            }
        }

        return $this->collAsyncJobsRelatedByCreationUserId;
    }

    /**
     * Sets a collection of AsyncJobRelatedByCreationUserId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $asyncJobsRelatedByCreationUserId A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Account The current object (for fluent API support)
     */
    public function setAsyncJobsRelatedByCreationUserId(PropelCollection $asyncJobsRelatedByCreationUserId, PropelPDO $con = null)
    {
        $asyncJobsRelatedByCreationUserIdToDelete = $this->getAsyncJobsRelatedByCreationUserId(new Criteria(), $con)->diff($asyncJobsRelatedByCreationUserId);


        $this->asyncJobsRelatedByCreationUserIdScheduledForDeletion = $asyncJobsRelatedByCreationUserIdToDelete;

        foreach ($asyncJobsRelatedByCreationUserIdToDelete as $asyncJobRelatedByCreationUserIdRemoved) {
            $asyncJobRelatedByCreationUserIdRemoved->setAccountRelatedByCreationUserId(null);
        }

        $this->collAsyncJobsRelatedByCreationUserId = null;
        foreach ($asyncJobsRelatedByCreationUserId as $asyncJobRelatedByCreationUserId) {
            $this->addAsyncJobRelatedByCreationUserId($asyncJobRelatedByCreationUserId);
        }

        $this->collAsyncJobsRelatedByCreationUserId = $asyncJobsRelatedByCreationUserId;
        $this->collAsyncJobsRelatedByCreationUserIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related AsyncJob objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related AsyncJob objects.
     * @throws PropelException
     */
    public function countAsyncJobsRelatedByCreationUserId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collAsyncJobsRelatedByCreationUserIdPartial && !$this->isNew();
        if (null === $this->collAsyncJobsRelatedByCreationUserId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collAsyncJobsRelatedByCreationUserId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getAsyncJobsRelatedByCreationUserId());
            }
            $query = AsyncJobQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByAccountRelatedByCreationUserId($this)
                ->count($con);
        }

        return count($this->collAsyncJobsRelatedByCreationUserId);
    }

    /**
     * Method called to associate a AsyncJob object to this object
     * through the AsyncJob foreign key attribute.
     *
     * @param    AsyncJob $l AsyncJob
     * @return Account The current object (for fluent API support)
     */
    public function addAsyncJobRelatedByCreationUserId(AsyncJob $l)
    {
        if ($this->collAsyncJobsRelatedByCreationUserId === null) {
            $this->initAsyncJobsRelatedByCreationUserId();
            $this->collAsyncJobsRelatedByCreationUserIdPartial = true;
        }

        if (!in_array($l, $this->collAsyncJobsRelatedByCreationUserId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddAsyncJobRelatedByCreationUserId($l);

            if ($this->asyncJobsRelatedByCreationUserIdScheduledForDeletion and $this->asyncJobsRelatedByCreationUserIdScheduledForDeletion->contains($l)) {
                $this->asyncJobsRelatedByCreationUserIdScheduledForDeletion->remove($this->asyncJobsRelatedByCreationUserIdScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	AsyncJobRelatedByCreationUserId $asyncJobRelatedByCreationUserId The asyncJobRelatedByCreationUserId object to add.
     */
    protected function doAddAsyncJobRelatedByCreationUserId($asyncJobRelatedByCreationUserId)
    {
        $this->collAsyncJobsRelatedByCreationUserId[]= $asyncJobRelatedByCreationUserId;
        $asyncJobRelatedByCreationUserId->setAccountRelatedByCreationUserId($this);
    }

    /**
     * @param	AsyncJobRelatedByCreationUserId $asyncJobRelatedByCreationUserId The asyncJobRelatedByCreationUserId object to remove.
     * @return Account The current object (for fluent API support)
     */
    public function removeAsyncJobRelatedByCreationUserId($asyncJobRelatedByCreationUserId)
    {
        if ($this->getAsyncJobsRelatedByCreationUserId()->contains($asyncJobRelatedByCreationUserId)) {
            $this->collAsyncJobsRelatedByCreationUserId->remove($this->collAsyncJobsRelatedByCreationUserId->search($asyncJobRelatedByCreationUserId));
            if (null === $this->asyncJobsRelatedByCreationUserIdScheduledForDeletion) {
                $this->asyncJobsRelatedByCreationUserIdScheduledForDeletion = clone $this->collAsyncJobsRelatedByCreationUserId;
                $this->asyncJobsRelatedByCreationUserIdScheduledForDeletion->clear();
            }
            $this->asyncJobsRelatedByCreationUserIdScheduledForDeletion[]= $asyncJobRelatedByCreationUserId;
            $asyncJobRelatedByCreationUserId->setAccountRelatedByCreationUserId(null);
        }

        return $this;
    }

    /**
     * Clears out the collAsyncJobsRelatedByUpdateUserId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Account The current object (for fluent API support)
     * @see        addAsyncJobsRelatedByUpdateUserId()
     */
    public function clearAsyncJobsRelatedByUpdateUserId()
    {
        $this->collAsyncJobsRelatedByUpdateUserId = null; // important to set this to null since that means it is uninitialized
        $this->collAsyncJobsRelatedByUpdateUserIdPartial = null;

        return $this;
    }

    /**
     * reset is the collAsyncJobsRelatedByUpdateUserId collection loaded partially
     *
     * @return void
     */
    public function resetPartialAsyncJobsRelatedByUpdateUserId($v = true)
    {
        $this->collAsyncJobsRelatedByUpdateUserIdPartial = $v;
    }

    /**
     * Initializes the collAsyncJobsRelatedByUpdateUserId collection.
     *
     * By default this just sets the collAsyncJobsRelatedByUpdateUserId collection to an empty array (like clearcollAsyncJobsRelatedByUpdateUserId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initAsyncJobsRelatedByUpdateUserId($overrideExisting = true)
    {
        if (null !== $this->collAsyncJobsRelatedByUpdateUserId && !$overrideExisting) {
            return;
        }
        $this->collAsyncJobsRelatedByUpdateUserId = new PropelObjectCollection();
        $this->collAsyncJobsRelatedByUpdateUserId->setModel('AsyncJob');
    }

    /**
     * Gets an array of AsyncJob objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Account is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|AsyncJob[] List of AsyncJob objects
     * @throws PropelException
     */
    public function getAsyncJobsRelatedByUpdateUserId($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collAsyncJobsRelatedByUpdateUserIdPartial && !$this->isNew();
        if (null === $this->collAsyncJobsRelatedByUpdateUserId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collAsyncJobsRelatedByUpdateUserId) {
                // return empty collection
                $this->initAsyncJobsRelatedByUpdateUserId();
            } else {
                $collAsyncJobsRelatedByUpdateUserId = AsyncJobQuery::create(null, $criteria)
                    ->filterByAccountRelatedByUpdateUserId($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collAsyncJobsRelatedByUpdateUserIdPartial && count($collAsyncJobsRelatedByUpdateUserId)) {
                      $this->initAsyncJobsRelatedByUpdateUserId(false);

                      foreach ($collAsyncJobsRelatedByUpdateUserId as $obj) {
                        if (false == $this->collAsyncJobsRelatedByUpdateUserId->contains($obj)) {
                          $this->collAsyncJobsRelatedByUpdateUserId->append($obj);
                        }
                      }

                      $this->collAsyncJobsRelatedByUpdateUserIdPartial = true;
                    }

                    $collAsyncJobsRelatedByUpdateUserId->getInternalIterator()->rewind();

                    return $collAsyncJobsRelatedByUpdateUserId;
                }

                if ($partial && $this->collAsyncJobsRelatedByUpdateUserId) {
                    foreach ($this->collAsyncJobsRelatedByUpdateUserId as $obj) {
                        if ($obj->isNew()) {
                            $collAsyncJobsRelatedByUpdateUserId[] = $obj;
                        }
                    }
                }

                $this->collAsyncJobsRelatedByUpdateUserId = $collAsyncJobsRelatedByUpdateUserId;
                $this->collAsyncJobsRelatedByUpdateUserIdPartial = false;
            }
        }

        return $this->collAsyncJobsRelatedByUpdateUserId;
    }

    /**
     * Sets a collection of AsyncJobRelatedByUpdateUserId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $asyncJobsRelatedByUpdateUserId A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Account The current object (for fluent API support)
     */
    public function setAsyncJobsRelatedByUpdateUserId(PropelCollection $asyncJobsRelatedByUpdateUserId, PropelPDO $con = null)
    {
        $asyncJobsRelatedByUpdateUserIdToDelete = $this->getAsyncJobsRelatedByUpdateUserId(new Criteria(), $con)->diff($asyncJobsRelatedByUpdateUserId);


        $this->asyncJobsRelatedByUpdateUserIdScheduledForDeletion = $asyncJobsRelatedByUpdateUserIdToDelete;

        foreach ($asyncJobsRelatedByUpdateUserIdToDelete as $asyncJobRelatedByUpdateUserIdRemoved) {
            $asyncJobRelatedByUpdateUserIdRemoved->setAccountRelatedByUpdateUserId(null);
        }

        $this->collAsyncJobsRelatedByUpdateUserId = null;
        foreach ($asyncJobsRelatedByUpdateUserId as $asyncJobRelatedByUpdateUserId) {
            $this->addAsyncJobRelatedByUpdateUserId($asyncJobRelatedByUpdateUserId);
        }

        $this->collAsyncJobsRelatedByUpdateUserId = $asyncJobsRelatedByUpdateUserId;
        $this->collAsyncJobsRelatedByUpdateUserIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related AsyncJob objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related AsyncJob objects.
     * @throws PropelException
     */
    public function countAsyncJobsRelatedByUpdateUserId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collAsyncJobsRelatedByUpdateUserIdPartial && !$this->isNew();
        if (null === $this->collAsyncJobsRelatedByUpdateUserId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collAsyncJobsRelatedByUpdateUserId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getAsyncJobsRelatedByUpdateUserId());
            }
            $query = AsyncJobQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByAccountRelatedByUpdateUserId($this)
                ->count($con);
        }

        return count($this->collAsyncJobsRelatedByUpdateUserId);
    }

    /**
     * Method called to associate a AsyncJob object to this object
     * through the AsyncJob foreign key attribute.
     *
     * @param    AsyncJob $l AsyncJob
     * @return Account The current object (for fluent API support)
     */
    public function addAsyncJobRelatedByUpdateUserId(AsyncJob $l)
    {
        if ($this->collAsyncJobsRelatedByUpdateUserId === null) {
            $this->initAsyncJobsRelatedByUpdateUserId();
            $this->collAsyncJobsRelatedByUpdateUserIdPartial = true;
        }

        if (!in_array($l, $this->collAsyncJobsRelatedByUpdateUserId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddAsyncJobRelatedByUpdateUserId($l);

            if ($this->asyncJobsRelatedByUpdateUserIdScheduledForDeletion and $this->asyncJobsRelatedByUpdateUserIdScheduledForDeletion->contains($l)) {
                $this->asyncJobsRelatedByUpdateUserIdScheduledForDeletion->remove($this->asyncJobsRelatedByUpdateUserIdScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	AsyncJobRelatedByUpdateUserId $asyncJobRelatedByUpdateUserId The asyncJobRelatedByUpdateUserId object to add.
     */
    protected function doAddAsyncJobRelatedByUpdateUserId($asyncJobRelatedByUpdateUserId)
    {
        $this->collAsyncJobsRelatedByUpdateUserId[]= $asyncJobRelatedByUpdateUserId;
        $asyncJobRelatedByUpdateUserId->setAccountRelatedByUpdateUserId($this);
    }

    /**
     * @param	AsyncJobRelatedByUpdateUserId $asyncJobRelatedByUpdateUserId The asyncJobRelatedByUpdateUserId object to remove.
     * @return Account The current object (for fluent API support)
     */
    public function removeAsyncJobRelatedByUpdateUserId($asyncJobRelatedByUpdateUserId)
    {
        if ($this->getAsyncJobsRelatedByUpdateUserId()->contains($asyncJobRelatedByUpdateUserId)) {
            $this->collAsyncJobsRelatedByUpdateUserId->remove($this->collAsyncJobsRelatedByUpdateUserId->search($asyncJobRelatedByUpdateUserId));
            if (null === $this->asyncJobsRelatedByUpdateUserIdScheduledForDeletion) {
                $this->asyncJobsRelatedByUpdateUserIdScheduledForDeletion = clone $this->collAsyncJobsRelatedByUpdateUserId;
                $this->asyncJobsRelatedByUpdateUserIdScheduledForDeletion->clear();
            }
            $this->asyncJobsRelatedByUpdateUserIdScheduledForDeletion[]= $asyncJobRelatedByUpdateUserId;
            $asyncJobRelatedByUpdateUserId->setAccountRelatedByUpdateUserId(null);
        }

        return $this;
    }

    /**
     * Clears out the collUserNotificationsRelatedByCreationUserId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Account The current object (for fluent API support)
     * @see        addUserNotificationsRelatedByCreationUserId()
     */
    public function clearUserNotificationsRelatedByCreationUserId()
    {
        $this->collUserNotificationsRelatedByCreationUserId = null; // important to set this to null since that means it is uninitialized
        $this->collUserNotificationsRelatedByCreationUserIdPartial = null;

        return $this;
    }

    /**
     * reset is the collUserNotificationsRelatedByCreationUserId collection loaded partially
     *
     * @return void
     */
    public function resetPartialUserNotificationsRelatedByCreationUserId($v = true)
    {
        $this->collUserNotificationsRelatedByCreationUserIdPartial = $v;
    }

    /**
     * Initializes the collUserNotificationsRelatedByCreationUserId collection.
     *
     * By default this just sets the collUserNotificationsRelatedByCreationUserId collection to an empty array (like clearcollUserNotificationsRelatedByCreationUserId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initUserNotificationsRelatedByCreationUserId($overrideExisting = true)
    {
        if (null !== $this->collUserNotificationsRelatedByCreationUserId && !$overrideExisting) {
            return;
        }
        $this->collUserNotificationsRelatedByCreationUserId = new PropelObjectCollection();
        $this->collUserNotificationsRelatedByCreationUserId->setModel('UserNotification');
    }

    /**
     * Gets an array of UserNotification objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Account is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|UserNotification[] List of UserNotification objects
     * @throws PropelException
     */
    public function getUserNotificationsRelatedByCreationUserId($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collUserNotificationsRelatedByCreationUserIdPartial && !$this->isNew();
        if (null === $this->collUserNotificationsRelatedByCreationUserId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collUserNotificationsRelatedByCreationUserId) {
                // return empty collection
                $this->initUserNotificationsRelatedByCreationUserId();
            } else {
                $collUserNotificationsRelatedByCreationUserId = UserNotificationQuery::create(null, $criteria)
                    ->filterByAccountRelatedByCreationUserId($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collUserNotificationsRelatedByCreationUserIdPartial && count($collUserNotificationsRelatedByCreationUserId)) {
                      $this->initUserNotificationsRelatedByCreationUserId(false);

                      foreach ($collUserNotificationsRelatedByCreationUserId as $obj) {
                        if (false == $this->collUserNotificationsRelatedByCreationUserId->contains($obj)) {
                          $this->collUserNotificationsRelatedByCreationUserId->append($obj);
                        }
                      }

                      $this->collUserNotificationsRelatedByCreationUserIdPartial = true;
                    }

                    $collUserNotificationsRelatedByCreationUserId->getInternalIterator()->rewind();

                    return $collUserNotificationsRelatedByCreationUserId;
                }

                if ($partial && $this->collUserNotificationsRelatedByCreationUserId) {
                    foreach ($this->collUserNotificationsRelatedByCreationUserId as $obj) {
                        if ($obj->isNew()) {
                            $collUserNotificationsRelatedByCreationUserId[] = $obj;
                        }
                    }
                }

                $this->collUserNotificationsRelatedByCreationUserId = $collUserNotificationsRelatedByCreationUserId;
                $this->collUserNotificationsRelatedByCreationUserIdPartial = false;
            }
        }

        return $this->collUserNotificationsRelatedByCreationUserId;
    }

    /**
     * Sets a collection of UserNotificationRelatedByCreationUserId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $userNotificationsRelatedByCreationUserId A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Account The current object (for fluent API support)
     */
    public function setUserNotificationsRelatedByCreationUserId(PropelCollection $userNotificationsRelatedByCreationUserId, PropelPDO $con = null)
    {
        $userNotificationsRelatedByCreationUserIdToDelete = $this->getUserNotificationsRelatedByCreationUserId(new Criteria(), $con)->diff($userNotificationsRelatedByCreationUserId);


        $this->userNotificationsRelatedByCreationUserIdScheduledForDeletion = $userNotificationsRelatedByCreationUserIdToDelete;

        foreach ($userNotificationsRelatedByCreationUserIdToDelete as $userNotificationRelatedByCreationUserIdRemoved) {
            $userNotificationRelatedByCreationUserIdRemoved->setAccountRelatedByCreationUserId(null);
        }

        $this->collUserNotificationsRelatedByCreationUserId = null;
        foreach ($userNotificationsRelatedByCreationUserId as $userNotificationRelatedByCreationUserId) {
            $this->addUserNotificationRelatedByCreationUserId($userNotificationRelatedByCreationUserId);
        }

        $this->collUserNotificationsRelatedByCreationUserId = $userNotificationsRelatedByCreationUserId;
        $this->collUserNotificationsRelatedByCreationUserIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related UserNotification objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related UserNotification objects.
     * @throws PropelException
     */
    public function countUserNotificationsRelatedByCreationUserId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collUserNotificationsRelatedByCreationUserIdPartial && !$this->isNew();
        if (null === $this->collUserNotificationsRelatedByCreationUserId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collUserNotificationsRelatedByCreationUserId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getUserNotificationsRelatedByCreationUserId());
            }
            $query = UserNotificationQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByAccountRelatedByCreationUserId($this)
                ->count($con);
        }

        return count($this->collUserNotificationsRelatedByCreationUserId);
    }

    /**
     * Method called to associate a UserNotification object to this object
     * through the UserNotification foreign key attribute.
     *
     * @param    UserNotification $l UserNotification
     * @return Account The current object (for fluent API support)
     */
    public function addUserNotificationRelatedByCreationUserId(UserNotification $l)
    {
        if ($this->collUserNotificationsRelatedByCreationUserId === null) {
            $this->initUserNotificationsRelatedByCreationUserId();
            $this->collUserNotificationsRelatedByCreationUserIdPartial = true;
        }

        if (!in_array($l, $this->collUserNotificationsRelatedByCreationUserId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddUserNotificationRelatedByCreationUserId($l);

            if ($this->userNotificationsRelatedByCreationUserIdScheduledForDeletion and $this->userNotificationsRelatedByCreationUserIdScheduledForDeletion->contains($l)) {
                $this->userNotificationsRelatedByCreationUserIdScheduledForDeletion->remove($this->userNotificationsRelatedByCreationUserIdScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	UserNotificationRelatedByCreationUserId $userNotificationRelatedByCreationUserId The userNotificationRelatedByCreationUserId object to add.
     */
    protected function doAddUserNotificationRelatedByCreationUserId($userNotificationRelatedByCreationUserId)
    {
        $this->collUserNotificationsRelatedByCreationUserId[]= $userNotificationRelatedByCreationUserId;
        $userNotificationRelatedByCreationUserId->setAccountRelatedByCreationUserId($this);
    }

    /**
     * @param	UserNotificationRelatedByCreationUserId $userNotificationRelatedByCreationUserId The userNotificationRelatedByCreationUserId object to remove.
     * @return Account The current object (for fluent API support)
     */
    public function removeUserNotificationRelatedByCreationUserId($userNotificationRelatedByCreationUserId)
    {
        if ($this->getUserNotificationsRelatedByCreationUserId()->contains($userNotificationRelatedByCreationUserId)) {
            $this->collUserNotificationsRelatedByCreationUserId->remove($this->collUserNotificationsRelatedByCreationUserId->search($userNotificationRelatedByCreationUserId));
            if (null === $this->userNotificationsRelatedByCreationUserIdScheduledForDeletion) {
                $this->userNotificationsRelatedByCreationUserIdScheduledForDeletion = clone $this->collUserNotificationsRelatedByCreationUserId;
                $this->userNotificationsRelatedByCreationUserIdScheduledForDeletion->clear();
            }
            $this->userNotificationsRelatedByCreationUserIdScheduledForDeletion[]= $userNotificationRelatedByCreationUserId;
            $userNotificationRelatedByCreationUserId->setAccountRelatedByCreationUserId(null);
        }

        return $this;
    }

    /**
     * Clears out the collUserNotificationsRelatedByUpdateUserId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Account The current object (for fluent API support)
     * @see        addUserNotificationsRelatedByUpdateUserId()
     */
    public function clearUserNotificationsRelatedByUpdateUserId()
    {
        $this->collUserNotificationsRelatedByUpdateUserId = null; // important to set this to null since that means it is uninitialized
        $this->collUserNotificationsRelatedByUpdateUserIdPartial = null;

        return $this;
    }

    /**
     * reset is the collUserNotificationsRelatedByUpdateUserId collection loaded partially
     *
     * @return void
     */
    public function resetPartialUserNotificationsRelatedByUpdateUserId($v = true)
    {
        $this->collUserNotificationsRelatedByUpdateUserIdPartial = $v;
    }

    /**
     * Initializes the collUserNotificationsRelatedByUpdateUserId collection.
     *
     * By default this just sets the collUserNotificationsRelatedByUpdateUserId collection to an empty array (like clearcollUserNotificationsRelatedByUpdateUserId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initUserNotificationsRelatedByUpdateUserId($overrideExisting = true)
    {
        if (null !== $this->collUserNotificationsRelatedByUpdateUserId && !$overrideExisting) {
            return;
        }
        $this->collUserNotificationsRelatedByUpdateUserId = new PropelObjectCollection();
        $this->collUserNotificationsRelatedByUpdateUserId->setModel('UserNotification');
    }

    /**
     * Gets an array of UserNotification objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Account is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|UserNotification[] List of UserNotification objects
     * @throws PropelException
     */
    public function getUserNotificationsRelatedByUpdateUserId($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collUserNotificationsRelatedByUpdateUserIdPartial && !$this->isNew();
        if (null === $this->collUserNotificationsRelatedByUpdateUserId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collUserNotificationsRelatedByUpdateUserId) {
                // return empty collection
                $this->initUserNotificationsRelatedByUpdateUserId();
            } else {
                $collUserNotificationsRelatedByUpdateUserId = UserNotificationQuery::create(null, $criteria)
                    ->filterByAccountRelatedByUpdateUserId($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collUserNotificationsRelatedByUpdateUserIdPartial && count($collUserNotificationsRelatedByUpdateUserId)) {
                      $this->initUserNotificationsRelatedByUpdateUserId(false);

                      foreach ($collUserNotificationsRelatedByUpdateUserId as $obj) {
                        if (false == $this->collUserNotificationsRelatedByUpdateUserId->contains($obj)) {
                          $this->collUserNotificationsRelatedByUpdateUserId->append($obj);
                        }
                      }

                      $this->collUserNotificationsRelatedByUpdateUserIdPartial = true;
                    }

                    $collUserNotificationsRelatedByUpdateUserId->getInternalIterator()->rewind();

                    return $collUserNotificationsRelatedByUpdateUserId;
                }

                if ($partial && $this->collUserNotificationsRelatedByUpdateUserId) {
                    foreach ($this->collUserNotificationsRelatedByUpdateUserId as $obj) {
                        if ($obj->isNew()) {
                            $collUserNotificationsRelatedByUpdateUserId[] = $obj;
                        }
                    }
                }

                $this->collUserNotificationsRelatedByUpdateUserId = $collUserNotificationsRelatedByUpdateUserId;
                $this->collUserNotificationsRelatedByUpdateUserIdPartial = false;
            }
        }

        return $this->collUserNotificationsRelatedByUpdateUserId;
    }

    /**
     * Sets a collection of UserNotificationRelatedByUpdateUserId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $userNotificationsRelatedByUpdateUserId A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Account The current object (for fluent API support)
     */
    public function setUserNotificationsRelatedByUpdateUserId(PropelCollection $userNotificationsRelatedByUpdateUserId, PropelPDO $con = null)
    {
        $userNotificationsRelatedByUpdateUserIdToDelete = $this->getUserNotificationsRelatedByUpdateUserId(new Criteria(), $con)->diff($userNotificationsRelatedByUpdateUserId);


        $this->userNotificationsRelatedByUpdateUserIdScheduledForDeletion = $userNotificationsRelatedByUpdateUserIdToDelete;

        foreach ($userNotificationsRelatedByUpdateUserIdToDelete as $userNotificationRelatedByUpdateUserIdRemoved) {
            $userNotificationRelatedByUpdateUserIdRemoved->setAccountRelatedByUpdateUserId(null);
        }

        $this->collUserNotificationsRelatedByUpdateUserId = null;
        foreach ($userNotificationsRelatedByUpdateUserId as $userNotificationRelatedByUpdateUserId) {
            $this->addUserNotificationRelatedByUpdateUserId($userNotificationRelatedByUpdateUserId);
        }

        $this->collUserNotificationsRelatedByUpdateUserId = $userNotificationsRelatedByUpdateUserId;
        $this->collUserNotificationsRelatedByUpdateUserIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related UserNotification objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related UserNotification objects.
     * @throws PropelException
     */
    public function countUserNotificationsRelatedByUpdateUserId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collUserNotificationsRelatedByUpdateUserIdPartial && !$this->isNew();
        if (null === $this->collUserNotificationsRelatedByUpdateUserId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collUserNotificationsRelatedByUpdateUserId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getUserNotificationsRelatedByUpdateUserId());
            }
            $query = UserNotificationQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByAccountRelatedByUpdateUserId($this)
                ->count($con);
        }

        return count($this->collUserNotificationsRelatedByUpdateUserId);
    }

    /**
     * Method called to associate a UserNotification object to this object
     * through the UserNotification foreign key attribute.
     *
     * @param    UserNotification $l UserNotification
     * @return Account The current object (for fluent API support)
     */
    public function addUserNotificationRelatedByUpdateUserId(UserNotification $l)
    {
        if ($this->collUserNotificationsRelatedByUpdateUserId === null) {
            $this->initUserNotificationsRelatedByUpdateUserId();
            $this->collUserNotificationsRelatedByUpdateUserIdPartial = true;
        }

        if (!in_array($l, $this->collUserNotificationsRelatedByUpdateUserId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddUserNotificationRelatedByUpdateUserId($l);

            if ($this->userNotificationsRelatedByUpdateUserIdScheduledForDeletion and $this->userNotificationsRelatedByUpdateUserIdScheduledForDeletion->contains($l)) {
                $this->userNotificationsRelatedByUpdateUserIdScheduledForDeletion->remove($this->userNotificationsRelatedByUpdateUserIdScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	UserNotificationRelatedByUpdateUserId $userNotificationRelatedByUpdateUserId The userNotificationRelatedByUpdateUserId object to add.
     */
    protected function doAddUserNotificationRelatedByUpdateUserId($userNotificationRelatedByUpdateUserId)
    {
        $this->collUserNotificationsRelatedByUpdateUserId[]= $userNotificationRelatedByUpdateUserId;
        $userNotificationRelatedByUpdateUserId->setAccountRelatedByUpdateUserId($this);
    }

    /**
     * @param	UserNotificationRelatedByUpdateUserId $userNotificationRelatedByUpdateUserId The userNotificationRelatedByUpdateUserId object to remove.
     * @return Account The current object (for fluent API support)
     */
    public function removeUserNotificationRelatedByUpdateUserId($userNotificationRelatedByUpdateUserId)
    {
        if ($this->getUserNotificationsRelatedByUpdateUserId()->contains($userNotificationRelatedByUpdateUserId)) {
            $this->collUserNotificationsRelatedByUpdateUserId->remove($this->collUserNotificationsRelatedByUpdateUserId->search($userNotificationRelatedByUpdateUserId));
            if (null === $this->userNotificationsRelatedByUpdateUserIdScheduledForDeletion) {
                $this->userNotificationsRelatedByUpdateUserIdScheduledForDeletion = clone $this->collUserNotificationsRelatedByUpdateUserId;
                $this->userNotificationsRelatedByUpdateUserIdScheduledForDeletion->clear();
            }
            $this->userNotificationsRelatedByUpdateUserIdScheduledForDeletion[]= $userNotificationRelatedByUpdateUserId;
            $userNotificationRelatedByUpdateUserId->setAccountRelatedByUpdateUserId(null);
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->account_id = null;
        $this->login_name = null;
        $this->hashed_password = null;
        $this->type = null;
        $this->first_name = null;
        $this->last_name = null;
        $this->sex = null;
        $this->email = null;
        $this->telephone = null;
        $this->mobile = null;
        $this->default_locale = null;
        $this->company_name = null;
        $this->validity = null;
        $this->roles = null;
        $this->last_password_update = null;
        $this->validate_method = null;
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
            if ($this->collAccountSettings) {
                foreach ($this->collAccountSettings as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collAccountProfileRefs) {
                foreach ($this->collAccountProfileRefs as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collAccountGroupRefs) {
                foreach ($this->collAccountGroupRefs as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collPendingCalls) {
                foreach ($this->collPendingCalls as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collAsyncJobsRelatedByIssuerUserId) {
                foreach ($this->collAsyncJobsRelatedByIssuerUserId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collUserNotificationsRelatedByUserId) {
                foreach ($this->collUserNotificationsRelatedByUserId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collAsyncJobsRelatedByCreationUserId) {
                foreach ($this->collAsyncJobsRelatedByCreationUserId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collAsyncJobsRelatedByUpdateUserId) {
                foreach ($this->collAsyncJobsRelatedByUpdateUserId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collUserNotificationsRelatedByCreationUserId) {
                foreach ($this->collUserNotificationsRelatedByCreationUserId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collUserNotificationsRelatedByUpdateUserId) {
                foreach ($this->collUserNotificationsRelatedByUpdateUserId as $o) {
                    $o->clearAllReferences($deep);
                }
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collAccountSettings instanceof PropelCollection) {
            $this->collAccountSettings->clearIterator();
        }
        $this->collAccountSettings = null;
        if ($this->collAccountProfileRefs instanceof PropelCollection) {
            $this->collAccountProfileRefs->clearIterator();
        }
        $this->collAccountProfileRefs = null;
        if ($this->collAccountGroupRefs instanceof PropelCollection) {
            $this->collAccountGroupRefs->clearIterator();
        }
        $this->collAccountGroupRefs = null;
        if ($this->collPendingCalls instanceof PropelCollection) {
            $this->collPendingCalls->clearIterator();
        }
        $this->collPendingCalls = null;
        if ($this->collAsyncJobsRelatedByIssuerUserId instanceof PropelCollection) {
            $this->collAsyncJobsRelatedByIssuerUserId->clearIterator();
        }
        $this->collAsyncJobsRelatedByIssuerUserId = null;
        if ($this->collUserNotificationsRelatedByUserId instanceof PropelCollection) {
            $this->collUserNotificationsRelatedByUserId->clearIterator();
        }
        $this->collUserNotificationsRelatedByUserId = null;
        if ($this->collAsyncJobsRelatedByCreationUserId instanceof PropelCollection) {
            $this->collAsyncJobsRelatedByCreationUserId->clearIterator();
        }
        $this->collAsyncJobsRelatedByCreationUserId = null;
        if ($this->collAsyncJobsRelatedByUpdateUserId instanceof PropelCollection) {
            $this->collAsyncJobsRelatedByUpdateUserId->clearIterator();
        }
        $this->collAsyncJobsRelatedByUpdateUserId = null;
        if ($this->collUserNotificationsRelatedByCreationUserId instanceof PropelCollection) {
            $this->collUserNotificationsRelatedByCreationUserId->clearIterator();
        }
        $this->collUserNotificationsRelatedByCreationUserId = null;
        if ($this->collUserNotificationsRelatedByUpdateUserId instanceof PropelCollection) {
            $this->collUserNotificationsRelatedByUpdateUserId->clearIterator();
        }
        $this->collUserNotificationsRelatedByUpdateUserId = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(AccountPeer::DEFAULT_STRING_FORMAT);
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

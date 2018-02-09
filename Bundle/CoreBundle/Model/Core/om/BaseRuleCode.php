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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\CodeSnippet;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\CodeSnippetQuery;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\Rule;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\RuleCode;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\RuleCodePeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\RuleCodeQuery;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\RuleQuery;
use Eulogix\Cool\Lib\Database\Propel\CoolPropelObject;

abstract class BaseRuleCode extends CoolPropelObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\RuleCodePeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        RuleCodePeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the rule_code_id field.
     * @var        int
     */
    protected $rule_code_id;

    /**
     * The value for the rule_id field.
     * @var        int
     */
    protected $rule_id;

    /**
     * The value for the enabled_flag field.
     * Note: this column has a database default value of: true
     * @var        boolean
     */
    protected $enabled_flag;

    /**
     * The value for the type field.
     * Note: this column has a database default value of: 'VARIABLE'
     * @var        string
     */
    protected $type;

    /**
     * The value for the name field.
     * @var        string
     */
    protected $name;

    /**
     * The value for the code_snippet_id field.
     * @var        int
     */
    protected $code_snippet_id;

    /**
     * The value for the code_snippet_variables field.
     * @var        string
     */
    protected $code_snippet_variables;

    /**
     * The value for the raw_code field.
     * @var        string
     */
    protected $raw_code;

    /**
     * @var        Rule
     */
    protected $aRule;

    /**
     * @var        CodeSnippet
     */
    protected $aCodeSnippet;

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
        $this->enabled_flag = true;
        $this->type = 'VARIABLE';
    }

    /**
     * Initializes internal state of BaseRuleCode object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
    }

    /**
     * Get the [rule_code_id] column value.
     *
     * @return int
     */
    public function getRuleCodeId()
    {

        return $this->rule_code_id;
    }

    /**
     * Get the [rule_id] column value.
     *
     * @return int
     */
    public function getRuleId()
    {

        return $this->rule_id;
    }

    /**
     * Get the [enabled_flag] column value.
     *
     * @return boolean
     */
    public function getEnabledFlag()
    {

        return $this->enabled_flag;
    }

    /**
     * Get the [type] column value.
     *
     * @return string
     */
    public function getType()
    {

        return $this->type;
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
     * Get the [code_snippet_id] column value.
     *
     * @return int
     */
    public function getCodeSnippetId()
    {

        return $this->code_snippet_id;
    }

    /**
     * Get the [code_snippet_variables] column value.
     *
     * @return string
     */
    public function getCodeSnippetVariables()
    {

        return $this->code_snippet_variables;
    }

    /**
     * Get the [raw_code] column value.
     *
     * @return string
     */
    public function getRawCode()
    {

        return $this->raw_code;
    }

    /**
     * Set the value of [rule_code_id] column.
     *
     * @param  int $v new value
     * @return RuleCode The current object (for fluent API support)
     */
    public function setRuleCodeId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->rule_code_id !== $v) {
            $this->rule_code_id = $v;
            $this->modifiedColumns[] = RuleCodePeer::RULE_CODE_ID;
        }


        return $this;
    } // setRuleCodeId()

    /**
     * Set the value of [rule_id] column.
     *
     * @param  int $v new value
     * @return RuleCode The current object (for fluent API support)
     */
    public function setRuleId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->rule_id !== $v) {
            $this->rule_id = $v;
            $this->modifiedColumns[] = RuleCodePeer::RULE_ID;
        }

        if ($this->aRule !== null && $this->aRule->getRuleId() !== $v) {
            $this->aRule = null;
        }


        return $this;
    } // setRuleId()

    /**
     * Sets the value of the [enabled_flag] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return RuleCode The current object (for fluent API support)
     */
    public function setEnabledFlag($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->enabled_flag !== $v) {
            $this->enabled_flag = $v;
            $this->modifiedColumns[] = RuleCodePeer::ENABLED_FLAG;
        }


        return $this;
    } // setEnabledFlag()

    /**
     * Set the value of [type] column.
     *
     * @param  string $v new value
     * @return RuleCode The current object (for fluent API support)
     */
    public function setType($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->type !== $v) {
            $this->type = $v;
            $this->modifiedColumns[] = RuleCodePeer::TYPE;
        }


        return $this;
    } // setType()

    /**
     * Set the value of [name] column.
     *
     * @param  string $v new value
     * @return RuleCode The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[] = RuleCodePeer::NAME;
        }


        return $this;
    } // setName()

    /**
     * Set the value of [code_snippet_id] column.
     *
     * @param  int $v new value
     * @return RuleCode The current object (for fluent API support)
     */
    public function setCodeSnippetId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->code_snippet_id !== $v) {
            $this->code_snippet_id = $v;
            $this->modifiedColumns[] = RuleCodePeer::CODE_SNIPPET_ID;
        }

        if ($this->aCodeSnippet !== null && $this->aCodeSnippet->getCodeSnippetId() !== $v) {
            $this->aCodeSnippet = null;
        }


        return $this;
    } // setCodeSnippetId()

    /**
     * Set the value of [code_snippet_variables] column.
     *
     * @param  string $v new value
     * @return RuleCode The current object (for fluent API support)
     */
    public function setCodeSnippetVariables($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->code_snippet_variables !== $v) {
            $this->code_snippet_variables = $v;
            $this->modifiedColumns[] = RuleCodePeer::CODE_SNIPPET_VARIABLES;
        }


        return $this;
    } // setCodeSnippetVariables()

    /**
     * Set the value of [raw_code] column.
     *
     * @param  string $v new value
     * @return RuleCode The current object (for fluent API support)
     */
    public function setRawCode($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->raw_code !== $v) {
            $this->raw_code = $v;
            $this->modifiedColumns[] = RuleCodePeer::RAW_CODE;
        }


        return $this;
    } // setRawCode()

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
            if ($this->enabled_flag !== true) {
                return false;
            }

            if ($this->type !== 'VARIABLE') {
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

            $this->rule_code_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->rule_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->enabled_flag = ($row[$startcol + 2] !== null) ? (boolean) $row[$startcol + 2] : null;
            $this->type = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->name = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->code_snippet_id = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
            $this->code_snippet_variables = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->raw_code = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 8; // 8 = RuleCodePeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating RuleCode object", $e);
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

        if ($this->aRule !== null && $this->rule_id !== $this->aRule->getRuleId()) {
            $this->aRule = null;
        }
        if ($this->aCodeSnippet !== null && $this->code_snippet_id !== $this->aCodeSnippet->getCodeSnippetId()) {
            $this->aCodeSnippet = null;
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
            $con = Propel::getConnection(RuleCodePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = RuleCodePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aRule = null;
            $this->aCodeSnippet = null;
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
            $con = Propel::getConnection(RuleCodePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = RuleCodeQuery::create()
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
            $con = Propel::getConnection(RuleCodePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                RuleCodePeer::addInstanceToPool($this);
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

            if ($this->aRule !== null) {
                if ($this->aRule->isModified() || $this->aRule->isNew()) {
                    $affectedRows += $this->aRule->save($con);
                }
                $this->setRule($this->aRule);
            }

            if ($this->aCodeSnippet !== null) {
                if ($this->aCodeSnippet->isModified() || $this->aCodeSnippet->isNew()) {
                    $affectedRows += $this->aCodeSnippet->save($con);
                }
                $this->setCodeSnippet($this->aCodeSnippet);
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

        $this->modifiedColumns[] = RuleCodePeer::RULE_CODE_ID;
        if (null !== $this->rule_code_id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . RuleCodePeer::RULE_CODE_ID . ')');
        }
        if (null === $this->rule_code_id) {
            try {
                $stmt = $con->query("SELECT nextval('core.rule_code_rule_code_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->rule_code_id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(RuleCodePeer::RULE_CODE_ID)) {
            $modifiedColumns[':p' . $index++]  = 'rule_code_id';
        }
        if ($this->isColumnModified(RuleCodePeer::RULE_ID)) {
            $modifiedColumns[':p' . $index++]  = 'rule_id';
        }
        if ($this->isColumnModified(RuleCodePeer::ENABLED_FLAG)) {
            $modifiedColumns[':p' . $index++]  = 'enabled_flag';
        }
        if ($this->isColumnModified(RuleCodePeer::TYPE)) {
            $modifiedColumns[':p' . $index++]  = 'type';
        }
        if ($this->isColumnModified(RuleCodePeer::NAME)) {
            $modifiedColumns[':p' . $index++]  = 'name';
        }
        if ($this->isColumnModified(RuleCodePeer::CODE_SNIPPET_ID)) {
            $modifiedColumns[':p' . $index++]  = 'code_snippet_id';
        }
        if ($this->isColumnModified(RuleCodePeer::CODE_SNIPPET_VARIABLES)) {
            $modifiedColumns[':p' . $index++]  = 'code_snippet_variables';
        }
        if ($this->isColumnModified(RuleCodePeer::RAW_CODE)) {
            $modifiedColumns[':p' . $index++]  = 'raw_code';
        }

        $sql = sprintf(
            'INSERT INTO core.rule_code (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'rule_code_id':
                        $stmt->bindValue($identifier, $this->rule_code_id, PDO::PARAM_INT);
                        break;
                    case 'rule_id':
                        $stmt->bindValue($identifier, $this->rule_id, PDO::PARAM_INT);
                        break;
                    case 'enabled_flag':
                        $stmt->bindValue($identifier, $this->enabled_flag, PDO::PARAM_BOOL);
                        break;
                    case 'type':
                        $stmt->bindValue($identifier, $this->type, PDO::PARAM_STR);
                        break;
                    case 'name':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case 'code_snippet_id':
                        $stmt->bindValue($identifier, $this->code_snippet_id, PDO::PARAM_INT);
                        break;
                    case 'code_snippet_variables':
                        $stmt->bindValue($identifier, $this->code_snippet_variables, PDO::PARAM_STR);
                        break;
                    case 'raw_code':
                        $stmt->bindValue($identifier, $this->raw_code, PDO::PARAM_STR);
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

            if ($this->aRule !== null) {
                if (!$this->aRule->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aRule->getValidationFailures());
                }
            }

            if ($this->aCodeSnippet !== null) {
                if (!$this->aCodeSnippet->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCodeSnippet->getValidationFailures());
                }
            }


            if (($retval = RuleCodePeer::doValidate($this, $columns)) !== true) {
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
        $pos = RuleCodePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getRuleCodeId();
                break;
            case 1:
                return $this->getRuleId();
                break;
            case 2:
                return $this->getEnabledFlag();
                break;
            case 3:
                return $this->getType();
                break;
            case 4:
                return $this->getName();
                break;
            case 5:
                return $this->getCodeSnippetId();
                break;
            case 6:
                return $this->getCodeSnippetVariables();
                break;
            case 7:
                return $this->getRawCode();
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
        if (isset($alreadyDumpedObjects['RuleCode'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['RuleCode'][$this->getPrimaryKey()] = true;
        $keys = RuleCodePeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getRuleCodeId(),
            $keys[1] => $this->getRuleId(),
            $keys[2] => $this->getEnabledFlag(),
            $keys[3] => $this->getType(),
            $keys[4] => $this->getName(),
            $keys[5] => $this->getCodeSnippetId(),
            $keys[6] => $this->getCodeSnippetVariables(),
            $keys[7] => $this->getRawCode(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aRule) {
                $result['Rule'] = $this->aRule->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCodeSnippet) {
                $result['CodeSnippet'] = $this->aCodeSnippet->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
        $pos = RuleCodePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setRuleCodeId($value);
                break;
            case 1:
                $this->setRuleId($value);
                break;
            case 2:
                $this->setEnabledFlag($value);
                break;
            case 3:
                $this->setType($value);
                break;
            case 4:
                $this->setName($value);
                break;
            case 5:
                $this->setCodeSnippetId($value);
                break;
            case 6:
                $this->setCodeSnippetVariables($value);
                break;
            case 7:
                $this->setRawCode($value);
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
        $keys = RuleCodePeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setRuleCodeId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setRuleId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setEnabledFlag($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setType($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setName($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setCodeSnippetId($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setCodeSnippetVariables($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setRawCode($arr[$keys[7]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(RuleCodePeer::DATABASE_NAME);

        if ($this->isColumnModified(RuleCodePeer::RULE_CODE_ID)) $criteria->add(RuleCodePeer::RULE_CODE_ID, $this->rule_code_id);
        if ($this->isColumnModified(RuleCodePeer::RULE_ID)) $criteria->add(RuleCodePeer::RULE_ID, $this->rule_id);
        if ($this->isColumnModified(RuleCodePeer::ENABLED_FLAG)) $criteria->add(RuleCodePeer::ENABLED_FLAG, $this->enabled_flag);
        if ($this->isColumnModified(RuleCodePeer::TYPE)) $criteria->add(RuleCodePeer::TYPE, $this->type);
        if ($this->isColumnModified(RuleCodePeer::NAME)) $criteria->add(RuleCodePeer::NAME, $this->name);
        if ($this->isColumnModified(RuleCodePeer::CODE_SNIPPET_ID)) $criteria->add(RuleCodePeer::CODE_SNIPPET_ID, $this->code_snippet_id);
        if ($this->isColumnModified(RuleCodePeer::CODE_SNIPPET_VARIABLES)) $criteria->add(RuleCodePeer::CODE_SNIPPET_VARIABLES, $this->code_snippet_variables);
        if ($this->isColumnModified(RuleCodePeer::RAW_CODE)) $criteria->add(RuleCodePeer::RAW_CODE, $this->raw_code);

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
        $criteria = new Criteria(RuleCodePeer::DATABASE_NAME);
        $criteria->add(RuleCodePeer::RULE_CODE_ID, $this->rule_code_id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getRuleCodeId();
    }

    /**
     * Generic method to set the primary key (rule_code_id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setRuleCodeId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getRuleCodeId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of RuleCode (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setRuleId($this->getRuleId());
        $copyObj->setEnabledFlag($this->getEnabledFlag());
        $copyObj->setType($this->getType());
        $copyObj->setName($this->getName());
        $copyObj->setCodeSnippetId($this->getCodeSnippetId());
        $copyObj->setCodeSnippetVariables($this->getCodeSnippetVariables());
        $copyObj->setRawCode($this->getRawCode());

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
            $copyObj->setRuleCodeId(NULL); // this is a auto-increment column, so set to default value
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
     * @return RuleCode Clone of current object.
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
     * @return RuleCodePeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new RuleCodePeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Rule object.
     *
     * @param                  Rule $v
     * @return RuleCode The current object (for fluent API support)
     * @throws PropelException
     */
    public function setRule(Rule $v = null)
    {
        if ($v === null) {
            $this->setRuleId(NULL);
        } else {
            $this->setRuleId($v->getRuleId());
        }

        $this->aRule = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Rule object, it will not be re-added.
        if ($v !== null) {
            $v->addRuleCode($this);
        }


        return $this;
    }


    /**
     * Get the associated Rule object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Rule The associated Rule object.
     * @throws PropelException
     */
    public function getRule(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aRule === null && ($this->rule_id !== null) && $doQuery) {
            $this->aRule = RuleQuery::create()->findPk($this->rule_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aRule->addRuleCodes($this);
             */
        }

        return $this->aRule;
    }

    /**
     * Declares an association between this object and a CodeSnippet object.
     *
     * @param                  CodeSnippet $v
     * @return RuleCode The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCodeSnippet(CodeSnippet $v = null)
    {
        if ($v === null) {
            $this->setCodeSnippetId(NULL);
        } else {
            $this->setCodeSnippetId($v->getCodeSnippetId());
        }

        $this->aCodeSnippet = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the CodeSnippet object, it will not be re-added.
        if ($v !== null) {
            $v->addRuleCode($this);
        }


        return $this;
    }


    /**
     * Get the associated CodeSnippet object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return CodeSnippet The associated CodeSnippet object.
     * @throws PropelException
     */
    public function getCodeSnippet(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCodeSnippet === null && ($this->code_snippet_id !== null) && $doQuery) {
            $this->aCodeSnippet = CodeSnippetQuery::create()->findPk($this->code_snippet_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCodeSnippet->addRuleCodes($this);
             */
        }

        return $this->aCodeSnippet;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->rule_code_id = null;
        $this->rule_id = null;
        $this->enabled_flag = null;
        $this->type = null;
        $this->name = null;
        $this->code_snippet_id = null;
        $this->code_snippet_variables = null;
        $this->raw_code = null;
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
            if ($this->aRule instanceof Persistent) {
              $this->aRule->clearAllReferences($deep);
            }
            if ($this->aCodeSnippet instanceof Persistent) {
              $this->aCodeSnippet->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        $this->aRule = null;
        $this->aCodeSnippet = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(RuleCodePeer::DEFAULT_STRING_FORMAT);
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

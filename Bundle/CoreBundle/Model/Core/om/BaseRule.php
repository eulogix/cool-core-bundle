<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core\om;

use \BasePeer;
use \Criteria;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\Rule;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\RuleCode;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\RuleCodeQuery;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\RulePeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\RuleQuery;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\WidgetRule;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\WidgetRuleQuery;
use Eulogix\Cool\Lib\Database\Propel\CoolPropelObject;

abstract class BaseRule extends CoolPropelObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\RulePeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        RulePeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the rule_id field.
     * @var        int
     */
    protected $rule_id;

    /**
     * The value for the name field.
     * @var        string
     */
    protected $name;

    /**
     * The value for the description field.
     * @var        string
     */
    protected $description;

    /**
     * The value for the category field.
     * @var        string
     */
    protected $category;

    /**
     * The value for the expression_type field.
     * Note: this column has a database default value of: 'HOA'
     * @var        string
     */
    protected $expression_type;

    /**
     * The value for the expression field.
     * @var        string
     */
    protected $expression;

    /**
     * @var        PropelObjectCollection|RuleCode[] Collection to store aggregation of RuleCode objects.
     */
    protected $collRuleCodes;
    protected $collRuleCodesPartial;

    /**
     * @var        PropelObjectCollection|WidgetRule[] Collection to store aggregation of WidgetRule objects.
     */
    protected $collWidgetRules;
    protected $collWidgetRulesPartial;

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
    protected $ruleCodesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $widgetRulesScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->expression_type = 'HOA';
    }

    /**
     * Initializes internal state of BaseRule object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
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
     * Get the [name] column value.
     *
     * @return string
     */
    public function getName()
    {

        return $this->name;
    }

    /**
     * Get the [description] column value.
     *
     * @return string
     */
    public function getDescription()
    {

        return $this->description;
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
     * Get the [expression_type] column value.
     *
     * @return string
     */
    public function getExpressionType()
    {

        return $this->expression_type;
    }

    /**
     * Get the [expression] column value.
     *
     * @return string
     */
    public function getExpression()
    {

        return $this->expression;
    }

    /**
     * Set the value of [rule_id] column.
     *
     * @param  int $v new value
     * @return Rule The current object (for fluent API support)
     */
    public function setRuleId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->rule_id !== $v) {
            $this->rule_id = $v;
            $this->modifiedColumns[] = RulePeer::RULE_ID;
        }


        return $this;
    } // setRuleId()

    /**
     * Set the value of [name] column.
     *
     * @param  string $v new value
     * @return Rule The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[] = RulePeer::NAME;
        }


        return $this;
    } // setName()

    /**
     * Set the value of [description] column.
     *
     * @param  string $v new value
     * @return Rule The current object (for fluent API support)
     */
    public function setDescription($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->description !== $v) {
            $this->description = $v;
            $this->modifiedColumns[] = RulePeer::DESCRIPTION;
        }


        return $this;
    } // setDescription()

    /**
     * Set the value of [category] column.
     *
     * @param  string $v new value
     * @return Rule The current object (for fluent API support)
     */
    public function setCategory($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->category !== $v) {
            $this->category = $v;
            $this->modifiedColumns[] = RulePeer::CATEGORY;
        }


        return $this;
    } // setCategory()

    /**
     * Set the value of [expression_type] column.
     *
     * @param  string $v new value
     * @return Rule The current object (for fluent API support)
     */
    public function setExpressionType($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->expression_type !== $v) {
            $this->expression_type = $v;
            $this->modifiedColumns[] = RulePeer::EXPRESSION_TYPE;
        }


        return $this;
    } // setExpressionType()

    /**
     * Set the value of [expression] column.
     *
     * @param  string $v new value
     * @return Rule The current object (for fluent API support)
     */
    public function setExpression($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->expression !== $v) {
            $this->expression = $v;
            $this->modifiedColumns[] = RulePeer::EXPRESSION;
        }


        return $this;
    } // setExpression()

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
            if ($this->expression_type !== 'HOA') {
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

            $this->rule_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->name = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->description = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->category = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->expression_type = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->expression = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 6; // 6 = RulePeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Rule object", $e);
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
            $con = Propel::getConnection(RulePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = RulePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collRuleCodes = null;

            $this->collWidgetRules = null;

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
            $con = Propel::getConnection(RulePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = RuleQuery::create()
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
            $con = Propel::getConnection(RulePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                RulePeer::addInstanceToPool($this);
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

            if ($this->ruleCodesScheduledForDeletion !== null) {
                if (!$this->ruleCodesScheduledForDeletion->isEmpty()) {
                    RuleCodeQuery::create()
                        ->filterByPrimaryKeys($this->ruleCodesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->ruleCodesScheduledForDeletion = null;
                }
            }

            if ($this->collRuleCodes !== null) {
                foreach ($this->collRuleCodes as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->widgetRulesScheduledForDeletion !== null) {
                if (!$this->widgetRulesScheduledForDeletion->isEmpty()) {
                    WidgetRuleQuery::create()
                        ->filterByPrimaryKeys($this->widgetRulesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->widgetRulesScheduledForDeletion = null;
                }
            }

            if ($this->collWidgetRules !== null) {
                foreach ($this->collWidgetRules as $referrerFK) {
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

        $this->modifiedColumns[] = RulePeer::RULE_ID;
        if (null !== $this->rule_id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . RulePeer::RULE_ID . ')');
        }
        if (null === $this->rule_id) {
            try {
                $stmt = $con->query("SELECT nextval('core.rule_rule_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->rule_id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(RulePeer::RULE_ID)) {
            $modifiedColumns[':p' . $index++]  = 'rule_id';
        }
        if ($this->isColumnModified(RulePeer::NAME)) {
            $modifiedColumns[':p' . $index++]  = 'name';
        }
        if ($this->isColumnModified(RulePeer::DESCRIPTION)) {
            $modifiedColumns[':p' . $index++]  = 'description';
        }
        if ($this->isColumnModified(RulePeer::CATEGORY)) {
            $modifiedColumns[':p' . $index++]  = 'category';
        }
        if ($this->isColumnModified(RulePeer::EXPRESSION_TYPE)) {
            $modifiedColumns[':p' . $index++]  = 'expression_type';
        }
        if ($this->isColumnModified(RulePeer::EXPRESSION)) {
            $modifiedColumns[':p' . $index++]  = 'expression';
        }

        $sql = sprintf(
            'INSERT INTO core.rule (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'rule_id':
                        $stmt->bindValue($identifier, $this->rule_id, PDO::PARAM_INT);
                        break;
                    case 'name':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case 'description':
                        $stmt->bindValue($identifier, $this->description, PDO::PARAM_STR);
                        break;
                    case 'category':
                        $stmt->bindValue($identifier, $this->category, PDO::PARAM_STR);
                        break;
                    case 'expression_type':
                        $stmt->bindValue($identifier, $this->expression_type, PDO::PARAM_STR);
                        break;
                    case 'expression':
                        $stmt->bindValue($identifier, $this->expression, PDO::PARAM_STR);
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


            if (($retval = RulePeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collRuleCodes !== null) {
                    foreach ($this->collRuleCodes as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collWidgetRules !== null) {
                    foreach ($this->collWidgetRules as $referrerFK) {
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
        $pos = RulePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getRuleId();
                break;
            case 1:
                return $this->getName();
                break;
            case 2:
                return $this->getDescription();
                break;
            case 3:
                return $this->getCategory();
                break;
            case 4:
                return $this->getExpressionType();
                break;
            case 5:
                return $this->getExpression();
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
        if (isset($alreadyDumpedObjects['Rule'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Rule'][$this->getPrimaryKey()] = true;
        $keys = RulePeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getRuleId(),
            $keys[1] => $this->getName(),
            $keys[2] => $this->getDescription(),
            $keys[3] => $this->getCategory(),
            $keys[4] => $this->getExpressionType(),
            $keys[5] => $this->getExpression(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collRuleCodes) {
                $result['RuleCodes'] = $this->collRuleCodes->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collWidgetRules) {
                $result['WidgetRules'] = $this->collWidgetRules->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = RulePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setRuleId($value);
                break;
            case 1:
                $this->setName($value);
                break;
            case 2:
                $this->setDescription($value);
                break;
            case 3:
                $this->setCategory($value);
                break;
            case 4:
                $this->setExpressionType($value);
                break;
            case 5:
                $this->setExpression($value);
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
        $keys = RulePeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setRuleId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setName($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setDescription($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setCategory($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setExpressionType($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setExpression($arr[$keys[5]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(RulePeer::DATABASE_NAME);

        if ($this->isColumnModified(RulePeer::RULE_ID)) $criteria->add(RulePeer::RULE_ID, $this->rule_id);
        if ($this->isColumnModified(RulePeer::NAME)) $criteria->add(RulePeer::NAME, $this->name);
        if ($this->isColumnModified(RulePeer::DESCRIPTION)) $criteria->add(RulePeer::DESCRIPTION, $this->description);
        if ($this->isColumnModified(RulePeer::CATEGORY)) $criteria->add(RulePeer::CATEGORY, $this->category);
        if ($this->isColumnModified(RulePeer::EXPRESSION_TYPE)) $criteria->add(RulePeer::EXPRESSION_TYPE, $this->expression_type);
        if ($this->isColumnModified(RulePeer::EXPRESSION)) $criteria->add(RulePeer::EXPRESSION, $this->expression);

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
        $criteria = new Criteria(RulePeer::DATABASE_NAME);
        $criteria->add(RulePeer::RULE_ID, $this->rule_id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getRuleId();
    }

    /**
     * Generic method to set the primary key (rule_id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setRuleId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getRuleId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of Rule (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setName($this->getName());
        $copyObj->setDescription($this->getDescription());
        $copyObj->setCategory($this->getCategory());
        $copyObj->setExpressionType($this->getExpressionType());
        $copyObj->setExpression($this->getExpression());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getRuleCodes() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addRuleCode($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getWidgetRules() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addWidgetRule($relObj->copy($deepCopy));
                }
            }

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setRuleId(NULL); // this is a auto-increment column, so set to default value
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
     * @return Rule Clone of current object.
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
     * @return RulePeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new RulePeer();
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
        if ('RuleCode' == $relationName) {
            $this->initRuleCodes();
        }
        if ('WidgetRule' == $relationName) {
            $this->initWidgetRules();
        }
    }

    /**
     * Clears out the collRuleCodes collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Rule The current object (for fluent API support)
     * @see        addRuleCodes()
     */
    public function clearRuleCodes()
    {
        $this->collRuleCodes = null; // important to set this to null since that means it is uninitialized
        $this->collRuleCodesPartial = null;

        return $this;
    }

    /**
     * reset is the collRuleCodes collection loaded partially
     *
     * @return void
     */
    public function resetPartialRuleCodes($v = true)
    {
        $this->collRuleCodesPartial = $v;
    }

    /**
     * Initializes the collRuleCodes collection.
     *
     * By default this just sets the collRuleCodes collection to an empty array (like clearcollRuleCodes());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initRuleCodes($overrideExisting = true)
    {
        if (null !== $this->collRuleCodes && !$overrideExisting) {
            return;
        }
        $this->collRuleCodes = new PropelObjectCollection();
        $this->collRuleCodes->setModel('RuleCode');
    }

    /**
     * Gets an array of RuleCode objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Rule is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|RuleCode[] List of RuleCode objects
     * @throws PropelException
     */
    public function getRuleCodes($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collRuleCodesPartial && !$this->isNew();
        if (null === $this->collRuleCodes || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collRuleCodes) {
                // return empty collection
                $this->initRuleCodes();
            } else {
                $collRuleCodes = RuleCodeQuery::create(null, $criteria)
                    ->filterByRule($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collRuleCodesPartial && count($collRuleCodes)) {
                      $this->initRuleCodes(false);

                      foreach ($collRuleCodes as $obj) {
                        if (false == $this->collRuleCodes->contains($obj)) {
                          $this->collRuleCodes->append($obj);
                        }
                      }

                      $this->collRuleCodesPartial = true;
                    }

                    $collRuleCodes->getInternalIterator()->rewind();

                    return $collRuleCodes;
                }

                if ($partial && $this->collRuleCodes) {
                    foreach ($this->collRuleCodes as $obj) {
                        if ($obj->isNew()) {
                            $collRuleCodes[] = $obj;
                        }
                    }
                }

                $this->collRuleCodes = $collRuleCodes;
                $this->collRuleCodesPartial = false;
            }
        }

        return $this->collRuleCodes;
    }

    /**
     * Sets a collection of RuleCode objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $ruleCodes A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Rule The current object (for fluent API support)
     */
    public function setRuleCodes(PropelCollection $ruleCodes, PropelPDO $con = null)
    {
        $ruleCodesToDelete = $this->getRuleCodes(new Criteria(), $con)->diff($ruleCodes);


        $this->ruleCodesScheduledForDeletion = $ruleCodesToDelete;

        foreach ($ruleCodesToDelete as $ruleCodeRemoved) {
            $ruleCodeRemoved->setRule(null);
        }

        $this->collRuleCodes = null;
        foreach ($ruleCodes as $ruleCode) {
            $this->addRuleCode($ruleCode);
        }

        $this->collRuleCodes = $ruleCodes;
        $this->collRuleCodesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related RuleCode objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related RuleCode objects.
     * @throws PropelException
     */
    public function countRuleCodes(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collRuleCodesPartial && !$this->isNew();
        if (null === $this->collRuleCodes || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collRuleCodes) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getRuleCodes());
            }
            $query = RuleCodeQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByRule($this)
                ->count($con);
        }

        return count($this->collRuleCodes);
    }

    /**
     * Method called to associate a RuleCode object to this object
     * through the RuleCode foreign key attribute.
     *
     * @param    RuleCode $l RuleCode
     * @return Rule The current object (for fluent API support)
     */
    public function addRuleCode(RuleCode $l)
    {
        if ($this->collRuleCodes === null) {
            $this->initRuleCodes();
            $this->collRuleCodesPartial = true;
        }

        if (!in_array($l, $this->collRuleCodes->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddRuleCode($l);

            if ($this->ruleCodesScheduledForDeletion and $this->ruleCodesScheduledForDeletion->contains($l)) {
                $this->ruleCodesScheduledForDeletion->remove($this->ruleCodesScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	RuleCode $ruleCode The ruleCode object to add.
     */
    protected function doAddRuleCode($ruleCode)
    {
        $this->collRuleCodes[]= $ruleCode;
        $ruleCode->setRule($this);
    }

    /**
     * @param	RuleCode $ruleCode The ruleCode object to remove.
     * @return Rule The current object (for fluent API support)
     */
    public function removeRuleCode($ruleCode)
    {
        if ($this->getRuleCodes()->contains($ruleCode)) {
            $this->collRuleCodes->remove($this->collRuleCodes->search($ruleCode));
            if (null === $this->ruleCodesScheduledForDeletion) {
                $this->ruleCodesScheduledForDeletion = clone $this->collRuleCodes;
                $this->ruleCodesScheduledForDeletion->clear();
            }
            $this->ruleCodesScheduledForDeletion[]= clone $ruleCode;
            $ruleCode->setRule(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Rule is new, it will return
     * an empty collection; or if this Rule has previously
     * been saved, it will retrieve related RuleCodes from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Rule.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|RuleCode[] List of RuleCode objects
     */
    public function getRuleCodesJoinCodeSnippet($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = RuleCodeQuery::create(null, $criteria);
        $query->joinWith('CodeSnippet', $join_behavior);

        return $this->getRuleCodes($query, $con);
    }

    /**
     * Clears out the collWidgetRules collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Rule The current object (for fluent API support)
     * @see        addWidgetRules()
     */
    public function clearWidgetRules()
    {
        $this->collWidgetRules = null; // important to set this to null since that means it is uninitialized
        $this->collWidgetRulesPartial = null;

        return $this;
    }

    /**
     * reset is the collWidgetRules collection loaded partially
     *
     * @return void
     */
    public function resetPartialWidgetRules($v = true)
    {
        $this->collWidgetRulesPartial = $v;
    }

    /**
     * Initializes the collWidgetRules collection.
     *
     * By default this just sets the collWidgetRules collection to an empty array (like clearcollWidgetRules());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initWidgetRules($overrideExisting = true)
    {
        if (null !== $this->collWidgetRules && !$overrideExisting) {
            return;
        }
        $this->collWidgetRules = new PropelObjectCollection();
        $this->collWidgetRules->setModel('WidgetRule');
    }

    /**
     * Gets an array of WidgetRule objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Rule is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|WidgetRule[] List of WidgetRule objects
     * @throws PropelException
     */
    public function getWidgetRules($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collWidgetRulesPartial && !$this->isNew();
        if (null === $this->collWidgetRules || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collWidgetRules) {
                // return empty collection
                $this->initWidgetRules();
            } else {
                $collWidgetRules = WidgetRuleQuery::create(null, $criteria)
                    ->filterByRule($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collWidgetRulesPartial && count($collWidgetRules)) {
                      $this->initWidgetRules(false);

                      foreach ($collWidgetRules as $obj) {
                        if (false == $this->collWidgetRules->contains($obj)) {
                          $this->collWidgetRules->append($obj);
                        }
                      }

                      $this->collWidgetRulesPartial = true;
                    }

                    $collWidgetRules->getInternalIterator()->rewind();

                    return $collWidgetRules;
                }

                if ($partial && $this->collWidgetRules) {
                    foreach ($this->collWidgetRules as $obj) {
                        if ($obj->isNew()) {
                            $collWidgetRules[] = $obj;
                        }
                    }
                }

                $this->collWidgetRules = $collWidgetRules;
                $this->collWidgetRulesPartial = false;
            }
        }

        return $this->collWidgetRules;
    }

    /**
     * Sets a collection of WidgetRule objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $widgetRules A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Rule The current object (for fluent API support)
     */
    public function setWidgetRules(PropelCollection $widgetRules, PropelPDO $con = null)
    {
        $widgetRulesToDelete = $this->getWidgetRules(new Criteria(), $con)->diff($widgetRules);


        $this->widgetRulesScheduledForDeletion = $widgetRulesToDelete;

        foreach ($widgetRulesToDelete as $widgetRuleRemoved) {
            $widgetRuleRemoved->setRule(null);
        }

        $this->collWidgetRules = null;
        foreach ($widgetRules as $widgetRule) {
            $this->addWidgetRule($widgetRule);
        }

        $this->collWidgetRules = $widgetRules;
        $this->collWidgetRulesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related WidgetRule objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related WidgetRule objects.
     * @throws PropelException
     */
    public function countWidgetRules(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collWidgetRulesPartial && !$this->isNew();
        if (null === $this->collWidgetRules || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collWidgetRules) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getWidgetRules());
            }
            $query = WidgetRuleQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByRule($this)
                ->count($con);
        }

        return count($this->collWidgetRules);
    }

    /**
     * Method called to associate a WidgetRule object to this object
     * through the WidgetRule foreign key attribute.
     *
     * @param    WidgetRule $l WidgetRule
     * @return Rule The current object (for fluent API support)
     */
    public function addWidgetRule(WidgetRule $l)
    {
        if ($this->collWidgetRules === null) {
            $this->initWidgetRules();
            $this->collWidgetRulesPartial = true;
        }

        if (!in_array($l, $this->collWidgetRules->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddWidgetRule($l);

            if ($this->widgetRulesScheduledForDeletion and $this->widgetRulesScheduledForDeletion->contains($l)) {
                $this->widgetRulesScheduledForDeletion->remove($this->widgetRulesScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	WidgetRule $widgetRule The widgetRule object to add.
     */
    protected function doAddWidgetRule($widgetRule)
    {
        $this->collWidgetRules[]= $widgetRule;
        $widgetRule->setRule($this);
    }

    /**
     * @param	WidgetRule $widgetRule The widgetRule object to remove.
     * @return Rule The current object (for fluent API support)
     */
    public function removeWidgetRule($widgetRule)
    {
        if ($this->getWidgetRules()->contains($widgetRule)) {
            $this->collWidgetRules->remove($this->collWidgetRules->search($widgetRule));
            if (null === $this->widgetRulesScheduledForDeletion) {
                $this->widgetRulesScheduledForDeletion = clone $this->collWidgetRules;
                $this->widgetRulesScheduledForDeletion->clear();
            }
            $this->widgetRulesScheduledForDeletion[]= clone $widgetRule;
            $widgetRule->setRule(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Rule is new, it will return
     * an empty collection; or if this Rule has previously
     * been saved, it will retrieve related WidgetRules from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Rule.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|WidgetRule[] List of WidgetRule objects
     */
    public function getWidgetRulesJoinWidgetRuleRelatedByParentWidgetRuleIdWidgetId($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = WidgetRuleQuery::create(null, $criteria);
        $query->joinWith('WidgetRuleRelatedByParentWidgetRuleIdWidgetId', $join_behavior);

        return $this->getWidgetRules($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->rule_id = null;
        $this->name = null;
        $this->description = null;
        $this->category = null;
        $this->expression_type = null;
        $this->expression = null;
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
            if ($this->collRuleCodes) {
                foreach ($this->collRuleCodes as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collWidgetRules) {
                foreach ($this->collWidgetRules as $o) {
                    $o->clearAllReferences($deep);
                }
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collRuleCodes instanceof PropelCollection) {
            $this->collRuleCodes->clearIterator();
        }
        $this->collRuleCodes = null;
        if ($this->collWidgetRules instanceof PropelCollection) {
            $this->collWidgetRules->clearIterator();
        }
        $this->collWidgetRules = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(RulePeer::DEFAULT_STRING_FORMAT);
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

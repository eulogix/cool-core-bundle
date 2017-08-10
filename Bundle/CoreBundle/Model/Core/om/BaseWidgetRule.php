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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\RuleQuery;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\WidgetRule;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\WidgetRulePeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\WidgetRuleQuery;
use Eulogix\Cool\Lib\Database\Propel\CoolPropelObject;

abstract class BaseWidgetRule extends CoolPropelObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\WidgetRulePeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        WidgetRulePeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the widget_rule_id field.
     * @var        int
     */
    protected $widget_rule_id;

    /**
     * The value for the parent_widget_rule_id field.
     * @var        int
     */
    protected $parent_widget_rule_id;

    /**
     * The value for the widget_id field.
     * @var        string
     */
    protected $widget_id;

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
     * The value for the evaluation field.
     * Note: this column has a database default value of: 'BEFORE_DEFINITION'
     * @var        string
     */
    protected $evaluation;

    /**
     * @var        Rule
     */
    protected $aRule;

    /**
     * @var        WidgetRule
     */
    protected $aWidgetRuleRelatedByParentWidgetRuleIdWidgetId;

    /**
     * @var        PropelObjectCollection|WidgetRule[] Collection to store aggregation of WidgetRule objects.
     */
    protected $collWidgetRulesRelatedByWidgetRuleIdWidgetId;
    protected $collWidgetRulesRelatedByWidgetRuleIdWidgetIdPartial;

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
    protected $widgetRulesRelatedByWidgetRuleIdWidgetIdScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->enabled_flag = true;
        $this->evaluation = 'BEFORE_DEFINITION';
    }

    /**
     * Initializes internal state of BaseWidgetRule object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
    }

    /**
     * Get the [widget_rule_id] column value.
     *
     * @return int
     */
    public function getWidgetRuleId()
    {

        return $this->widget_rule_id;
    }

    /**
     * Get the [parent_widget_rule_id] column value.
     *
     * @return int
     */
    public function getParentWidgetRuleId()
    {

        return $this->parent_widget_rule_id;
    }

    /**
     * Get the [widget_id] column value.
     *
     * @return string
     */
    public function getWidgetId()
    {

        return $this->widget_id;
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
     * Get the [evaluation] column value.
     *
     * @return string
     */
    public function getEvaluation()
    {

        return $this->evaluation;
    }

    /**
     * Set the value of [widget_rule_id] column.
     *
     * @param  int $v new value
     * @return WidgetRule The current object (for fluent API support)
     */
    public function setWidgetRuleId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->widget_rule_id !== $v) {
            $this->widget_rule_id = $v;
            $this->modifiedColumns[] = WidgetRulePeer::WIDGET_RULE_ID;
        }


        return $this;
    } // setWidgetRuleId()

    /**
     * Set the value of [parent_widget_rule_id] column.
     *
     * @param  int $v new value
     * @return WidgetRule The current object (for fluent API support)
     */
    public function setParentWidgetRuleId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->parent_widget_rule_id !== $v) {
            $this->parent_widget_rule_id = $v;
            $this->modifiedColumns[] = WidgetRulePeer::PARENT_WIDGET_RULE_ID;
        }

        if ($this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId !== null && $this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId->getWidgetRuleId() !== $v) {
            $this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId = null;
        }


        return $this;
    } // setParentWidgetRuleId()

    /**
     * Set the value of [widget_id] column.
     *
     * @param  string $v new value
     * @return WidgetRule The current object (for fluent API support)
     */
    public function setWidgetId($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->widget_id !== $v) {
            $this->widget_id = $v;
            $this->modifiedColumns[] = WidgetRulePeer::WIDGET_ID;
        }

        if ($this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId !== null && $this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId->getWidgetId() !== $v) {
            $this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId = null;
        }


        return $this;
    } // setWidgetId()

    /**
     * Set the value of [rule_id] column.
     *
     * @param  int $v new value
     * @return WidgetRule The current object (for fluent API support)
     */
    public function setRuleId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->rule_id !== $v) {
            $this->rule_id = $v;
            $this->modifiedColumns[] = WidgetRulePeer::RULE_ID;
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
     * @return WidgetRule The current object (for fluent API support)
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
            $this->modifiedColumns[] = WidgetRulePeer::ENABLED_FLAG;
        }


        return $this;
    } // setEnabledFlag()

    /**
     * Set the value of [evaluation] column.
     *
     * @param  string $v new value
     * @return WidgetRule The current object (for fluent API support)
     */
    public function setEvaluation($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->evaluation !== $v) {
            $this->evaluation = $v;
            $this->modifiedColumns[] = WidgetRulePeer::EVALUATION;
        }


        return $this;
    } // setEvaluation()

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

            if ($this->evaluation !== 'BEFORE_DEFINITION') {
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

            $this->widget_rule_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->parent_widget_rule_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->widget_id = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->rule_id = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
            $this->enabled_flag = ($row[$startcol + 4] !== null) ? (boolean) $row[$startcol + 4] : null;
            $this->evaluation = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 6; // 6 = WidgetRulePeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating WidgetRule object", $e);
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

        if ($this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId !== null && $this->parent_widget_rule_id !== $this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId->getWidgetRuleId()) {
            $this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId = null;
        }
        if ($this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId !== null && $this->widget_id !== $this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId->getWidgetId()) {
            $this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId = null;
        }
        if ($this->aRule !== null && $this->rule_id !== $this->aRule->getRuleId()) {
            $this->aRule = null;
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
            $con = Propel::getConnection(WidgetRulePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = WidgetRulePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aRule = null;
            $this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId = null;
            $this->collWidgetRulesRelatedByWidgetRuleIdWidgetId = null;

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
            $con = Propel::getConnection(WidgetRulePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = WidgetRuleQuery::create()
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
            $con = Propel::getConnection(WidgetRulePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                WidgetRulePeer::addInstanceToPool($this);
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

            if ($this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId !== null) {
                if ($this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId->isModified() || $this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId->isNew()) {
                    $affectedRows += $this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId->save($con);
                }
                $this->setWidgetRuleRelatedByParentWidgetRuleIdWidgetId($this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId);
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

            if ($this->widgetRulesRelatedByWidgetRuleIdWidgetIdScheduledForDeletion !== null) {
                if (!$this->widgetRulesRelatedByWidgetRuleIdWidgetIdScheduledForDeletion->isEmpty()) {
                    foreach ($this->widgetRulesRelatedByWidgetRuleIdWidgetIdScheduledForDeletion as $widgetRuleRelatedByWidgetRuleIdWidgetId) {
                        // need to save related object because we set the relation to null
                        $widgetRuleRelatedByWidgetRuleIdWidgetId->save($con);
                    }
                    $this->widgetRulesRelatedByWidgetRuleIdWidgetIdScheduledForDeletion = null;
                }
            }

            if ($this->collWidgetRulesRelatedByWidgetRuleIdWidgetId !== null) {
                foreach ($this->collWidgetRulesRelatedByWidgetRuleIdWidgetId as $referrerFK) {
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

        $this->modifiedColumns[] = WidgetRulePeer::WIDGET_RULE_ID;
        if (null !== $this->widget_rule_id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . WidgetRulePeer::WIDGET_RULE_ID . ')');
        }
        if (null === $this->widget_rule_id) {
            try {
                $stmt = $con->query("SELECT nextval('core.widget_rule_widget_rule_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->widget_rule_id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(WidgetRulePeer::WIDGET_RULE_ID)) {
            $modifiedColumns[':p' . $index++]  = 'widget_rule_id';
        }
        if ($this->isColumnModified(WidgetRulePeer::PARENT_WIDGET_RULE_ID)) {
            $modifiedColumns[':p' . $index++]  = 'parent_widget_rule_id';
        }
        if ($this->isColumnModified(WidgetRulePeer::WIDGET_ID)) {
            $modifiedColumns[':p' . $index++]  = 'widget_id';
        }
        if ($this->isColumnModified(WidgetRulePeer::RULE_ID)) {
            $modifiedColumns[':p' . $index++]  = 'rule_id';
        }
        if ($this->isColumnModified(WidgetRulePeer::ENABLED_FLAG)) {
            $modifiedColumns[':p' . $index++]  = 'enabled_flag';
        }
        if ($this->isColumnModified(WidgetRulePeer::EVALUATION)) {
            $modifiedColumns[':p' . $index++]  = 'evaluation';
        }

        $sql = sprintf(
            'INSERT INTO core.widget_rule (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'widget_rule_id':
                        $stmt->bindValue($identifier, $this->widget_rule_id, PDO::PARAM_INT);
                        break;
                    case 'parent_widget_rule_id':
                        $stmt->bindValue($identifier, $this->parent_widget_rule_id, PDO::PARAM_INT);
                        break;
                    case 'widget_id':
                        $stmt->bindValue($identifier, $this->widget_id, PDO::PARAM_STR);
                        break;
                    case 'rule_id':
                        $stmt->bindValue($identifier, $this->rule_id, PDO::PARAM_INT);
                        break;
                    case 'enabled_flag':
                        $stmt->bindValue($identifier, $this->enabled_flag, PDO::PARAM_BOOL);
                        break;
                    case 'evaluation':
                        $stmt->bindValue($identifier, $this->evaluation, PDO::PARAM_STR);
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

            if ($this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId !== null) {
                if (!$this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId->getValidationFailures());
                }
            }


            if (($retval = WidgetRulePeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collWidgetRulesRelatedByWidgetRuleIdWidgetId !== null) {
                    foreach ($this->collWidgetRulesRelatedByWidgetRuleIdWidgetId as $referrerFK) {
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
        $pos = WidgetRulePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getWidgetRuleId();
                break;
            case 1:
                return $this->getParentWidgetRuleId();
                break;
            case 2:
                return $this->getWidgetId();
                break;
            case 3:
                return $this->getRuleId();
                break;
            case 4:
                return $this->getEnabledFlag();
                break;
            case 5:
                return $this->getEvaluation();
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
        if (isset($alreadyDumpedObjects['WidgetRule'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['WidgetRule'][$this->getPrimaryKey()] = true;
        $keys = WidgetRulePeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getWidgetRuleId(),
            $keys[1] => $this->getParentWidgetRuleId(),
            $keys[2] => $this->getWidgetId(),
            $keys[3] => $this->getRuleId(),
            $keys[4] => $this->getEnabledFlag(),
            $keys[5] => $this->getEvaluation(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aRule) {
                $result['Rule'] = $this->aRule->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId) {
                $result['WidgetRuleRelatedByParentWidgetRuleIdWidgetId'] = $this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collWidgetRulesRelatedByWidgetRuleIdWidgetId) {
                $result['WidgetRulesRelatedByWidgetRuleIdWidgetId'] = $this->collWidgetRulesRelatedByWidgetRuleIdWidgetId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = WidgetRulePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setWidgetRuleId($value);
                break;
            case 1:
                $this->setParentWidgetRuleId($value);
                break;
            case 2:
                $this->setWidgetId($value);
                break;
            case 3:
                $this->setRuleId($value);
                break;
            case 4:
                $this->setEnabledFlag($value);
                break;
            case 5:
                $this->setEvaluation($value);
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
        $keys = WidgetRulePeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setWidgetRuleId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setParentWidgetRuleId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setWidgetId($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setRuleId($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setEnabledFlag($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setEvaluation($arr[$keys[5]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(WidgetRulePeer::DATABASE_NAME);

        if ($this->isColumnModified(WidgetRulePeer::WIDGET_RULE_ID)) $criteria->add(WidgetRulePeer::WIDGET_RULE_ID, $this->widget_rule_id);
        if ($this->isColumnModified(WidgetRulePeer::PARENT_WIDGET_RULE_ID)) $criteria->add(WidgetRulePeer::PARENT_WIDGET_RULE_ID, $this->parent_widget_rule_id);
        if ($this->isColumnModified(WidgetRulePeer::WIDGET_ID)) $criteria->add(WidgetRulePeer::WIDGET_ID, $this->widget_id);
        if ($this->isColumnModified(WidgetRulePeer::RULE_ID)) $criteria->add(WidgetRulePeer::RULE_ID, $this->rule_id);
        if ($this->isColumnModified(WidgetRulePeer::ENABLED_FLAG)) $criteria->add(WidgetRulePeer::ENABLED_FLAG, $this->enabled_flag);
        if ($this->isColumnModified(WidgetRulePeer::EVALUATION)) $criteria->add(WidgetRulePeer::EVALUATION, $this->evaluation);

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
        $criteria = new Criteria(WidgetRulePeer::DATABASE_NAME);
        $criteria->add(WidgetRulePeer::WIDGET_RULE_ID, $this->widget_rule_id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getWidgetRuleId();
    }

    /**
     * Generic method to set the primary key (widget_rule_id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setWidgetRuleId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getWidgetRuleId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of WidgetRule (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setParentWidgetRuleId($this->getParentWidgetRuleId());
        $copyObj->setWidgetId($this->getWidgetId());
        $copyObj->setRuleId($this->getRuleId());
        $copyObj->setEnabledFlag($this->getEnabledFlag());
        $copyObj->setEvaluation($this->getEvaluation());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getWidgetRulesRelatedByWidgetRuleIdWidgetId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addWidgetRuleRelatedByWidgetRuleIdWidgetId($relObj->copy($deepCopy));
                }
            }

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setWidgetRuleId(NULL); // this is a auto-increment column, so set to default value
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
     * @return WidgetRule Clone of current object.
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
     * @return WidgetRulePeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new WidgetRulePeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Rule object.
     *
     * @param                  Rule $v
     * @return WidgetRule The current object (for fluent API support)
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
            $v->addWidgetRule($this);
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
                $this->aRule->addWidgetRules($this);
             */
        }

        return $this->aRule;
    }

    /**
     * Declares an association between this object and a WidgetRule object.
     *
     * @param                  WidgetRule $v
     * @return WidgetRule The current object (for fluent API support)
     * @throws PropelException
     */
    public function setWidgetRuleRelatedByParentWidgetRuleIdWidgetId(WidgetRule $v = null)
    {
        if ($v === null) {
            $this->setParentWidgetRuleId(NULL);
        } else {
            $this->setParentWidgetRuleId($v->getWidgetRuleId());
        }

        if ($v === null) {
            $this->setWidgetId(NULL);
        } else {
            $this->setWidgetId($v->getWidgetId());
        }

        $this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the WidgetRule object, it will not be re-added.
        if ($v !== null) {
            $v->addWidgetRuleRelatedByWidgetRuleIdWidgetId($this);
        }


        return $this;
    }


    /**
     * Get the associated WidgetRule object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return WidgetRule The associated WidgetRule object.
     * @throws PropelException
     */
    public function getWidgetRuleRelatedByParentWidgetRuleIdWidgetId(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId === null && ($this->parent_widget_rule_id !== null && ($this->widget_id !== "" && $this->widget_id !== null)) && $doQuery) {
            $this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId = WidgetRuleQuery::create()
                ->filterByWidgetRuleRelatedByWidgetRuleIdWidgetId($this) // here
                ->findOne($con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId->addWidgetRulesRelatedByWidgetRuleIdWidgetId($this);
             */
        }

        return $this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId;
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
        if ('WidgetRuleRelatedByWidgetRuleIdWidgetId' == $relationName) {
            $this->initWidgetRulesRelatedByWidgetRuleIdWidgetId();
        }
    }

    /**
     * Clears out the collWidgetRulesRelatedByWidgetRuleIdWidgetId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return WidgetRule The current object (for fluent API support)
     * @see        addWidgetRulesRelatedByWidgetRuleIdWidgetId()
     */
    public function clearWidgetRulesRelatedByWidgetRuleIdWidgetId()
    {
        $this->collWidgetRulesRelatedByWidgetRuleIdWidgetId = null; // important to set this to null since that means it is uninitialized
        $this->collWidgetRulesRelatedByWidgetRuleIdWidgetIdPartial = null;

        return $this;
    }

    /**
     * reset is the collWidgetRulesRelatedByWidgetRuleIdWidgetId collection loaded partially
     *
     * @return void
     */
    public function resetPartialWidgetRulesRelatedByWidgetRuleIdWidgetId($v = true)
    {
        $this->collWidgetRulesRelatedByWidgetRuleIdWidgetIdPartial = $v;
    }

    /**
     * Initializes the collWidgetRulesRelatedByWidgetRuleIdWidgetId collection.
     *
     * By default this just sets the collWidgetRulesRelatedByWidgetRuleIdWidgetId collection to an empty array (like clearcollWidgetRulesRelatedByWidgetRuleIdWidgetId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initWidgetRulesRelatedByWidgetRuleIdWidgetId($overrideExisting = true)
    {
        if (null !== $this->collWidgetRulesRelatedByWidgetRuleIdWidgetId && !$overrideExisting) {
            return;
        }
        $this->collWidgetRulesRelatedByWidgetRuleIdWidgetId = new PropelObjectCollection();
        $this->collWidgetRulesRelatedByWidgetRuleIdWidgetId->setModel('WidgetRule');
    }

    /**
     * Gets an array of WidgetRule objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this WidgetRule is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|WidgetRule[] List of WidgetRule objects
     * @throws PropelException
     */
    public function getWidgetRulesRelatedByWidgetRuleIdWidgetId($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collWidgetRulesRelatedByWidgetRuleIdWidgetIdPartial && !$this->isNew();
        if (null === $this->collWidgetRulesRelatedByWidgetRuleIdWidgetId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collWidgetRulesRelatedByWidgetRuleIdWidgetId) {
                // return empty collection
                $this->initWidgetRulesRelatedByWidgetRuleIdWidgetId();
            } else {
                $collWidgetRulesRelatedByWidgetRuleIdWidgetId = WidgetRuleQuery::create(null, $criteria)
                    ->filterByWidgetRuleRelatedByParentWidgetRuleIdWidgetId($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collWidgetRulesRelatedByWidgetRuleIdWidgetIdPartial && count($collWidgetRulesRelatedByWidgetRuleIdWidgetId)) {
                      $this->initWidgetRulesRelatedByWidgetRuleIdWidgetId(false);

                      foreach ($collWidgetRulesRelatedByWidgetRuleIdWidgetId as $obj) {
                        if (false == $this->collWidgetRulesRelatedByWidgetRuleIdWidgetId->contains($obj)) {
                          $this->collWidgetRulesRelatedByWidgetRuleIdWidgetId->append($obj);
                        }
                      }

                      $this->collWidgetRulesRelatedByWidgetRuleIdWidgetIdPartial = true;
                    }

                    $collWidgetRulesRelatedByWidgetRuleIdWidgetId->getInternalIterator()->rewind();

                    return $collWidgetRulesRelatedByWidgetRuleIdWidgetId;
                }

                if ($partial && $this->collWidgetRulesRelatedByWidgetRuleIdWidgetId) {
                    foreach ($this->collWidgetRulesRelatedByWidgetRuleIdWidgetId as $obj) {
                        if ($obj->isNew()) {
                            $collWidgetRulesRelatedByWidgetRuleIdWidgetId[] = $obj;
                        }
                    }
                }

                $this->collWidgetRulesRelatedByWidgetRuleIdWidgetId = $collWidgetRulesRelatedByWidgetRuleIdWidgetId;
                $this->collWidgetRulesRelatedByWidgetRuleIdWidgetIdPartial = false;
            }
        }

        return $this->collWidgetRulesRelatedByWidgetRuleIdWidgetId;
    }

    /**
     * Sets a collection of WidgetRuleRelatedByWidgetRuleIdWidgetId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $widgetRulesRelatedByWidgetRuleIdWidgetId A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return WidgetRule The current object (for fluent API support)
     */
    public function setWidgetRulesRelatedByWidgetRuleIdWidgetId(PropelCollection $widgetRulesRelatedByWidgetRuleIdWidgetId, PropelPDO $con = null)
    {
        $widgetRulesRelatedByWidgetRuleIdWidgetIdToDelete = $this->getWidgetRulesRelatedByWidgetRuleIdWidgetId(new Criteria(), $con)->diff($widgetRulesRelatedByWidgetRuleIdWidgetId);


        $this->widgetRulesRelatedByWidgetRuleIdWidgetIdScheduledForDeletion = $widgetRulesRelatedByWidgetRuleIdWidgetIdToDelete;

        foreach ($widgetRulesRelatedByWidgetRuleIdWidgetIdToDelete as $widgetRuleRelatedByWidgetRuleIdWidgetIdRemoved) {
            $widgetRuleRelatedByWidgetRuleIdWidgetIdRemoved->setWidgetRuleRelatedByParentWidgetRuleIdWidgetId(null);
        }

        $this->collWidgetRulesRelatedByWidgetRuleIdWidgetId = null;
        foreach ($widgetRulesRelatedByWidgetRuleIdWidgetId as $widgetRuleRelatedByWidgetRuleIdWidgetId) {
            $this->addWidgetRuleRelatedByWidgetRuleIdWidgetId($widgetRuleRelatedByWidgetRuleIdWidgetId);
        }

        $this->collWidgetRulesRelatedByWidgetRuleIdWidgetId = $widgetRulesRelatedByWidgetRuleIdWidgetId;
        $this->collWidgetRulesRelatedByWidgetRuleIdWidgetIdPartial = false;

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
    public function countWidgetRulesRelatedByWidgetRuleIdWidgetId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collWidgetRulesRelatedByWidgetRuleIdWidgetIdPartial && !$this->isNew();
        if (null === $this->collWidgetRulesRelatedByWidgetRuleIdWidgetId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collWidgetRulesRelatedByWidgetRuleIdWidgetId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getWidgetRulesRelatedByWidgetRuleIdWidgetId());
            }
            $query = WidgetRuleQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByWidgetRuleRelatedByParentWidgetRuleIdWidgetId($this)
                ->count($con);
        }

        return count($this->collWidgetRulesRelatedByWidgetRuleIdWidgetId);
    }

    /**
     * Method called to associate a WidgetRule object to this object
     * through the WidgetRule foreign key attribute.
     *
     * @param    WidgetRule $l WidgetRule
     * @return WidgetRule The current object (for fluent API support)
     */
    public function addWidgetRuleRelatedByWidgetRuleIdWidgetId(WidgetRule $l)
    {
        if ($this->collWidgetRulesRelatedByWidgetRuleIdWidgetId === null) {
            $this->initWidgetRulesRelatedByWidgetRuleIdWidgetId();
            $this->collWidgetRulesRelatedByWidgetRuleIdWidgetIdPartial = true;
        }

        if (!in_array($l, $this->collWidgetRulesRelatedByWidgetRuleIdWidgetId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddWidgetRuleRelatedByWidgetRuleIdWidgetId($l);

            if ($this->widgetRulesRelatedByWidgetRuleIdWidgetIdScheduledForDeletion and $this->widgetRulesRelatedByWidgetRuleIdWidgetIdScheduledForDeletion->contains($l)) {
                $this->widgetRulesRelatedByWidgetRuleIdWidgetIdScheduledForDeletion->remove($this->widgetRulesRelatedByWidgetRuleIdWidgetIdScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	WidgetRuleRelatedByWidgetRuleIdWidgetId $widgetRuleRelatedByWidgetRuleIdWidgetId The widgetRuleRelatedByWidgetRuleIdWidgetId object to add.
     */
    protected function doAddWidgetRuleRelatedByWidgetRuleIdWidgetId($widgetRuleRelatedByWidgetRuleIdWidgetId)
    {
        $this->collWidgetRulesRelatedByWidgetRuleIdWidgetId[]= $widgetRuleRelatedByWidgetRuleIdWidgetId;
        $widgetRuleRelatedByWidgetRuleIdWidgetId->setWidgetRuleRelatedByParentWidgetRuleIdWidgetId($this);
    }

    /**
     * @param	WidgetRuleRelatedByWidgetRuleIdWidgetId $widgetRuleRelatedByWidgetRuleIdWidgetId The widgetRuleRelatedByWidgetRuleIdWidgetId object to remove.
     * @return WidgetRule The current object (for fluent API support)
     */
    public function removeWidgetRuleRelatedByWidgetRuleIdWidgetId($widgetRuleRelatedByWidgetRuleIdWidgetId)
    {
        if ($this->getWidgetRulesRelatedByWidgetRuleIdWidgetId()->contains($widgetRuleRelatedByWidgetRuleIdWidgetId)) {
            $this->collWidgetRulesRelatedByWidgetRuleIdWidgetId->remove($this->collWidgetRulesRelatedByWidgetRuleIdWidgetId->search($widgetRuleRelatedByWidgetRuleIdWidgetId));
            if (null === $this->widgetRulesRelatedByWidgetRuleIdWidgetIdScheduledForDeletion) {
                $this->widgetRulesRelatedByWidgetRuleIdWidgetIdScheduledForDeletion = clone $this->collWidgetRulesRelatedByWidgetRuleIdWidgetId;
                $this->widgetRulesRelatedByWidgetRuleIdWidgetIdScheduledForDeletion->clear();
            }
            $this->widgetRulesRelatedByWidgetRuleIdWidgetIdScheduledForDeletion[]= clone $widgetRuleRelatedByWidgetRuleIdWidgetId;
            $widgetRuleRelatedByWidgetRuleIdWidgetId->setWidgetRuleRelatedByParentWidgetRuleIdWidgetId(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this WidgetRule is new, it will return
     * an empty collection; or if this WidgetRule has previously
     * been saved, it will retrieve related WidgetRulesRelatedByWidgetRuleIdWidgetId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in WidgetRule.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|WidgetRule[] List of WidgetRule objects
     */
    public function getWidgetRulesRelatedByWidgetRuleIdWidgetIdJoinRule($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = WidgetRuleQuery::create(null, $criteria);
        $query->joinWith('Rule', $join_behavior);

        return $this->getWidgetRulesRelatedByWidgetRuleIdWidgetId($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->widget_rule_id = null;
        $this->parent_widget_rule_id = null;
        $this->widget_id = null;
        $this->rule_id = null;
        $this->enabled_flag = null;
        $this->evaluation = null;
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
            if ($this->collWidgetRulesRelatedByWidgetRuleIdWidgetId) {
                foreach ($this->collWidgetRulesRelatedByWidgetRuleIdWidgetId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aRule instanceof Persistent) {
              $this->aRule->clearAllReferences($deep);
            }
            if ($this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId instanceof Persistent) {
              $this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collWidgetRulesRelatedByWidgetRuleIdWidgetId instanceof PropelCollection) {
            $this->collWidgetRulesRelatedByWidgetRuleIdWidgetId->clearIterator();
        }
        $this->collWidgetRulesRelatedByWidgetRuleIdWidgetId = null;
        $this->aRule = null;
        $this->aWidgetRuleRelatedByParentWidgetRuleIdWidgetId = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(WidgetRulePeer::DEFAULT_STRING_FORMAT);
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

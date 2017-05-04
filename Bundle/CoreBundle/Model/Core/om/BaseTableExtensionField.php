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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\FieldDefinition;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\FieldDefinitionQuery;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtension;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtensionField;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtensionFieldPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtensionFieldQuery;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtensionQuery;
use Eulogix\Cool\Lib\Database\Propel\CoolPropelObject;

abstract class BaseTableExtensionField extends CoolPropelObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\TableExtensionFieldPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        TableExtensionFieldPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the table_extension_field_id field.
     * @var        int
     */
    protected $table_extension_field_id;

    /**
     * The value for the table_extension_id field.
     * @var        int
     */
    protected $table_extension_id;

    /**
     * The value for the field_definition_id field.
     * @var        int
     */
    protected $field_definition_id;

    /**
     * The value for the require_index field.
     * @var        boolean
     */
    protected $require_index;

    /**
     * The value for the active_flag field.
     * Note: this column has a database default value of: true
     * @var        boolean
     */
    protected $active_flag;

    /**
     * @var        TableExtension
     */
    protected $aTableExtension;

    /**
     * @var        FieldDefinition
     */
    protected $aFieldDefinition;

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
        $this->active_flag = true;
    }

    /**
     * Initializes internal state of BaseTableExtensionField object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
    }

    /**
     * Get the [table_extension_field_id] column value.
     *
     * @return int
     */
    public function getTableExtensionFieldId()
    {

        return $this->table_extension_field_id;
    }

    /**
     * Get the [table_extension_id] column value.
     *
     * @return int
     */
    public function getTableExtensionId()
    {

        return $this->table_extension_id;
    }

    /**
     * Get the [field_definition_id] column value.
     *
     * @return int
     */
    public function getFieldDefinitionId()
    {

        return $this->field_definition_id;
    }

    /**
     * Get the [require_index] column value.
     *
     * @return boolean
     */
    public function getRequireIndex()
    {

        return $this->require_index;
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
     * Set the value of [table_extension_field_id] column.
     *
     * @param  int $v new value
     * @return TableExtensionField The current object (for fluent API support)
     */
    public function setTableExtensionFieldId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->table_extension_field_id !== $v) {
            $this->table_extension_field_id = $v;
            $this->modifiedColumns[] = TableExtensionFieldPeer::TABLE_EXTENSION_FIELD_ID;
        }


        return $this;
    } // setTableExtensionFieldId()

    /**
     * Set the value of [table_extension_id] column.
     *
     * @param  int $v new value
     * @return TableExtensionField The current object (for fluent API support)
     */
    public function setTableExtensionId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->table_extension_id !== $v) {
            $this->table_extension_id = $v;
            $this->modifiedColumns[] = TableExtensionFieldPeer::TABLE_EXTENSION_ID;
        }

        if ($this->aTableExtension !== null && $this->aTableExtension->getTableExtensionId() !== $v) {
            $this->aTableExtension = null;
        }


        return $this;
    } // setTableExtensionId()

    /**
     * Set the value of [field_definition_id] column.
     *
     * @param  int $v new value
     * @return TableExtensionField The current object (for fluent API support)
     */
    public function setFieldDefinitionId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->field_definition_id !== $v) {
            $this->field_definition_id = $v;
            $this->modifiedColumns[] = TableExtensionFieldPeer::FIELD_DEFINITION_ID;
        }

        if ($this->aFieldDefinition !== null && $this->aFieldDefinition->getFieldDefinitionId() !== $v) {
            $this->aFieldDefinition = null;
        }


        return $this;
    } // setFieldDefinitionId()

    /**
     * Sets the value of the [require_index] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return TableExtensionField The current object (for fluent API support)
     */
    public function setRequireIndex($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->require_index !== $v) {
            $this->require_index = $v;
            $this->modifiedColumns[] = TableExtensionFieldPeer::REQUIRE_INDEX;
        }


        return $this;
    } // setRequireIndex()

    /**
     * Sets the value of the [active_flag] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return TableExtensionField The current object (for fluent API support)
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
            $this->modifiedColumns[] = TableExtensionFieldPeer::ACTIVE_FLAG;
        }


        return $this;
    } // setActiveFlag()

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
            if ($this->active_flag !== true) {
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

            $this->table_extension_field_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->table_extension_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->field_definition_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
            $this->require_index = ($row[$startcol + 3] !== null) ? (boolean) $row[$startcol + 3] : null;
            $this->active_flag = ($row[$startcol + 4] !== null) ? (boolean) $row[$startcol + 4] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 5; // 5 = TableExtensionFieldPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating TableExtensionField object", $e);
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

        if ($this->aTableExtension !== null && $this->table_extension_id !== $this->aTableExtension->getTableExtensionId()) {
            $this->aTableExtension = null;
        }
        if ($this->aFieldDefinition !== null && $this->field_definition_id !== $this->aFieldDefinition->getFieldDefinitionId()) {
            $this->aFieldDefinition = null;
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
            $con = Propel::getConnection(TableExtensionFieldPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = TableExtensionFieldPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aTableExtension = null;
            $this->aFieldDefinition = null;
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
            $con = Propel::getConnection(TableExtensionFieldPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = TableExtensionFieldQuery::create()
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
            $con = Propel::getConnection(TableExtensionFieldPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                TableExtensionFieldPeer::addInstanceToPool($this);
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

            if ($this->aTableExtension !== null) {
                if ($this->aTableExtension->isModified() || $this->aTableExtension->isNew()) {
                    $affectedRows += $this->aTableExtension->save($con);
                }
                $this->setTableExtension($this->aTableExtension);
            }

            if ($this->aFieldDefinition !== null) {
                if ($this->aFieldDefinition->isModified() || $this->aFieldDefinition->isNew()) {
                    $affectedRows += $this->aFieldDefinition->save($con);
                }
                $this->setFieldDefinition($this->aFieldDefinition);
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

        $this->modifiedColumns[] = TableExtensionFieldPeer::TABLE_EXTENSION_FIELD_ID;
        if (null !== $this->table_extension_field_id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . TableExtensionFieldPeer::TABLE_EXTENSION_FIELD_ID . ')');
        }
        if (null === $this->table_extension_field_id) {
            try {
                $stmt = $con->query("SELECT nextval('core.table_extension_field_table_extension_field_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->table_extension_field_id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(TableExtensionFieldPeer::TABLE_EXTENSION_FIELD_ID)) {
            $modifiedColumns[':p' . $index++]  = 'table_extension_field_id';
        }
        if ($this->isColumnModified(TableExtensionFieldPeer::TABLE_EXTENSION_ID)) {
            $modifiedColumns[':p' . $index++]  = 'table_extension_id';
        }
        if ($this->isColumnModified(TableExtensionFieldPeer::FIELD_DEFINITION_ID)) {
            $modifiedColumns[':p' . $index++]  = 'field_definition_id';
        }
        if ($this->isColumnModified(TableExtensionFieldPeer::REQUIRE_INDEX)) {
            $modifiedColumns[':p' . $index++]  = 'require_index';
        }
        if ($this->isColumnModified(TableExtensionFieldPeer::ACTIVE_FLAG)) {
            $modifiedColumns[':p' . $index++]  = 'active_flag';
        }

        $sql = sprintf(
            'INSERT INTO core.table_extension_field (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'table_extension_field_id':
                        $stmt->bindValue($identifier, $this->table_extension_field_id, PDO::PARAM_INT);
                        break;
                    case 'table_extension_id':
                        $stmt->bindValue($identifier, $this->table_extension_id, PDO::PARAM_INT);
                        break;
                    case 'field_definition_id':
                        $stmt->bindValue($identifier, $this->field_definition_id, PDO::PARAM_INT);
                        break;
                    case 'require_index':
                        $stmt->bindValue($identifier, $this->require_index, PDO::PARAM_BOOL);
                        break;
                    case 'active_flag':
                        $stmt->bindValue($identifier, $this->active_flag, PDO::PARAM_BOOL);
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

            if ($this->aTableExtension !== null) {
                if (!$this->aTableExtension->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aTableExtension->getValidationFailures());
                }
            }

            if ($this->aFieldDefinition !== null) {
                if (!$this->aFieldDefinition->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aFieldDefinition->getValidationFailures());
                }
            }


            if (($retval = TableExtensionFieldPeer::doValidate($this, $columns)) !== true) {
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
        $pos = TableExtensionFieldPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getTableExtensionFieldId();
                break;
            case 1:
                return $this->getTableExtensionId();
                break;
            case 2:
                return $this->getFieldDefinitionId();
                break;
            case 3:
                return $this->getRequireIndex();
                break;
            case 4:
                return $this->getActiveFlag();
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
        if (isset($alreadyDumpedObjects['TableExtensionField'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['TableExtensionField'][$this->getPrimaryKey()] = true;
        $keys = TableExtensionFieldPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getTableExtensionFieldId(),
            $keys[1] => $this->getTableExtensionId(),
            $keys[2] => $this->getFieldDefinitionId(),
            $keys[3] => $this->getRequireIndex(),
            $keys[4] => $this->getActiveFlag(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aTableExtension) {
                $result['TableExtension'] = $this->aTableExtension->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aFieldDefinition) {
                $result['FieldDefinition'] = $this->aFieldDefinition->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
        $pos = TableExtensionFieldPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setTableExtensionFieldId($value);
                break;
            case 1:
                $this->setTableExtensionId($value);
                break;
            case 2:
                $this->setFieldDefinitionId($value);
                break;
            case 3:
                $this->setRequireIndex($value);
                break;
            case 4:
                $this->setActiveFlag($value);
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
        $keys = TableExtensionFieldPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setTableExtensionFieldId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setTableExtensionId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setFieldDefinitionId($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setRequireIndex($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setActiveFlag($arr[$keys[4]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(TableExtensionFieldPeer::DATABASE_NAME);

        if ($this->isColumnModified(TableExtensionFieldPeer::TABLE_EXTENSION_FIELD_ID)) $criteria->add(TableExtensionFieldPeer::TABLE_EXTENSION_FIELD_ID, $this->table_extension_field_id);
        if ($this->isColumnModified(TableExtensionFieldPeer::TABLE_EXTENSION_ID)) $criteria->add(TableExtensionFieldPeer::TABLE_EXTENSION_ID, $this->table_extension_id);
        if ($this->isColumnModified(TableExtensionFieldPeer::FIELD_DEFINITION_ID)) $criteria->add(TableExtensionFieldPeer::FIELD_DEFINITION_ID, $this->field_definition_id);
        if ($this->isColumnModified(TableExtensionFieldPeer::REQUIRE_INDEX)) $criteria->add(TableExtensionFieldPeer::REQUIRE_INDEX, $this->require_index);
        if ($this->isColumnModified(TableExtensionFieldPeer::ACTIVE_FLAG)) $criteria->add(TableExtensionFieldPeer::ACTIVE_FLAG, $this->active_flag);

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
        $criteria = new Criteria(TableExtensionFieldPeer::DATABASE_NAME);
        $criteria->add(TableExtensionFieldPeer::TABLE_EXTENSION_FIELD_ID, $this->table_extension_field_id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getTableExtensionFieldId();
    }

    /**
     * Generic method to set the primary key (table_extension_field_id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setTableExtensionFieldId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getTableExtensionFieldId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of TableExtensionField (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setTableExtensionId($this->getTableExtensionId());
        $copyObj->setFieldDefinitionId($this->getFieldDefinitionId());
        $copyObj->setRequireIndex($this->getRequireIndex());
        $copyObj->setActiveFlag($this->getActiveFlag());

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
            $copyObj->setTableExtensionFieldId(NULL); // this is a auto-increment column, so set to default value
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
     * @return TableExtensionField Clone of current object.
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
     * @return TableExtensionFieldPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new TableExtensionFieldPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a TableExtension object.
     *
     * @param                  TableExtension $v
     * @return TableExtensionField The current object (for fluent API support)
     * @throws PropelException
     */
    public function setTableExtension(TableExtension $v = null)
    {
        if ($v === null) {
            $this->setTableExtensionId(NULL);
        } else {
            $this->setTableExtensionId($v->getTableExtensionId());
        }

        $this->aTableExtension = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the TableExtension object, it will not be re-added.
        if ($v !== null) {
            $v->addTableExtensionField($this);
        }


        return $this;
    }


    /**
     * Get the associated TableExtension object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return TableExtension The associated TableExtension object.
     * @throws PropelException
     */
    public function getTableExtension(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aTableExtension === null && ($this->table_extension_id !== null) && $doQuery) {
            $this->aTableExtension = TableExtensionQuery::create()->findPk($this->table_extension_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aTableExtension->addTableExtensionFields($this);
             */
        }

        return $this->aTableExtension;
    }

    /**
     * Declares an association between this object and a FieldDefinition object.
     *
     * @param                  FieldDefinition $v
     * @return TableExtensionField The current object (for fluent API support)
     * @throws PropelException
     */
    public function setFieldDefinition(FieldDefinition $v = null)
    {
        if ($v === null) {
            $this->setFieldDefinitionId(NULL);
        } else {
            $this->setFieldDefinitionId($v->getFieldDefinitionId());
        }

        $this->aFieldDefinition = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the FieldDefinition object, it will not be re-added.
        if ($v !== null) {
            $v->addTableExtensionField($this);
        }


        return $this;
    }


    /**
     * Get the associated FieldDefinition object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return FieldDefinition The associated FieldDefinition object.
     * @throws PropelException
     */
    public function getFieldDefinition(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aFieldDefinition === null && ($this->field_definition_id !== null) && $doQuery) {
            $this->aFieldDefinition = FieldDefinitionQuery::create()->findPk($this->field_definition_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aFieldDefinition->addTableExtensionFields($this);
             */
        }

        return $this->aFieldDefinition;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->table_extension_field_id = null;
        $this->table_extension_id = null;
        $this->field_definition_id = null;
        $this->require_index = null;
        $this->active_flag = null;
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
            if ($this->aTableExtension instanceof Persistent) {
              $this->aTableExtension->clearAllReferences($deep);
            }
            if ($this->aFieldDefinition instanceof Persistent) {
              $this->aFieldDefinition->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        $this->aTableExtension = null;
        $this->aFieldDefinition = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(TableExtensionFieldPeer::DEFAULT_STRING_FORMAT);
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

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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\FieldDefinition;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\FieldDefinitionPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\FieldDefinitionQuery;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\FileProperty;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\FilePropertyQuery;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtensionField;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtensionFieldQuery;
use Eulogix\Cool\Lib\Database\Propel\CoolPropelObject;

abstract class BaseFieldDefinition extends CoolPropelObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\FieldDefinitionPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        FieldDefinitionPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the field_definition_id field.
     * @var        int
     */
    protected $field_definition_id;

    /**
     * The value for the name field.
     * @var        string
     */
    protected $name;

    /**
     * The value for the type field.
     * @var        string
     */
    protected $type;

    /**
     * The value for the control_type field.
     * @var        string
     */
    protected $control_type;

    /**
     * The value for the lookup_type field.
     * @var        string
     */
    protected $lookup_type;

    /**
     * The value for the lookup field.
     * @var        string
     */
    protected $lookup;

    /**
     * The value for the description field.
     * @var        string
     */
    protected $description;

    /**
     * @var        PropelObjectCollection|TableExtensionField[] Collection to store aggregation of TableExtensionField objects.
     */
    protected $collTableExtensionFields;
    protected $collTableExtensionFieldsPartial;

    /**
     * @var        PropelObjectCollection|FileProperty[] Collection to store aggregation of FileProperty objects.
     */
    protected $collFileProperties;
    protected $collFilePropertiesPartial;

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
    protected $tableExtensionFieldsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $filePropertiesScheduledForDeletion = null;

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
     * Get the [name] column value.
     *
     * @return string
     */
    public function getName()
    {

        return $this->name;
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
     * Get the [control_type] column value.
     *
     * @return string
     */
    public function getControlType()
    {

        return $this->control_type;
    }

    /**
     * Get the [lookup_type] column value.
     *
     * @return string
     */
    public function getLookupType()
    {

        return $this->lookup_type;
    }

    /**
     * Get the [lookup] column value.
     * table,OTLT: the domain name, FK: the FQN of the table this field contains a FK to, enum: comma separated list of values, valueMap: a namespaced class name implementing ValueMapInterface, valueMapService: the service id of the valueMap
     * @return string
     */
    public function getLookup()
    {

        return $this->lookup;
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
     * Set the value of [field_definition_id] column.
     *
     * @param  int $v new value
     * @return FieldDefinition The current object (for fluent API support)
     */
    public function setFieldDefinitionId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->field_definition_id !== $v) {
            $this->field_definition_id = $v;
            $this->modifiedColumns[] = FieldDefinitionPeer::FIELD_DEFINITION_ID;
        }


        return $this;
    } // setFieldDefinitionId()

    /**
     * Set the value of [name] column.
     *
     * @param  string $v new value
     * @return FieldDefinition The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[] = FieldDefinitionPeer::NAME;
        }


        return $this;
    } // setName()

    /**
     * Set the value of [type] column.
     *
     * @param  string $v new value
     * @return FieldDefinition The current object (for fluent API support)
     */
    public function setType($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->type !== $v) {
            $this->type = $v;
            $this->modifiedColumns[] = FieldDefinitionPeer::TYPE;
        }


        return $this;
    } // setType()

    /**
     * Set the value of [control_type] column.
     *
     * @param  string $v new value
     * @return FieldDefinition The current object (for fluent API support)
     */
    public function setControlType($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->control_type !== $v) {
            $this->control_type = $v;
            $this->modifiedColumns[] = FieldDefinitionPeer::CONTROL_TYPE;
        }


        return $this;
    } // setControlType()

    /**
     * Set the value of [lookup_type] column.
     *
     * @param  string $v new value
     * @return FieldDefinition The current object (for fluent API support)
     */
    public function setLookupType($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->lookup_type !== $v) {
            $this->lookup_type = $v;
            $this->modifiedColumns[] = FieldDefinitionPeer::LOOKUP_TYPE;
        }


        return $this;
    } // setLookupType()

    /**
     * Set the value of [lookup] column.
     * table,OTLT: the domain name, FK: the FQN of the table this field contains a FK to, enum: comma separated list of values, valueMap: a namespaced class name implementing ValueMapInterface, valueMapService: the service id of the valueMap
     * @param  string $v new value
     * @return FieldDefinition The current object (for fluent API support)
     */
    public function setLookup($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->lookup !== $v) {
            $this->lookup = $v;
            $this->modifiedColumns[] = FieldDefinitionPeer::LOOKUP;
        }


        return $this;
    } // setLookup()

    /**
     * Set the value of [description] column.
     *
     * @param  string $v new value
     * @return FieldDefinition The current object (for fluent API support)
     */
    public function setDescription($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->description !== $v) {
            $this->description = $v;
            $this->modifiedColumns[] = FieldDefinitionPeer::DESCRIPTION;
        }


        return $this;
    } // setDescription()

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

            $this->field_definition_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->name = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->type = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->control_type = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->lookup_type = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->lookup = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->description = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 7; // 7 = FieldDefinitionPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating FieldDefinition object", $e);
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
            $con = Propel::getConnection(FieldDefinitionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = FieldDefinitionPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collTableExtensionFields = null;

            $this->collFileProperties = null;

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
            $con = Propel::getConnection(FieldDefinitionPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = FieldDefinitionQuery::create()
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
            $con = Propel::getConnection(FieldDefinitionPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                FieldDefinitionPeer::addInstanceToPool($this);
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

            if ($this->tableExtensionFieldsScheduledForDeletion !== null) {
                if (!$this->tableExtensionFieldsScheduledForDeletion->isEmpty()) {
                    foreach ($this->tableExtensionFieldsScheduledForDeletion as $tableExtensionField) {
                        // need to save related object because we set the relation to null
                        $tableExtensionField->save($con);
                    }
                    $this->tableExtensionFieldsScheduledForDeletion = null;
                }
            }

            if ($this->collTableExtensionFields !== null) {
                foreach ($this->collTableExtensionFields as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->filePropertiesScheduledForDeletion !== null) {
                if (!$this->filePropertiesScheduledForDeletion->isEmpty()) {
                    foreach ($this->filePropertiesScheduledForDeletion as $fileProperty) {
                        // need to save related object because we set the relation to null
                        $fileProperty->save($con);
                    }
                    $this->filePropertiesScheduledForDeletion = null;
                }
            }

            if ($this->collFileProperties !== null) {
                foreach ($this->collFileProperties as $referrerFK) {
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

        $this->modifiedColumns[] = FieldDefinitionPeer::FIELD_DEFINITION_ID;
        if (null !== $this->field_definition_id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . FieldDefinitionPeer::FIELD_DEFINITION_ID . ')');
        }
        if (null === $this->field_definition_id) {
            try {
                $stmt = $con->query("SELECT nextval('core.field_definition_field_definition_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->field_definition_id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(FieldDefinitionPeer::FIELD_DEFINITION_ID)) {
            $modifiedColumns[':p' . $index++]  = 'field_definition_id';
        }
        if ($this->isColumnModified(FieldDefinitionPeer::NAME)) {
            $modifiedColumns[':p' . $index++]  = 'name';
        }
        if ($this->isColumnModified(FieldDefinitionPeer::TYPE)) {
            $modifiedColumns[':p' . $index++]  = 'type';
        }
        if ($this->isColumnModified(FieldDefinitionPeer::CONTROL_TYPE)) {
            $modifiedColumns[':p' . $index++]  = 'control_type';
        }
        if ($this->isColumnModified(FieldDefinitionPeer::LOOKUP_TYPE)) {
            $modifiedColumns[':p' . $index++]  = 'lookup_type';
        }
        if ($this->isColumnModified(FieldDefinitionPeer::LOOKUP)) {
            $modifiedColumns[':p' . $index++]  = 'lookup';
        }
        if ($this->isColumnModified(FieldDefinitionPeer::DESCRIPTION)) {
            $modifiedColumns[':p' . $index++]  = 'description';
        }

        $sql = sprintf(
            'INSERT INTO core.field_definition (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'field_definition_id':
                        $stmt->bindValue($identifier, $this->field_definition_id, PDO::PARAM_INT);
                        break;
                    case 'name':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case 'type':
                        $stmt->bindValue($identifier, $this->type, PDO::PARAM_STR);
                        break;
                    case 'control_type':
                        $stmt->bindValue($identifier, $this->control_type, PDO::PARAM_STR);
                        break;
                    case 'lookup_type':
                        $stmt->bindValue($identifier, $this->lookup_type, PDO::PARAM_STR);
                        break;
                    case 'lookup':
                        $stmt->bindValue($identifier, $this->lookup, PDO::PARAM_STR);
                        break;
                    case 'description':
                        $stmt->bindValue($identifier, $this->description, PDO::PARAM_STR);
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


            if (($retval = FieldDefinitionPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collTableExtensionFields !== null) {
                    foreach ($this->collTableExtensionFields as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collFileProperties !== null) {
                    foreach ($this->collFileProperties as $referrerFK) {
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
        $pos = FieldDefinitionPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getFieldDefinitionId();
                break;
            case 1:
                return $this->getName();
                break;
            case 2:
                return $this->getType();
                break;
            case 3:
                return $this->getControlType();
                break;
            case 4:
                return $this->getLookupType();
                break;
            case 5:
                return $this->getLookup();
                break;
            case 6:
                return $this->getDescription();
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
        if (isset($alreadyDumpedObjects['FieldDefinition'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['FieldDefinition'][$this->getPrimaryKey()] = true;
        $keys = FieldDefinitionPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getFieldDefinitionId(),
            $keys[1] => $this->getName(),
            $keys[2] => $this->getType(),
            $keys[3] => $this->getControlType(),
            $keys[4] => $this->getLookupType(),
            $keys[5] => $this->getLookup(),
            $keys[6] => $this->getDescription(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collTableExtensionFields) {
                $result['TableExtensionFields'] = $this->collTableExtensionFields->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collFileProperties) {
                $result['FileProperties'] = $this->collFileProperties->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = FieldDefinitionPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setFieldDefinitionId($value);
                break;
            case 1:
                $this->setName($value);
                break;
            case 2:
                $this->setType($value);
                break;
            case 3:
                $this->setControlType($value);
                break;
            case 4:
                $this->setLookupType($value);
                break;
            case 5:
                $this->setLookup($value);
                break;
            case 6:
                $this->setDescription($value);
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
        $keys = FieldDefinitionPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setFieldDefinitionId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setName($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setType($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setControlType($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setLookupType($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setLookup($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setDescription($arr[$keys[6]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(FieldDefinitionPeer::DATABASE_NAME);

        if ($this->isColumnModified(FieldDefinitionPeer::FIELD_DEFINITION_ID)) $criteria->add(FieldDefinitionPeer::FIELD_DEFINITION_ID, $this->field_definition_id);
        if ($this->isColumnModified(FieldDefinitionPeer::NAME)) $criteria->add(FieldDefinitionPeer::NAME, $this->name);
        if ($this->isColumnModified(FieldDefinitionPeer::TYPE)) $criteria->add(FieldDefinitionPeer::TYPE, $this->type);
        if ($this->isColumnModified(FieldDefinitionPeer::CONTROL_TYPE)) $criteria->add(FieldDefinitionPeer::CONTROL_TYPE, $this->control_type);
        if ($this->isColumnModified(FieldDefinitionPeer::LOOKUP_TYPE)) $criteria->add(FieldDefinitionPeer::LOOKUP_TYPE, $this->lookup_type);
        if ($this->isColumnModified(FieldDefinitionPeer::LOOKUP)) $criteria->add(FieldDefinitionPeer::LOOKUP, $this->lookup);
        if ($this->isColumnModified(FieldDefinitionPeer::DESCRIPTION)) $criteria->add(FieldDefinitionPeer::DESCRIPTION, $this->description);

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
        $criteria = new Criteria(FieldDefinitionPeer::DATABASE_NAME);
        $criteria->add(FieldDefinitionPeer::FIELD_DEFINITION_ID, $this->field_definition_id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getFieldDefinitionId();
    }

    /**
     * Generic method to set the primary key (field_definition_id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setFieldDefinitionId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getFieldDefinitionId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of FieldDefinition (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setName($this->getName());
        $copyObj->setType($this->getType());
        $copyObj->setControlType($this->getControlType());
        $copyObj->setLookupType($this->getLookupType());
        $copyObj->setLookup($this->getLookup());
        $copyObj->setDescription($this->getDescription());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getTableExtensionFields() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addTableExtensionField($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getFileProperties() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addFileProperty($relObj->copy($deepCopy));
                }
            }

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setFieldDefinitionId(NULL); // this is a auto-increment column, so set to default value
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
     * @return FieldDefinition Clone of current object.
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
     * @return FieldDefinitionPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new FieldDefinitionPeer();
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
        if ('TableExtensionField' == $relationName) {
            $this->initTableExtensionFields();
        }
        if ('FileProperty' == $relationName) {
            $this->initFileProperties();
        }
    }

    /**
     * Clears out the collTableExtensionFields collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return FieldDefinition The current object (for fluent API support)
     * @see        addTableExtensionFields()
     */
    public function clearTableExtensionFields()
    {
        $this->collTableExtensionFields = null; // important to set this to null since that means it is uninitialized
        $this->collTableExtensionFieldsPartial = null;

        return $this;
    }

    /**
     * reset is the collTableExtensionFields collection loaded partially
     *
     * @return void
     */
    public function resetPartialTableExtensionFields($v = true)
    {
        $this->collTableExtensionFieldsPartial = $v;
    }

    /**
     * Initializes the collTableExtensionFields collection.
     *
     * By default this just sets the collTableExtensionFields collection to an empty array (like clearcollTableExtensionFields());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initTableExtensionFields($overrideExisting = true)
    {
        if (null !== $this->collTableExtensionFields && !$overrideExisting) {
            return;
        }
        $this->collTableExtensionFields = new PropelObjectCollection();
        $this->collTableExtensionFields->setModel('TableExtensionField');
    }

    /**
     * Gets an array of TableExtensionField objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this FieldDefinition is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|TableExtensionField[] List of TableExtensionField objects
     * @throws PropelException
     */
    public function getTableExtensionFields($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collTableExtensionFieldsPartial && !$this->isNew();
        if (null === $this->collTableExtensionFields || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collTableExtensionFields) {
                // return empty collection
                $this->initTableExtensionFields();
            } else {
                $collTableExtensionFields = TableExtensionFieldQuery::create(null, $criteria)
                    ->filterByFieldDefinition($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collTableExtensionFieldsPartial && count($collTableExtensionFields)) {
                      $this->initTableExtensionFields(false);

                      foreach ($collTableExtensionFields as $obj) {
                        if (false == $this->collTableExtensionFields->contains($obj)) {
                          $this->collTableExtensionFields->append($obj);
                        }
                      }

                      $this->collTableExtensionFieldsPartial = true;
                    }

                    $collTableExtensionFields->getInternalIterator()->rewind();

                    return $collTableExtensionFields;
                }

                if ($partial && $this->collTableExtensionFields) {
                    foreach ($this->collTableExtensionFields as $obj) {
                        if ($obj->isNew()) {
                            $collTableExtensionFields[] = $obj;
                        }
                    }
                }

                $this->collTableExtensionFields = $collTableExtensionFields;
                $this->collTableExtensionFieldsPartial = false;
            }
        }

        return $this->collTableExtensionFields;
    }

    /**
     * Sets a collection of TableExtensionField objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $tableExtensionFields A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return FieldDefinition The current object (for fluent API support)
     */
    public function setTableExtensionFields(PropelCollection $tableExtensionFields, PropelPDO $con = null)
    {
        $tableExtensionFieldsToDelete = $this->getTableExtensionFields(new Criteria(), $con)->diff($tableExtensionFields);


        $this->tableExtensionFieldsScheduledForDeletion = $tableExtensionFieldsToDelete;

        foreach ($tableExtensionFieldsToDelete as $tableExtensionFieldRemoved) {
            $tableExtensionFieldRemoved->setFieldDefinition(null);
        }

        $this->collTableExtensionFields = null;
        foreach ($tableExtensionFields as $tableExtensionField) {
            $this->addTableExtensionField($tableExtensionField);
        }

        $this->collTableExtensionFields = $tableExtensionFields;
        $this->collTableExtensionFieldsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related TableExtensionField objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related TableExtensionField objects.
     * @throws PropelException
     */
    public function countTableExtensionFields(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collTableExtensionFieldsPartial && !$this->isNew();
        if (null === $this->collTableExtensionFields || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collTableExtensionFields) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getTableExtensionFields());
            }
            $query = TableExtensionFieldQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByFieldDefinition($this)
                ->count($con);
        }

        return count($this->collTableExtensionFields);
    }

    /**
     * Method called to associate a TableExtensionField object to this object
     * through the TableExtensionField foreign key attribute.
     *
     * @param    TableExtensionField $l TableExtensionField
     * @return FieldDefinition The current object (for fluent API support)
     */
    public function addTableExtensionField(TableExtensionField $l)
    {
        if ($this->collTableExtensionFields === null) {
            $this->initTableExtensionFields();
            $this->collTableExtensionFieldsPartial = true;
        }

        if (!in_array($l, $this->collTableExtensionFields->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddTableExtensionField($l);

            if ($this->tableExtensionFieldsScheduledForDeletion and $this->tableExtensionFieldsScheduledForDeletion->contains($l)) {
                $this->tableExtensionFieldsScheduledForDeletion->remove($this->tableExtensionFieldsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	TableExtensionField $tableExtensionField The tableExtensionField object to add.
     */
    protected function doAddTableExtensionField($tableExtensionField)
    {
        $this->collTableExtensionFields[]= $tableExtensionField;
        $tableExtensionField->setFieldDefinition($this);
    }

    /**
     * @param	TableExtensionField $tableExtensionField The tableExtensionField object to remove.
     * @return FieldDefinition The current object (for fluent API support)
     */
    public function removeTableExtensionField($tableExtensionField)
    {
        if ($this->getTableExtensionFields()->contains($tableExtensionField)) {
            $this->collTableExtensionFields->remove($this->collTableExtensionFields->search($tableExtensionField));
            if (null === $this->tableExtensionFieldsScheduledForDeletion) {
                $this->tableExtensionFieldsScheduledForDeletion = clone $this->collTableExtensionFields;
                $this->tableExtensionFieldsScheduledForDeletion->clear();
            }
            $this->tableExtensionFieldsScheduledForDeletion[]= $tableExtensionField;
            $tableExtensionField->setFieldDefinition(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this FieldDefinition is new, it will return
     * an empty collection; or if this FieldDefinition has previously
     * been saved, it will retrieve related TableExtensionFields from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in FieldDefinition.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|TableExtensionField[] List of TableExtensionField objects
     */
    public function getTableExtensionFieldsJoinTableExtension($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TableExtensionFieldQuery::create(null, $criteria);
        $query->joinWith('TableExtension', $join_behavior);

        return $this->getTableExtensionFields($query, $con);
    }

    /**
     * Clears out the collFileProperties collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return FieldDefinition The current object (for fluent API support)
     * @see        addFileProperties()
     */
    public function clearFileProperties()
    {
        $this->collFileProperties = null; // important to set this to null since that means it is uninitialized
        $this->collFilePropertiesPartial = null;

        return $this;
    }

    /**
     * reset is the collFileProperties collection loaded partially
     *
     * @return void
     */
    public function resetPartialFileProperties($v = true)
    {
        $this->collFilePropertiesPartial = $v;
    }

    /**
     * Initializes the collFileProperties collection.
     *
     * By default this just sets the collFileProperties collection to an empty array (like clearcollFileProperties());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initFileProperties($overrideExisting = true)
    {
        if (null !== $this->collFileProperties && !$overrideExisting) {
            return;
        }
        $this->collFileProperties = new PropelObjectCollection();
        $this->collFileProperties->setModel('FileProperty');
    }

    /**
     * Gets an array of FileProperty objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this FieldDefinition is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|FileProperty[] List of FileProperty objects
     * @throws PropelException
     */
    public function getFileProperties($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collFilePropertiesPartial && !$this->isNew();
        if (null === $this->collFileProperties || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collFileProperties) {
                // return empty collection
                $this->initFileProperties();
            } else {
                $collFileProperties = FilePropertyQuery::create(null, $criteria)
                    ->filterByFieldDefinition($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collFilePropertiesPartial && count($collFileProperties)) {
                      $this->initFileProperties(false);

                      foreach ($collFileProperties as $obj) {
                        if (false == $this->collFileProperties->contains($obj)) {
                          $this->collFileProperties->append($obj);
                        }
                      }

                      $this->collFilePropertiesPartial = true;
                    }

                    $collFileProperties->getInternalIterator()->rewind();

                    return $collFileProperties;
                }

                if ($partial && $this->collFileProperties) {
                    foreach ($this->collFileProperties as $obj) {
                        if ($obj->isNew()) {
                            $collFileProperties[] = $obj;
                        }
                    }
                }

                $this->collFileProperties = $collFileProperties;
                $this->collFilePropertiesPartial = false;
            }
        }

        return $this->collFileProperties;
    }

    /**
     * Sets a collection of FileProperty objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $fileProperties A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return FieldDefinition The current object (for fluent API support)
     */
    public function setFileProperties(PropelCollection $fileProperties, PropelPDO $con = null)
    {
        $filePropertiesToDelete = $this->getFileProperties(new Criteria(), $con)->diff($fileProperties);


        $this->filePropertiesScheduledForDeletion = $filePropertiesToDelete;

        foreach ($filePropertiesToDelete as $filePropertyRemoved) {
            $filePropertyRemoved->setFieldDefinition(null);
        }

        $this->collFileProperties = null;
        foreach ($fileProperties as $fileProperty) {
            $this->addFileProperty($fileProperty);
        }

        $this->collFileProperties = $fileProperties;
        $this->collFilePropertiesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related FileProperty objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related FileProperty objects.
     * @throws PropelException
     */
    public function countFileProperties(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collFilePropertiesPartial && !$this->isNew();
        if (null === $this->collFileProperties || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collFileProperties) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getFileProperties());
            }
            $query = FilePropertyQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByFieldDefinition($this)
                ->count($con);
        }

        return count($this->collFileProperties);
    }

    /**
     * Method called to associate a FileProperty object to this object
     * through the FileProperty foreign key attribute.
     *
     * @param    FileProperty $l FileProperty
     * @return FieldDefinition The current object (for fluent API support)
     */
    public function addFileProperty(FileProperty $l)
    {
        if ($this->collFileProperties === null) {
            $this->initFileProperties();
            $this->collFilePropertiesPartial = true;
        }

        if (!in_array($l, $this->collFileProperties->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddFileProperty($l);

            if ($this->filePropertiesScheduledForDeletion and $this->filePropertiesScheduledForDeletion->contains($l)) {
                $this->filePropertiesScheduledForDeletion->remove($this->filePropertiesScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	FileProperty $fileProperty The fileProperty object to add.
     */
    protected function doAddFileProperty($fileProperty)
    {
        $this->collFileProperties[]= $fileProperty;
        $fileProperty->setFieldDefinition($this);
    }

    /**
     * @param	FileProperty $fileProperty The fileProperty object to remove.
     * @return FieldDefinition The current object (for fluent API support)
     */
    public function removeFileProperty($fileProperty)
    {
        if ($this->getFileProperties()->contains($fileProperty)) {
            $this->collFileProperties->remove($this->collFileProperties->search($fileProperty));
            if (null === $this->filePropertiesScheduledForDeletion) {
                $this->filePropertiesScheduledForDeletion = clone $this->collFileProperties;
                $this->filePropertiesScheduledForDeletion->clear();
            }
            $this->filePropertiesScheduledForDeletion[]= $fileProperty;
            $fileProperty->setFieldDefinition(null);
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->field_definition_id = null;
        $this->name = null;
        $this->type = null;
        $this->control_type = null;
        $this->lookup_type = null;
        $this->lookup = null;
        $this->description = null;
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
            if ($this->collTableExtensionFields) {
                foreach ($this->collTableExtensionFields as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collFileProperties) {
                foreach ($this->collFileProperties as $o) {
                    $o->clearAllReferences($deep);
                }
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collTableExtensionFields instanceof PropelCollection) {
            $this->collTableExtensionFields->clearIterator();
        }
        $this->collTableExtensionFields = null;
        if ($this->collFileProperties instanceof PropelCollection) {
            $this->collFileProperties->clearIterator();
        }
        $this->collFileProperties = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(FieldDefinitionPeer::DEFAULT_STRING_FORMAT);
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

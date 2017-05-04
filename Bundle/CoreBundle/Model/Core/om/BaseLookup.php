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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\Lookup;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\LookupPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\LookupQuery;
use Eulogix\Cool\Lib\Database\Propel\CoolPropelObject;

abstract class BaseLookup extends CoolPropelObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\LookupPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        LookupPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the lookup_id field.
     * @var        int
     */
    protected $lookup_id;

    /**
     * The value for the domain_name field.
     * @var        string
     */
    protected $domain_name;

    /**
     * The value for the value field.
     * @var        string
     */
    protected $value;

    /**
     * The value for the dec_it field.
     * @var        string
     */
    protected $dec_it;

    /**
     * The value for the dec_en field.
     * @var        string
     */
    protected $dec_en;

    /**
     * The value for the dec_es field.
     * @var        string
     */
    protected $dec_es;

    /**
     * The value for the dec_pt field.
     * @var        string
     */
    protected $dec_pt;

    /**
     * The value for the sort_order field.
     * @var        int
     */
    protected $sort_order;

    /**
     * The value for the schema_filter field.
     * @var        string
     */
    protected $schema_filter;

    /**
     * The value for the filter field.
     * @var        string
     */
    protected $filter;

    /**
     * The value for the ext field.
     * @var        string
     */
    protected $ext;

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
     * Get the [lookup_id] column value.
     *
     * @return int
     */
    public function getLookupId()
    {

        return $this->lookup_id;
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
     * Get the [value] column value.
     *
     * @return string
     */
    public function getValue()
    {

        return $this->value;
    }

    /**
     * Get the [dec_it] column value.
     *
     * @return string
     */
    public function getDecIt()
    {

        return $this->dec_it;
    }

    /**
     * Get the [dec_en] column value.
     *
     * @return string
     */
    public function getDecEn()
    {

        return $this->dec_en;
    }

    /**
     * Get the [dec_es] column value.
     *
     * @return string
     */
    public function getDecEs()
    {

        return $this->dec_es;
    }

    /**
     * Get the [dec_pt] column value.
     *
     * @return string
     */
    public function getDecPt()
    {

        return $this->dec_pt;
    }

    /**
     * Get the [sort_order] column value.
     *
     * @return int
     */
    public function getSortOrder()
    {

        return $this->sort_order;
    }

    /**
     * Get the [schema_filter] column value.
     * this field allows to define specific lookup sets for multi tenant schemas
     * @return string
     */
    public function getSchemaFilter()
    {

        return $this->schema_filter;
    }

    /**
     * Get the [filter] column value.
     *
     * @return string
     */
    public function getFilter()
    {

        return $this->filter;
    }

    /**
     * Get the [ext] column value.
     *
     * @return string
     */
    public function getExt()
    {

        return $this->ext;
    }

    /**
     * Set the value of [lookup_id] column.
     *
     * @param  int $v new value
     * @return Lookup The current object (for fluent API support)
     */
    public function setLookupId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->lookup_id !== $v) {
            $this->lookup_id = $v;
            $this->modifiedColumns[] = LookupPeer::LOOKUP_ID;
        }


        return $this;
    } // setLookupId()

    /**
     * Set the value of [domain_name] column.
     *
     * @param  string $v new value
     * @return Lookup The current object (for fluent API support)
     */
    public function setDomainName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->domain_name !== $v) {
            $this->domain_name = $v;
            $this->modifiedColumns[] = LookupPeer::DOMAIN_NAME;
        }


        return $this;
    } // setDomainName()

    /**
     * Set the value of [value] column.
     *
     * @param  string $v new value
     * @return Lookup The current object (for fluent API support)
     */
    public function setValue($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->value !== $v) {
            $this->value = $v;
            $this->modifiedColumns[] = LookupPeer::VALUE;
        }


        return $this;
    } // setValue()

    /**
     * Set the value of [dec_it] column.
     *
     * @param  string $v new value
     * @return Lookup The current object (for fluent API support)
     */
    public function setDecIt($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->dec_it !== $v) {
            $this->dec_it = $v;
            $this->modifiedColumns[] = LookupPeer::DEC_IT;
        }


        return $this;
    } // setDecIt()

    /**
     * Set the value of [dec_en] column.
     *
     * @param  string $v new value
     * @return Lookup The current object (for fluent API support)
     */
    public function setDecEn($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->dec_en !== $v) {
            $this->dec_en = $v;
            $this->modifiedColumns[] = LookupPeer::DEC_EN;
        }


        return $this;
    } // setDecEn()

    /**
     * Set the value of [dec_es] column.
     *
     * @param  string $v new value
     * @return Lookup The current object (for fluent API support)
     */
    public function setDecEs($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->dec_es !== $v) {
            $this->dec_es = $v;
            $this->modifiedColumns[] = LookupPeer::DEC_ES;
        }


        return $this;
    } // setDecEs()

    /**
     * Set the value of [dec_pt] column.
     *
     * @param  string $v new value
     * @return Lookup The current object (for fluent API support)
     */
    public function setDecPt($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->dec_pt !== $v) {
            $this->dec_pt = $v;
            $this->modifiedColumns[] = LookupPeer::DEC_PT;
        }


        return $this;
    } // setDecPt()

    /**
     * Set the value of [sort_order] column.
     *
     * @param  int $v new value
     * @return Lookup The current object (for fluent API support)
     */
    public function setSortOrder($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->sort_order !== $v) {
            $this->sort_order = $v;
            $this->modifiedColumns[] = LookupPeer::SORT_ORDER;
        }


        return $this;
    } // setSortOrder()

    /**
     * Set the value of [schema_filter] column.
     * this field allows to define specific lookup sets for multi tenant schemas
     * @param  string $v new value
     * @return Lookup The current object (for fluent API support)
     */
    public function setSchemaFilter($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->schema_filter !== $v) {
            $this->schema_filter = $v;
            $this->modifiedColumns[] = LookupPeer::SCHEMA_FILTER;
        }


        return $this;
    } // setSchemaFilter()

    /**
     * Set the value of [filter] column.
     *
     * @param  string $v new value
     * @return Lookup The current object (for fluent API support)
     */
    public function setFilter($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->filter !== $v) {
            $this->filter = $v;
            $this->modifiedColumns[] = LookupPeer::FILTER;
        }


        return $this;
    } // setFilter()

    /**
     * Set the value of [ext] column.
     *
     * @param  string $v new value
     * @return Lookup The current object (for fluent API support)
     */
    public function setExt($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->ext !== $v) {
            $this->ext = $v;
            $this->modifiedColumns[] = LookupPeer::EXT;
        }


        return $this;
    } // setExt()

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

            $this->lookup_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->domain_name = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->value = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->dec_it = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->dec_en = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->dec_es = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->dec_pt = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->sort_order = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
            $this->schema_filter = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->filter = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
            $this->ext = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 11; // 11 = LookupPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Lookup object", $e);
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
            $con = Propel::getConnection(LookupPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = LookupPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
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
            $con = Propel::getConnection(LookupPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = LookupQuery::create()
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
            $con = Propel::getConnection(LookupPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                LookupPeer::addInstanceToPool($this);
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

        $this->modifiedColumns[] = LookupPeer::LOOKUP_ID;
        if (null !== $this->lookup_id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . LookupPeer::LOOKUP_ID . ')');
        }
        if (null === $this->lookup_id) {
            try {
                $stmt = $con->query("SELECT nextval('core.lookup_lookup_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->lookup_id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(LookupPeer::LOOKUP_ID)) {
            $modifiedColumns[':p' . $index++]  = 'lookup_id';
        }
        if ($this->isColumnModified(LookupPeer::DOMAIN_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'domain_name';
        }
        if ($this->isColumnModified(LookupPeer::VALUE)) {
            $modifiedColumns[':p' . $index++]  = 'value';
        }
        if ($this->isColumnModified(LookupPeer::DEC_IT)) {
            $modifiedColumns[':p' . $index++]  = 'dec_it';
        }
        if ($this->isColumnModified(LookupPeer::DEC_EN)) {
            $modifiedColumns[':p' . $index++]  = 'dec_en';
        }
        if ($this->isColumnModified(LookupPeer::DEC_ES)) {
            $modifiedColumns[':p' . $index++]  = 'dec_es';
        }
        if ($this->isColumnModified(LookupPeer::DEC_PT)) {
            $modifiedColumns[':p' . $index++]  = 'dec_pt';
        }
        if ($this->isColumnModified(LookupPeer::SORT_ORDER)) {
            $modifiedColumns[':p' . $index++]  = 'sort_order';
        }
        if ($this->isColumnModified(LookupPeer::SCHEMA_FILTER)) {
            $modifiedColumns[':p' . $index++]  = 'schema_filter';
        }
        if ($this->isColumnModified(LookupPeer::FILTER)) {
            $modifiedColumns[':p' . $index++]  = 'filter';
        }
        if ($this->isColumnModified(LookupPeer::EXT)) {
            $modifiedColumns[':p' . $index++]  = 'ext';
        }

        $sql = sprintf(
            'INSERT INTO core.lookup (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'lookup_id':
                        $stmt->bindValue($identifier, $this->lookup_id, PDO::PARAM_INT);
                        break;
                    case 'domain_name':
                        $stmt->bindValue($identifier, $this->domain_name, PDO::PARAM_STR);
                        break;
                    case 'value':
                        $stmt->bindValue($identifier, $this->value, PDO::PARAM_STR);
                        break;
                    case 'dec_it':
                        $stmt->bindValue($identifier, $this->dec_it, PDO::PARAM_STR);
                        break;
                    case 'dec_en':
                        $stmt->bindValue($identifier, $this->dec_en, PDO::PARAM_STR);
                        break;
                    case 'dec_es':
                        $stmt->bindValue($identifier, $this->dec_es, PDO::PARAM_STR);
                        break;
                    case 'dec_pt':
                        $stmt->bindValue($identifier, $this->dec_pt, PDO::PARAM_STR);
                        break;
                    case 'sort_order':
                        $stmt->bindValue($identifier, $this->sort_order, PDO::PARAM_INT);
                        break;
                    case 'schema_filter':
                        $stmt->bindValue($identifier, $this->schema_filter, PDO::PARAM_STR);
                        break;
                    case 'filter':
                        $stmt->bindValue($identifier, $this->filter, PDO::PARAM_STR);
                        break;
                    case 'ext':
                        $stmt->bindValue($identifier, $this->ext, PDO::PARAM_STR);
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


            if (($retval = LookupPeer::doValidate($this, $columns)) !== true) {
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
        $pos = LookupPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getLookupId();
                break;
            case 1:
                return $this->getDomainName();
                break;
            case 2:
                return $this->getValue();
                break;
            case 3:
                return $this->getDecIt();
                break;
            case 4:
                return $this->getDecEn();
                break;
            case 5:
                return $this->getDecEs();
                break;
            case 6:
                return $this->getDecPt();
                break;
            case 7:
                return $this->getSortOrder();
                break;
            case 8:
                return $this->getSchemaFilter();
                break;
            case 9:
                return $this->getFilter();
                break;
            case 10:
                return $this->getExt();
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
        if (isset($alreadyDumpedObjects['Lookup'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Lookup'][$this->getPrimaryKey()] = true;
        $keys = LookupPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getLookupId(),
            $keys[1] => $this->getDomainName(),
            $keys[2] => $this->getValue(),
            $keys[3] => $this->getDecIt(),
            $keys[4] => $this->getDecEn(),
            $keys[5] => $this->getDecEs(),
            $keys[6] => $this->getDecPt(),
            $keys[7] => $this->getSortOrder(),
            $keys[8] => $this->getSchemaFilter(),
            $keys[9] => $this->getFilter(),
            $keys[10] => $this->getExt(),
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
        $pos = LookupPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setLookupId($value);
                break;
            case 1:
                $this->setDomainName($value);
                break;
            case 2:
                $this->setValue($value);
                break;
            case 3:
                $this->setDecIt($value);
                break;
            case 4:
                $this->setDecEn($value);
                break;
            case 5:
                $this->setDecEs($value);
                break;
            case 6:
                $this->setDecPt($value);
                break;
            case 7:
                $this->setSortOrder($value);
                break;
            case 8:
                $this->setSchemaFilter($value);
                break;
            case 9:
                $this->setFilter($value);
                break;
            case 10:
                $this->setExt($value);
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
        $keys = LookupPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setLookupId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setDomainName($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setValue($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setDecIt($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setDecEn($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setDecEs($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setDecPt($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setSortOrder($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setSchemaFilter($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setFilter($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setExt($arr[$keys[10]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(LookupPeer::DATABASE_NAME);

        if ($this->isColumnModified(LookupPeer::LOOKUP_ID)) $criteria->add(LookupPeer::LOOKUP_ID, $this->lookup_id);
        if ($this->isColumnModified(LookupPeer::DOMAIN_NAME)) $criteria->add(LookupPeer::DOMAIN_NAME, $this->domain_name);
        if ($this->isColumnModified(LookupPeer::VALUE)) $criteria->add(LookupPeer::VALUE, $this->value);
        if ($this->isColumnModified(LookupPeer::DEC_IT)) $criteria->add(LookupPeer::DEC_IT, $this->dec_it);
        if ($this->isColumnModified(LookupPeer::DEC_EN)) $criteria->add(LookupPeer::DEC_EN, $this->dec_en);
        if ($this->isColumnModified(LookupPeer::DEC_ES)) $criteria->add(LookupPeer::DEC_ES, $this->dec_es);
        if ($this->isColumnModified(LookupPeer::DEC_PT)) $criteria->add(LookupPeer::DEC_PT, $this->dec_pt);
        if ($this->isColumnModified(LookupPeer::SORT_ORDER)) $criteria->add(LookupPeer::SORT_ORDER, $this->sort_order);
        if ($this->isColumnModified(LookupPeer::SCHEMA_FILTER)) $criteria->add(LookupPeer::SCHEMA_FILTER, $this->schema_filter);
        if ($this->isColumnModified(LookupPeer::FILTER)) $criteria->add(LookupPeer::FILTER, $this->filter);
        if ($this->isColumnModified(LookupPeer::EXT)) $criteria->add(LookupPeer::EXT, $this->ext);

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
        $criteria = new Criteria(LookupPeer::DATABASE_NAME);
        $criteria->add(LookupPeer::LOOKUP_ID, $this->lookup_id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getLookupId();
    }

    /**
     * Generic method to set the primary key (lookup_id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setLookupId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getLookupId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of Lookup (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setDomainName($this->getDomainName());
        $copyObj->setValue($this->getValue());
        $copyObj->setDecIt($this->getDecIt());
        $copyObj->setDecEn($this->getDecEn());
        $copyObj->setDecEs($this->getDecEs());
        $copyObj->setDecPt($this->getDecPt());
        $copyObj->setSortOrder($this->getSortOrder());
        $copyObj->setSchemaFilter($this->getSchemaFilter());
        $copyObj->setFilter($this->getFilter());
        $copyObj->setExt($this->getExt());
        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setLookupId(NULL); // this is a auto-increment column, so set to default value
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
     * @return Lookup Clone of current object.
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
     * @return LookupPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new LookupPeer();
        }

        return self::$peer;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->lookup_id = null;
        $this->domain_name = null;
        $this->value = null;
        $this->dec_it = null;
        $this->dec_en = null;
        $this->dec_es = null;
        $this->dec_pt = null;
        $this->sort_order = null;
        $this->schema_filter = null;
        $this->filter = null;
        $this->ext = null;
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
        return (string) $this->exportTo(LookupPeer::DEFAULT_STRING_FORMAT);
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

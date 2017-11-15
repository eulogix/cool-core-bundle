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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\CodeSnippet;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\CodeSnippetPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\CodeSnippetQuery;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\CodeSnippetVariable;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\CodeSnippetVariableQuery;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\RuleCode;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\RuleCodeQuery;
use Eulogix\Cool\Lib\Database\Propel\CoolPropelObject;

abstract class BaseCodeSnippet extends CoolPropelObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\CodeSnippetPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        CodeSnippetPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the code_snippet_id field.
     * @var        int
     */
    protected $code_snippet_id;

    /**
     * The value for the category field.
     * @var        string
     */
    protected $category;

    /**
     * The value for the language field.
     * Note: this column has a database default value of: 'PHP'
     * @var        string
     */
    protected $language;

    /**
     * The value for the type field.
     * Note: this column has a database default value of: 'EXPRESSION'
     * @var        string
     */
    protected $type;

    /**
     * The value for the return_type field.
     * Note: this column has a database default value of: 'NONE'
     * @var        string
     */
    protected $return_type;

    /**
     * The value for the nspace field.
     * @var        string
     */
    protected $nspace;

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
     * The value for the long_description field.
     * @var        string
     */
    protected $long_description;

    /**
     * The value for the lock_updates_flag field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $lock_updates_flag;

    /**
     * The value for the snippet field.
     * @var        string
     */
    protected $snippet;

    /**
     * @var        PropelObjectCollection|RuleCode[] Collection to store aggregation of RuleCode objects.
     */
    protected $collRuleCodes;
    protected $collRuleCodesPartial;

    /**
     * @var        PropelObjectCollection|CodeSnippetVariable[] Collection to store aggregation of CodeSnippetVariable objects.
     */
    protected $collCodeSnippetVariables;
    protected $collCodeSnippetVariablesPartial;

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
    protected $codeSnippetVariablesScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->language = 'PHP';
        $this->type = 'EXPRESSION';
        $this->return_type = 'NONE';
        $this->lock_updates_flag = false;
    }

    /**
     * Initializes internal state of BaseCodeSnippet object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
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
     * Get the [category] column value.
     *
     * @return string
     */
    public function getCategory()
    {

        return $this->category;
    }

    /**
     * Get the [language] column value.
     *
     * @return string
     */
    public function getLanguage()
    {

        return $this->language;
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
     * Get the [return_type] column value.
     *
     * @return string
     */
    public function getReturnType()
    {

        return $this->return_type;
    }

    /**
     * Get the [nspace] column value.
     *
     * @return string
     */
    public function getNspace()
    {

        return $this->nspace;
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
     * Get the [long_description] column value.
     *
     * @return string
     */
    public function getLongDescription()
    {

        return $this->long_description;
    }

    /**
     * Get the [lock_updates_flag] column value.
     * if set, importing new snippets will not overwrite this snippet with newer versions
     * @return boolean
     */
    public function getLockUpdatesFlag()
    {

        return $this->lock_updates_flag;
    }

    /**
     * Get the [snippet] column value.
     *
     * @return string
     */
    public function getSnippet()
    {

        return $this->snippet;
    }

    /**
     * Set the value of [code_snippet_id] column.
     *
     * @param  int $v new value
     * @return CodeSnippet The current object (for fluent API support)
     */
    public function setCodeSnippetId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->code_snippet_id !== $v) {
            $this->code_snippet_id = $v;
            $this->modifiedColumns[] = CodeSnippetPeer::CODE_SNIPPET_ID;
        }


        return $this;
    } // setCodeSnippetId()

    /**
     * Set the value of [category] column.
     *
     * @param  string $v new value
     * @return CodeSnippet The current object (for fluent API support)
     */
    public function setCategory($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->category !== $v) {
            $this->category = $v;
            $this->modifiedColumns[] = CodeSnippetPeer::CATEGORY;
        }


        return $this;
    } // setCategory()

    /**
     * Set the value of [language] column.
     *
     * @param  string $v new value
     * @return CodeSnippet The current object (for fluent API support)
     */
    public function setLanguage($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->language !== $v) {
            $this->language = $v;
            $this->modifiedColumns[] = CodeSnippetPeer::LANGUAGE;
        }


        return $this;
    } // setLanguage()

    /**
     * Set the value of [type] column.
     *
     * @param  string $v new value
     * @return CodeSnippet The current object (for fluent API support)
     */
    public function setType($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->type !== $v) {
            $this->type = $v;
            $this->modifiedColumns[] = CodeSnippetPeer::TYPE;
        }


        return $this;
    } // setType()

    /**
     * Set the value of [return_type] column.
     *
     * @param  string $v new value
     * @return CodeSnippet The current object (for fluent API support)
     */
    public function setReturnType($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->return_type !== $v) {
            $this->return_type = $v;
            $this->modifiedColumns[] = CodeSnippetPeer::RETURN_TYPE;
        }


        return $this;
    } // setReturnType()

    /**
     * Set the value of [nspace] column.
     *
     * @param  string $v new value
     * @return CodeSnippet The current object (for fluent API support)
     */
    public function setNspace($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->nspace !== $v) {
            $this->nspace = $v;
            $this->modifiedColumns[] = CodeSnippetPeer::NSPACE;
        }


        return $this;
    } // setNspace()

    /**
     * Set the value of [name] column.
     *
     * @param  string $v new value
     * @return CodeSnippet The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[] = CodeSnippetPeer::NAME;
        }


        return $this;
    } // setName()

    /**
     * Set the value of [description] column.
     *
     * @param  string $v new value
     * @return CodeSnippet The current object (for fluent API support)
     */
    public function setDescription($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->description !== $v) {
            $this->description = $v;
            $this->modifiedColumns[] = CodeSnippetPeer::DESCRIPTION;
        }


        return $this;
    } // setDescription()

    /**
     * Set the value of [long_description] column.
     *
     * @param  string $v new value
     * @return CodeSnippet The current object (for fluent API support)
     */
    public function setLongDescription($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->long_description !== $v) {
            $this->long_description = $v;
            $this->modifiedColumns[] = CodeSnippetPeer::LONG_DESCRIPTION;
        }


        return $this;
    } // setLongDescription()

    /**
     * Sets the value of the [lock_updates_flag] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * if set, importing new snippets will not overwrite this snippet with newer versions
     * @param boolean|integer|string $v The new value
     * @return CodeSnippet The current object (for fluent API support)
     */
    public function setLockUpdatesFlag($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->lock_updates_flag !== $v) {
            $this->lock_updates_flag = $v;
            $this->modifiedColumns[] = CodeSnippetPeer::LOCK_UPDATES_FLAG;
        }


        return $this;
    } // setLockUpdatesFlag()

    /**
     * Set the value of [snippet] column.
     *
     * @param  string $v new value
     * @return CodeSnippet The current object (for fluent API support)
     */
    public function setSnippet($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->snippet !== $v) {
            $this->snippet = $v;
            $this->modifiedColumns[] = CodeSnippetPeer::SNIPPET;
        }


        return $this;
    } // setSnippet()

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
            if ($this->language !== 'PHP') {
                return false;
            }

            if ($this->type !== 'EXPRESSION') {
                return false;
            }

            if ($this->return_type !== 'NONE') {
                return false;
            }

            if ($this->lock_updates_flag !== false) {
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

            $this->code_snippet_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->category = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->language = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->type = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->return_type = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->nspace = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->name = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->description = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->long_description = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->lock_updates_flag = ($row[$startcol + 9] !== null) ? (boolean) $row[$startcol + 9] : null;
            $this->snippet = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 11; // 11 = CodeSnippetPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating CodeSnippet object", $e);
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
            $con = Propel::getConnection(CodeSnippetPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = CodeSnippetPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collRuleCodes = null;

            $this->collCodeSnippetVariables = null;

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
            $con = Propel::getConnection(CodeSnippetPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = CodeSnippetQuery::create()
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
            $con = Propel::getConnection(CodeSnippetPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                CodeSnippetPeer::addInstanceToPool($this);
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
                    foreach ($this->ruleCodesScheduledForDeletion as $ruleCode) {
                        // need to save related object because we set the relation to null
                        $ruleCode->save($con);
                    }
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

            if ($this->codeSnippetVariablesScheduledForDeletion !== null) {
                if (!$this->codeSnippetVariablesScheduledForDeletion->isEmpty()) {
                    CodeSnippetVariableQuery::create()
                        ->filterByPrimaryKeys($this->codeSnippetVariablesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->codeSnippetVariablesScheduledForDeletion = null;
                }
            }

            if ($this->collCodeSnippetVariables !== null) {
                foreach ($this->collCodeSnippetVariables as $referrerFK) {
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

        $this->modifiedColumns[] = CodeSnippetPeer::CODE_SNIPPET_ID;
        if (null !== $this->code_snippet_id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . CodeSnippetPeer::CODE_SNIPPET_ID . ')');
        }
        if (null === $this->code_snippet_id) {
            try {
                $stmt = $con->query("SELECT nextval('core.code_snippet_code_snippet_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->code_snippet_id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CodeSnippetPeer::CODE_SNIPPET_ID)) {
            $modifiedColumns[':p' . $index++]  = 'code_snippet_id';
        }
        if ($this->isColumnModified(CodeSnippetPeer::CATEGORY)) {
            $modifiedColumns[':p' . $index++]  = 'category';
        }
        if ($this->isColumnModified(CodeSnippetPeer::LANGUAGE)) {
            $modifiedColumns[':p' . $index++]  = 'language';
        }
        if ($this->isColumnModified(CodeSnippetPeer::TYPE)) {
            $modifiedColumns[':p' . $index++]  = 'type';
        }
        if ($this->isColumnModified(CodeSnippetPeer::RETURN_TYPE)) {
            $modifiedColumns[':p' . $index++]  = 'return_type';
        }
        if ($this->isColumnModified(CodeSnippetPeer::NSPACE)) {
            $modifiedColumns[':p' . $index++]  = 'nspace';
        }
        if ($this->isColumnModified(CodeSnippetPeer::NAME)) {
            $modifiedColumns[':p' . $index++]  = 'name';
        }
        if ($this->isColumnModified(CodeSnippetPeer::DESCRIPTION)) {
            $modifiedColumns[':p' . $index++]  = 'description';
        }
        if ($this->isColumnModified(CodeSnippetPeer::LONG_DESCRIPTION)) {
            $modifiedColumns[':p' . $index++]  = 'long_description';
        }
        if ($this->isColumnModified(CodeSnippetPeer::LOCK_UPDATES_FLAG)) {
            $modifiedColumns[':p' . $index++]  = 'lock_updates_flag';
        }
        if ($this->isColumnModified(CodeSnippetPeer::SNIPPET)) {
            $modifiedColumns[':p' . $index++]  = 'snippet';
        }

        $sql = sprintf(
            'INSERT INTO core.code_snippet (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'code_snippet_id':
                        $stmt->bindValue($identifier, $this->code_snippet_id, PDO::PARAM_INT);
                        break;
                    case 'category':
                        $stmt->bindValue($identifier, $this->category, PDO::PARAM_STR);
                        break;
                    case 'language':
                        $stmt->bindValue($identifier, $this->language, PDO::PARAM_STR);
                        break;
                    case 'type':
                        $stmt->bindValue($identifier, $this->type, PDO::PARAM_STR);
                        break;
                    case 'return_type':
                        $stmt->bindValue($identifier, $this->return_type, PDO::PARAM_STR);
                        break;
                    case 'nspace':
                        $stmt->bindValue($identifier, $this->nspace, PDO::PARAM_STR);
                        break;
                    case 'name':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case 'description':
                        $stmt->bindValue($identifier, $this->description, PDO::PARAM_STR);
                        break;
                    case 'long_description':
                        $stmt->bindValue($identifier, $this->long_description, PDO::PARAM_STR);
                        break;
                    case 'lock_updates_flag':
                        $stmt->bindValue($identifier, $this->lock_updates_flag, PDO::PARAM_BOOL);
                        break;
                    case 'snippet':
                        $stmt->bindValue($identifier, $this->snippet, PDO::PARAM_STR);
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


            if (($retval = CodeSnippetPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collRuleCodes !== null) {
                    foreach ($this->collRuleCodes as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collCodeSnippetVariables !== null) {
                    foreach ($this->collCodeSnippetVariables as $referrerFK) {
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
        $pos = CodeSnippetPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getCodeSnippetId();
                break;
            case 1:
                return $this->getCategory();
                break;
            case 2:
                return $this->getLanguage();
                break;
            case 3:
                return $this->getType();
                break;
            case 4:
                return $this->getReturnType();
                break;
            case 5:
                return $this->getNspace();
                break;
            case 6:
                return $this->getName();
                break;
            case 7:
                return $this->getDescription();
                break;
            case 8:
                return $this->getLongDescription();
                break;
            case 9:
                return $this->getLockUpdatesFlag();
                break;
            case 10:
                return $this->getSnippet();
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
        if (isset($alreadyDumpedObjects['CodeSnippet'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['CodeSnippet'][$this->getPrimaryKey()] = true;
        $keys = CodeSnippetPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getCodeSnippetId(),
            $keys[1] => $this->getCategory(),
            $keys[2] => $this->getLanguage(),
            $keys[3] => $this->getType(),
            $keys[4] => $this->getReturnType(),
            $keys[5] => $this->getNspace(),
            $keys[6] => $this->getName(),
            $keys[7] => $this->getDescription(),
            $keys[8] => $this->getLongDescription(),
            $keys[9] => $this->getLockUpdatesFlag(),
            $keys[10] => $this->getSnippet(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collRuleCodes) {
                $result['RuleCodes'] = $this->collRuleCodes->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCodeSnippetVariables) {
                $result['CodeSnippetVariables'] = $this->collCodeSnippetVariables->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = CodeSnippetPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setCodeSnippetId($value);
                break;
            case 1:
                $this->setCategory($value);
                break;
            case 2:
                $this->setLanguage($value);
                break;
            case 3:
                $this->setType($value);
                break;
            case 4:
                $this->setReturnType($value);
                break;
            case 5:
                $this->setNspace($value);
                break;
            case 6:
                $this->setName($value);
                break;
            case 7:
                $this->setDescription($value);
                break;
            case 8:
                $this->setLongDescription($value);
                break;
            case 9:
                $this->setLockUpdatesFlag($value);
                break;
            case 10:
                $this->setSnippet($value);
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
        $keys = CodeSnippetPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setCodeSnippetId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setCategory($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setLanguage($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setType($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setReturnType($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setNspace($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setName($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setDescription($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setLongDescription($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setLockUpdatesFlag($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setSnippet($arr[$keys[10]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CodeSnippetPeer::DATABASE_NAME);

        if ($this->isColumnModified(CodeSnippetPeer::CODE_SNIPPET_ID)) $criteria->add(CodeSnippetPeer::CODE_SNIPPET_ID, $this->code_snippet_id);
        if ($this->isColumnModified(CodeSnippetPeer::CATEGORY)) $criteria->add(CodeSnippetPeer::CATEGORY, $this->category);
        if ($this->isColumnModified(CodeSnippetPeer::LANGUAGE)) $criteria->add(CodeSnippetPeer::LANGUAGE, $this->language);
        if ($this->isColumnModified(CodeSnippetPeer::TYPE)) $criteria->add(CodeSnippetPeer::TYPE, $this->type);
        if ($this->isColumnModified(CodeSnippetPeer::RETURN_TYPE)) $criteria->add(CodeSnippetPeer::RETURN_TYPE, $this->return_type);
        if ($this->isColumnModified(CodeSnippetPeer::NSPACE)) $criteria->add(CodeSnippetPeer::NSPACE, $this->nspace);
        if ($this->isColumnModified(CodeSnippetPeer::NAME)) $criteria->add(CodeSnippetPeer::NAME, $this->name);
        if ($this->isColumnModified(CodeSnippetPeer::DESCRIPTION)) $criteria->add(CodeSnippetPeer::DESCRIPTION, $this->description);
        if ($this->isColumnModified(CodeSnippetPeer::LONG_DESCRIPTION)) $criteria->add(CodeSnippetPeer::LONG_DESCRIPTION, $this->long_description);
        if ($this->isColumnModified(CodeSnippetPeer::LOCK_UPDATES_FLAG)) $criteria->add(CodeSnippetPeer::LOCK_UPDATES_FLAG, $this->lock_updates_flag);
        if ($this->isColumnModified(CodeSnippetPeer::SNIPPET)) $criteria->add(CodeSnippetPeer::SNIPPET, $this->snippet);

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
        $criteria = new Criteria(CodeSnippetPeer::DATABASE_NAME);
        $criteria->add(CodeSnippetPeer::CODE_SNIPPET_ID, $this->code_snippet_id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getCodeSnippetId();
    }

    /**
     * Generic method to set the primary key (code_snippet_id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setCodeSnippetId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getCodeSnippetId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of CodeSnippet (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setCategory($this->getCategory());
        $copyObj->setLanguage($this->getLanguage());
        $copyObj->setType($this->getType());
        $copyObj->setReturnType($this->getReturnType());
        $copyObj->setNspace($this->getNspace());
        $copyObj->setName($this->getName());
        $copyObj->setDescription($this->getDescription());
        $copyObj->setLongDescription($this->getLongDescription());
        $copyObj->setLockUpdatesFlag($this->getLockUpdatesFlag());
        $copyObj->setSnippet($this->getSnippet());

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

            foreach ($this->getCodeSnippetVariables() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCodeSnippetVariable($relObj->copy($deepCopy));
                }
            }

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setCodeSnippetId(NULL); // this is a auto-increment column, so set to default value
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
     * @return CodeSnippet Clone of current object.
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
     * @return CodeSnippetPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new CodeSnippetPeer();
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
        if ('CodeSnippetVariable' == $relationName) {
            $this->initCodeSnippetVariables();
        }
    }

    /**
     * Clears out the collRuleCodes collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CodeSnippet The current object (for fluent API support)
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
     * If this CodeSnippet is new, it will return
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
                    ->filterByCodeSnippet($this)
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
     * @return CodeSnippet The current object (for fluent API support)
     */
    public function setRuleCodes(PropelCollection $ruleCodes, PropelPDO $con = null)
    {
        $ruleCodesToDelete = $this->getRuleCodes(new Criteria(), $con)->diff($ruleCodes);


        $this->ruleCodesScheduledForDeletion = $ruleCodesToDelete;

        foreach ($ruleCodesToDelete as $ruleCodeRemoved) {
            $ruleCodeRemoved->setCodeSnippet(null);
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
                ->filterByCodeSnippet($this)
                ->count($con);
        }

        return count($this->collRuleCodes);
    }

    /**
     * Method called to associate a RuleCode object to this object
     * through the RuleCode foreign key attribute.
     *
     * @param    RuleCode $l RuleCode
     * @return CodeSnippet The current object (for fluent API support)
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
        $ruleCode->setCodeSnippet($this);
    }

    /**
     * @param	RuleCode $ruleCode The ruleCode object to remove.
     * @return CodeSnippet The current object (for fluent API support)
     */
    public function removeRuleCode($ruleCode)
    {
        if ($this->getRuleCodes()->contains($ruleCode)) {
            $this->collRuleCodes->remove($this->collRuleCodes->search($ruleCode));
            if (null === $this->ruleCodesScheduledForDeletion) {
                $this->ruleCodesScheduledForDeletion = clone $this->collRuleCodes;
                $this->ruleCodesScheduledForDeletion->clear();
            }
            $this->ruleCodesScheduledForDeletion[]= $ruleCode;
            $ruleCode->setCodeSnippet(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CodeSnippet is new, it will return
     * an empty collection; or if this CodeSnippet has previously
     * been saved, it will retrieve related RuleCodes from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CodeSnippet.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|RuleCode[] List of RuleCode objects
     */
    public function getRuleCodesJoinRule($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = RuleCodeQuery::create(null, $criteria);
        $query->joinWith('Rule', $join_behavior);

        return $this->getRuleCodes($query, $con);
    }

    /**
     * Clears out the collCodeSnippetVariables collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return CodeSnippet The current object (for fluent API support)
     * @see        addCodeSnippetVariables()
     */
    public function clearCodeSnippetVariables()
    {
        $this->collCodeSnippetVariables = null; // important to set this to null since that means it is uninitialized
        $this->collCodeSnippetVariablesPartial = null;

        return $this;
    }

    /**
     * reset is the collCodeSnippetVariables collection loaded partially
     *
     * @return void
     */
    public function resetPartialCodeSnippetVariables($v = true)
    {
        $this->collCodeSnippetVariablesPartial = $v;
    }

    /**
     * Initializes the collCodeSnippetVariables collection.
     *
     * By default this just sets the collCodeSnippetVariables collection to an empty array (like clearcollCodeSnippetVariables());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCodeSnippetVariables($overrideExisting = true)
    {
        if (null !== $this->collCodeSnippetVariables && !$overrideExisting) {
            return;
        }
        $this->collCodeSnippetVariables = new PropelObjectCollection();
        $this->collCodeSnippetVariables->setModel('CodeSnippetVariable');
    }

    /**
     * Gets an array of CodeSnippetVariable objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this CodeSnippet is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CodeSnippetVariable[] List of CodeSnippetVariable objects
     * @throws PropelException
     */
    public function getCodeSnippetVariables($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCodeSnippetVariablesPartial && !$this->isNew();
        if (null === $this->collCodeSnippetVariables || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCodeSnippetVariables) {
                // return empty collection
                $this->initCodeSnippetVariables();
            } else {
                $collCodeSnippetVariables = CodeSnippetVariableQuery::create(null, $criteria)
                    ->filterByCodeSnippet($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCodeSnippetVariablesPartial && count($collCodeSnippetVariables)) {
                      $this->initCodeSnippetVariables(false);

                      foreach ($collCodeSnippetVariables as $obj) {
                        if (false == $this->collCodeSnippetVariables->contains($obj)) {
                          $this->collCodeSnippetVariables->append($obj);
                        }
                      }

                      $this->collCodeSnippetVariablesPartial = true;
                    }

                    $collCodeSnippetVariables->getInternalIterator()->rewind();

                    return $collCodeSnippetVariables;
                }

                if ($partial && $this->collCodeSnippetVariables) {
                    foreach ($this->collCodeSnippetVariables as $obj) {
                        if ($obj->isNew()) {
                            $collCodeSnippetVariables[] = $obj;
                        }
                    }
                }

                $this->collCodeSnippetVariables = $collCodeSnippetVariables;
                $this->collCodeSnippetVariablesPartial = false;
            }
        }

        return $this->collCodeSnippetVariables;
    }

    /**
     * Sets a collection of CodeSnippetVariable objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $codeSnippetVariables A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return CodeSnippet The current object (for fluent API support)
     */
    public function setCodeSnippetVariables(PropelCollection $codeSnippetVariables, PropelPDO $con = null)
    {
        $codeSnippetVariablesToDelete = $this->getCodeSnippetVariables(new Criteria(), $con)->diff($codeSnippetVariables);


        $this->codeSnippetVariablesScheduledForDeletion = $codeSnippetVariablesToDelete;

        foreach ($codeSnippetVariablesToDelete as $codeSnippetVariableRemoved) {
            $codeSnippetVariableRemoved->setCodeSnippet(null);
        }

        $this->collCodeSnippetVariables = null;
        foreach ($codeSnippetVariables as $codeSnippetVariable) {
            $this->addCodeSnippetVariable($codeSnippetVariable);
        }

        $this->collCodeSnippetVariables = $codeSnippetVariables;
        $this->collCodeSnippetVariablesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CodeSnippetVariable objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CodeSnippetVariable objects.
     * @throws PropelException
     */
    public function countCodeSnippetVariables(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCodeSnippetVariablesPartial && !$this->isNew();
        if (null === $this->collCodeSnippetVariables || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCodeSnippetVariables) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCodeSnippetVariables());
            }
            $query = CodeSnippetVariableQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCodeSnippet($this)
                ->count($con);
        }

        return count($this->collCodeSnippetVariables);
    }

    /**
     * Method called to associate a CodeSnippetVariable object to this object
     * through the CodeSnippetVariable foreign key attribute.
     *
     * @param    CodeSnippetVariable $l CodeSnippetVariable
     * @return CodeSnippet The current object (for fluent API support)
     */
    public function addCodeSnippetVariable(CodeSnippetVariable $l)
    {
        if ($this->collCodeSnippetVariables === null) {
            $this->initCodeSnippetVariables();
            $this->collCodeSnippetVariablesPartial = true;
        }

        if (!in_array($l, $this->collCodeSnippetVariables->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCodeSnippetVariable($l);

            if ($this->codeSnippetVariablesScheduledForDeletion and $this->codeSnippetVariablesScheduledForDeletion->contains($l)) {
                $this->codeSnippetVariablesScheduledForDeletion->remove($this->codeSnippetVariablesScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	CodeSnippetVariable $codeSnippetVariable The codeSnippetVariable object to add.
     */
    protected function doAddCodeSnippetVariable($codeSnippetVariable)
    {
        $this->collCodeSnippetVariables[]= $codeSnippetVariable;
        $codeSnippetVariable->setCodeSnippet($this);
    }

    /**
     * @param	CodeSnippetVariable $codeSnippetVariable The codeSnippetVariable object to remove.
     * @return CodeSnippet The current object (for fluent API support)
     */
    public function removeCodeSnippetVariable($codeSnippetVariable)
    {
        if ($this->getCodeSnippetVariables()->contains($codeSnippetVariable)) {
            $this->collCodeSnippetVariables->remove($this->collCodeSnippetVariables->search($codeSnippetVariable));
            if (null === $this->codeSnippetVariablesScheduledForDeletion) {
                $this->codeSnippetVariablesScheduledForDeletion = clone $this->collCodeSnippetVariables;
                $this->codeSnippetVariablesScheduledForDeletion->clear();
            }
            $this->codeSnippetVariablesScheduledForDeletion[]= clone $codeSnippetVariable;
            $codeSnippetVariable->setCodeSnippet(null);
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->code_snippet_id = null;
        $this->category = null;
        $this->language = null;
        $this->type = null;
        $this->return_type = null;
        $this->nspace = null;
        $this->name = null;
        $this->description = null;
        $this->long_description = null;
        $this->lock_updates_flag = null;
        $this->snippet = null;
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
            if ($this->collCodeSnippetVariables) {
                foreach ($this->collCodeSnippetVariables as $o) {
                    $o->clearAllReferences($deep);
                }
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collRuleCodes instanceof PropelCollection) {
            $this->collRuleCodes->clearIterator();
        }
        $this->collRuleCodes = null;
        if ($this->collCodeSnippetVariables instanceof PropelCollection) {
            $this->collCodeSnippetVariables->clearIterator();
        }
        $this->collCodeSnippetVariables = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(CodeSnippetPeer::DEFAULT_STRING_FORMAT);
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

<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core\om;

use \Criteria;
use \Exception;
use \ModelCriteria;
use \ModelJoin;
use \PDO;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\FieldDefinition;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtension;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtensionField;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtensionFieldPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtensionFieldQuery;

/**
 * @method TableExtensionFieldQuery orderByTableExtensionFieldId($order = Criteria::ASC) Order by the table_extension_field_id column
 * @method TableExtensionFieldQuery orderByTableExtensionId($order = Criteria::ASC) Order by the table_extension_id column
 * @method TableExtensionFieldQuery orderByFieldDefinitionId($order = Criteria::ASC) Order by the field_definition_id column
 * @method TableExtensionFieldQuery orderByRequireIndex($order = Criteria::ASC) Order by the require_index column
 * @method TableExtensionFieldQuery orderByActiveFlag($order = Criteria::ASC) Order by the active_flag column
 *
 * @method TableExtensionFieldQuery groupByTableExtensionFieldId() Group by the table_extension_field_id column
 * @method TableExtensionFieldQuery groupByTableExtensionId() Group by the table_extension_id column
 * @method TableExtensionFieldQuery groupByFieldDefinitionId() Group by the field_definition_id column
 * @method TableExtensionFieldQuery groupByRequireIndex() Group by the require_index column
 * @method TableExtensionFieldQuery groupByActiveFlag() Group by the active_flag column
 *
 * @method TableExtensionFieldQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method TableExtensionFieldQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method TableExtensionFieldQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method TableExtensionFieldQuery leftJoinTableExtension($relationAlias = null) Adds a LEFT JOIN clause to the query using the TableExtension relation
 * @method TableExtensionFieldQuery rightJoinTableExtension($relationAlias = null) Adds a RIGHT JOIN clause to the query using the TableExtension relation
 * @method TableExtensionFieldQuery innerJoinTableExtension($relationAlias = null) Adds a INNER JOIN clause to the query using the TableExtension relation
 *
 * @method TableExtensionFieldQuery leftJoinFieldDefinition($relationAlias = null) Adds a LEFT JOIN clause to the query using the FieldDefinition relation
 * @method TableExtensionFieldQuery rightJoinFieldDefinition($relationAlias = null) Adds a RIGHT JOIN clause to the query using the FieldDefinition relation
 * @method TableExtensionFieldQuery innerJoinFieldDefinition($relationAlias = null) Adds a INNER JOIN clause to the query using the FieldDefinition relation
 *
 * @method TableExtensionField findOne(PropelPDO $con = null) Return the first TableExtensionField matching the query
 * @method TableExtensionField findOneOrCreate(PropelPDO $con = null) Return the first TableExtensionField matching the query, or a new TableExtensionField object populated from the query conditions when no match is found
 *
 * @method TableExtensionField findOneByTableExtensionId(int $table_extension_id) Return the first TableExtensionField filtered by the table_extension_id column
 * @method TableExtensionField findOneByFieldDefinitionId(int $field_definition_id) Return the first TableExtensionField filtered by the field_definition_id column
 * @method TableExtensionField findOneByRequireIndex(boolean $require_index) Return the first TableExtensionField filtered by the require_index column
 * @method TableExtensionField findOneByActiveFlag(boolean $active_flag) Return the first TableExtensionField filtered by the active_flag column
 *
 * @method array findByTableExtensionFieldId(int $table_extension_field_id) Return TableExtensionField objects filtered by the table_extension_field_id column
 * @method array findByTableExtensionId(int $table_extension_id) Return TableExtensionField objects filtered by the table_extension_id column
 * @method array findByFieldDefinitionId(int $field_definition_id) Return TableExtensionField objects filtered by the field_definition_id column
 * @method array findByRequireIndex(boolean $require_index) Return TableExtensionField objects filtered by the require_index column
 * @method array findByActiveFlag(boolean $active_flag) Return TableExtensionField objects filtered by the active_flag column
 */
abstract class BaseTableExtensionFieldQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseTableExtensionFieldQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = null, $modelName = null, $modelAlias = null)
    {
        if (null === $dbName) {
            $dbName = 'cool_db';
        }
        if (null === $modelName) {
            $modelName = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\TableExtensionField';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new TableExtensionFieldQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   TableExtensionFieldQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return TableExtensionFieldQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof TableExtensionFieldQuery) {
            return $criteria;
        }
        $query = new TableExtensionFieldQuery(null, null, $modelAlias);

        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return   TableExtensionField|TableExtensionField[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = TableExtensionFieldPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(TableExtensionFieldPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Alias of findPk to use instance pooling
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 TableExtensionField A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByTableExtensionFieldId($key, $con = null)
     {
        return $this->findPk($key, $con);
     }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 TableExtensionField A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT table_extension_field_id, table_extension_id, field_definition_id, require_index, active_flag FROM core.table_extension_field WHERE table_extension_field_id = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new TableExtensionField();
            $obj->hydrate($row);
            TableExtensionFieldPeer::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return TableExtensionField|TableExtensionField[]|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|TableExtensionField[]|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($stmt);
    }

    /**
     * Find objects by primary key while maintaining the original sort order of the keys
     * <code>
     * $objs = $c->findPksKeepingKeyOrder(array(12, 56, 832), $con);
     
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return TableExtensionField[]
     */
    public function findPksKeepingKeyOrder($keys, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
        }
        $ret = array();

        foreach($keys as $key)
            $ret[ $key ] = $this->findPk($key, $con);

        return $ret;
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return TableExtensionFieldQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(TableExtensionFieldPeer::TABLE_EXTENSION_FIELD_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return TableExtensionFieldQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(TableExtensionFieldPeer::TABLE_EXTENSION_FIELD_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the table_extension_field_id column
     *
     * Example usage:
     * <code>
     * $query->filterByTableExtensionFieldId(1234); // WHERE table_extension_field_id = 1234
     * $query->filterByTableExtensionFieldId(array(12, 34)); // WHERE table_extension_field_id IN (12, 34)
     * $query->filterByTableExtensionFieldId(array('min' => 12)); // WHERE table_extension_field_id >= 12
     * $query->filterByTableExtensionFieldId(array('max' => 12)); // WHERE table_extension_field_id <= 12
     * </code>
     *
     * @param     mixed $tableExtensionFieldId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TableExtensionFieldQuery The current query, for fluid interface
     */
    public function filterByTableExtensionFieldId($tableExtensionFieldId = null, $comparison = null)
    {
        if (is_array($tableExtensionFieldId)) {
            $useMinMax = false;
            if (isset($tableExtensionFieldId['min'])) {
                $this->addUsingAlias(TableExtensionFieldPeer::TABLE_EXTENSION_FIELD_ID, $tableExtensionFieldId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($tableExtensionFieldId['max'])) {
                $this->addUsingAlias(TableExtensionFieldPeer::TABLE_EXTENSION_FIELD_ID, $tableExtensionFieldId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TableExtensionFieldPeer::TABLE_EXTENSION_FIELD_ID, $tableExtensionFieldId, $comparison);
    }

    /**
     * Filter the query on the table_extension_id column
     *
     * Example usage:
     * <code>
     * $query->filterByTableExtensionId(1234); // WHERE table_extension_id = 1234
     * $query->filterByTableExtensionId(array(12, 34)); // WHERE table_extension_id IN (12, 34)
     * $query->filterByTableExtensionId(array('min' => 12)); // WHERE table_extension_id >= 12
     * $query->filterByTableExtensionId(array('max' => 12)); // WHERE table_extension_id <= 12
     * </code>
     *
     * @see       filterByTableExtension()
     *
     * @param     mixed $tableExtensionId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TableExtensionFieldQuery The current query, for fluid interface
     */
    public function filterByTableExtensionId($tableExtensionId = null, $comparison = null)
    {
        if (is_array($tableExtensionId)) {
            $useMinMax = false;
            if (isset($tableExtensionId['min'])) {
                $this->addUsingAlias(TableExtensionFieldPeer::TABLE_EXTENSION_ID, $tableExtensionId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($tableExtensionId['max'])) {
                $this->addUsingAlias(TableExtensionFieldPeer::TABLE_EXTENSION_ID, $tableExtensionId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TableExtensionFieldPeer::TABLE_EXTENSION_ID, $tableExtensionId, $comparison);
    }

    /**
     * Filter the query on the field_definition_id column
     *
     * Example usage:
     * <code>
     * $query->filterByFieldDefinitionId(1234); // WHERE field_definition_id = 1234
     * $query->filterByFieldDefinitionId(array(12, 34)); // WHERE field_definition_id IN (12, 34)
     * $query->filterByFieldDefinitionId(array('min' => 12)); // WHERE field_definition_id >= 12
     * $query->filterByFieldDefinitionId(array('max' => 12)); // WHERE field_definition_id <= 12
     * </code>
     *
     * @see       filterByFieldDefinition()
     *
     * @param     mixed $fieldDefinitionId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TableExtensionFieldQuery The current query, for fluid interface
     */
    public function filterByFieldDefinitionId($fieldDefinitionId = null, $comparison = null)
    {
        if (is_array($fieldDefinitionId)) {
            $useMinMax = false;
            if (isset($fieldDefinitionId['min'])) {
                $this->addUsingAlias(TableExtensionFieldPeer::FIELD_DEFINITION_ID, $fieldDefinitionId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($fieldDefinitionId['max'])) {
                $this->addUsingAlias(TableExtensionFieldPeer::FIELD_DEFINITION_ID, $fieldDefinitionId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TableExtensionFieldPeer::FIELD_DEFINITION_ID, $fieldDefinitionId, $comparison);
    }

    /**
     * Filter the query on the require_index column
     *
     * Example usage:
     * <code>
     * $query->filterByRequireIndex(true); // WHERE require_index = true
     * $query->filterByRequireIndex('yes'); // WHERE require_index = true
     * </code>
     *
     * @param     boolean|string $requireIndex The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TableExtensionFieldQuery The current query, for fluid interface
     */
    public function filterByRequireIndex($requireIndex = null, $comparison = null)
    {
        if (is_string($requireIndex)) {
            $requireIndex = in_array(strtolower($requireIndex), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(TableExtensionFieldPeer::REQUIRE_INDEX, $requireIndex, $comparison);
    }

    /**
     * Filter the query on the active_flag column
     *
     * Example usage:
     * <code>
     * $query->filterByActiveFlag(true); // WHERE active_flag = true
     * $query->filterByActiveFlag('yes'); // WHERE active_flag = true
     * </code>
     *
     * @param     boolean|string $activeFlag The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TableExtensionFieldQuery The current query, for fluid interface
     */
    public function filterByActiveFlag($activeFlag = null, $comparison = null)
    {
        if (is_string($activeFlag)) {
            $activeFlag = in_array(strtolower($activeFlag), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(TableExtensionFieldPeer::ACTIVE_FLAG, $activeFlag, $comparison);
    }

    /**
     * Filter the query by a related TableExtension object
     *
     * @param   TableExtension|PropelObjectCollection $tableExtension The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 TableExtensionFieldQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByTableExtension($tableExtension, $comparison = null)
    {
        if ($tableExtension instanceof TableExtension) {
            return $this
                ->addUsingAlias(TableExtensionFieldPeer::TABLE_EXTENSION_ID, $tableExtension->getTableExtensionId(), $comparison);
        } elseif ($tableExtension instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(TableExtensionFieldPeer::TABLE_EXTENSION_ID, $tableExtension->toKeyValue('PrimaryKey', 'TableExtensionId'), $comparison);
        } else {
            throw new PropelException('filterByTableExtension() only accepts arguments of type TableExtension or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the TableExtension relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return TableExtensionFieldQuery The current query, for fluid interface
     */
    public function joinTableExtension($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('TableExtension');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'TableExtension');
        }

        return $this;
    }

    /**
     * Use the TableExtension relation TableExtension object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtensionQuery A secondary query class using the current class as primary query
     */
    public function useTableExtensionQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinTableExtension($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'TableExtension', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtensionQuery');
    }

    /**
     * Filter the query by a related FieldDefinition object
     *
     * @param   FieldDefinition|PropelObjectCollection $fieldDefinition The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 TableExtensionFieldQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByFieldDefinition($fieldDefinition, $comparison = null)
    {
        if ($fieldDefinition instanceof FieldDefinition) {
            return $this
                ->addUsingAlias(TableExtensionFieldPeer::FIELD_DEFINITION_ID, $fieldDefinition->getFieldDefinitionId(), $comparison);
        } elseif ($fieldDefinition instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(TableExtensionFieldPeer::FIELD_DEFINITION_ID, $fieldDefinition->toKeyValue('PrimaryKey', 'FieldDefinitionId'), $comparison);
        } else {
            throw new PropelException('filterByFieldDefinition() only accepts arguments of type FieldDefinition or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the FieldDefinition relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return TableExtensionFieldQuery The current query, for fluid interface
     */
    public function joinFieldDefinition($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('FieldDefinition');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'FieldDefinition');
        }

        return $this;
    }

    /**
     * Use the FieldDefinition relation FieldDefinition object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\FieldDefinitionQuery A secondary query class using the current class as primary query
     */
    public function useFieldDefinitionQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinFieldDefinition($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'FieldDefinition', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\FieldDefinitionQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   TableExtensionField $tableExtensionField Object to remove from the list of results
     *
     * @return TableExtensionFieldQuery The current query, for fluid interface
     */
    public function prune($tableExtensionField = null)
    {
        if ($tableExtensionField) {
            $this->addUsingAlias(TableExtensionFieldPeer::TABLE_EXTENSION_FIELD_ID, $tableExtensionField->getTableExtensionFieldId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtension;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtensionField;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtensionPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtensionQuery;

/**
 * @method TableExtensionQuery orderByTableExtensionId($order = Criteria::ASC) Order by the table_extension_id column
 * @method TableExtensionQuery orderByDbSchema($order = Criteria::ASC) Order by the db_schema column
 * @method TableExtensionQuery orderByDbTable($order = Criteria::ASC) Order by the db_table column
 * @method TableExtensionQuery orderByActiveFlag($order = Criteria::ASC) Order by the active_flag column
 * @method TableExtensionQuery orderByDescription($order = Criteria::ASC) Order by the description column
 *
 * @method TableExtensionQuery groupByTableExtensionId() Group by the table_extension_id column
 * @method TableExtensionQuery groupByDbSchema() Group by the db_schema column
 * @method TableExtensionQuery groupByDbTable() Group by the db_table column
 * @method TableExtensionQuery groupByActiveFlag() Group by the active_flag column
 * @method TableExtensionQuery groupByDescription() Group by the description column
 *
 * @method TableExtensionQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method TableExtensionQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method TableExtensionQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method TableExtensionQuery leftJoinTableExtensionField($relationAlias = null) Adds a LEFT JOIN clause to the query using the TableExtensionField relation
 * @method TableExtensionQuery rightJoinTableExtensionField($relationAlias = null) Adds a RIGHT JOIN clause to the query using the TableExtensionField relation
 * @method TableExtensionQuery innerJoinTableExtensionField($relationAlias = null) Adds a INNER JOIN clause to the query using the TableExtensionField relation
 *
 * @method TableExtension findOne(PropelPDO $con = null) Return the first TableExtension matching the query
 * @method TableExtension findOneOrCreate(PropelPDO $con = null) Return the first TableExtension matching the query, or a new TableExtension object populated from the query conditions when no match is found
 *
 * @method TableExtension findOneByDbSchema(string $db_schema) Return the first TableExtension filtered by the db_schema column
 * @method TableExtension findOneByDbTable(string $db_table) Return the first TableExtension filtered by the db_table column
 * @method TableExtension findOneByActiveFlag(boolean $active_flag) Return the first TableExtension filtered by the active_flag column
 * @method TableExtension findOneByDescription(string $description) Return the first TableExtension filtered by the description column
 *
 * @method array findByTableExtensionId(int $table_extension_id) Return TableExtension objects filtered by the table_extension_id column
 * @method array findByDbSchema(string $db_schema) Return TableExtension objects filtered by the db_schema column
 * @method array findByDbTable(string $db_table) Return TableExtension objects filtered by the db_table column
 * @method array findByActiveFlag(boolean $active_flag) Return TableExtension objects filtered by the active_flag column
 * @method array findByDescription(string $description) Return TableExtension objects filtered by the description column
 */
abstract class BaseTableExtensionQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseTableExtensionQuery object.
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
            $modelName = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\TableExtension';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new TableExtensionQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   TableExtensionQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return TableExtensionQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof TableExtensionQuery) {
            return $criteria;
        }
        $query = new TableExtensionQuery(null, null, $modelAlias);

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
     * @return   TableExtension|TableExtension[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = TableExtensionPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(TableExtensionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 TableExtension A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByTableExtensionId($key, $con = null)
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
     * @return                 TableExtension A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT table_extension_id, db_schema, db_table, active_flag, description FROM core.table_extension WHERE table_extension_id = :p0';
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
            $obj = new TableExtension();
            $obj->hydrate($row);
            TableExtensionPeer::addInstanceToPool($obj, (string) $key);
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
     * @return TableExtension|TableExtension[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|TableExtension[]|mixed the list of results, formatted by the current formatter
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
     * @return TableExtension[]
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
     * @return TableExtensionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(TableExtensionPeer::TABLE_EXTENSION_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return TableExtensionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(TableExtensionPeer::TABLE_EXTENSION_ID, $keys, Criteria::IN);
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
     * @param     mixed $tableExtensionId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TableExtensionQuery The current query, for fluid interface
     */
    public function filterByTableExtensionId($tableExtensionId = null, $comparison = null)
    {
        if (is_array($tableExtensionId)) {
            $useMinMax = false;
            if (isset($tableExtensionId['min'])) {
                $this->addUsingAlias(TableExtensionPeer::TABLE_EXTENSION_ID, $tableExtensionId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($tableExtensionId['max'])) {
                $this->addUsingAlias(TableExtensionPeer::TABLE_EXTENSION_ID, $tableExtensionId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TableExtensionPeer::TABLE_EXTENSION_ID, $tableExtensionId, $comparison);
    }

    /**
     * Filter the query on the db_schema column
     *
     * Example usage:
     * <code>
     * $query->filterByDbSchema('fooValue');   // WHERE db_schema = 'fooValue'
     * $query->filterByDbSchema('%fooValue%'); // WHERE db_schema LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbSchema The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TableExtensionQuery The current query, for fluid interface
     */
    public function filterByDbSchema($dbSchema = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbSchema)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbSchema)) {
                $dbSchema = str_replace('*', '%', $dbSchema);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(TableExtensionPeer::DB_SCHEMA, $dbSchema, $comparison);
    }

    /**
     * Filter the query on the db_table column
     *
     * Example usage:
     * <code>
     * $query->filterByDbTable('fooValue');   // WHERE db_table = 'fooValue'
     * $query->filterByDbTable('%fooValue%'); // WHERE db_table LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dbTable The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TableExtensionQuery The current query, for fluid interface
     */
    public function filterByDbTable($dbTable = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dbTable)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dbTable)) {
                $dbTable = str_replace('*', '%', $dbTable);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(TableExtensionPeer::DB_TABLE, $dbTable, $comparison);
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
     * @return TableExtensionQuery The current query, for fluid interface
     */
    public function filterByActiveFlag($activeFlag = null, $comparison = null)
    {
        if (is_string($activeFlag)) {
            $activeFlag = in_array(strtolower($activeFlag), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(TableExtensionPeer::ACTIVE_FLAG, $activeFlag, $comparison);
    }

    /**
     * Filter the query on the description column
     *
     * Example usage:
     * <code>
     * $query->filterByDescription('fooValue');   // WHERE description = 'fooValue'
     * $query->filterByDescription('%fooValue%'); // WHERE description LIKE '%fooValue%'
     * </code>
     *
     * @param     string $description The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TableExtensionQuery The current query, for fluid interface
     */
    public function filterByDescription($description = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($description)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $description)) {
                $description = str_replace('*', '%', $description);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(TableExtensionPeer::DESCRIPTION, $description, $comparison);
    }

    /**
     * Filter the query by a related TableExtensionField object
     *
     * @param   TableExtensionField|PropelObjectCollection $tableExtensionField  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 TableExtensionQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByTableExtensionField($tableExtensionField, $comparison = null)
    {
        if ($tableExtensionField instanceof TableExtensionField) {
            return $this
                ->addUsingAlias(TableExtensionPeer::TABLE_EXTENSION_ID, $tableExtensionField->getTableExtensionId(), $comparison);
        } elseif ($tableExtensionField instanceof PropelObjectCollection) {
            return $this
                ->useTableExtensionFieldQuery()
                ->filterByPrimaryKeys($tableExtensionField->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByTableExtensionField() only accepts arguments of type TableExtensionField or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the TableExtensionField relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return TableExtensionQuery The current query, for fluid interface
     */
    public function joinTableExtensionField($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('TableExtensionField');

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
            $this->addJoinObject($join, 'TableExtensionField');
        }

        return $this;
    }

    /**
     * Use the TableExtensionField relation TableExtensionField object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtensionFieldQuery A secondary query class using the current class as primary query
     */
    public function useTableExtensionFieldQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinTableExtensionField($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'TableExtensionField', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtensionFieldQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   TableExtension $tableExtension Object to remove from the list of results
     *
     * @return TableExtensionQuery The current query, for fluid interface
     */
    public function prune($tableExtension = null)
    {
        if ($tableExtension) {
            $this->addUsingAlias(TableExtensionPeer::TABLE_EXTENSION_ID, $tableExtension->getTableExtensionId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

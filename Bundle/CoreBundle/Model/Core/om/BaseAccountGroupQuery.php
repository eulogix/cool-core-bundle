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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountGroup;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountGroupPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountGroupQuery;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountGroupRef;

/**
 * @method AccountGroupQuery orderByAccountGroupId($order = Criteria::ASC) Order by the account_group_id column
 * @method AccountGroupQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method AccountGroupQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method AccountGroupQuery orderByDescription($order = Criteria::ASC) Order by the description column
 *
 * @method AccountGroupQuery groupByAccountGroupId() Group by the account_group_id column
 * @method AccountGroupQuery groupByType() Group by the type column
 * @method AccountGroupQuery groupByName() Group by the name column
 * @method AccountGroupQuery groupByDescription() Group by the description column
 *
 * @method AccountGroupQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method AccountGroupQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method AccountGroupQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method AccountGroupQuery leftJoinAccountGroupRef($relationAlias = null) Adds a LEFT JOIN clause to the query using the AccountGroupRef relation
 * @method AccountGroupQuery rightJoinAccountGroupRef($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AccountGroupRef relation
 * @method AccountGroupQuery innerJoinAccountGroupRef($relationAlias = null) Adds a INNER JOIN clause to the query using the AccountGroupRef relation
 *
 * @method AccountGroup findOne(PropelPDO $con = null) Return the first AccountGroup matching the query
 * @method AccountGroup findOneOrCreate(PropelPDO $con = null) Return the first AccountGroup matching the query, or a new AccountGroup object populated from the query conditions when no match is found
 *
 * @method AccountGroup findOneByType(string $type) Return the first AccountGroup filtered by the type column
 * @method AccountGroup findOneByName(string $name) Return the first AccountGroup filtered by the name column
 * @method AccountGroup findOneByDescription(string $description) Return the first AccountGroup filtered by the description column
 *
 * @method array findByAccountGroupId(int $account_group_id) Return AccountGroup objects filtered by the account_group_id column
 * @method array findByType(string $type) Return AccountGroup objects filtered by the type column
 * @method array findByName(string $name) Return AccountGroup objects filtered by the name column
 * @method array findByDescription(string $description) Return AccountGroup objects filtered by the description column
 */
abstract class BaseAccountGroupQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseAccountGroupQuery object.
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
            $modelName = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountGroup';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new AccountGroupQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   AccountGroupQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return AccountGroupQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof AccountGroupQuery) {
            return $criteria;
        }
        $query = new AccountGroupQuery(null, null, $modelAlias);

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
     * @return   AccountGroup|AccountGroup[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = AccountGroupPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(AccountGroupPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 AccountGroup A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByAccountGroupId($key, $con = null)
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
     * @return                 AccountGroup A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT account_group_id, type, name, description FROM core.account_group WHERE account_group_id = :p0';
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
            $obj = new AccountGroup();
            $obj->hydrate($row);
            AccountGroupPeer::addInstanceToPool($obj, (string) $key);
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
     * @return AccountGroup|AccountGroup[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|AccountGroup[]|mixed the list of results, formatted by the current formatter
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
     * @return AccountGroup[]
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
     * @return AccountGroupQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(AccountGroupPeer::ACCOUNT_GROUP_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return AccountGroupQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(AccountGroupPeer::ACCOUNT_GROUP_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the account_group_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAccountGroupId(1234); // WHERE account_group_id = 1234
     * $query->filterByAccountGroupId(array(12, 34)); // WHERE account_group_id IN (12, 34)
     * $query->filterByAccountGroupId(array('min' => 12)); // WHERE account_group_id >= 12
     * $query->filterByAccountGroupId(array('max' => 12)); // WHERE account_group_id <= 12
     * </code>
     *
     * @param     mixed $accountGroupId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountGroupQuery The current query, for fluid interface
     */
    public function filterByAccountGroupId($accountGroupId = null, $comparison = null)
    {
        if (is_array($accountGroupId)) {
            $useMinMax = false;
            if (isset($accountGroupId['min'])) {
                $this->addUsingAlias(AccountGroupPeer::ACCOUNT_GROUP_ID, $accountGroupId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accountGroupId['max'])) {
                $this->addUsingAlias(AccountGroupPeer::ACCOUNT_GROUP_ID, $accountGroupId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AccountGroupPeer::ACCOUNT_GROUP_ID, $accountGroupId, $comparison);
    }

    /**
     * Filter the query on the type column
     *
     * Example usage:
     * <code>
     * $query->filterByType('fooValue');   // WHERE type = 'fooValue'
     * $query->filterByType('%fooValue%'); // WHERE type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $type The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountGroupQuery The current query, for fluid interface
     */
    public function filterByType($type = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($type)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $type)) {
                $type = str_replace('*', '%', $type);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AccountGroupPeer::TYPE, $type, $comparison);
    }

    /**
     * Filter the query on the name column
     *
     * Example usage:
     * <code>
     * $query->filterByName('fooValue');   // WHERE name = 'fooValue'
     * $query->filterByName('%fooValue%'); // WHERE name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $name The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountGroupQuery The current query, for fluid interface
     */
    public function filterByName($name = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($name)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $name)) {
                $name = str_replace('*', '%', $name);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AccountGroupPeer::NAME, $name, $comparison);
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
     * @return AccountGroupQuery The current query, for fluid interface
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

        return $this->addUsingAlias(AccountGroupPeer::DESCRIPTION, $description, $comparison);
    }

    /**
     * Filter the query by a related AccountGroupRef object
     *
     * @param   AccountGroupRef|PropelObjectCollection $accountGroupRef  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AccountGroupQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAccountGroupRef($accountGroupRef, $comparison = null)
    {
        if ($accountGroupRef instanceof AccountGroupRef) {
            return $this
                ->addUsingAlias(AccountGroupPeer::ACCOUNT_GROUP_ID, $accountGroupRef->getAccountGroupId(), $comparison);
        } elseif ($accountGroupRef instanceof PropelObjectCollection) {
            return $this
                ->useAccountGroupRefQuery()
                ->filterByPrimaryKeys($accountGroupRef->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByAccountGroupRef() only accepts arguments of type AccountGroupRef or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AccountGroupRef relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return AccountGroupQuery The current query, for fluid interface
     */
    public function joinAccountGroupRef($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AccountGroupRef');

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
            $this->addJoinObject($join, 'AccountGroupRef');
        }

        return $this;
    }

    /**
     * Use the AccountGroupRef relation AccountGroupRef object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountGroupRefQuery A secondary query class using the current class as primary query
     */
    public function useAccountGroupRefQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAccountGroupRef($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AccountGroupRef', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountGroupRefQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   AccountGroup $accountGroup Object to remove from the list of results
     *
     * @return AccountGroupQuery The current query, for fluid interface
     */
    public function prune($accountGroup = null)
    {
        if ($accountGroup) {
            $this->addUsingAlias(AccountGroupPeer::ACCOUNT_GROUP_ID, $accountGroup->getAccountGroupId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

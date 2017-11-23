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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\Account;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountGroup;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountGroupRef;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountGroupRefPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountGroupRefQuery;

/**
 * @method AccountGroupRefQuery orderByAccountGroupRefId($order = Criteria::ASC) Order by the account_group_ref_id column
 * @method AccountGroupRefQuery orderByAccountId($order = Criteria::ASC) Order by the account_id column
 * @method AccountGroupRefQuery orderByAccountGroupId($order = Criteria::ASC) Order by the account_group_id column
 * @method AccountGroupRefQuery orderByRole($order = Criteria::ASC) Order by the role column
 *
 * @method AccountGroupRefQuery groupByAccountGroupRefId() Group by the account_group_ref_id column
 * @method AccountGroupRefQuery groupByAccountId() Group by the account_id column
 * @method AccountGroupRefQuery groupByAccountGroupId() Group by the account_group_id column
 * @method AccountGroupRefQuery groupByRole() Group by the role column
 *
 * @method AccountGroupRefQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method AccountGroupRefQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method AccountGroupRefQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method AccountGroupRefQuery leftJoinAccount($relationAlias = null) Adds a LEFT JOIN clause to the query using the Account relation
 * @method AccountGroupRefQuery rightJoinAccount($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Account relation
 * @method AccountGroupRefQuery innerJoinAccount($relationAlias = null) Adds a INNER JOIN clause to the query using the Account relation
 *
 * @method AccountGroupRefQuery leftJoinAccountGroup($relationAlias = null) Adds a LEFT JOIN clause to the query using the AccountGroup relation
 * @method AccountGroupRefQuery rightJoinAccountGroup($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AccountGroup relation
 * @method AccountGroupRefQuery innerJoinAccountGroup($relationAlias = null) Adds a INNER JOIN clause to the query using the AccountGroup relation
 *
 * @method AccountGroupRef findOne(PropelPDO $con = null) Return the first AccountGroupRef matching the query
 * @method AccountGroupRef findOneOrCreate(PropelPDO $con = null) Return the first AccountGroupRef matching the query, or a new AccountGroupRef object populated from the query conditions when no match is found
 *
 * @method AccountGroupRef findOneByAccountId(int $account_id) Return the first AccountGroupRef filtered by the account_id column
 * @method AccountGroupRef findOneByAccountGroupId(int $account_group_id) Return the first AccountGroupRef filtered by the account_group_id column
 * @method AccountGroupRef findOneByRole(string $role) Return the first AccountGroupRef filtered by the role column
 *
 * @method array findByAccountGroupRefId(int $account_group_ref_id) Return AccountGroupRef objects filtered by the account_group_ref_id column
 * @method array findByAccountId(int $account_id) Return AccountGroupRef objects filtered by the account_id column
 * @method array findByAccountGroupId(int $account_group_id) Return AccountGroupRef objects filtered by the account_group_id column
 * @method array findByRole(string $role) Return AccountGroupRef objects filtered by the role column
 */
abstract class BaseAccountGroupRefQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseAccountGroupRefQuery object.
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
            $modelName = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountGroupRef';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new AccountGroupRefQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   AccountGroupRefQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return AccountGroupRefQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof AccountGroupRefQuery) {
            return $criteria;
        }
        $query = new AccountGroupRefQuery(null, null, $modelAlias);

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
     * @return   AccountGroupRef|AccountGroupRef[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = AccountGroupRefPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(AccountGroupRefPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 AccountGroupRef A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByAccountGroupRefId($key, $con = null)
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
     * @return                 AccountGroupRef A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT account_group_ref_id, account_id, account_group_id, role FROM core.account_group_ref WHERE account_group_ref_id = :p0';
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
            $obj = new AccountGroupRef();
            $obj->hydrate($row);
            AccountGroupRefPeer::addInstanceToPool($obj, (string) $key);
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
     * @return AccountGroupRef|AccountGroupRef[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|AccountGroupRef[]|mixed the list of results, formatted by the current formatter
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
     * @return AccountGroupRef[]
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
     * @return AccountGroupRefQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(AccountGroupRefPeer::ACCOUNT_GROUP_REF_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return AccountGroupRefQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(AccountGroupRefPeer::ACCOUNT_GROUP_REF_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the account_group_ref_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAccountGroupRefId(1234); // WHERE account_group_ref_id = 1234
     * $query->filterByAccountGroupRefId(array(12, 34)); // WHERE account_group_ref_id IN (12, 34)
     * $query->filterByAccountGroupRefId(array('min' => 12)); // WHERE account_group_ref_id >= 12
     * $query->filterByAccountGroupRefId(array('max' => 12)); // WHERE account_group_ref_id <= 12
     * </code>
     *
     * @param     mixed $accountGroupRefId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountGroupRefQuery The current query, for fluid interface
     */
    public function filterByAccountGroupRefId($accountGroupRefId = null, $comparison = null)
    {
        if (is_array($accountGroupRefId)) {
            $useMinMax = false;
            if (isset($accountGroupRefId['min'])) {
                $this->addUsingAlias(AccountGroupRefPeer::ACCOUNT_GROUP_REF_ID, $accountGroupRefId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accountGroupRefId['max'])) {
                $this->addUsingAlias(AccountGroupRefPeer::ACCOUNT_GROUP_REF_ID, $accountGroupRefId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AccountGroupRefPeer::ACCOUNT_GROUP_REF_ID, $accountGroupRefId, $comparison);
    }

    /**
     * Filter the query on the account_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAccountId(1234); // WHERE account_id = 1234
     * $query->filterByAccountId(array(12, 34)); // WHERE account_id IN (12, 34)
     * $query->filterByAccountId(array('min' => 12)); // WHERE account_id >= 12
     * $query->filterByAccountId(array('max' => 12)); // WHERE account_id <= 12
     * </code>
     *
     * @see       filterByAccount()
     *
     * @param     mixed $accountId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountGroupRefQuery The current query, for fluid interface
     */
    public function filterByAccountId($accountId = null, $comparison = null)
    {
        if (is_array($accountId)) {
            $useMinMax = false;
            if (isset($accountId['min'])) {
                $this->addUsingAlias(AccountGroupRefPeer::ACCOUNT_ID, $accountId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accountId['max'])) {
                $this->addUsingAlias(AccountGroupRefPeer::ACCOUNT_ID, $accountId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AccountGroupRefPeer::ACCOUNT_ID, $accountId, $comparison);
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
     * @see       filterByAccountGroup()
     *
     * @param     mixed $accountGroupId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountGroupRefQuery The current query, for fluid interface
     */
    public function filterByAccountGroupId($accountGroupId = null, $comparison = null)
    {
        if (is_array($accountGroupId)) {
            $useMinMax = false;
            if (isset($accountGroupId['min'])) {
                $this->addUsingAlias(AccountGroupRefPeer::ACCOUNT_GROUP_ID, $accountGroupId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accountGroupId['max'])) {
                $this->addUsingAlias(AccountGroupRefPeer::ACCOUNT_GROUP_ID, $accountGroupId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AccountGroupRefPeer::ACCOUNT_GROUP_ID, $accountGroupId, $comparison);
    }

    /**
     * Filter the query on the role column
     *
     * Example usage:
     * <code>
     * $query->filterByRole('fooValue');   // WHERE role = 'fooValue'
     * $query->filterByRole('%fooValue%'); // WHERE role LIKE '%fooValue%'
     * </code>
     *
     * @param     string $role The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountGroupRefQuery The current query, for fluid interface
     */
    public function filterByRole($role = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($role)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $role)) {
                $role = str_replace('*', '%', $role);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AccountGroupRefPeer::ROLE, $role, $comparison);
    }

    /**
     * Filter the query by a related Account object
     *
     * @param   Account|PropelObjectCollection $account The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AccountGroupRefQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAccount($account, $comparison = null)
    {
        if ($account instanceof Account) {
            return $this
                ->addUsingAlias(AccountGroupRefPeer::ACCOUNT_ID, $account->getAccountId(), $comparison);
        } elseif ($account instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(AccountGroupRefPeer::ACCOUNT_ID, $account->toKeyValue('PrimaryKey', 'AccountId'), $comparison);
        } else {
            throw new PropelException('filterByAccount() only accepts arguments of type Account or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Account relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return AccountGroupRefQuery The current query, for fluid interface
     */
    public function joinAccount($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Account');

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
            $this->addJoinObject($join, 'Account');
        }

        return $this;
    }

    /**
     * Use the Account relation Account object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountQuery A secondary query class using the current class as primary query
     */
    public function useAccountQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAccount($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Account', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountQuery');
    }

    /**
     * Filter the query by a related AccountGroup object
     *
     * @param   AccountGroup|PropelObjectCollection $accountGroup The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AccountGroupRefQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAccountGroup($accountGroup, $comparison = null)
    {
        if ($accountGroup instanceof AccountGroup) {
            return $this
                ->addUsingAlias(AccountGroupRefPeer::ACCOUNT_GROUP_ID, $accountGroup->getAccountGroupId(), $comparison);
        } elseif ($accountGroup instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(AccountGroupRefPeer::ACCOUNT_GROUP_ID, $accountGroup->toKeyValue('PrimaryKey', 'AccountGroupId'), $comparison);
        } else {
            throw new PropelException('filterByAccountGroup() only accepts arguments of type AccountGroup or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AccountGroup relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return AccountGroupRefQuery The current query, for fluid interface
     */
    public function joinAccountGroup($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AccountGroup');

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
            $this->addJoinObject($join, 'AccountGroup');
        }

        return $this;
    }

    /**
     * Use the AccountGroup relation AccountGroup object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountGroupQuery A secondary query class using the current class as primary query
     */
    public function useAccountGroupQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAccountGroup($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AccountGroup', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountGroupQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   AccountGroupRef $accountGroupRef Object to remove from the list of results
     *
     * @return AccountGroupRefQuery The current query, for fluid interface
     */
    public function prune($accountGroupRef = null)
    {
        if ($accountGroupRef) {
            $this->addUsingAlias(AccountGroupRefPeer::ACCOUNT_GROUP_REF_ID, $accountGroupRef->getAccountGroupRefId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

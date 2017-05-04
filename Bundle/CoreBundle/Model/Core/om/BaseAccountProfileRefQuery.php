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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountProfile;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountProfileRef;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountProfileRefPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountProfileRefQuery;

/**
 * @method AccountProfileRefQuery orderByAccountProfileRefId($order = Criteria::ASC) Order by the account_profile_ref_id column
 * @method AccountProfileRefQuery orderByAccountId($order = Criteria::ASC) Order by the account_id column
 * @method AccountProfileRefQuery orderByAccountProfileId($order = Criteria::ASC) Order by the account_profile_id column
 * @method AccountProfileRefQuery orderBySortOrder($order = Criteria::ASC) Order by the sort_order column
 *
 * @method AccountProfileRefQuery groupByAccountProfileRefId() Group by the account_profile_ref_id column
 * @method AccountProfileRefQuery groupByAccountId() Group by the account_id column
 * @method AccountProfileRefQuery groupByAccountProfileId() Group by the account_profile_id column
 * @method AccountProfileRefQuery groupBySortOrder() Group by the sort_order column
 *
 * @method AccountProfileRefQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method AccountProfileRefQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method AccountProfileRefQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method AccountProfileRefQuery leftJoinAccount($relationAlias = null) Adds a LEFT JOIN clause to the query using the Account relation
 * @method AccountProfileRefQuery rightJoinAccount($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Account relation
 * @method AccountProfileRefQuery innerJoinAccount($relationAlias = null) Adds a INNER JOIN clause to the query using the Account relation
 *
 * @method AccountProfileRefQuery leftJoinAccountProfile($relationAlias = null) Adds a LEFT JOIN clause to the query using the AccountProfile relation
 * @method AccountProfileRefQuery rightJoinAccountProfile($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AccountProfile relation
 * @method AccountProfileRefQuery innerJoinAccountProfile($relationAlias = null) Adds a INNER JOIN clause to the query using the AccountProfile relation
 *
 * @method AccountProfileRef findOne(PropelPDO $con = null) Return the first AccountProfileRef matching the query
 * @method AccountProfileRef findOneOrCreate(PropelPDO $con = null) Return the first AccountProfileRef matching the query, or a new AccountProfileRef object populated from the query conditions when no match is found
 *
 * @method AccountProfileRef findOneByAccountId(int $account_id) Return the first AccountProfileRef filtered by the account_id column
 * @method AccountProfileRef findOneByAccountProfileId(int $account_profile_id) Return the first AccountProfileRef filtered by the account_profile_id column
 * @method AccountProfileRef findOneBySortOrder(int $sort_order) Return the first AccountProfileRef filtered by the sort_order column
 *
 * @method array findByAccountProfileRefId(int $account_profile_ref_id) Return AccountProfileRef objects filtered by the account_profile_ref_id column
 * @method array findByAccountId(int $account_id) Return AccountProfileRef objects filtered by the account_id column
 * @method array findByAccountProfileId(int $account_profile_id) Return AccountProfileRef objects filtered by the account_profile_id column
 * @method array findBySortOrder(int $sort_order) Return AccountProfileRef objects filtered by the sort_order column
 */
abstract class BaseAccountProfileRefQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseAccountProfileRefQuery object.
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
            $modelName = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountProfileRef';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new AccountProfileRefQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   AccountProfileRefQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return AccountProfileRefQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof AccountProfileRefQuery) {
            return $criteria;
        }
        $query = new AccountProfileRefQuery(null, null, $modelAlias);

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
     * @return   AccountProfileRef|AccountProfileRef[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = AccountProfileRefPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(AccountProfileRefPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 AccountProfileRef A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByAccountProfileRefId($key, $con = null)
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
     * @return                 AccountProfileRef A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT account_profile_ref_id, account_id, account_profile_id, sort_order FROM core.account_profile_ref WHERE account_profile_ref_id = :p0';
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
            $obj = new AccountProfileRef();
            $obj->hydrate($row);
            AccountProfileRefPeer::addInstanceToPool($obj, (string) $key);
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
     * @return AccountProfileRef|AccountProfileRef[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|AccountProfileRef[]|mixed the list of results, formatted by the current formatter
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
     * $objs = $c->findPksKeepingKeyOrder(array(12, 56, 832), $con); STUOCAZZO
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return AccountProfileRef[]
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
     * @return AccountProfileRefQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(AccountProfileRefPeer::ACCOUNT_PROFILE_REF_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return AccountProfileRefQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(AccountProfileRefPeer::ACCOUNT_PROFILE_REF_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the account_profile_ref_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAccountProfileRefId(1234); // WHERE account_profile_ref_id = 1234
     * $query->filterByAccountProfileRefId(array(12, 34)); // WHERE account_profile_ref_id IN (12, 34)
     * $query->filterByAccountProfileRefId(array('min' => 12)); // WHERE account_profile_ref_id >= 12
     * $query->filterByAccountProfileRefId(array('max' => 12)); // WHERE account_profile_ref_id <= 12
     * </code>
     *
     * @param     mixed $accountProfileRefId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountProfileRefQuery The current query, for fluid interface
     */
    public function filterByAccountProfileRefId($accountProfileRefId = null, $comparison = null)
    {
        if (is_array($accountProfileRefId)) {
            $useMinMax = false;
            if (isset($accountProfileRefId['min'])) {
                $this->addUsingAlias(AccountProfileRefPeer::ACCOUNT_PROFILE_REF_ID, $accountProfileRefId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accountProfileRefId['max'])) {
                $this->addUsingAlias(AccountProfileRefPeer::ACCOUNT_PROFILE_REF_ID, $accountProfileRefId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AccountProfileRefPeer::ACCOUNT_PROFILE_REF_ID, $accountProfileRefId, $comparison);
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
     * @return AccountProfileRefQuery The current query, for fluid interface
     */
    public function filterByAccountId($accountId = null, $comparison = null)
    {
        if (is_array($accountId)) {
            $useMinMax = false;
            if (isset($accountId['min'])) {
                $this->addUsingAlias(AccountProfileRefPeer::ACCOUNT_ID, $accountId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accountId['max'])) {
                $this->addUsingAlias(AccountProfileRefPeer::ACCOUNT_ID, $accountId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AccountProfileRefPeer::ACCOUNT_ID, $accountId, $comparison);
    }

    /**
     * Filter the query on the account_profile_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAccountProfileId(1234); // WHERE account_profile_id = 1234
     * $query->filterByAccountProfileId(array(12, 34)); // WHERE account_profile_id IN (12, 34)
     * $query->filterByAccountProfileId(array('min' => 12)); // WHERE account_profile_id >= 12
     * $query->filterByAccountProfileId(array('max' => 12)); // WHERE account_profile_id <= 12
     * </code>
     *
     * @see       filterByAccountProfile()
     *
     * @param     mixed $accountProfileId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountProfileRefQuery The current query, for fluid interface
     */
    public function filterByAccountProfileId($accountProfileId = null, $comparison = null)
    {
        if (is_array($accountProfileId)) {
            $useMinMax = false;
            if (isset($accountProfileId['min'])) {
                $this->addUsingAlias(AccountProfileRefPeer::ACCOUNT_PROFILE_ID, $accountProfileId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accountProfileId['max'])) {
                $this->addUsingAlias(AccountProfileRefPeer::ACCOUNT_PROFILE_ID, $accountProfileId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AccountProfileRefPeer::ACCOUNT_PROFILE_ID, $accountProfileId, $comparison);
    }

    /**
     * Filter the query on the sort_order column
     *
     * Example usage:
     * <code>
     * $query->filterBySortOrder(1234); // WHERE sort_order = 1234
     * $query->filterBySortOrder(array(12, 34)); // WHERE sort_order IN (12, 34)
     * $query->filterBySortOrder(array('min' => 12)); // WHERE sort_order >= 12
     * $query->filterBySortOrder(array('max' => 12)); // WHERE sort_order <= 12
     * </code>
     *
     * @param     mixed $sortOrder The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountProfileRefQuery The current query, for fluid interface
     */
    public function filterBySortOrder($sortOrder = null, $comparison = null)
    {
        if (is_array($sortOrder)) {
            $useMinMax = false;
            if (isset($sortOrder['min'])) {
                $this->addUsingAlias(AccountProfileRefPeer::SORT_ORDER, $sortOrder['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sortOrder['max'])) {
                $this->addUsingAlias(AccountProfileRefPeer::SORT_ORDER, $sortOrder['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AccountProfileRefPeer::SORT_ORDER, $sortOrder, $comparison);
    }

    /**
     * Filter the query by a related Account object
     *
     * @param   Account|PropelObjectCollection $account The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AccountProfileRefQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAccount($account, $comparison = null)
    {
        if ($account instanceof Account) {
            return $this
                ->addUsingAlias(AccountProfileRefPeer::ACCOUNT_ID, $account->getAccountId(), $comparison);
        } elseif ($account instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(AccountProfileRefPeer::ACCOUNT_ID, $account->toKeyValue('PrimaryKey', 'AccountId'), $comparison);
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
     * @return AccountProfileRefQuery The current query, for fluid interface
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
     * Filter the query by a related AccountProfile object
     *
     * @param   AccountProfile|PropelObjectCollection $accountProfile The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AccountProfileRefQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAccountProfile($accountProfile, $comparison = null)
    {
        if ($accountProfile instanceof AccountProfile) {
            return $this
                ->addUsingAlias(AccountProfileRefPeer::ACCOUNT_PROFILE_ID, $accountProfile->getAccountProfileId(), $comparison);
        } elseif ($accountProfile instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(AccountProfileRefPeer::ACCOUNT_PROFILE_ID, $accountProfile->toKeyValue('PrimaryKey', 'AccountProfileId'), $comparison);
        } else {
            throw new PropelException('filterByAccountProfile() only accepts arguments of type AccountProfile or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AccountProfile relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return AccountProfileRefQuery The current query, for fluid interface
     */
    public function joinAccountProfile($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AccountProfile');

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
            $this->addJoinObject($join, 'AccountProfile');
        }

        return $this;
    }

    /**
     * Use the AccountProfile relation AccountProfile object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountProfileQuery A secondary query class using the current class as primary query
     */
    public function useAccountProfileQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAccountProfile($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AccountProfile', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountProfileQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   AccountProfileRef $accountProfileRef Object to remove from the list of results
     *
     * @return AccountProfileRefQuery The current query, for fluid interface
     */
    public function prune($accountProfileRef = null)
    {
        if ($accountProfileRef) {
            $this->addUsingAlias(AccountProfileRefPeer::ACCOUNT_PROFILE_REF_ID, $accountProfileRef->getAccountProfileRefId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

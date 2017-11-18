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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountSetting;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountSettingPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountSettingQuery;

/**
 * @method AccountSettingQuery orderByAccountSettingId($order = Criteria::ASC) Order by the account_setting_id column
 * @method AccountSettingQuery orderByAccountId($order = Criteria::ASC) Order by the account_id column
 * @method AccountSettingQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method AccountSettingQuery orderByValue($order = Criteria::ASC) Order by the value column
 *
 * @method AccountSettingQuery groupByAccountSettingId() Group by the account_setting_id column
 * @method AccountSettingQuery groupByAccountId() Group by the account_id column
 * @method AccountSettingQuery groupByName() Group by the name column
 * @method AccountSettingQuery groupByValue() Group by the value column
 *
 * @method AccountSettingQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method AccountSettingQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method AccountSettingQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method AccountSettingQuery leftJoinAccount($relationAlias = null) Adds a LEFT JOIN clause to the query using the Account relation
 * @method AccountSettingQuery rightJoinAccount($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Account relation
 * @method AccountSettingQuery innerJoinAccount($relationAlias = null) Adds a INNER JOIN clause to the query using the Account relation
 *
 * @method AccountSetting findOne(PropelPDO $con = null) Return the first AccountSetting matching the query
 * @method AccountSetting findOneOrCreate(PropelPDO $con = null) Return the first AccountSetting matching the query, or a new AccountSetting object populated from the query conditions when no match is found
 *
 * @method AccountSetting findOneByAccountId(int $account_id) Return the first AccountSetting filtered by the account_id column
 * @method AccountSetting findOneByName(string $name) Return the first AccountSetting filtered by the name column
 * @method AccountSetting findOneByValue(string $value) Return the first AccountSetting filtered by the value column
 *
 * @method array findByAccountSettingId(int $account_setting_id) Return AccountSetting objects filtered by the account_setting_id column
 * @method array findByAccountId(int $account_id) Return AccountSetting objects filtered by the account_id column
 * @method array findByName(string $name) Return AccountSetting objects filtered by the name column
 * @method array findByValue(string $value) Return AccountSetting objects filtered by the value column
 */
abstract class BaseAccountSettingQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseAccountSettingQuery object.
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
            $modelName = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountSetting';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new AccountSettingQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   AccountSettingQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return AccountSettingQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof AccountSettingQuery) {
            return $criteria;
        }
        $query = new AccountSettingQuery(null, null, $modelAlias);

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
     * @return   AccountSetting|AccountSetting[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = AccountSettingPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(AccountSettingPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 AccountSetting A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByAccountSettingId($key, $con = null)
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
     * @return                 AccountSetting A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT account_setting_id, account_id, name, value FROM core.account_setting WHERE account_setting_id = :p0';
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
            $obj = new AccountSetting();
            $obj->hydrate($row);
            AccountSettingPeer::addInstanceToPool($obj, (string) $key);
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
     * @return AccountSetting|AccountSetting[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|AccountSetting[]|mixed the list of results, formatted by the current formatter
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
     * @return AccountSetting[]
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
     * @return AccountSettingQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(AccountSettingPeer::ACCOUNT_SETTING_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return AccountSettingQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(AccountSettingPeer::ACCOUNT_SETTING_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the account_setting_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAccountSettingId(1234); // WHERE account_setting_id = 1234
     * $query->filterByAccountSettingId(array(12, 34)); // WHERE account_setting_id IN (12, 34)
     * $query->filterByAccountSettingId(array('min' => 12)); // WHERE account_setting_id >= 12
     * $query->filterByAccountSettingId(array('max' => 12)); // WHERE account_setting_id <= 12
     * </code>
     *
     * @param     mixed $accountSettingId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountSettingQuery The current query, for fluid interface
     */
    public function filterByAccountSettingId($accountSettingId = null, $comparison = null)
    {
        if (is_array($accountSettingId)) {
            $useMinMax = false;
            if (isset($accountSettingId['min'])) {
                $this->addUsingAlias(AccountSettingPeer::ACCOUNT_SETTING_ID, $accountSettingId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accountSettingId['max'])) {
                $this->addUsingAlias(AccountSettingPeer::ACCOUNT_SETTING_ID, $accountSettingId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AccountSettingPeer::ACCOUNT_SETTING_ID, $accountSettingId, $comparison);
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
     * @return AccountSettingQuery The current query, for fluid interface
     */
    public function filterByAccountId($accountId = null, $comparison = null)
    {
        if (is_array($accountId)) {
            $useMinMax = false;
            if (isset($accountId['min'])) {
                $this->addUsingAlias(AccountSettingPeer::ACCOUNT_ID, $accountId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accountId['max'])) {
                $this->addUsingAlias(AccountSettingPeer::ACCOUNT_ID, $accountId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AccountSettingPeer::ACCOUNT_ID, $accountId, $comparison);
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
     * @return AccountSettingQuery The current query, for fluid interface
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

        return $this->addUsingAlias(AccountSettingPeer::NAME, $name, $comparison);
    }

    /**
     * Filter the query on the value column
     *
     * Example usage:
     * <code>
     * $query->filterByValue('fooValue');   // WHERE value = 'fooValue'
     * $query->filterByValue('%fooValue%'); // WHERE value LIKE '%fooValue%'
     * </code>
     *
     * @param     string $value The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountSettingQuery The current query, for fluid interface
     */
    public function filterByValue($value = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($value)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $value)) {
                $value = str_replace('*', '%', $value);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AccountSettingPeer::VALUE, $value, $comparison);
    }

    /**
     * Filter the query by a related Account object
     *
     * @param   Account|PropelObjectCollection $account The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AccountSettingQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAccount($account, $comparison = null)
    {
        if ($account instanceof Account) {
            return $this
                ->addUsingAlias(AccountSettingPeer::ACCOUNT_ID, $account->getAccountId(), $comparison);
        } elseif ($account instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(AccountSettingPeer::ACCOUNT_ID, $account->toKeyValue('PrimaryKey', 'AccountId'), $comparison);
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
     * @return AccountSettingQuery The current query, for fluid interface
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
     * Exclude object from result
     *
     * @param   AccountSetting $accountSetting Object to remove from the list of results
     *
     * @return AccountSettingQuery The current query, for fluid interface
     */
    public function prune($accountSetting = null)
    {
        if ($accountSetting) {
            $this->addUsingAlias(AccountSettingPeer::ACCOUNT_SETTING_ID, $accountSetting->getAccountSettingId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

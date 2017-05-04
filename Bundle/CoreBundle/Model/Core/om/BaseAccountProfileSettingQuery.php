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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountProfile;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountProfileSetting;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountProfileSettingPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountProfileSettingQuery;

/**
 * @method AccountProfileSettingQuery orderByAccountProfileSettingId($order = Criteria::ASC) Order by the account_profile_setting_id column
 * @method AccountProfileSettingQuery orderByAccountProfileId($order = Criteria::ASC) Order by the account_profile_id column
 * @method AccountProfileSettingQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method AccountProfileSettingQuery orderByValue($order = Criteria::ASC) Order by the value column
 *
 * @method AccountProfileSettingQuery groupByAccountProfileSettingId() Group by the account_profile_setting_id column
 * @method AccountProfileSettingQuery groupByAccountProfileId() Group by the account_profile_id column
 * @method AccountProfileSettingQuery groupByName() Group by the name column
 * @method AccountProfileSettingQuery groupByValue() Group by the value column
 *
 * @method AccountProfileSettingQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method AccountProfileSettingQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method AccountProfileSettingQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method AccountProfileSettingQuery leftJoinAccountProfile($relationAlias = null) Adds a LEFT JOIN clause to the query using the AccountProfile relation
 * @method AccountProfileSettingQuery rightJoinAccountProfile($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AccountProfile relation
 * @method AccountProfileSettingQuery innerJoinAccountProfile($relationAlias = null) Adds a INNER JOIN clause to the query using the AccountProfile relation
 *
 * @method AccountProfileSetting findOne(PropelPDO $con = null) Return the first AccountProfileSetting matching the query
 * @method AccountProfileSetting findOneOrCreate(PropelPDO $con = null) Return the first AccountProfileSetting matching the query, or a new AccountProfileSetting object populated from the query conditions when no match is found
 *
 * @method AccountProfileSetting findOneByAccountProfileId(int $account_profile_id) Return the first AccountProfileSetting filtered by the account_profile_id column
 * @method AccountProfileSetting findOneByName(string $name) Return the first AccountProfileSetting filtered by the name column
 * @method AccountProfileSetting findOneByValue(string $value) Return the first AccountProfileSetting filtered by the value column
 *
 * @method array findByAccountProfileSettingId(int $account_profile_setting_id) Return AccountProfileSetting objects filtered by the account_profile_setting_id column
 * @method array findByAccountProfileId(int $account_profile_id) Return AccountProfileSetting objects filtered by the account_profile_id column
 * @method array findByName(string $name) Return AccountProfileSetting objects filtered by the name column
 * @method array findByValue(string $value) Return AccountProfileSetting objects filtered by the value column
 */
abstract class BaseAccountProfileSettingQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseAccountProfileSettingQuery object.
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
            $modelName = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountProfileSetting';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new AccountProfileSettingQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   AccountProfileSettingQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return AccountProfileSettingQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof AccountProfileSettingQuery) {
            return $criteria;
        }
        $query = new AccountProfileSettingQuery(null, null, $modelAlias);

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
     * @return   AccountProfileSetting|AccountProfileSetting[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = AccountProfileSettingPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(AccountProfileSettingPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 AccountProfileSetting A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByAccountProfileSettingId($key, $con = null)
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
     * @return                 AccountProfileSetting A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT account_profile_setting_id, account_profile_id, name, value FROM core.account_profile_setting WHERE account_profile_setting_id = :p0';
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
            $obj = new AccountProfileSetting();
            $obj->hydrate($row);
            AccountProfileSettingPeer::addInstanceToPool($obj, (string) $key);
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
     * @return AccountProfileSetting|AccountProfileSetting[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|AccountProfileSetting[]|mixed the list of results, formatted by the current formatter
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
     * @return AccountProfileSetting[]
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
     * @return AccountProfileSettingQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(AccountProfileSettingPeer::ACCOUNT_PROFILE_SETTING_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return AccountProfileSettingQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(AccountProfileSettingPeer::ACCOUNT_PROFILE_SETTING_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the account_profile_setting_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAccountProfileSettingId(1234); // WHERE account_profile_setting_id = 1234
     * $query->filterByAccountProfileSettingId(array(12, 34)); // WHERE account_profile_setting_id IN (12, 34)
     * $query->filterByAccountProfileSettingId(array('min' => 12)); // WHERE account_profile_setting_id >= 12
     * $query->filterByAccountProfileSettingId(array('max' => 12)); // WHERE account_profile_setting_id <= 12
     * </code>
     *
     * @param     mixed $accountProfileSettingId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountProfileSettingQuery The current query, for fluid interface
     */
    public function filterByAccountProfileSettingId($accountProfileSettingId = null, $comparison = null)
    {
        if (is_array($accountProfileSettingId)) {
            $useMinMax = false;
            if (isset($accountProfileSettingId['min'])) {
                $this->addUsingAlias(AccountProfileSettingPeer::ACCOUNT_PROFILE_SETTING_ID, $accountProfileSettingId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accountProfileSettingId['max'])) {
                $this->addUsingAlias(AccountProfileSettingPeer::ACCOUNT_PROFILE_SETTING_ID, $accountProfileSettingId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AccountProfileSettingPeer::ACCOUNT_PROFILE_SETTING_ID, $accountProfileSettingId, $comparison);
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
     * @return AccountProfileSettingQuery The current query, for fluid interface
     */
    public function filterByAccountProfileId($accountProfileId = null, $comparison = null)
    {
        if (is_array($accountProfileId)) {
            $useMinMax = false;
            if (isset($accountProfileId['min'])) {
                $this->addUsingAlias(AccountProfileSettingPeer::ACCOUNT_PROFILE_ID, $accountProfileId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accountProfileId['max'])) {
                $this->addUsingAlias(AccountProfileSettingPeer::ACCOUNT_PROFILE_ID, $accountProfileId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AccountProfileSettingPeer::ACCOUNT_PROFILE_ID, $accountProfileId, $comparison);
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
     * @return AccountProfileSettingQuery The current query, for fluid interface
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

        return $this->addUsingAlias(AccountProfileSettingPeer::NAME, $name, $comparison);
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
     * @return AccountProfileSettingQuery The current query, for fluid interface
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

        return $this->addUsingAlias(AccountProfileSettingPeer::VALUE, $value, $comparison);
    }

    /**
     * Filter the query by a related AccountProfile object
     *
     * @param   AccountProfile|PropelObjectCollection $accountProfile The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AccountProfileSettingQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAccountProfile($accountProfile, $comparison = null)
    {
        if ($accountProfile instanceof AccountProfile) {
            return $this
                ->addUsingAlias(AccountProfileSettingPeer::ACCOUNT_PROFILE_ID, $accountProfile->getAccountProfileId(), $comparison);
        } elseif ($accountProfile instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(AccountProfileSettingPeer::ACCOUNT_PROFILE_ID, $accountProfile->toKeyValue('PrimaryKey', 'AccountProfileId'), $comparison);
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
     * @return AccountProfileSettingQuery The current query, for fluid interface
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
     * @param   AccountProfileSetting $accountProfileSetting Object to remove from the list of results
     *
     * @return AccountProfileSettingQuery The current query, for fluid interface
     */
    public function prune($accountProfileSetting = null)
    {
        if ($accountProfileSetting) {
            $this->addUsingAlias(AccountProfileSettingPeer::ACCOUNT_PROFILE_SETTING_ID, $accountProfileSetting->getAccountProfileSettingId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

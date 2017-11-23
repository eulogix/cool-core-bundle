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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountProfilePeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountProfileQuery;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountProfileRef;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountProfileSetting;

/**
 * @method AccountProfileQuery orderByAccountProfileId($order = Criteria::ASC) Order by the account_profile_id column
 * @method AccountProfileQuery orderByName($order = Criteria::ASC) Order by the name column
 *
 * @method AccountProfileQuery groupByAccountProfileId() Group by the account_profile_id column
 * @method AccountProfileQuery groupByName() Group by the name column
 *
 * @method AccountProfileQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method AccountProfileQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method AccountProfileQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method AccountProfileQuery leftJoinAccountProfileSetting($relationAlias = null) Adds a LEFT JOIN clause to the query using the AccountProfileSetting relation
 * @method AccountProfileQuery rightJoinAccountProfileSetting($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AccountProfileSetting relation
 * @method AccountProfileQuery innerJoinAccountProfileSetting($relationAlias = null) Adds a INNER JOIN clause to the query using the AccountProfileSetting relation
 *
 * @method AccountProfileQuery leftJoinAccountProfileRef($relationAlias = null) Adds a LEFT JOIN clause to the query using the AccountProfileRef relation
 * @method AccountProfileQuery rightJoinAccountProfileRef($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AccountProfileRef relation
 * @method AccountProfileQuery innerJoinAccountProfileRef($relationAlias = null) Adds a INNER JOIN clause to the query using the AccountProfileRef relation
 *
 * @method AccountProfile findOne(PropelPDO $con = null) Return the first AccountProfile matching the query
 * @method AccountProfile findOneOrCreate(PropelPDO $con = null) Return the first AccountProfile matching the query, or a new AccountProfile object populated from the query conditions when no match is found
 *
 * @method AccountProfile findOneByName(string $name) Return the first AccountProfile filtered by the name column
 *
 * @method array findByAccountProfileId(int $account_profile_id) Return AccountProfile objects filtered by the account_profile_id column
 * @method array findByName(string $name) Return AccountProfile objects filtered by the name column
 */
abstract class BaseAccountProfileQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseAccountProfileQuery object.
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
            $modelName = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountProfile';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new AccountProfileQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   AccountProfileQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return AccountProfileQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof AccountProfileQuery) {
            return $criteria;
        }
        $query = new AccountProfileQuery(null, null, $modelAlias);

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
     * @return   AccountProfile|AccountProfile[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = AccountProfilePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(AccountProfilePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 AccountProfile A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByAccountProfileId($key, $con = null)
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
     * @return                 AccountProfile A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT account_profile_id, name FROM core.account_profile WHERE account_profile_id = :p0';
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
            $obj = new AccountProfile();
            $obj->hydrate($row);
            AccountProfilePeer::addInstanceToPool($obj, (string) $key);
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
     * @return AccountProfile|AccountProfile[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|AccountProfile[]|mixed the list of results, formatted by the current formatter
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
     * @return AccountProfile[]
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
     * @return AccountProfileQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(AccountProfilePeer::ACCOUNT_PROFILE_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return AccountProfileQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(AccountProfilePeer::ACCOUNT_PROFILE_ID, $keys, Criteria::IN);
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
     * @param     mixed $accountProfileId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountProfileQuery The current query, for fluid interface
     */
    public function filterByAccountProfileId($accountProfileId = null, $comparison = null)
    {
        if (is_array($accountProfileId)) {
            $useMinMax = false;
            if (isset($accountProfileId['min'])) {
                $this->addUsingAlias(AccountProfilePeer::ACCOUNT_PROFILE_ID, $accountProfileId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accountProfileId['max'])) {
                $this->addUsingAlias(AccountProfilePeer::ACCOUNT_PROFILE_ID, $accountProfileId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AccountProfilePeer::ACCOUNT_PROFILE_ID, $accountProfileId, $comparison);
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
     * @return AccountProfileQuery The current query, for fluid interface
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

        return $this->addUsingAlias(AccountProfilePeer::NAME, $name, $comparison);
    }

    /**
     * Filter the query by a related AccountProfileSetting object
     *
     * @param   AccountProfileSetting|PropelObjectCollection $accountProfileSetting  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AccountProfileQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAccountProfileSetting($accountProfileSetting, $comparison = null)
    {
        if ($accountProfileSetting instanceof AccountProfileSetting) {
            return $this
                ->addUsingAlias(AccountProfilePeer::ACCOUNT_PROFILE_ID, $accountProfileSetting->getAccountProfileId(), $comparison);
        } elseif ($accountProfileSetting instanceof PropelObjectCollection) {
            return $this
                ->useAccountProfileSettingQuery()
                ->filterByPrimaryKeys($accountProfileSetting->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByAccountProfileSetting() only accepts arguments of type AccountProfileSetting or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AccountProfileSetting relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return AccountProfileQuery The current query, for fluid interface
     */
    public function joinAccountProfileSetting($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AccountProfileSetting');

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
            $this->addJoinObject($join, 'AccountProfileSetting');
        }

        return $this;
    }

    /**
     * Use the AccountProfileSetting relation AccountProfileSetting object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountProfileSettingQuery A secondary query class using the current class as primary query
     */
    public function useAccountProfileSettingQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAccountProfileSetting($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AccountProfileSetting', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountProfileSettingQuery');
    }

    /**
     * Filter the query by a related AccountProfileRef object
     *
     * @param   AccountProfileRef|PropelObjectCollection $accountProfileRef  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AccountProfileQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAccountProfileRef($accountProfileRef, $comparison = null)
    {
        if ($accountProfileRef instanceof AccountProfileRef) {
            return $this
                ->addUsingAlias(AccountProfilePeer::ACCOUNT_PROFILE_ID, $accountProfileRef->getAccountProfileId(), $comparison);
        } elseif ($accountProfileRef instanceof PropelObjectCollection) {
            return $this
                ->useAccountProfileRefQuery()
                ->filterByPrimaryKeys($accountProfileRef->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByAccountProfileRef() only accepts arguments of type AccountProfileRef or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AccountProfileRef relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return AccountProfileQuery The current query, for fluid interface
     */
    public function joinAccountProfileRef($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AccountProfileRef');

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
            $this->addJoinObject($join, 'AccountProfileRef');
        }

        return $this;
    }

    /**
     * Use the AccountProfileRef relation AccountProfileRef object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountProfileRefQuery A secondary query class using the current class as primary query
     */
    public function useAccountProfileRefQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAccountProfileRef($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AccountProfileRef', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountProfileRefQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   AccountProfile $accountProfile Object to remove from the list of results
     *
     * @return AccountProfileQuery The current query, for fluid interface
     */
    public function prune($accountProfile = null)
    {
        if ($accountProfile) {
            $this->addUsingAlias(AccountProfilePeer::ACCOUNT_PROFILE_ID, $accountProfile->getAccountProfileId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

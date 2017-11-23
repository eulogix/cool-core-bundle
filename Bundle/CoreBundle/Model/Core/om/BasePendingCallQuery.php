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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\PendingCall;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\PendingCallPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\PendingCallQuery;

/**
 * @method PendingCallQuery orderByPendingCallId($order = Criteria::ASC) Order by the pending_call_id column
 * @method PendingCallQuery orderBySid($order = Criteria::ASC) Order by the sid column
 * @method PendingCallQuery orderByRecordingUrl($order = Criteria::ASC) Order by the recording_url column
 * @method PendingCallQuery orderByClientSid($order = Criteria::ASC) Order by the client_sid column
 * @method PendingCallQuery orderByCreationDate($order = Criteria::ASC) Order by the creation_date column
 * @method PendingCallQuery orderByCallerUserId($order = Criteria::ASC) Order by the caller_user_id column
 * @method PendingCallQuery orderByTarget($order = Criteria::ASC) Order by the target column
 * @method PendingCallQuery orderBySerializedCall($order = Criteria::ASC) Order by the serialized_call column
 * @method PendingCallQuery orderByProperties($order = Criteria::ASC) Order by the properties column
 *
 * @method PendingCallQuery groupByPendingCallId() Group by the pending_call_id column
 * @method PendingCallQuery groupBySid() Group by the sid column
 * @method PendingCallQuery groupByRecordingUrl() Group by the recording_url column
 * @method PendingCallQuery groupByClientSid() Group by the client_sid column
 * @method PendingCallQuery groupByCreationDate() Group by the creation_date column
 * @method PendingCallQuery groupByCallerUserId() Group by the caller_user_id column
 * @method PendingCallQuery groupByTarget() Group by the target column
 * @method PendingCallQuery groupBySerializedCall() Group by the serialized_call column
 * @method PendingCallQuery groupByProperties() Group by the properties column
 *
 * @method PendingCallQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method PendingCallQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method PendingCallQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method PendingCallQuery leftJoinAccount($relationAlias = null) Adds a LEFT JOIN clause to the query using the Account relation
 * @method PendingCallQuery rightJoinAccount($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Account relation
 * @method PendingCallQuery innerJoinAccount($relationAlias = null) Adds a INNER JOIN clause to the query using the Account relation
 *
 * @method PendingCall findOne(PropelPDO $con = null) Return the first PendingCall matching the query
 * @method PendingCall findOneOrCreate(PropelPDO $con = null) Return the first PendingCall matching the query, or a new PendingCall object populated from the query conditions when no match is found
 *
 * @method PendingCall findOneBySid(string $sid) Return the first PendingCall filtered by the sid column
 * @method PendingCall findOneByRecordingUrl(string $recording_url) Return the first PendingCall filtered by the recording_url column
 * @method PendingCall findOneByClientSid(string $client_sid) Return the first PendingCall filtered by the client_sid column
 * @method PendingCall findOneByCreationDate(string $creation_date) Return the first PendingCall filtered by the creation_date column
 * @method PendingCall findOneByCallerUserId(int $caller_user_id) Return the first PendingCall filtered by the caller_user_id column
 * @method PendingCall findOneByTarget(string $target) Return the first PendingCall filtered by the target column
 * @method PendingCall findOneBySerializedCall(string $serialized_call) Return the first PendingCall filtered by the serialized_call column
 * @method PendingCall findOneByProperties(string $properties) Return the first PendingCall filtered by the properties column
 *
 * @method array findByPendingCallId(int $pending_call_id) Return PendingCall objects filtered by the pending_call_id column
 * @method array findBySid(string $sid) Return PendingCall objects filtered by the sid column
 * @method array findByRecordingUrl(string $recording_url) Return PendingCall objects filtered by the recording_url column
 * @method array findByClientSid(string $client_sid) Return PendingCall objects filtered by the client_sid column
 * @method array findByCreationDate(string $creation_date) Return PendingCall objects filtered by the creation_date column
 * @method array findByCallerUserId(int $caller_user_id) Return PendingCall objects filtered by the caller_user_id column
 * @method array findByTarget(string $target) Return PendingCall objects filtered by the target column
 * @method array findBySerializedCall(string $serialized_call) Return PendingCall objects filtered by the serialized_call column
 * @method array findByProperties(string $properties) Return PendingCall objects filtered by the properties column
 */
abstract class BasePendingCallQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BasePendingCallQuery object.
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
            $modelName = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\PendingCall';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new PendingCallQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   PendingCallQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return PendingCallQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof PendingCallQuery) {
            return $criteria;
        }
        $query = new PendingCallQuery(null, null, $modelAlias);

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
     * @return   PendingCall|PendingCall[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = PendingCallPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(PendingCallPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 PendingCall A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByPendingCallId($key, $con = null)
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
     * @return                 PendingCall A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT pending_call_id, sid, recording_url, client_sid, creation_date, caller_user_id, target, serialized_call, properties FROM core.pending_call WHERE pending_call_id = :p0';
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
            $obj = new PendingCall();
            $obj->hydrate($row);
            PendingCallPeer::addInstanceToPool($obj, (string) $key);
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
     * @return PendingCall|PendingCall[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|PendingCall[]|mixed the list of results, formatted by the current formatter
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
     * @return PendingCall[]
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
     * @return PendingCallQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(PendingCallPeer::PENDING_CALL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return PendingCallQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(PendingCallPeer::PENDING_CALL_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the pending_call_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPendingCallId(1234); // WHERE pending_call_id = 1234
     * $query->filterByPendingCallId(array(12, 34)); // WHERE pending_call_id IN (12, 34)
     * $query->filterByPendingCallId(array('min' => 12)); // WHERE pending_call_id >= 12
     * $query->filterByPendingCallId(array('max' => 12)); // WHERE pending_call_id <= 12
     * </code>
     *
     * @param     mixed $pendingCallId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PendingCallQuery The current query, for fluid interface
     */
    public function filterByPendingCallId($pendingCallId = null, $comparison = null)
    {
        if (is_array($pendingCallId)) {
            $useMinMax = false;
            if (isset($pendingCallId['min'])) {
                $this->addUsingAlias(PendingCallPeer::PENDING_CALL_ID, $pendingCallId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($pendingCallId['max'])) {
                $this->addUsingAlias(PendingCallPeer::PENDING_CALL_ID, $pendingCallId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PendingCallPeer::PENDING_CALL_ID, $pendingCallId, $comparison);
    }

    /**
     * Filter the query on the sid column
     *
     * Example usage:
     * <code>
     * $query->filterBySid('fooValue');   // WHERE sid = 'fooValue'
     * $query->filterBySid('%fooValue%'); // WHERE sid LIKE '%fooValue%'
     * </code>
     *
     * @param     string $sid The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PendingCallQuery The current query, for fluid interface
     */
    public function filterBySid($sid = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($sid)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $sid)) {
                $sid = str_replace('*', '%', $sid);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PendingCallPeer::SID, $sid, $comparison);
    }

    /**
     * Filter the query on the recording_url column
     *
     * Example usage:
     * <code>
     * $query->filterByRecordingUrl('fooValue');   // WHERE recording_url = 'fooValue'
     * $query->filterByRecordingUrl('%fooValue%'); // WHERE recording_url LIKE '%fooValue%'
     * </code>
     *
     * @param     string $recordingUrl The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PendingCallQuery The current query, for fluid interface
     */
    public function filterByRecordingUrl($recordingUrl = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($recordingUrl)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $recordingUrl)) {
                $recordingUrl = str_replace('*', '%', $recordingUrl);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PendingCallPeer::RECORDING_URL, $recordingUrl, $comparison);
    }

    /**
     * Filter the query on the client_sid column
     *
     * Example usage:
     * <code>
     * $query->filterByClientSid('fooValue');   // WHERE client_sid = 'fooValue'
     * $query->filterByClientSid('%fooValue%'); // WHERE client_sid LIKE '%fooValue%'
     * </code>
     *
     * @param     string $clientSid The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PendingCallQuery The current query, for fluid interface
     */
    public function filterByClientSid($clientSid = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($clientSid)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $clientSid)) {
                $clientSid = str_replace('*', '%', $clientSid);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PendingCallPeer::CLIENT_SID, $clientSid, $comparison);
    }

    /**
     * Filter the query on the creation_date column
     *
     * Example usage:
     * <code>
     * $query->filterByCreationDate('2011-03-14'); // WHERE creation_date = '2011-03-14'
     * $query->filterByCreationDate('now'); // WHERE creation_date = '2011-03-14'
     * $query->filterByCreationDate(array('max' => 'yesterday')); // WHERE creation_date < '2011-03-13'
     * </code>
     *
     * @param     mixed $creationDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PendingCallQuery The current query, for fluid interface
     */
    public function filterByCreationDate($creationDate = null, $comparison = null)
    {
        if (is_array($creationDate)) {
            $useMinMax = false;
            if (isset($creationDate['min'])) {
                $this->addUsingAlias(PendingCallPeer::CREATION_DATE, $creationDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($creationDate['max'])) {
                $this->addUsingAlias(PendingCallPeer::CREATION_DATE, $creationDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PendingCallPeer::CREATION_DATE, $creationDate, $comparison);
    }

    /**
     * Filter the query on the caller_user_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCallerUserId(1234); // WHERE caller_user_id = 1234
     * $query->filterByCallerUserId(array(12, 34)); // WHERE caller_user_id IN (12, 34)
     * $query->filterByCallerUserId(array('min' => 12)); // WHERE caller_user_id >= 12
     * $query->filterByCallerUserId(array('max' => 12)); // WHERE caller_user_id <= 12
     * </code>
     *
     * @see       filterByAccount()
     *
     * @param     mixed $callerUserId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PendingCallQuery The current query, for fluid interface
     */
    public function filterByCallerUserId($callerUserId = null, $comparison = null)
    {
        if (is_array($callerUserId)) {
            $useMinMax = false;
            if (isset($callerUserId['min'])) {
                $this->addUsingAlias(PendingCallPeer::CALLER_USER_ID, $callerUserId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($callerUserId['max'])) {
                $this->addUsingAlias(PendingCallPeer::CALLER_USER_ID, $callerUserId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PendingCallPeer::CALLER_USER_ID, $callerUserId, $comparison);
    }

    /**
     * Filter the query on the target column
     *
     * Example usage:
     * <code>
     * $query->filterByTarget('fooValue');   // WHERE target = 'fooValue'
     * $query->filterByTarget('%fooValue%'); // WHERE target LIKE '%fooValue%'
     * </code>
     *
     * @param     string $target The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PendingCallQuery The current query, for fluid interface
     */
    public function filterByTarget($target = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($target)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $target)) {
                $target = str_replace('*', '%', $target);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PendingCallPeer::TARGET, $target, $comparison);
    }

    /**
     * Filter the query on the serialized_call column
     *
     * Example usage:
     * <code>
     * $query->filterBySerializedCall('fooValue');   // WHERE serialized_call = 'fooValue'
     * $query->filterBySerializedCall('%fooValue%'); // WHERE serialized_call LIKE '%fooValue%'
     * </code>
     *
     * @param     string $serializedCall The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PendingCallQuery The current query, for fluid interface
     */
    public function filterBySerializedCall($serializedCall = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($serializedCall)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $serializedCall)) {
                $serializedCall = str_replace('*', '%', $serializedCall);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PendingCallPeer::SERIALIZED_CALL, $serializedCall, $comparison);
    }

    /**
     * Filter the query on the properties column
     *
     * Example usage:
     * <code>
     * $query->filterByProperties('fooValue');   // WHERE properties = 'fooValue'
     * $query->filterByProperties('%fooValue%'); // WHERE properties LIKE '%fooValue%'
     * </code>
     *
     * @param     string $properties The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PendingCallQuery The current query, for fluid interface
     */
    public function filterByProperties($properties = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($properties)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $properties)) {
                $properties = str_replace('*', '%', $properties);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PendingCallPeer::PROPERTIES, $properties, $comparison);
    }

    /**
     * Filter the query by a related Account object
     *
     * @param   Account|PropelObjectCollection $account The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 PendingCallQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAccount($account, $comparison = null)
    {
        if ($account instanceof Account) {
            return $this
                ->addUsingAlias(PendingCallPeer::CALLER_USER_ID, $account->getAccountId(), $comparison);
        } elseif ($account instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(PendingCallPeer::CALLER_USER_ID, $account->toKeyValue('PrimaryKey', 'AccountId'), $comparison);
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
     * @return PendingCallQuery The current query, for fluid interface
     */
    public function joinAccount($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
    public function useAccountQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinAccount($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Account', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   PendingCall $pendingCall Object to remove from the list of results
     *
     * @return PendingCallQuery The current query, for fluid interface
     */
    public function prune($pendingCall = null)
    {
        if ($pendingCall) {
            $this->addUsingAlias(PendingCallPeer::PENDING_CALL_ID, $pendingCall->getPendingCallId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

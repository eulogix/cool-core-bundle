<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core\om;

use \Criteria;
use \Exception;
use \ModelCriteria;
use \PDO;
use \Propel;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AppLock;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AppLockPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AppLockQuery;

/**
 * @method AppLockQuery orderByAppLockId($order = Criteria::ASC) Order by the app_lock_id column
 * @method AppLockQuery orderByReason($order = Criteria::ASC) Order by the reason column
 * @method AppLockQuery orderByMessage($order = Criteria::ASC) Order by the message column
 * @method AppLockQuery orderByFromDate($order = Criteria::ASC) Order by the from_date column
 * @method AppLockQuery orderByToDate($order = Criteria::ASC) Order by the to_date column
 * @method AppLockQuery orderByActiveFlag($order = Criteria::ASC) Order by the active_flag column
 * @method AppLockQuery orderByMeta($order = Criteria::ASC) Order by the meta column
 *
 * @method AppLockQuery groupByAppLockId() Group by the app_lock_id column
 * @method AppLockQuery groupByReason() Group by the reason column
 * @method AppLockQuery groupByMessage() Group by the message column
 * @method AppLockQuery groupByFromDate() Group by the from_date column
 * @method AppLockQuery groupByToDate() Group by the to_date column
 * @method AppLockQuery groupByActiveFlag() Group by the active_flag column
 * @method AppLockQuery groupByMeta() Group by the meta column
 *
 * @method AppLockQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method AppLockQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method AppLockQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method AppLock findOne(PropelPDO $con = null) Return the first AppLock matching the query
 * @method AppLock findOneOrCreate(PropelPDO $con = null) Return the first AppLock matching the query, or a new AppLock object populated from the query conditions when no match is found
 *
 * @method AppLock findOneByReason(string $reason) Return the first AppLock filtered by the reason column
 * @method AppLock findOneByMessage(string $message) Return the first AppLock filtered by the message column
 * @method AppLock findOneByFromDate(string $from_date) Return the first AppLock filtered by the from_date column
 * @method AppLock findOneByToDate(string $to_date) Return the first AppLock filtered by the to_date column
 * @method AppLock findOneByActiveFlag(boolean $active_flag) Return the first AppLock filtered by the active_flag column
 * @method AppLock findOneByMeta(string $meta) Return the first AppLock filtered by the meta column
 *
 * @method array findByAppLockId(int $app_lock_id) Return AppLock objects filtered by the app_lock_id column
 * @method array findByReason(string $reason) Return AppLock objects filtered by the reason column
 * @method array findByMessage(string $message) Return AppLock objects filtered by the message column
 * @method array findByFromDate(string $from_date) Return AppLock objects filtered by the from_date column
 * @method array findByToDate(string $to_date) Return AppLock objects filtered by the to_date column
 * @method array findByActiveFlag(boolean $active_flag) Return AppLock objects filtered by the active_flag column
 * @method array findByMeta(string $meta) Return AppLock objects filtered by the meta column
 */
abstract class BaseAppLockQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseAppLockQuery object.
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
            $modelName = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AppLock';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new AppLockQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   AppLockQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return AppLockQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof AppLockQuery) {
            return $criteria;
        }
        $query = new AppLockQuery(null, null, $modelAlias);

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
     * @return   AppLock|AppLock[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = AppLockPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(AppLockPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 AppLock A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByAppLockId($key, $con = null)
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
     * @return                 AppLock A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT app_lock_id, reason, message, from_date, to_date, active_flag, meta FROM core.app_lock WHERE app_lock_id = :p0';
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
            $obj = new AppLock();
            $obj->hydrate($row);
            AppLockPeer::addInstanceToPool($obj, (string) $key);
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
     * @return AppLock|AppLock[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|AppLock[]|mixed the list of results, formatted by the current formatter
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
     * @return AppLock[]
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
     * @return AppLockQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(AppLockPeer::APP_LOCK_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return AppLockQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(AppLockPeer::APP_LOCK_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the app_lock_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAppLockId(1234); // WHERE app_lock_id = 1234
     * $query->filterByAppLockId(array(12, 34)); // WHERE app_lock_id IN (12, 34)
     * $query->filterByAppLockId(array('min' => 12)); // WHERE app_lock_id >= 12
     * $query->filterByAppLockId(array('max' => 12)); // WHERE app_lock_id <= 12
     * </code>
     *
     * @param     mixed $appLockId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AppLockQuery The current query, for fluid interface
     */
    public function filterByAppLockId($appLockId = null, $comparison = null)
    {
        if (is_array($appLockId)) {
            $useMinMax = false;
            if (isset($appLockId['min'])) {
                $this->addUsingAlias(AppLockPeer::APP_LOCK_ID, $appLockId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($appLockId['max'])) {
                $this->addUsingAlias(AppLockPeer::APP_LOCK_ID, $appLockId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AppLockPeer::APP_LOCK_ID, $appLockId, $comparison);
    }

    /**
     * Filter the query on the reason column
     *
     * Example usage:
     * <code>
     * $query->filterByReason('fooValue');   // WHERE reason = 'fooValue'
     * $query->filterByReason('%fooValue%'); // WHERE reason LIKE '%fooValue%'
     * </code>
     *
     * @param     string $reason The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AppLockQuery The current query, for fluid interface
     */
    public function filterByReason($reason = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($reason)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $reason)) {
                $reason = str_replace('*', '%', $reason);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AppLockPeer::REASON, $reason, $comparison);
    }

    /**
     * Filter the query on the message column
     *
     * Example usage:
     * <code>
     * $query->filterByMessage('fooValue');   // WHERE message = 'fooValue'
     * $query->filterByMessage('%fooValue%'); // WHERE message LIKE '%fooValue%'
     * </code>
     *
     * @param     string $message The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AppLockQuery The current query, for fluid interface
     */
    public function filterByMessage($message = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($message)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $message)) {
                $message = str_replace('*', '%', $message);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AppLockPeer::MESSAGE, $message, $comparison);
    }

    /**
     * Filter the query on the from_date column
     *
     * Example usage:
     * <code>
     * $query->filterByFromDate('2011-03-14'); // WHERE from_date = '2011-03-14'
     * $query->filterByFromDate('now'); // WHERE from_date = '2011-03-14'
     * $query->filterByFromDate(array('max' => 'yesterday')); // WHERE from_date < '2011-03-13'
     * </code>
     *
     * @param     mixed $fromDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AppLockQuery The current query, for fluid interface
     */
    public function filterByFromDate($fromDate = null, $comparison = null)
    {
        if (is_array($fromDate)) {
            $useMinMax = false;
            if (isset($fromDate['min'])) {
                $this->addUsingAlias(AppLockPeer::FROM_DATE, $fromDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($fromDate['max'])) {
                $this->addUsingAlias(AppLockPeer::FROM_DATE, $fromDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AppLockPeer::FROM_DATE, $fromDate, $comparison);
    }

    /**
     * Filter the query on the to_date column
     *
     * Example usage:
     * <code>
     * $query->filterByToDate('2011-03-14'); // WHERE to_date = '2011-03-14'
     * $query->filterByToDate('now'); // WHERE to_date = '2011-03-14'
     * $query->filterByToDate(array('max' => 'yesterday')); // WHERE to_date < '2011-03-13'
     * </code>
     *
     * @param     mixed $toDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AppLockQuery The current query, for fluid interface
     */
    public function filterByToDate($toDate = null, $comparison = null)
    {
        if (is_array($toDate)) {
            $useMinMax = false;
            if (isset($toDate['min'])) {
                $this->addUsingAlias(AppLockPeer::TO_DATE, $toDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($toDate['max'])) {
                $this->addUsingAlias(AppLockPeer::TO_DATE, $toDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AppLockPeer::TO_DATE, $toDate, $comparison);
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
     * @return AppLockQuery The current query, for fluid interface
     */
    public function filterByActiveFlag($activeFlag = null, $comparison = null)
    {
        if (is_string($activeFlag)) {
            $activeFlag = in_array(strtolower($activeFlag), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(AppLockPeer::ACTIVE_FLAG, $activeFlag, $comparison);
    }

    /**
     * Filter the query on the meta column
     *
     * Example usage:
     * <code>
     * $query->filterByMeta('fooValue');   // WHERE meta = 'fooValue'
     * $query->filterByMeta('%fooValue%'); // WHERE meta LIKE '%fooValue%'
     * </code>
     *
     * @param     string $meta The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AppLockQuery The current query, for fluid interface
     */
    public function filterByMeta($meta = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($meta)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $meta)) {
                $meta = str_replace('*', '%', $meta);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AppLockPeer::META, $meta, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   AppLock $appLock Object to remove from the list of results
     *
     * @return AppLockQuery The current query, for fluid interface
     */
    public function prune($appLock = null)
    {
        if ($appLock) {
            $this->addUsingAlias(AppLockPeer::APP_LOCK_ID, $appLock->getAppLockId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

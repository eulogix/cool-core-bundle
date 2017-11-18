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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserNotification;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserNotificationPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserNotificationQuery;

/**
 * @method UserNotificationQuery orderByUserNotificationId($order = Criteria::ASC) Order by the user_notification_id column
 * @method UserNotificationQuery orderByUserId($order = Criteria::ASC) Order by the user_id column
 * @method UserNotificationQuery orderByContext($order = Criteria::ASC) Order by the context column
 * @method UserNotificationQuery orderByTitle($order = Criteria::ASC) Order by the title column
 * @method UserNotificationQuery orderByNotification($order = Criteria::ASC) Order by the notification column
 * @method UserNotificationQuery orderByNotificationData($order = Criteria::ASC) Order by the notification_data column
 * @method UserNotificationQuery orderByCreationDate($order = Criteria::ASC) Order by the creation_date column
 * @method UserNotificationQuery orderByUpdateDate($order = Criteria::ASC) Order by the update_date column
 * @method UserNotificationQuery orderByCreationUserId($order = Criteria::ASC) Order by the creation_user_id column
 * @method UserNotificationQuery orderByUpdateUserId($order = Criteria::ASC) Order by the update_user_id column
 * @method UserNotificationQuery orderByRecordVersion($order = Criteria::ASC) Order by the record_version column
 *
 * @method UserNotificationQuery groupByUserNotificationId() Group by the user_notification_id column
 * @method UserNotificationQuery groupByUserId() Group by the user_id column
 * @method UserNotificationQuery groupByContext() Group by the context column
 * @method UserNotificationQuery groupByTitle() Group by the title column
 * @method UserNotificationQuery groupByNotification() Group by the notification column
 * @method UserNotificationQuery groupByNotificationData() Group by the notification_data column
 * @method UserNotificationQuery groupByCreationDate() Group by the creation_date column
 * @method UserNotificationQuery groupByUpdateDate() Group by the update_date column
 * @method UserNotificationQuery groupByCreationUserId() Group by the creation_user_id column
 * @method UserNotificationQuery groupByUpdateUserId() Group by the update_user_id column
 * @method UserNotificationQuery groupByRecordVersion() Group by the record_version column
 *
 * @method UserNotificationQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method UserNotificationQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method UserNotificationQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method UserNotificationQuery leftJoinAccountRelatedByUserId($relationAlias = null) Adds a LEFT JOIN clause to the query using the AccountRelatedByUserId relation
 * @method UserNotificationQuery rightJoinAccountRelatedByUserId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AccountRelatedByUserId relation
 * @method UserNotificationQuery innerJoinAccountRelatedByUserId($relationAlias = null) Adds a INNER JOIN clause to the query using the AccountRelatedByUserId relation
 *
 * @method UserNotificationQuery leftJoinAccountRelatedByCreationUserId($relationAlias = null) Adds a LEFT JOIN clause to the query using the AccountRelatedByCreationUserId relation
 * @method UserNotificationQuery rightJoinAccountRelatedByCreationUserId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AccountRelatedByCreationUserId relation
 * @method UserNotificationQuery innerJoinAccountRelatedByCreationUserId($relationAlias = null) Adds a INNER JOIN clause to the query using the AccountRelatedByCreationUserId relation
 *
 * @method UserNotificationQuery leftJoinAccountRelatedByUpdateUserId($relationAlias = null) Adds a LEFT JOIN clause to the query using the AccountRelatedByUpdateUserId relation
 * @method UserNotificationQuery rightJoinAccountRelatedByUpdateUserId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AccountRelatedByUpdateUserId relation
 * @method UserNotificationQuery innerJoinAccountRelatedByUpdateUserId($relationAlias = null) Adds a INNER JOIN clause to the query using the AccountRelatedByUpdateUserId relation
 *
 * @method UserNotification findOne(PropelPDO $con = null) Return the first UserNotification matching the query
 * @method UserNotification findOneOrCreate(PropelPDO $con = null) Return the first UserNotification matching the query, or a new UserNotification object populated from the query conditions when no match is found
 *
 * @method UserNotification findOneByUserId(int $user_id) Return the first UserNotification filtered by the user_id column
 * @method UserNotification findOneByContext(string $context) Return the first UserNotification filtered by the context column
 * @method UserNotification findOneByTitle(string $title) Return the first UserNotification filtered by the title column
 * @method UserNotification findOneByNotification(string $notification) Return the first UserNotification filtered by the notification column
 * @method UserNotification findOneByNotificationData(string $notification_data) Return the first UserNotification filtered by the notification_data column
 * @method UserNotification findOneByCreationDate(string $creation_date) Return the first UserNotification filtered by the creation_date column
 * @method UserNotification findOneByUpdateDate(string $update_date) Return the first UserNotification filtered by the update_date column
 * @method UserNotification findOneByCreationUserId(int $creation_user_id) Return the first UserNotification filtered by the creation_user_id column
 * @method UserNotification findOneByUpdateUserId(int $update_user_id) Return the first UserNotification filtered by the update_user_id column
 * @method UserNotification findOneByRecordVersion(int $record_version) Return the first UserNotification filtered by the record_version column
 *
 * @method array findByUserNotificationId(int $user_notification_id) Return UserNotification objects filtered by the user_notification_id column
 * @method array findByUserId(int $user_id) Return UserNotification objects filtered by the user_id column
 * @method array findByContext(string $context) Return UserNotification objects filtered by the context column
 * @method array findByTitle(string $title) Return UserNotification objects filtered by the title column
 * @method array findByNotification(string $notification) Return UserNotification objects filtered by the notification column
 * @method array findByNotificationData(string $notification_data) Return UserNotification objects filtered by the notification_data column
 * @method array findByCreationDate(string $creation_date) Return UserNotification objects filtered by the creation_date column
 * @method array findByUpdateDate(string $update_date) Return UserNotification objects filtered by the update_date column
 * @method array findByCreationUserId(int $creation_user_id) Return UserNotification objects filtered by the creation_user_id column
 * @method array findByUpdateUserId(int $update_user_id) Return UserNotification objects filtered by the update_user_id column
 * @method array findByRecordVersion(int $record_version) Return UserNotification objects filtered by the record_version column
 */
abstract class BaseUserNotificationQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseUserNotificationQuery object.
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
            $modelName = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\UserNotification';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new UserNotificationQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   UserNotificationQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return UserNotificationQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof UserNotificationQuery) {
            return $criteria;
        }
        $query = new UserNotificationQuery(null, null, $modelAlias);

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
     * @return   UserNotification|UserNotification[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = UserNotificationPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(UserNotificationPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 UserNotification A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByUserNotificationId($key, $con = null)
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
     * @return                 UserNotification A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT user_notification_id, user_id, context, title, notification, notification_data, creation_date, update_date, creation_user_id, update_user_id, record_version FROM core.user_notification WHERE user_notification_id = :p0';
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
            $obj = new UserNotification();
            $obj->hydrate($row);
            UserNotificationPeer::addInstanceToPool($obj, (string) $key);
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
     * @return UserNotification|UserNotification[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|UserNotification[]|mixed the list of results, formatted by the current formatter
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
     * @return UserNotification[]
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
     * @return UserNotificationQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(UserNotificationPeer::USER_NOTIFICATION_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return UserNotificationQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(UserNotificationPeer::USER_NOTIFICATION_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the user_notification_id column
     *
     * Example usage:
     * <code>
     * $query->filterByUserNotificationId(1234); // WHERE user_notification_id = 1234
     * $query->filterByUserNotificationId(array(12, 34)); // WHERE user_notification_id IN (12, 34)
     * $query->filterByUserNotificationId(array('min' => 12)); // WHERE user_notification_id >= 12
     * $query->filterByUserNotificationId(array('max' => 12)); // WHERE user_notification_id <= 12
     * </code>
     *
     * @param     mixed $userNotificationId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserNotificationQuery The current query, for fluid interface
     */
    public function filterByUserNotificationId($userNotificationId = null, $comparison = null)
    {
        if (is_array($userNotificationId)) {
            $useMinMax = false;
            if (isset($userNotificationId['min'])) {
                $this->addUsingAlias(UserNotificationPeer::USER_NOTIFICATION_ID, $userNotificationId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($userNotificationId['max'])) {
                $this->addUsingAlias(UserNotificationPeer::USER_NOTIFICATION_ID, $userNotificationId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserNotificationPeer::USER_NOTIFICATION_ID, $userNotificationId, $comparison);
    }

    /**
     * Filter the query on the user_id column
     *
     * Example usage:
     * <code>
     * $query->filterByUserId(1234); // WHERE user_id = 1234
     * $query->filterByUserId(array(12, 34)); // WHERE user_id IN (12, 34)
     * $query->filterByUserId(array('min' => 12)); // WHERE user_id >= 12
     * $query->filterByUserId(array('max' => 12)); // WHERE user_id <= 12
     * </code>
     *
     * @see       filterByAccountRelatedByUserId()
     *
     * @param     mixed $userId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserNotificationQuery The current query, for fluid interface
     */
    public function filterByUserId($userId = null, $comparison = null)
    {
        if (is_array($userId)) {
            $useMinMax = false;
            if (isset($userId['min'])) {
                $this->addUsingAlias(UserNotificationPeer::USER_ID, $userId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($userId['max'])) {
                $this->addUsingAlias(UserNotificationPeer::USER_ID, $userId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserNotificationPeer::USER_ID, $userId, $comparison);
    }

    /**
     * Filter the query on the context column
     *
     * Example usage:
     * <code>
     * $query->filterByContext('fooValue');   // WHERE context = 'fooValue'
     * $query->filterByContext('%fooValue%'); // WHERE context LIKE '%fooValue%'
     * </code>
     *
     * @param     string $context The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserNotificationQuery The current query, for fluid interface
     */
    public function filterByContext($context = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($context)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $context)) {
                $context = str_replace('*', '%', $context);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserNotificationPeer::CONTEXT, $context, $comparison);
    }

    /**
     * Filter the query on the title column
     *
     * Example usage:
     * <code>
     * $query->filterByTitle('fooValue');   // WHERE title = 'fooValue'
     * $query->filterByTitle('%fooValue%'); // WHERE title LIKE '%fooValue%'
     * </code>
     *
     * @param     string $title The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserNotificationQuery The current query, for fluid interface
     */
    public function filterByTitle($title = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($title)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $title)) {
                $title = str_replace('*', '%', $title);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserNotificationPeer::TITLE, $title, $comparison);
    }

    /**
     * Filter the query on the notification column
     *
     * Example usage:
     * <code>
     * $query->filterByNotification('fooValue');   // WHERE notification = 'fooValue'
     * $query->filterByNotification('%fooValue%'); // WHERE notification LIKE '%fooValue%'
     * </code>
     *
     * @param     string $notification The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserNotificationQuery The current query, for fluid interface
     */
    public function filterByNotification($notification = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($notification)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $notification)) {
                $notification = str_replace('*', '%', $notification);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserNotificationPeer::NOTIFICATION, $notification, $comparison);
    }

    /**
     * Filter the query on the notification_data column
     *
     * Example usage:
     * <code>
     * $query->filterByNotificationData('fooValue');   // WHERE notification_data = 'fooValue'
     * $query->filterByNotificationData('%fooValue%'); // WHERE notification_data LIKE '%fooValue%'
     * </code>
     *
     * @param     string $notificationData The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserNotificationQuery The current query, for fluid interface
     */
    public function filterByNotificationData($notificationData = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($notificationData)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $notificationData)) {
                $notificationData = str_replace('*', '%', $notificationData);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserNotificationPeer::NOTIFICATION_DATA, $notificationData, $comparison);
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
     * @return UserNotificationQuery The current query, for fluid interface
     */
    public function filterByCreationDate($creationDate = null, $comparison = null)
    {
        if (is_array($creationDate)) {
            $useMinMax = false;
            if (isset($creationDate['min'])) {
                $this->addUsingAlias(UserNotificationPeer::CREATION_DATE, $creationDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($creationDate['max'])) {
                $this->addUsingAlias(UserNotificationPeer::CREATION_DATE, $creationDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserNotificationPeer::CREATION_DATE, $creationDate, $comparison);
    }

    /**
     * Filter the query on the update_date column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdateDate('2011-03-14'); // WHERE update_date = '2011-03-14'
     * $query->filterByUpdateDate('now'); // WHERE update_date = '2011-03-14'
     * $query->filterByUpdateDate(array('max' => 'yesterday')); // WHERE update_date < '2011-03-13'
     * </code>
     *
     * @param     mixed $updateDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserNotificationQuery The current query, for fluid interface
     */
    public function filterByUpdateDate($updateDate = null, $comparison = null)
    {
        if (is_array($updateDate)) {
            $useMinMax = false;
            if (isset($updateDate['min'])) {
                $this->addUsingAlias(UserNotificationPeer::UPDATE_DATE, $updateDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updateDate['max'])) {
                $this->addUsingAlias(UserNotificationPeer::UPDATE_DATE, $updateDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserNotificationPeer::UPDATE_DATE, $updateDate, $comparison);
    }

    /**
     * Filter the query on the creation_user_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCreationUserId(1234); // WHERE creation_user_id = 1234
     * $query->filterByCreationUserId(array(12, 34)); // WHERE creation_user_id IN (12, 34)
     * $query->filterByCreationUserId(array('min' => 12)); // WHERE creation_user_id >= 12
     * $query->filterByCreationUserId(array('max' => 12)); // WHERE creation_user_id <= 12
     * </code>
     *
     * @see       filterByAccountRelatedByCreationUserId()
     *
     * @param     mixed $creationUserId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserNotificationQuery The current query, for fluid interface
     */
    public function filterByCreationUserId($creationUserId = null, $comparison = null)
    {
        if (is_array($creationUserId)) {
            $useMinMax = false;
            if (isset($creationUserId['min'])) {
                $this->addUsingAlias(UserNotificationPeer::CREATION_USER_ID, $creationUserId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($creationUserId['max'])) {
                $this->addUsingAlias(UserNotificationPeer::CREATION_USER_ID, $creationUserId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserNotificationPeer::CREATION_USER_ID, $creationUserId, $comparison);
    }

    /**
     * Filter the query on the update_user_id column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdateUserId(1234); // WHERE update_user_id = 1234
     * $query->filterByUpdateUserId(array(12, 34)); // WHERE update_user_id IN (12, 34)
     * $query->filterByUpdateUserId(array('min' => 12)); // WHERE update_user_id >= 12
     * $query->filterByUpdateUserId(array('max' => 12)); // WHERE update_user_id <= 12
     * </code>
     *
     * @see       filterByAccountRelatedByUpdateUserId()
     *
     * @param     mixed $updateUserId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserNotificationQuery The current query, for fluid interface
     */
    public function filterByUpdateUserId($updateUserId = null, $comparison = null)
    {
        if (is_array($updateUserId)) {
            $useMinMax = false;
            if (isset($updateUserId['min'])) {
                $this->addUsingAlias(UserNotificationPeer::UPDATE_USER_ID, $updateUserId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updateUserId['max'])) {
                $this->addUsingAlias(UserNotificationPeer::UPDATE_USER_ID, $updateUserId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserNotificationPeer::UPDATE_USER_ID, $updateUserId, $comparison);
    }

    /**
     * Filter the query on the record_version column
     *
     * Example usage:
     * <code>
     * $query->filterByRecordVersion(1234); // WHERE record_version = 1234
     * $query->filterByRecordVersion(array(12, 34)); // WHERE record_version IN (12, 34)
     * $query->filterByRecordVersion(array('min' => 12)); // WHERE record_version >= 12
     * $query->filterByRecordVersion(array('max' => 12)); // WHERE record_version <= 12
     * </code>
     *
     * @param     mixed $recordVersion The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserNotificationQuery The current query, for fluid interface
     */
    public function filterByRecordVersion($recordVersion = null, $comparison = null)
    {
        if (is_array($recordVersion)) {
            $useMinMax = false;
            if (isset($recordVersion['min'])) {
                $this->addUsingAlias(UserNotificationPeer::RECORD_VERSION, $recordVersion['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($recordVersion['max'])) {
                $this->addUsingAlias(UserNotificationPeer::RECORD_VERSION, $recordVersion['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserNotificationPeer::RECORD_VERSION, $recordVersion, $comparison);
    }

    /**
     * Filter the query by a related Account object
     *
     * @param   Account|PropelObjectCollection $account The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 UserNotificationQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAccountRelatedByUserId($account, $comparison = null)
    {
        if ($account instanceof Account) {
            return $this
                ->addUsingAlias(UserNotificationPeer::USER_ID, $account->getAccountId(), $comparison);
        } elseif ($account instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(UserNotificationPeer::USER_ID, $account->toKeyValue('PrimaryKey', 'AccountId'), $comparison);
        } else {
            throw new PropelException('filterByAccountRelatedByUserId() only accepts arguments of type Account or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AccountRelatedByUserId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return UserNotificationQuery The current query, for fluid interface
     */
    public function joinAccountRelatedByUserId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AccountRelatedByUserId');

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
            $this->addJoinObject($join, 'AccountRelatedByUserId');
        }

        return $this;
    }

    /**
     * Use the AccountRelatedByUserId relation Account object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountQuery A secondary query class using the current class as primary query
     */
    public function useAccountRelatedByUserIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinAccountRelatedByUserId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AccountRelatedByUserId', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountQuery');
    }

    /**
     * Filter the query by a related Account object
     *
     * @param   Account|PropelObjectCollection $account The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 UserNotificationQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAccountRelatedByCreationUserId($account, $comparison = null)
    {
        if ($account instanceof Account) {
            return $this
                ->addUsingAlias(UserNotificationPeer::CREATION_USER_ID, $account->getAccountId(), $comparison);
        } elseif ($account instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(UserNotificationPeer::CREATION_USER_ID, $account->toKeyValue('PrimaryKey', 'AccountId'), $comparison);
        } else {
            throw new PropelException('filterByAccountRelatedByCreationUserId() only accepts arguments of type Account or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AccountRelatedByCreationUserId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return UserNotificationQuery The current query, for fluid interface
     */
    public function joinAccountRelatedByCreationUserId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AccountRelatedByCreationUserId');

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
            $this->addJoinObject($join, 'AccountRelatedByCreationUserId');
        }

        return $this;
    }

    /**
     * Use the AccountRelatedByCreationUserId relation Account object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountQuery A secondary query class using the current class as primary query
     */
    public function useAccountRelatedByCreationUserIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinAccountRelatedByCreationUserId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AccountRelatedByCreationUserId', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountQuery');
    }

    /**
     * Filter the query by a related Account object
     *
     * @param   Account|PropelObjectCollection $account The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 UserNotificationQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAccountRelatedByUpdateUserId($account, $comparison = null)
    {
        if ($account instanceof Account) {
            return $this
                ->addUsingAlias(UserNotificationPeer::UPDATE_USER_ID, $account->getAccountId(), $comparison);
        } elseif ($account instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(UserNotificationPeer::UPDATE_USER_ID, $account->toKeyValue('PrimaryKey', 'AccountId'), $comparison);
        } else {
            throw new PropelException('filterByAccountRelatedByUpdateUserId() only accepts arguments of type Account or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AccountRelatedByUpdateUserId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return UserNotificationQuery The current query, for fluid interface
     */
    public function joinAccountRelatedByUpdateUserId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AccountRelatedByUpdateUserId');

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
            $this->addJoinObject($join, 'AccountRelatedByUpdateUserId');
        }

        return $this;
    }

    /**
     * Use the AccountRelatedByUpdateUserId relation Account object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountQuery A secondary query class using the current class as primary query
     */
    public function useAccountRelatedByUpdateUserIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinAccountRelatedByUpdateUserId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AccountRelatedByUpdateUserId', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   UserNotification $userNotification Object to remove from the list of results
     *
     * @return UserNotificationQuery The current query, for fluid interface
     */
    public function prune($userNotification = null)
    {
        if ($userNotification) {
            $this->addUsingAlias(UserNotificationPeer::USER_NOTIFICATION_ID, $userNotification->getUserNotificationId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    // auditable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     UserNotificationQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(UserNotificationPeer::UPDATE_DATE, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     UserNotificationQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(UserNotificationPeer::UPDATE_DATE);
    }

    /**
     * Order by update date asc
     *
     * @return     UserNotificationQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(UserNotificationPeer::UPDATE_DATE);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     UserNotificationQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(UserNotificationPeer::CREATION_DATE, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date desc
     *
     * @return     UserNotificationQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(UserNotificationPeer::CREATION_DATE);
    }

    /**
     * Order by create date asc
     *
     * @return     UserNotificationQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(UserNotificationPeer::CREATION_DATE);
    }
}

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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AsyncJob;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AsyncJobPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AsyncJobQuery;

/**
 * @method AsyncJobQuery orderByAsyncJobId($order = Criteria::ASC) Order by the async_job_id column
 * @method AsyncJobQuery orderByIssuerUserId($order = Criteria::ASC) Order by the issuer_user_id column
 * @method AsyncJobQuery orderByContext($order = Criteria::ASC) Order by the context column
 * @method AsyncJobQuery orderByExecutorType($order = Criteria::ASC) Order by the executor_type column
 * @method AsyncJobQuery orderByExecutionId($order = Criteria::ASC) Order by the execution_id column
 * @method AsyncJobQuery orderByJobPath($order = Criteria::ASC) Order by the job_path column
 * @method AsyncJobQuery orderByParameters($order = Criteria::ASC) Order by the parameters column
 * @method AsyncJobQuery orderByStartDate($order = Criteria::ASC) Order by the start_date column
 * @method AsyncJobQuery orderByCompletionDate($order = Criteria::ASC) Order by the completion_date column
 * @method AsyncJobQuery orderByCompletionPercentage($order = Criteria::ASC) Order by the completion_percentage column
 * @method AsyncJobQuery orderByOutcome($order = Criteria::ASC) Order by the outcome column
 * @method AsyncJobQuery orderByJobOutput($order = Criteria::ASC) Order by the job_output column
 * @method AsyncJobQuery orderByCreationDate($order = Criteria::ASC) Order by the creation_date column
 * @method AsyncJobQuery orderByUpdateDate($order = Criteria::ASC) Order by the update_date column
 * @method AsyncJobQuery orderByCreationUserId($order = Criteria::ASC) Order by the creation_user_id column
 * @method AsyncJobQuery orderByUpdateUserId($order = Criteria::ASC) Order by the update_user_id column
 * @method AsyncJobQuery orderByRecordVersion($order = Criteria::ASC) Order by the record_version column
 *
 * @method AsyncJobQuery groupByAsyncJobId() Group by the async_job_id column
 * @method AsyncJobQuery groupByIssuerUserId() Group by the issuer_user_id column
 * @method AsyncJobQuery groupByContext() Group by the context column
 * @method AsyncJobQuery groupByExecutorType() Group by the executor_type column
 * @method AsyncJobQuery groupByExecutionId() Group by the execution_id column
 * @method AsyncJobQuery groupByJobPath() Group by the job_path column
 * @method AsyncJobQuery groupByParameters() Group by the parameters column
 * @method AsyncJobQuery groupByStartDate() Group by the start_date column
 * @method AsyncJobQuery groupByCompletionDate() Group by the completion_date column
 * @method AsyncJobQuery groupByCompletionPercentage() Group by the completion_percentage column
 * @method AsyncJobQuery groupByOutcome() Group by the outcome column
 * @method AsyncJobQuery groupByJobOutput() Group by the job_output column
 * @method AsyncJobQuery groupByCreationDate() Group by the creation_date column
 * @method AsyncJobQuery groupByUpdateDate() Group by the update_date column
 * @method AsyncJobQuery groupByCreationUserId() Group by the creation_user_id column
 * @method AsyncJobQuery groupByUpdateUserId() Group by the update_user_id column
 * @method AsyncJobQuery groupByRecordVersion() Group by the record_version column
 *
 * @method AsyncJobQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method AsyncJobQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method AsyncJobQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method AsyncJobQuery leftJoinAccountRelatedByIssuerUserId($relationAlias = null) Adds a LEFT JOIN clause to the query using the AccountRelatedByIssuerUserId relation
 * @method AsyncJobQuery rightJoinAccountRelatedByIssuerUserId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AccountRelatedByIssuerUserId relation
 * @method AsyncJobQuery innerJoinAccountRelatedByIssuerUserId($relationAlias = null) Adds a INNER JOIN clause to the query using the AccountRelatedByIssuerUserId relation
 *
 * @method AsyncJobQuery leftJoinAccountRelatedByCreationUserId($relationAlias = null) Adds a LEFT JOIN clause to the query using the AccountRelatedByCreationUserId relation
 * @method AsyncJobQuery rightJoinAccountRelatedByCreationUserId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AccountRelatedByCreationUserId relation
 * @method AsyncJobQuery innerJoinAccountRelatedByCreationUserId($relationAlias = null) Adds a INNER JOIN clause to the query using the AccountRelatedByCreationUserId relation
 *
 * @method AsyncJobQuery leftJoinAccountRelatedByUpdateUserId($relationAlias = null) Adds a LEFT JOIN clause to the query using the AccountRelatedByUpdateUserId relation
 * @method AsyncJobQuery rightJoinAccountRelatedByUpdateUserId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AccountRelatedByUpdateUserId relation
 * @method AsyncJobQuery innerJoinAccountRelatedByUpdateUserId($relationAlias = null) Adds a INNER JOIN clause to the query using the AccountRelatedByUpdateUserId relation
 *
 * @method AsyncJob findOne(PropelPDO $con = null) Return the first AsyncJob matching the query
 * @method AsyncJob findOneOrCreate(PropelPDO $con = null) Return the first AsyncJob matching the query, or a new AsyncJob object populated from the query conditions when no match is found
 *
 * @method AsyncJob findOneByIssuerUserId(int $issuer_user_id) Return the first AsyncJob filtered by the issuer_user_id column
 * @method AsyncJob findOneByContext(string $context) Return the first AsyncJob filtered by the context column
 * @method AsyncJob findOneByExecutorType(string $executor_type) Return the first AsyncJob filtered by the executor_type column
 * @method AsyncJob findOneByExecutionId(string $execution_id) Return the first AsyncJob filtered by the execution_id column
 * @method AsyncJob findOneByJobPath(string $job_path) Return the first AsyncJob filtered by the job_path column
 * @method AsyncJob findOneByParameters(string $parameters) Return the first AsyncJob filtered by the parameters column
 * @method AsyncJob findOneByStartDate(string $start_date) Return the first AsyncJob filtered by the start_date column
 * @method AsyncJob findOneByCompletionDate(string $completion_date) Return the first AsyncJob filtered by the completion_date column
 * @method AsyncJob findOneByCompletionPercentage(int $completion_percentage) Return the first AsyncJob filtered by the completion_percentage column
 * @method AsyncJob findOneByOutcome(string $outcome) Return the first AsyncJob filtered by the outcome column
 * @method AsyncJob findOneByJobOutput(string $job_output) Return the first AsyncJob filtered by the job_output column
 * @method AsyncJob findOneByCreationDate(string $creation_date) Return the first AsyncJob filtered by the creation_date column
 * @method AsyncJob findOneByUpdateDate(string $update_date) Return the first AsyncJob filtered by the update_date column
 * @method AsyncJob findOneByCreationUserId(int $creation_user_id) Return the first AsyncJob filtered by the creation_user_id column
 * @method AsyncJob findOneByUpdateUserId(int $update_user_id) Return the first AsyncJob filtered by the update_user_id column
 * @method AsyncJob findOneByRecordVersion(int $record_version) Return the first AsyncJob filtered by the record_version column
 *
 * @method array findByAsyncJobId(int $async_job_id) Return AsyncJob objects filtered by the async_job_id column
 * @method array findByIssuerUserId(int $issuer_user_id) Return AsyncJob objects filtered by the issuer_user_id column
 * @method array findByContext(string $context) Return AsyncJob objects filtered by the context column
 * @method array findByExecutorType(string $executor_type) Return AsyncJob objects filtered by the executor_type column
 * @method array findByExecutionId(string $execution_id) Return AsyncJob objects filtered by the execution_id column
 * @method array findByJobPath(string $job_path) Return AsyncJob objects filtered by the job_path column
 * @method array findByParameters(string $parameters) Return AsyncJob objects filtered by the parameters column
 * @method array findByStartDate(string $start_date) Return AsyncJob objects filtered by the start_date column
 * @method array findByCompletionDate(string $completion_date) Return AsyncJob objects filtered by the completion_date column
 * @method array findByCompletionPercentage(int $completion_percentage) Return AsyncJob objects filtered by the completion_percentage column
 * @method array findByOutcome(string $outcome) Return AsyncJob objects filtered by the outcome column
 * @method array findByJobOutput(string $job_output) Return AsyncJob objects filtered by the job_output column
 * @method array findByCreationDate(string $creation_date) Return AsyncJob objects filtered by the creation_date column
 * @method array findByUpdateDate(string $update_date) Return AsyncJob objects filtered by the update_date column
 * @method array findByCreationUserId(int $creation_user_id) Return AsyncJob objects filtered by the creation_user_id column
 * @method array findByUpdateUserId(int $update_user_id) Return AsyncJob objects filtered by the update_user_id column
 * @method array findByRecordVersion(int $record_version) Return AsyncJob objects filtered by the record_version column
 */
abstract class BaseAsyncJobQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseAsyncJobQuery object.
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
            $modelName = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AsyncJob';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new AsyncJobQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   AsyncJobQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return AsyncJobQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof AsyncJobQuery) {
            return $criteria;
        }
        $query = new AsyncJobQuery(null, null, $modelAlias);

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
     * @return   AsyncJob|AsyncJob[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = AsyncJobPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(AsyncJobPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 AsyncJob A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByAsyncJobId($key, $con = null)
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
     * @return                 AsyncJob A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT async_job_id, issuer_user_id, context, executor_type, execution_id, job_path, parameters, start_date, completion_date, completion_percentage, outcome, job_output, creation_date, update_date, creation_user_id, update_user_id, record_version FROM core.async_job WHERE async_job_id = :p0';
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
            $obj = new AsyncJob();
            $obj->hydrate($row);
            AsyncJobPeer::addInstanceToPool($obj, (string) $key);
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
     * @return AsyncJob|AsyncJob[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|AsyncJob[]|mixed the list of results, formatted by the current formatter
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
     * @return AsyncJob[]
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
     * @return AsyncJobQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(AsyncJobPeer::ASYNC_JOB_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return AsyncJobQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(AsyncJobPeer::ASYNC_JOB_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the async_job_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAsyncJobId(1234); // WHERE async_job_id = 1234
     * $query->filterByAsyncJobId(array(12, 34)); // WHERE async_job_id IN (12, 34)
     * $query->filterByAsyncJobId(array('min' => 12)); // WHERE async_job_id >= 12
     * $query->filterByAsyncJobId(array('max' => 12)); // WHERE async_job_id <= 12
     * </code>
     *
     * @param     mixed $asyncJobId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AsyncJobQuery The current query, for fluid interface
     */
    public function filterByAsyncJobId($asyncJobId = null, $comparison = null)
    {
        if (is_array($asyncJobId)) {
            $useMinMax = false;
            if (isset($asyncJobId['min'])) {
                $this->addUsingAlias(AsyncJobPeer::ASYNC_JOB_ID, $asyncJobId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($asyncJobId['max'])) {
                $this->addUsingAlias(AsyncJobPeer::ASYNC_JOB_ID, $asyncJobId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AsyncJobPeer::ASYNC_JOB_ID, $asyncJobId, $comparison);
    }

    /**
     * Filter the query on the issuer_user_id column
     *
     * Example usage:
     * <code>
     * $query->filterByIssuerUserId(1234); // WHERE issuer_user_id = 1234
     * $query->filterByIssuerUserId(array(12, 34)); // WHERE issuer_user_id IN (12, 34)
     * $query->filterByIssuerUserId(array('min' => 12)); // WHERE issuer_user_id >= 12
     * $query->filterByIssuerUserId(array('max' => 12)); // WHERE issuer_user_id <= 12
     * </code>
     *
     * @see       filterByAccountRelatedByIssuerUserId()
     *
     * @param     mixed $issuerUserId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AsyncJobQuery The current query, for fluid interface
     */
    public function filterByIssuerUserId($issuerUserId = null, $comparison = null)
    {
        if (is_array($issuerUserId)) {
            $useMinMax = false;
            if (isset($issuerUserId['min'])) {
                $this->addUsingAlias(AsyncJobPeer::ISSUER_USER_ID, $issuerUserId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($issuerUserId['max'])) {
                $this->addUsingAlias(AsyncJobPeer::ISSUER_USER_ID, $issuerUserId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AsyncJobPeer::ISSUER_USER_ID, $issuerUserId, $comparison);
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
     * @return AsyncJobQuery The current query, for fluid interface
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

        return $this->addUsingAlias(AsyncJobPeer::CONTEXT, $context, $comparison);
    }

    /**
     * Filter the query on the executor_type column
     *
     * Example usage:
     * <code>
     * $query->filterByExecutorType('fooValue');   // WHERE executor_type = 'fooValue'
     * $query->filterByExecutorType('%fooValue%'); // WHERE executor_type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $executorType The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AsyncJobQuery The current query, for fluid interface
     */
    public function filterByExecutorType($executorType = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($executorType)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $executorType)) {
                $executorType = str_replace('*', '%', $executorType);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AsyncJobPeer::EXECUTOR_TYPE, $executorType, $comparison);
    }

    /**
     * Filter the query on the execution_id column
     *
     * Example usage:
     * <code>
     * $query->filterByExecutionId('fooValue');   // WHERE execution_id = 'fooValue'
     * $query->filterByExecutionId('%fooValue%'); // WHERE execution_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $executionId The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AsyncJobQuery The current query, for fluid interface
     */
    public function filterByExecutionId($executionId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($executionId)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $executionId)) {
                $executionId = str_replace('*', '%', $executionId);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AsyncJobPeer::EXECUTION_ID, $executionId, $comparison);
    }

    /**
     * Filter the query on the job_path column
     *
     * Example usage:
     * <code>
     * $query->filterByJobPath('fooValue');   // WHERE job_path = 'fooValue'
     * $query->filterByJobPath('%fooValue%'); // WHERE job_path LIKE '%fooValue%'
     * </code>
     *
     * @param     string $jobPath The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AsyncJobQuery The current query, for fluid interface
     */
    public function filterByJobPath($jobPath = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($jobPath)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $jobPath)) {
                $jobPath = str_replace('*', '%', $jobPath);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AsyncJobPeer::JOB_PATH, $jobPath, $comparison);
    }

    /**
     * Filter the query on the parameters column
     *
     * Example usage:
     * <code>
     * $query->filterByParameters('fooValue');   // WHERE parameters = 'fooValue'
     * $query->filterByParameters('%fooValue%'); // WHERE parameters LIKE '%fooValue%'
     * </code>
     *
     * @param     string $parameters The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AsyncJobQuery The current query, for fluid interface
     */
    public function filterByParameters($parameters = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($parameters)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $parameters)) {
                $parameters = str_replace('*', '%', $parameters);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AsyncJobPeer::PARAMETERS, $parameters, $comparison);
    }

    /**
     * Filter the query on the start_date column
     *
     * Example usage:
     * <code>
     * $query->filterByStartDate('2011-03-14'); // WHERE start_date = '2011-03-14'
     * $query->filterByStartDate('now'); // WHERE start_date = '2011-03-14'
     * $query->filterByStartDate(array('max' => 'yesterday')); // WHERE start_date < '2011-03-13'
     * </code>
     *
     * @param     mixed $startDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AsyncJobQuery The current query, for fluid interface
     */
    public function filterByStartDate($startDate = null, $comparison = null)
    {
        if (is_array($startDate)) {
            $useMinMax = false;
            if (isset($startDate['min'])) {
                $this->addUsingAlias(AsyncJobPeer::START_DATE, $startDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($startDate['max'])) {
                $this->addUsingAlias(AsyncJobPeer::START_DATE, $startDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AsyncJobPeer::START_DATE, $startDate, $comparison);
    }

    /**
     * Filter the query on the completion_date column
     *
     * Example usage:
     * <code>
     * $query->filterByCompletionDate('2011-03-14'); // WHERE completion_date = '2011-03-14'
     * $query->filterByCompletionDate('now'); // WHERE completion_date = '2011-03-14'
     * $query->filterByCompletionDate(array('max' => 'yesterday')); // WHERE completion_date < '2011-03-13'
     * </code>
     *
     * @param     mixed $completionDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AsyncJobQuery The current query, for fluid interface
     */
    public function filterByCompletionDate($completionDate = null, $comparison = null)
    {
        if (is_array($completionDate)) {
            $useMinMax = false;
            if (isset($completionDate['min'])) {
                $this->addUsingAlias(AsyncJobPeer::COMPLETION_DATE, $completionDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($completionDate['max'])) {
                $this->addUsingAlias(AsyncJobPeer::COMPLETION_DATE, $completionDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AsyncJobPeer::COMPLETION_DATE, $completionDate, $comparison);
    }

    /**
     * Filter the query on the completion_percentage column
     *
     * Example usage:
     * <code>
     * $query->filterByCompletionPercentage(1234); // WHERE completion_percentage = 1234
     * $query->filterByCompletionPercentage(array(12, 34)); // WHERE completion_percentage IN (12, 34)
     * $query->filterByCompletionPercentage(array('min' => 12)); // WHERE completion_percentage >= 12
     * $query->filterByCompletionPercentage(array('max' => 12)); // WHERE completion_percentage <= 12
     * </code>
     *
     * @param     mixed $completionPercentage The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AsyncJobQuery The current query, for fluid interface
     */
    public function filterByCompletionPercentage($completionPercentage = null, $comparison = null)
    {
        if (is_array($completionPercentage)) {
            $useMinMax = false;
            if (isset($completionPercentage['min'])) {
                $this->addUsingAlias(AsyncJobPeer::COMPLETION_PERCENTAGE, $completionPercentage['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($completionPercentage['max'])) {
                $this->addUsingAlias(AsyncJobPeer::COMPLETION_PERCENTAGE, $completionPercentage['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AsyncJobPeer::COMPLETION_PERCENTAGE, $completionPercentage, $comparison);
    }

    /**
     * Filter the query on the outcome column
     *
     * Example usage:
     * <code>
     * $query->filterByOutcome('fooValue');   // WHERE outcome = 'fooValue'
     * $query->filterByOutcome('%fooValue%'); // WHERE outcome LIKE '%fooValue%'
     * </code>
     *
     * @param     string $outcome The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AsyncJobQuery The current query, for fluid interface
     */
    public function filterByOutcome($outcome = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($outcome)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $outcome)) {
                $outcome = str_replace('*', '%', $outcome);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AsyncJobPeer::OUTCOME, $outcome, $comparison);
    }

    /**
     * Filter the query on the job_output column
     *
     * Example usage:
     * <code>
     * $query->filterByJobOutput('fooValue');   // WHERE job_output = 'fooValue'
     * $query->filterByJobOutput('%fooValue%'); // WHERE job_output LIKE '%fooValue%'
     * </code>
     *
     * @param     string $jobOutput The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AsyncJobQuery The current query, for fluid interface
     */
    public function filterByJobOutput($jobOutput = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($jobOutput)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $jobOutput)) {
                $jobOutput = str_replace('*', '%', $jobOutput);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AsyncJobPeer::JOB_OUTPUT, $jobOutput, $comparison);
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
     * @return AsyncJobQuery The current query, for fluid interface
     */
    public function filterByCreationDate($creationDate = null, $comparison = null)
    {
        if (is_array($creationDate)) {
            $useMinMax = false;
            if (isset($creationDate['min'])) {
                $this->addUsingAlias(AsyncJobPeer::CREATION_DATE, $creationDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($creationDate['max'])) {
                $this->addUsingAlias(AsyncJobPeer::CREATION_DATE, $creationDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AsyncJobPeer::CREATION_DATE, $creationDate, $comparison);
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
     * @return AsyncJobQuery The current query, for fluid interface
     */
    public function filterByUpdateDate($updateDate = null, $comparison = null)
    {
        if (is_array($updateDate)) {
            $useMinMax = false;
            if (isset($updateDate['min'])) {
                $this->addUsingAlias(AsyncJobPeer::UPDATE_DATE, $updateDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updateDate['max'])) {
                $this->addUsingAlias(AsyncJobPeer::UPDATE_DATE, $updateDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AsyncJobPeer::UPDATE_DATE, $updateDate, $comparison);
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
     * @return AsyncJobQuery The current query, for fluid interface
     */
    public function filterByCreationUserId($creationUserId = null, $comparison = null)
    {
        if (is_array($creationUserId)) {
            $useMinMax = false;
            if (isset($creationUserId['min'])) {
                $this->addUsingAlias(AsyncJobPeer::CREATION_USER_ID, $creationUserId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($creationUserId['max'])) {
                $this->addUsingAlias(AsyncJobPeer::CREATION_USER_ID, $creationUserId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AsyncJobPeer::CREATION_USER_ID, $creationUserId, $comparison);
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
     * @return AsyncJobQuery The current query, for fluid interface
     */
    public function filterByUpdateUserId($updateUserId = null, $comparison = null)
    {
        if (is_array($updateUserId)) {
            $useMinMax = false;
            if (isset($updateUserId['min'])) {
                $this->addUsingAlias(AsyncJobPeer::UPDATE_USER_ID, $updateUserId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updateUserId['max'])) {
                $this->addUsingAlias(AsyncJobPeer::UPDATE_USER_ID, $updateUserId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AsyncJobPeer::UPDATE_USER_ID, $updateUserId, $comparison);
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
     * @return AsyncJobQuery The current query, for fluid interface
     */
    public function filterByRecordVersion($recordVersion = null, $comparison = null)
    {
        if (is_array($recordVersion)) {
            $useMinMax = false;
            if (isset($recordVersion['min'])) {
                $this->addUsingAlias(AsyncJobPeer::RECORD_VERSION, $recordVersion['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($recordVersion['max'])) {
                $this->addUsingAlias(AsyncJobPeer::RECORD_VERSION, $recordVersion['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AsyncJobPeer::RECORD_VERSION, $recordVersion, $comparison);
    }

    /**
     * Filter the query by a related Account object
     *
     * @param   Account|PropelObjectCollection $account The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AsyncJobQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAccountRelatedByIssuerUserId($account, $comparison = null)
    {
        if ($account instanceof Account) {
            return $this
                ->addUsingAlias(AsyncJobPeer::ISSUER_USER_ID, $account->getAccountId(), $comparison);
        } elseif ($account instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(AsyncJobPeer::ISSUER_USER_ID, $account->toKeyValue('PrimaryKey', 'AccountId'), $comparison);
        } else {
            throw new PropelException('filterByAccountRelatedByIssuerUserId() only accepts arguments of type Account or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AccountRelatedByIssuerUserId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return AsyncJobQuery The current query, for fluid interface
     */
    public function joinAccountRelatedByIssuerUserId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AccountRelatedByIssuerUserId');

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
            $this->addJoinObject($join, 'AccountRelatedByIssuerUserId');
        }

        return $this;
    }

    /**
     * Use the AccountRelatedByIssuerUserId relation Account object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountQuery A secondary query class using the current class as primary query
     */
    public function useAccountRelatedByIssuerUserIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinAccountRelatedByIssuerUserId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AccountRelatedByIssuerUserId', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountQuery');
    }

    /**
     * Filter the query by a related Account object
     *
     * @param   Account|PropelObjectCollection $account The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AsyncJobQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAccountRelatedByCreationUserId($account, $comparison = null)
    {
        if ($account instanceof Account) {
            return $this
                ->addUsingAlias(AsyncJobPeer::CREATION_USER_ID, $account->getAccountId(), $comparison);
        } elseif ($account instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(AsyncJobPeer::CREATION_USER_ID, $account->toKeyValue('PrimaryKey', 'AccountId'), $comparison);
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
     * @return AsyncJobQuery The current query, for fluid interface
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
     * @return                 AsyncJobQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAccountRelatedByUpdateUserId($account, $comparison = null)
    {
        if ($account instanceof Account) {
            return $this
                ->addUsingAlias(AsyncJobPeer::UPDATE_USER_ID, $account->getAccountId(), $comparison);
        } elseif ($account instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(AsyncJobPeer::UPDATE_USER_ID, $account->toKeyValue('PrimaryKey', 'AccountId'), $comparison);
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
     * @return AsyncJobQuery The current query, for fluid interface
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
     * @param   AsyncJob $asyncJob Object to remove from the list of results
     *
     * @return AsyncJobQuery The current query, for fluid interface
     */
    public function prune($asyncJob = null)
    {
        if ($asyncJob) {
            $this->addUsingAlias(AsyncJobPeer::ASYNC_JOB_ID, $asyncJob->getAsyncJobId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    // auditable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     AsyncJobQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(AsyncJobPeer::UPDATE_DATE, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     AsyncJobQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(AsyncJobPeer::UPDATE_DATE);
    }

    /**
     * Order by update date asc
     *
     * @return     AsyncJobQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(AsyncJobPeer::UPDATE_DATE);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     AsyncJobQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(AsyncJobPeer::CREATION_DATE, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date desc
     *
     * @return     AsyncJobQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(AsyncJobPeer::CREATION_DATE);
    }

    /**
     * Order by create date asc
     *
     * @return     AsyncJobQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(AsyncJobPeer::CREATION_DATE);
    }
}

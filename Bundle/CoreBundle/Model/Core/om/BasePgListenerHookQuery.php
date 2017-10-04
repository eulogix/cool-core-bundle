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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\PgListenerHook;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\PgListenerHookPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\PgListenerHookQuery;

/**
 * @method PgListenerHookQuery orderByPgListenerHookId($order = Criteria::ASC) Order by the pg_listener_hook_id column
 * @method PgListenerHookQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method PgListenerHookQuery orderByChannelsRegex($order = Criteria::ASC) Order by the channels_regex column
 * @method PgListenerHookQuery orderByDescription($order = Criteria::ASC) Order by the description column
 * @method PgListenerHookQuery orderByExecSqlStatements($order = Criteria::ASC) Order by the exec_sql_statements column
 * @method PgListenerHookQuery orderByExecSfCommand($order = Criteria::ASC) Order by the exec_sf_command column
 * @method PgListenerHookQuery orderByExecShellCommand($order = Criteria::ASC) Order by the exec_shell_command column
 * @method PgListenerHookQuery orderByExecPhpCode($order = Criteria::ASC) Order by the exec_php_code column
 *
 * @method PgListenerHookQuery groupByPgListenerHookId() Group by the pg_listener_hook_id column
 * @method PgListenerHookQuery groupByName() Group by the name column
 * @method PgListenerHookQuery groupByChannelsRegex() Group by the channels_regex column
 * @method PgListenerHookQuery groupByDescription() Group by the description column
 * @method PgListenerHookQuery groupByExecSqlStatements() Group by the exec_sql_statements column
 * @method PgListenerHookQuery groupByExecSfCommand() Group by the exec_sf_command column
 * @method PgListenerHookQuery groupByExecShellCommand() Group by the exec_shell_command column
 * @method PgListenerHookQuery groupByExecPhpCode() Group by the exec_php_code column
 *
 * @method PgListenerHookQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method PgListenerHookQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method PgListenerHookQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method PgListenerHook findOne(PropelPDO $con = null) Return the first PgListenerHook matching the query
 * @method PgListenerHook findOneOrCreate(PropelPDO $con = null) Return the first PgListenerHook matching the query, or a new PgListenerHook object populated from the query conditions when no match is found
 *
 * @method PgListenerHook findOneByName(string $name) Return the first PgListenerHook filtered by the name column
 * @method PgListenerHook findOneByChannelsRegex(string $channels_regex) Return the first PgListenerHook filtered by the channels_regex column
 * @method PgListenerHook findOneByDescription(string $description) Return the first PgListenerHook filtered by the description column
 * @method PgListenerHook findOneByExecSqlStatements(string $exec_sql_statements) Return the first PgListenerHook filtered by the exec_sql_statements column
 * @method PgListenerHook findOneByExecSfCommand(string $exec_sf_command) Return the first PgListenerHook filtered by the exec_sf_command column
 * @method PgListenerHook findOneByExecShellCommand(string $exec_shell_command) Return the first PgListenerHook filtered by the exec_shell_command column
 * @method PgListenerHook findOneByExecPhpCode(string $exec_php_code) Return the first PgListenerHook filtered by the exec_php_code column
 *
 * @method array findByPgListenerHookId(int $pg_listener_hook_id) Return PgListenerHook objects filtered by the pg_listener_hook_id column
 * @method array findByName(string $name) Return PgListenerHook objects filtered by the name column
 * @method array findByChannelsRegex(string $channels_regex) Return PgListenerHook objects filtered by the channels_regex column
 * @method array findByDescription(string $description) Return PgListenerHook objects filtered by the description column
 * @method array findByExecSqlStatements(string $exec_sql_statements) Return PgListenerHook objects filtered by the exec_sql_statements column
 * @method array findByExecSfCommand(string $exec_sf_command) Return PgListenerHook objects filtered by the exec_sf_command column
 * @method array findByExecShellCommand(string $exec_shell_command) Return PgListenerHook objects filtered by the exec_shell_command column
 * @method array findByExecPhpCode(string $exec_php_code) Return PgListenerHook objects filtered by the exec_php_code column
 */
abstract class BasePgListenerHookQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BasePgListenerHookQuery object.
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
            $modelName = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\PgListenerHook';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new PgListenerHookQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   PgListenerHookQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return PgListenerHookQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof PgListenerHookQuery) {
            return $criteria;
        }
        $query = new PgListenerHookQuery(null, null, $modelAlias);

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
     * @return   PgListenerHook|PgListenerHook[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = PgListenerHookPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(PgListenerHookPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 PgListenerHook A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByPgListenerHookId($key, $con = null)
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
     * @return                 PgListenerHook A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT pg_listener_hook_id, name, channels_regex, description, exec_sql_statements, exec_sf_command, exec_shell_command, exec_php_code FROM core.pg_listener_hook WHERE pg_listener_hook_id = :p0';
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
            $obj = new PgListenerHook();
            $obj->hydrate($row);
            PgListenerHookPeer::addInstanceToPool($obj, (string) $key);
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
     * @return PgListenerHook|PgListenerHook[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|PgListenerHook[]|mixed the list of results, formatted by the current formatter
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
     * @return PgListenerHook[]
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
     * @return PgListenerHookQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(PgListenerHookPeer::PG_LISTENER_HOOK_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return PgListenerHookQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(PgListenerHookPeer::PG_LISTENER_HOOK_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the pg_listener_hook_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPgListenerHookId(1234); // WHERE pg_listener_hook_id = 1234
     * $query->filterByPgListenerHookId(array(12, 34)); // WHERE pg_listener_hook_id IN (12, 34)
     * $query->filterByPgListenerHookId(array('min' => 12)); // WHERE pg_listener_hook_id >= 12
     * $query->filterByPgListenerHookId(array('max' => 12)); // WHERE pg_listener_hook_id <= 12
     * </code>
     *
     * @param     mixed $pgListenerHookId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PgListenerHookQuery The current query, for fluid interface
     */
    public function filterByPgListenerHookId($pgListenerHookId = null, $comparison = null)
    {
        if (is_array($pgListenerHookId)) {
            $useMinMax = false;
            if (isset($pgListenerHookId['min'])) {
                $this->addUsingAlias(PgListenerHookPeer::PG_LISTENER_HOOK_ID, $pgListenerHookId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($pgListenerHookId['max'])) {
                $this->addUsingAlias(PgListenerHookPeer::PG_LISTENER_HOOK_ID, $pgListenerHookId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PgListenerHookPeer::PG_LISTENER_HOOK_ID, $pgListenerHookId, $comparison);
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
     * @return PgListenerHookQuery The current query, for fluid interface
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

        return $this->addUsingAlias(PgListenerHookPeer::NAME, $name, $comparison);
    }

    /**
     * Filter the query on the channels_regex column
     *
     * Example usage:
     * <code>
     * $query->filterByChannelsRegex('fooValue');   // WHERE channels_regex = 'fooValue'
     * $query->filterByChannelsRegex('%fooValue%'); // WHERE channels_regex LIKE '%fooValue%'
     * </code>
     *
     * @param     string $channelsRegex The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PgListenerHookQuery The current query, for fluid interface
     */
    public function filterByChannelsRegex($channelsRegex = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($channelsRegex)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $channelsRegex)) {
                $channelsRegex = str_replace('*', '%', $channelsRegex);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PgListenerHookPeer::CHANNELS_REGEX, $channelsRegex, $comparison);
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
     * @return PgListenerHookQuery The current query, for fluid interface
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

        return $this->addUsingAlias(PgListenerHookPeer::DESCRIPTION, $description, $comparison);
    }

    /**
     * Filter the query on the exec_sql_statements column
     *
     * Example usage:
     * <code>
     * $query->filterByExecSqlStatements('fooValue');   // WHERE exec_sql_statements = 'fooValue'
     * $query->filterByExecSqlStatements('%fooValue%'); // WHERE exec_sql_statements LIKE '%fooValue%'
     * </code>
     *
     * @param     string $execSqlStatements The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PgListenerHookQuery The current query, for fluid interface
     */
    public function filterByExecSqlStatements($execSqlStatements = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($execSqlStatements)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $execSqlStatements)) {
                $execSqlStatements = str_replace('*', '%', $execSqlStatements);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PgListenerHookPeer::EXEC_SQL_STATEMENTS, $execSqlStatements, $comparison);
    }

    /**
     * Filter the query on the exec_sf_command column
     *
     * Example usage:
     * <code>
     * $query->filterByExecSfCommand('fooValue');   // WHERE exec_sf_command = 'fooValue'
     * $query->filterByExecSfCommand('%fooValue%'); // WHERE exec_sf_command LIKE '%fooValue%'
     * </code>
     *
     * @param     string $execSfCommand The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PgListenerHookQuery The current query, for fluid interface
     */
    public function filterByExecSfCommand($execSfCommand = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($execSfCommand)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $execSfCommand)) {
                $execSfCommand = str_replace('*', '%', $execSfCommand);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PgListenerHookPeer::EXEC_SF_COMMAND, $execSfCommand, $comparison);
    }

    /**
     * Filter the query on the exec_shell_command column
     *
     * Example usage:
     * <code>
     * $query->filterByExecShellCommand('fooValue');   // WHERE exec_shell_command = 'fooValue'
     * $query->filterByExecShellCommand('%fooValue%'); // WHERE exec_shell_command LIKE '%fooValue%'
     * </code>
     *
     * @param     string $execShellCommand The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PgListenerHookQuery The current query, for fluid interface
     */
    public function filterByExecShellCommand($execShellCommand = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($execShellCommand)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $execShellCommand)) {
                $execShellCommand = str_replace('*', '%', $execShellCommand);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PgListenerHookPeer::EXEC_SHELL_COMMAND, $execShellCommand, $comparison);
    }

    /**
     * Filter the query on the exec_php_code column
     *
     * Example usage:
     * <code>
     * $query->filterByExecPhpCode('fooValue');   // WHERE exec_php_code = 'fooValue'
     * $query->filterByExecPhpCode('%fooValue%'); // WHERE exec_php_code LIKE '%fooValue%'
     * </code>
     *
     * @param     string $execPhpCode The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PgListenerHookQuery The current query, for fluid interface
     */
    public function filterByExecPhpCode($execPhpCode = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($execPhpCode)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $execPhpCode)) {
                $execPhpCode = str_replace('*', '%', $execPhpCode);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PgListenerHookPeer::EXEC_PHP_CODE, $execPhpCode, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   PgListenerHook $pgListenerHook Object to remove from the list of results
     *
     * @return PgListenerHookQuery The current query, for fluid interface
     */
    public function prune($pgListenerHook = null)
    {
        if ($pgListenerHook) {
            $this->addUsingAlias(PgListenerHookPeer::PG_LISTENER_HOOK_ID, $pgListenerHook->getPgListenerHookId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

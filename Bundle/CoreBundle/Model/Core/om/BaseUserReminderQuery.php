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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserReminder;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserReminderPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserReminderQuery;

/**
 * @method UserReminderQuery orderByUserReminderId($order = Criteria::ASC) Order by the user_reminder_id column
 * @method UserReminderQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method UserReminderQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method UserReminderQuery orderByCategory($order = Criteria::ASC) Order by the category column
 * @method UserReminderQuery orderBySortOrder($order = Criteria::ASC) Order by the sort_order column
 * @method UserReminderQuery orderByLister($order = Criteria::ASC) Order by the lister column
 * @method UserReminderQuery orderByListerTranslationDomain($order = Criteria::ASC) Order by the lister_translation_domain column
 * @method UserReminderQuery orderByParentTables($order = Criteria::ASC) Order by the parent_tables column
 * @method UserReminderQuery orderByContextSchema($order = Criteria::ASC) Order by the context_schema column
 * @method UserReminderQuery orderBySqlQuery($order = Criteria::ASC) Order by the sql_query column
 * @method UserReminderQuery orderByCountSqlQuery($order = Criteria::ASC) Order by the count_sql_query column
 *
 * @method UserReminderQuery groupByUserReminderId() Group by the user_reminder_id column
 * @method UserReminderQuery groupByName() Group by the name column
 * @method UserReminderQuery groupByType() Group by the type column
 * @method UserReminderQuery groupByCategory() Group by the category column
 * @method UserReminderQuery groupBySortOrder() Group by the sort_order column
 * @method UserReminderQuery groupByLister() Group by the lister column
 * @method UserReminderQuery groupByListerTranslationDomain() Group by the lister_translation_domain column
 * @method UserReminderQuery groupByParentTables() Group by the parent_tables column
 * @method UserReminderQuery groupByContextSchema() Group by the context_schema column
 * @method UserReminderQuery groupBySqlQuery() Group by the sql_query column
 * @method UserReminderQuery groupByCountSqlQuery() Group by the count_sql_query column
 *
 * @method UserReminderQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method UserReminderQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method UserReminderQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method UserReminder findOne(PropelPDO $con = null) Return the first UserReminder matching the query
 * @method UserReminder findOneOrCreate(PropelPDO $con = null) Return the first UserReminder matching the query, or a new UserReminder object populated from the query conditions when no match is found
 *
 * @method UserReminder findOneByName(string $name) Return the first UserReminder filtered by the name column
 * @method UserReminder findOneByType(string $type) Return the first UserReminder filtered by the type column
 * @method UserReminder findOneByCategory(string $category) Return the first UserReminder filtered by the category column
 * @method UserReminder findOneBySortOrder(int $sort_order) Return the first UserReminder filtered by the sort_order column
 * @method UserReminder findOneByLister(string $lister) Return the first UserReminder filtered by the lister column
 * @method UserReminder findOneByListerTranslationDomain(string $lister_translation_domain) Return the first UserReminder filtered by the lister_translation_domain column
 * @method UserReminder findOneByParentTables(string $parent_tables) Return the first UserReminder filtered by the parent_tables column
 * @method UserReminder findOneByContextSchema(string $context_schema) Return the first UserReminder filtered by the context_schema column
 * @method UserReminder findOneBySqlQuery(string $sql_query) Return the first UserReminder filtered by the sql_query column
 * @method UserReminder findOneByCountSqlQuery(string $count_sql_query) Return the first UserReminder filtered by the count_sql_query column
 *
 * @method array findByUserReminderId(int $user_reminder_id) Return UserReminder objects filtered by the user_reminder_id column
 * @method array findByName(string $name) Return UserReminder objects filtered by the name column
 * @method array findByType(string $type) Return UserReminder objects filtered by the type column
 * @method array findByCategory(string $category) Return UserReminder objects filtered by the category column
 * @method array findBySortOrder(int $sort_order) Return UserReminder objects filtered by the sort_order column
 * @method array findByLister(string $lister) Return UserReminder objects filtered by the lister column
 * @method array findByListerTranslationDomain(string $lister_translation_domain) Return UserReminder objects filtered by the lister_translation_domain column
 * @method array findByParentTables(string $parent_tables) Return UserReminder objects filtered by the parent_tables column
 * @method array findByContextSchema(string $context_schema) Return UserReminder objects filtered by the context_schema column
 * @method array findBySqlQuery(string $sql_query) Return UserReminder objects filtered by the sql_query column
 * @method array findByCountSqlQuery(string $count_sql_query) Return UserReminder objects filtered by the count_sql_query column
 */
abstract class BaseUserReminderQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseUserReminderQuery object.
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
            $modelName = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\UserReminder';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new UserReminderQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   UserReminderQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return UserReminderQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof UserReminderQuery) {
            return $criteria;
        }
        $query = new UserReminderQuery(null, null, $modelAlias);

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
     * @return   UserReminder|UserReminder[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = UserReminderPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(UserReminderPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 UserReminder A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByUserReminderId($key, $con = null)
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
     * @return                 UserReminder A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT user_reminder_id, name, type, category, sort_order, lister, lister_translation_domain, parent_tables, context_schema, sql_query, count_sql_query FROM core.user_reminder WHERE user_reminder_id = :p0';
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
            $obj = new UserReminder();
            $obj->hydrate($row);
            UserReminderPeer::addInstanceToPool($obj, (string) $key);
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
     * @return UserReminder|UserReminder[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|UserReminder[]|mixed the list of results, formatted by the current formatter
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
     * @return UserReminder[]
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
     * @return UserReminderQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(UserReminderPeer::USER_REMINDER_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return UserReminderQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(UserReminderPeer::USER_REMINDER_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the user_reminder_id column
     *
     * Example usage:
     * <code>
     * $query->filterByUserReminderId(1234); // WHERE user_reminder_id = 1234
     * $query->filterByUserReminderId(array(12, 34)); // WHERE user_reminder_id IN (12, 34)
     * $query->filterByUserReminderId(array('min' => 12)); // WHERE user_reminder_id >= 12
     * $query->filterByUserReminderId(array('max' => 12)); // WHERE user_reminder_id <= 12
     * </code>
     *
     * @param     mixed $userReminderId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserReminderQuery The current query, for fluid interface
     */
    public function filterByUserReminderId($userReminderId = null, $comparison = null)
    {
        if (is_array($userReminderId)) {
            $useMinMax = false;
            if (isset($userReminderId['min'])) {
                $this->addUsingAlias(UserReminderPeer::USER_REMINDER_ID, $userReminderId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($userReminderId['max'])) {
                $this->addUsingAlias(UserReminderPeer::USER_REMINDER_ID, $userReminderId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserReminderPeer::USER_REMINDER_ID, $userReminderId, $comparison);
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
     * @return UserReminderQuery The current query, for fluid interface
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

        return $this->addUsingAlias(UserReminderPeer::NAME, $name, $comparison);
    }

    /**
     * Filter the query on the type column
     *
     * Example usage:
     * <code>
     * $query->filterByType('fooValue');   // WHERE type = 'fooValue'
     * $query->filterByType('%fooValue%'); // WHERE type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $type The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserReminderQuery The current query, for fluid interface
     */
    public function filterByType($type = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($type)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $type)) {
                $type = str_replace('*', '%', $type);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserReminderPeer::TYPE, $type, $comparison);
    }

    /**
     * Filter the query on the category column
     *
     * Example usage:
     * <code>
     * $query->filterByCategory('fooValue');   // WHERE category = 'fooValue'
     * $query->filterByCategory('%fooValue%'); // WHERE category LIKE '%fooValue%'
     * </code>
     *
     * @param     string $category The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserReminderQuery The current query, for fluid interface
     */
    public function filterByCategory($category = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($category)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $category)) {
                $category = str_replace('*', '%', $category);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserReminderPeer::CATEGORY, $category, $comparison);
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
     * @return UserReminderQuery The current query, for fluid interface
     */
    public function filterBySortOrder($sortOrder = null, $comparison = null)
    {
        if (is_array($sortOrder)) {
            $useMinMax = false;
            if (isset($sortOrder['min'])) {
                $this->addUsingAlias(UserReminderPeer::SORT_ORDER, $sortOrder['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sortOrder['max'])) {
                $this->addUsingAlias(UserReminderPeer::SORT_ORDER, $sortOrder['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserReminderPeer::SORT_ORDER, $sortOrder, $comparison);
    }

    /**
     * Filter the query on the lister column
     *
     * Example usage:
     * <code>
     * $query->filterByLister('fooValue');   // WHERE lister = 'fooValue'
     * $query->filterByLister('%fooValue%'); // WHERE lister LIKE '%fooValue%'
     * </code>
     *
     * @param     string $lister The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserReminderQuery The current query, for fluid interface
     */
    public function filterByLister($lister = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($lister)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $lister)) {
                $lister = str_replace('*', '%', $lister);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserReminderPeer::LISTER, $lister, $comparison);
    }

    /**
     * Filter the query on the lister_translation_domain column
     *
     * Example usage:
     * <code>
     * $query->filterByListerTranslationDomain('fooValue');   // WHERE lister_translation_domain = 'fooValue'
     * $query->filterByListerTranslationDomain('%fooValue%'); // WHERE lister_translation_domain LIKE '%fooValue%'
     * </code>
     *
     * @param     string $listerTranslationDomain The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserReminderQuery The current query, for fluid interface
     */
    public function filterByListerTranslationDomain($listerTranslationDomain = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($listerTranslationDomain)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $listerTranslationDomain)) {
                $listerTranslationDomain = str_replace('*', '%', $listerTranslationDomain);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserReminderPeer::LISTER_TRANSLATION_DOMAIN, $listerTranslationDomain, $comparison);
    }

    /**
     * Filter the query on the parent_tables column
     *
     * Example usage:
     * <code>
     * $query->filterByParentTables('fooValue');   // WHERE parent_tables = 'fooValue'
     * $query->filterByParentTables('%fooValue%'); // WHERE parent_tables LIKE '%fooValue%'
     * </code>
     *
     * @param     string $parentTables The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserReminderQuery The current query, for fluid interface
     */
    public function filterByParentTables($parentTables = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($parentTables)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $parentTables)) {
                $parentTables = str_replace('*', '%', $parentTables);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserReminderPeer::PARENT_TABLES, $parentTables, $comparison);
    }

    /**
     * Filter the query on the context_schema column
     *
     * Example usage:
     * <code>
     * $query->filterByContextSchema('fooValue');   // WHERE context_schema = 'fooValue'
     * $query->filterByContextSchema('%fooValue%'); // WHERE context_schema LIKE '%fooValue%'
     * </code>
     *
     * @param     string $contextSchema The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserReminderQuery The current query, for fluid interface
     */
    public function filterByContextSchema($contextSchema = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($contextSchema)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $contextSchema)) {
                $contextSchema = str_replace('*', '%', $contextSchema);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserReminderPeer::CONTEXT_SCHEMA, $contextSchema, $comparison);
    }

    /**
     * Filter the query on the sql_query column
     *
     * Example usage:
     * <code>
     * $query->filterBySqlQuery('fooValue');   // WHERE sql_query = 'fooValue'
     * $query->filterBySqlQuery('%fooValue%'); // WHERE sql_query LIKE '%fooValue%'
     * </code>
     *
     * @param     string $sqlQuery The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserReminderQuery The current query, for fluid interface
     */
    public function filterBySqlQuery($sqlQuery = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($sqlQuery)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $sqlQuery)) {
                $sqlQuery = str_replace('*', '%', $sqlQuery);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserReminderPeer::SQL_QUERY, $sqlQuery, $comparison);
    }

    /**
     * Filter the query on the count_sql_query column
     *
     * Example usage:
     * <code>
     * $query->filterByCountSqlQuery('fooValue');   // WHERE count_sql_query = 'fooValue'
     * $query->filterByCountSqlQuery('%fooValue%'); // WHERE count_sql_query LIKE '%fooValue%'
     * </code>
     *
     * @param     string $countSqlQuery The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserReminderQuery The current query, for fluid interface
     */
    public function filterByCountSqlQuery($countSqlQuery = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($countSqlQuery)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $countSqlQuery)) {
                $countSqlQuery = str_replace('*', '%', $countSqlQuery);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserReminderPeer::COUNT_SQL_QUERY, $countSqlQuery, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   UserReminder $userReminder Object to remove from the list of results
     *
     * @return UserReminderQuery The current query, for fluid interface
     */
    public function prune($userReminder = null)
    {
        if ($userReminder) {
            $this->addUsingAlias(UserReminderPeer::USER_REMINDER_ID, $userReminder->getUserReminderId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

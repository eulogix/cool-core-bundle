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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\CodeSnippet;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\CodeSnippetPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\CodeSnippetQuery;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\CodeSnippetVariable;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\RuleCode;

/**
 * @method CodeSnippetQuery orderByCodeSnippetId($order = Criteria::ASC) Order by the code_snippet_id column
 * @method CodeSnippetQuery orderByCategory($order = Criteria::ASC) Order by the category column
 * @method CodeSnippetQuery orderByLanguage($order = Criteria::ASC) Order by the language column
 * @method CodeSnippetQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method CodeSnippetQuery orderByReturnType($order = Criteria::ASC) Order by the return_type column
 * @method CodeSnippetQuery orderByNspace($order = Criteria::ASC) Order by the nspace column
 * @method CodeSnippetQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method CodeSnippetQuery orderByDescription($order = Criteria::ASC) Order by the description column
 * @method CodeSnippetQuery orderByLongDescription($order = Criteria::ASC) Order by the long_description column
 * @method CodeSnippetQuery orderByLockUpdatesFlag($order = Criteria::ASC) Order by the lock_updates_flag column
 * @method CodeSnippetQuery orderBySnippet($order = Criteria::ASC) Order by the snippet column
 *
 * @method CodeSnippetQuery groupByCodeSnippetId() Group by the code_snippet_id column
 * @method CodeSnippetQuery groupByCategory() Group by the category column
 * @method CodeSnippetQuery groupByLanguage() Group by the language column
 * @method CodeSnippetQuery groupByType() Group by the type column
 * @method CodeSnippetQuery groupByReturnType() Group by the return_type column
 * @method CodeSnippetQuery groupByNspace() Group by the nspace column
 * @method CodeSnippetQuery groupByName() Group by the name column
 * @method CodeSnippetQuery groupByDescription() Group by the description column
 * @method CodeSnippetQuery groupByLongDescription() Group by the long_description column
 * @method CodeSnippetQuery groupByLockUpdatesFlag() Group by the lock_updates_flag column
 * @method CodeSnippetQuery groupBySnippet() Group by the snippet column
 *
 * @method CodeSnippetQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CodeSnippetQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CodeSnippetQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CodeSnippetQuery leftJoinRuleCode($relationAlias = null) Adds a LEFT JOIN clause to the query using the RuleCode relation
 * @method CodeSnippetQuery rightJoinRuleCode($relationAlias = null) Adds a RIGHT JOIN clause to the query using the RuleCode relation
 * @method CodeSnippetQuery innerJoinRuleCode($relationAlias = null) Adds a INNER JOIN clause to the query using the RuleCode relation
 *
 * @method CodeSnippetQuery leftJoinCodeSnippetVariable($relationAlias = null) Adds a LEFT JOIN clause to the query using the CodeSnippetVariable relation
 * @method CodeSnippetQuery rightJoinCodeSnippetVariable($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CodeSnippetVariable relation
 * @method CodeSnippetQuery innerJoinCodeSnippetVariable($relationAlias = null) Adds a INNER JOIN clause to the query using the CodeSnippetVariable relation
 *
 * @method CodeSnippet findOne(PropelPDO $con = null) Return the first CodeSnippet matching the query
 * @method CodeSnippet findOneOrCreate(PropelPDO $con = null) Return the first CodeSnippet matching the query, or a new CodeSnippet object populated from the query conditions when no match is found
 *
 * @method CodeSnippet findOneByCategory(string $category) Return the first CodeSnippet filtered by the category column
 * @method CodeSnippet findOneByLanguage(string $language) Return the first CodeSnippet filtered by the language column
 * @method CodeSnippet findOneByType(string $type) Return the first CodeSnippet filtered by the type column
 * @method CodeSnippet findOneByReturnType(string $return_type) Return the first CodeSnippet filtered by the return_type column
 * @method CodeSnippet findOneByNspace(string $nspace) Return the first CodeSnippet filtered by the nspace column
 * @method CodeSnippet findOneByName(string $name) Return the first CodeSnippet filtered by the name column
 * @method CodeSnippet findOneByDescription(string $description) Return the first CodeSnippet filtered by the description column
 * @method CodeSnippet findOneByLongDescription(string $long_description) Return the first CodeSnippet filtered by the long_description column
 * @method CodeSnippet findOneByLockUpdatesFlag(boolean $lock_updates_flag) Return the first CodeSnippet filtered by the lock_updates_flag column
 * @method CodeSnippet findOneBySnippet(string $snippet) Return the first CodeSnippet filtered by the snippet column
 *
 * @method array findByCodeSnippetId(int $code_snippet_id) Return CodeSnippet objects filtered by the code_snippet_id column
 * @method array findByCategory(string $category) Return CodeSnippet objects filtered by the category column
 * @method array findByLanguage(string $language) Return CodeSnippet objects filtered by the language column
 * @method array findByType(string $type) Return CodeSnippet objects filtered by the type column
 * @method array findByReturnType(string $return_type) Return CodeSnippet objects filtered by the return_type column
 * @method array findByNspace(string $nspace) Return CodeSnippet objects filtered by the nspace column
 * @method array findByName(string $name) Return CodeSnippet objects filtered by the name column
 * @method array findByDescription(string $description) Return CodeSnippet objects filtered by the description column
 * @method array findByLongDescription(string $long_description) Return CodeSnippet objects filtered by the long_description column
 * @method array findByLockUpdatesFlag(boolean $lock_updates_flag) Return CodeSnippet objects filtered by the lock_updates_flag column
 * @method array findBySnippet(string $snippet) Return CodeSnippet objects filtered by the snippet column
 */
abstract class BaseCodeSnippetQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCodeSnippetQuery object.
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
            $modelName = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\CodeSnippet';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CodeSnippetQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CodeSnippetQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CodeSnippetQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CodeSnippetQuery) {
            return $criteria;
        }
        $query = new CodeSnippetQuery(null, null, $modelAlias);

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
     * @return   CodeSnippet|CodeSnippet[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CodeSnippetPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CodeSnippetPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 CodeSnippet A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByCodeSnippetId($key, $con = null)
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
     * @return                 CodeSnippet A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT code_snippet_id, category, language, type, return_type, nspace, name, description, long_description, lock_updates_flag, snippet FROM core.code_snippet WHERE code_snippet_id = :p0';
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
            $obj = new CodeSnippet();
            $obj->hydrate($row);
            CodeSnippetPeer::addInstanceToPool($obj, (string) $key);
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
     * @return CodeSnippet|CodeSnippet[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|CodeSnippet[]|mixed the list of results, formatted by the current formatter
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
     * @return CodeSnippet[]
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
     * @return CodeSnippetQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CodeSnippetPeer::CODE_SNIPPET_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CodeSnippetQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CodeSnippetPeer::CODE_SNIPPET_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the code_snippet_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCodeSnippetId(1234); // WHERE code_snippet_id = 1234
     * $query->filterByCodeSnippetId(array(12, 34)); // WHERE code_snippet_id IN (12, 34)
     * $query->filterByCodeSnippetId(array('min' => 12)); // WHERE code_snippet_id >= 12
     * $query->filterByCodeSnippetId(array('max' => 12)); // WHERE code_snippet_id <= 12
     * </code>
     *
     * @param     mixed $codeSnippetId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CodeSnippetQuery The current query, for fluid interface
     */
    public function filterByCodeSnippetId($codeSnippetId = null, $comparison = null)
    {
        if (is_array($codeSnippetId)) {
            $useMinMax = false;
            if (isset($codeSnippetId['min'])) {
                $this->addUsingAlias(CodeSnippetPeer::CODE_SNIPPET_ID, $codeSnippetId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($codeSnippetId['max'])) {
                $this->addUsingAlias(CodeSnippetPeer::CODE_SNIPPET_ID, $codeSnippetId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CodeSnippetPeer::CODE_SNIPPET_ID, $codeSnippetId, $comparison);
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
     * @return CodeSnippetQuery The current query, for fluid interface
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

        return $this->addUsingAlias(CodeSnippetPeer::CATEGORY, $category, $comparison);
    }

    /**
     * Filter the query on the language column
     *
     * Example usage:
     * <code>
     * $query->filterByLanguage('fooValue');   // WHERE language = 'fooValue'
     * $query->filterByLanguage('%fooValue%'); // WHERE language LIKE '%fooValue%'
     * </code>
     *
     * @param     string $language The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CodeSnippetQuery The current query, for fluid interface
     */
    public function filterByLanguage($language = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($language)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $language)) {
                $language = str_replace('*', '%', $language);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CodeSnippetPeer::LANGUAGE, $language, $comparison);
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
     * @return CodeSnippetQuery The current query, for fluid interface
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

        return $this->addUsingAlias(CodeSnippetPeer::TYPE, $type, $comparison);
    }

    /**
     * Filter the query on the return_type column
     *
     * Example usage:
     * <code>
     * $query->filterByReturnType('fooValue');   // WHERE return_type = 'fooValue'
     * $query->filterByReturnType('%fooValue%'); // WHERE return_type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $returnType The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CodeSnippetQuery The current query, for fluid interface
     */
    public function filterByReturnType($returnType = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($returnType)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $returnType)) {
                $returnType = str_replace('*', '%', $returnType);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CodeSnippetPeer::RETURN_TYPE, $returnType, $comparison);
    }

    /**
     * Filter the query on the nspace column
     *
     * Example usage:
     * <code>
     * $query->filterByNspace('fooValue');   // WHERE nspace = 'fooValue'
     * $query->filterByNspace('%fooValue%'); // WHERE nspace LIKE '%fooValue%'
     * </code>
     *
     * @param     string $nspace The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CodeSnippetQuery The current query, for fluid interface
     */
    public function filterByNspace($nspace = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($nspace)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $nspace)) {
                $nspace = str_replace('*', '%', $nspace);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CodeSnippetPeer::NSPACE, $nspace, $comparison);
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
     * @return CodeSnippetQuery The current query, for fluid interface
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

        return $this->addUsingAlias(CodeSnippetPeer::NAME, $name, $comparison);
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
     * @return CodeSnippetQuery The current query, for fluid interface
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

        return $this->addUsingAlias(CodeSnippetPeer::DESCRIPTION, $description, $comparison);
    }

    /**
     * Filter the query on the long_description column
     *
     * Example usage:
     * <code>
     * $query->filterByLongDescription('fooValue');   // WHERE long_description = 'fooValue'
     * $query->filterByLongDescription('%fooValue%'); // WHERE long_description LIKE '%fooValue%'
     * </code>
     *
     * @param     string $longDescription The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CodeSnippetQuery The current query, for fluid interface
     */
    public function filterByLongDescription($longDescription = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($longDescription)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $longDescription)) {
                $longDescription = str_replace('*', '%', $longDescription);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CodeSnippetPeer::LONG_DESCRIPTION, $longDescription, $comparison);
    }

    /**
     * Filter the query on the lock_updates_flag column
     *
     * Example usage:
     * <code>
     * $query->filterByLockUpdatesFlag(true); // WHERE lock_updates_flag = true
     * $query->filterByLockUpdatesFlag('yes'); // WHERE lock_updates_flag = true
     * </code>
     *
     * @param     boolean|string $lockUpdatesFlag The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CodeSnippetQuery The current query, for fluid interface
     */
    public function filterByLockUpdatesFlag($lockUpdatesFlag = null, $comparison = null)
    {
        if (is_string($lockUpdatesFlag)) {
            $lockUpdatesFlag = in_array(strtolower($lockUpdatesFlag), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CodeSnippetPeer::LOCK_UPDATES_FLAG, $lockUpdatesFlag, $comparison);
    }

    /**
     * Filter the query on the snippet column
     *
     * Example usage:
     * <code>
     * $query->filterBySnippet('fooValue');   // WHERE snippet = 'fooValue'
     * $query->filterBySnippet('%fooValue%'); // WHERE snippet LIKE '%fooValue%'
     * </code>
     *
     * @param     string $snippet The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CodeSnippetQuery The current query, for fluid interface
     */
    public function filterBySnippet($snippet = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($snippet)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $snippet)) {
                $snippet = str_replace('*', '%', $snippet);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CodeSnippetPeer::SNIPPET, $snippet, $comparison);
    }

    /**
     * Filter the query by a related RuleCode object
     *
     * @param   RuleCode|PropelObjectCollection $ruleCode  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CodeSnippetQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByRuleCode($ruleCode, $comparison = null)
    {
        if ($ruleCode instanceof RuleCode) {
            return $this
                ->addUsingAlias(CodeSnippetPeer::CODE_SNIPPET_ID, $ruleCode->getCodeSnippetId(), $comparison);
        } elseif ($ruleCode instanceof PropelObjectCollection) {
            return $this
                ->useRuleCodeQuery()
                ->filterByPrimaryKeys($ruleCode->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByRuleCode() only accepts arguments of type RuleCode or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the RuleCode relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CodeSnippetQuery The current query, for fluid interface
     */
    public function joinRuleCode($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('RuleCode');

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
            $this->addJoinObject($join, 'RuleCode');
        }

        return $this;
    }

    /**
     * Use the RuleCode relation RuleCode object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\RuleCodeQuery A secondary query class using the current class as primary query
     */
    public function useRuleCodeQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinRuleCode($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'RuleCode', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\RuleCodeQuery');
    }

    /**
     * Filter the query by a related CodeSnippetVariable object
     *
     * @param   CodeSnippetVariable|PropelObjectCollection $codeSnippetVariable  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CodeSnippetQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCodeSnippetVariable($codeSnippetVariable, $comparison = null)
    {
        if ($codeSnippetVariable instanceof CodeSnippetVariable) {
            return $this
                ->addUsingAlias(CodeSnippetPeer::CODE_SNIPPET_ID, $codeSnippetVariable->getCodeSnippetId(), $comparison);
        } elseif ($codeSnippetVariable instanceof PropelObjectCollection) {
            return $this
                ->useCodeSnippetVariableQuery()
                ->filterByPrimaryKeys($codeSnippetVariable->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCodeSnippetVariable() only accepts arguments of type CodeSnippetVariable or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CodeSnippetVariable relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CodeSnippetQuery The current query, for fluid interface
     */
    public function joinCodeSnippetVariable($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CodeSnippetVariable');

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
            $this->addJoinObject($join, 'CodeSnippetVariable');
        }

        return $this;
    }

    /**
     * Use the CodeSnippetVariable relation CodeSnippetVariable object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\CodeSnippetVariableQuery A secondary query class using the current class as primary query
     */
    public function useCodeSnippetVariableQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCodeSnippetVariable($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CodeSnippetVariable', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\CodeSnippetVariableQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   CodeSnippet $codeSnippet Object to remove from the list of results
     *
     * @return CodeSnippetQuery The current query, for fluid interface
     */
    public function prune($codeSnippet = null)
    {
        if ($codeSnippet) {
            $this->addUsingAlias(CodeSnippetPeer::CODE_SNIPPET_ID, $codeSnippet->getCodeSnippetId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

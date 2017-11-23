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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\Rule;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\RuleCode;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\RuleCodePeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\RuleCodeQuery;

/**
 * @method RuleCodeQuery orderByRuleCodeId($order = Criteria::ASC) Order by the rule_code_id column
 * @method RuleCodeQuery orderByRuleId($order = Criteria::ASC) Order by the rule_id column
 * @method RuleCodeQuery orderByEnabledFlag($order = Criteria::ASC) Order by the enabled_flag column
 * @method RuleCodeQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method RuleCodeQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method RuleCodeQuery orderByCodeSnippetId($order = Criteria::ASC) Order by the code_snippet_id column
 * @method RuleCodeQuery orderByCodeSnippetVariables($order = Criteria::ASC) Order by the code_snippet_variables column
 * @method RuleCodeQuery orderByRawCode($order = Criteria::ASC) Order by the raw_code column
 *
 * @method RuleCodeQuery groupByRuleCodeId() Group by the rule_code_id column
 * @method RuleCodeQuery groupByRuleId() Group by the rule_id column
 * @method RuleCodeQuery groupByEnabledFlag() Group by the enabled_flag column
 * @method RuleCodeQuery groupByType() Group by the type column
 * @method RuleCodeQuery groupByName() Group by the name column
 * @method RuleCodeQuery groupByCodeSnippetId() Group by the code_snippet_id column
 * @method RuleCodeQuery groupByCodeSnippetVariables() Group by the code_snippet_variables column
 * @method RuleCodeQuery groupByRawCode() Group by the raw_code column
 *
 * @method RuleCodeQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method RuleCodeQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method RuleCodeQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method RuleCodeQuery leftJoinRule($relationAlias = null) Adds a LEFT JOIN clause to the query using the Rule relation
 * @method RuleCodeQuery rightJoinRule($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Rule relation
 * @method RuleCodeQuery innerJoinRule($relationAlias = null) Adds a INNER JOIN clause to the query using the Rule relation
 *
 * @method RuleCodeQuery leftJoinCodeSnippet($relationAlias = null) Adds a LEFT JOIN clause to the query using the CodeSnippet relation
 * @method RuleCodeQuery rightJoinCodeSnippet($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CodeSnippet relation
 * @method RuleCodeQuery innerJoinCodeSnippet($relationAlias = null) Adds a INNER JOIN clause to the query using the CodeSnippet relation
 *
 * @method RuleCode findOne(PropelPDO $con = null) Return the first RuleCode matching the query
 * @method RuleCode findOneOrCreate(PropelPDO $con = null) Return the first RuleCode matching the query, or a new RuleCode object populated from the query conditions when no match is found
 *
 * @method RuleCode findOneByRuleId(int $rule_id) Return the first RuleCode filtered by the rule_id column
 * @method RuleCode findOneByEnabledFlag(boolean $enabled_flag) Return the first RuleCode filtered by the enabled_flag column
 * @method RuleCode findOneByType(string $type) Return the first RuleCode filtered by the type column
 * @method RuleCode findOneByName(string $name) Return the first RuleCode filtered by the name column
 * @method RuleCode findOneByCodeSnippetId(int $code_snippet_id) Return the first RuleCode filtered by the code_snippet_id column
 * @method RuleCode findOneByCodeSnippetVariables(string $code_snippet_variables) Return the first RuleCode filtered by the code_snippet_variables column
 * @method RuleCode findOneByRawCode(string $raw_code) Return the first RuleCode filtered by the raw_code column
 *
 * @method array findByRuleCodeId(int $rule_code_id) Return RuleCode objects filtered by the rule_code_id column
 * @method array findByRuleId(int $rule_id) Return RuleCode objects filtered by the rule_id column
 * @method array findByEnabledFlag(boolean $enabled_flag) Return RuleCode objects filtered by the enabled_flag column
 * @method array findByType(string $type) Return RuleCode objects filtered by the type column
 * @method array findByName(string $name) Return RuleCode objects filtered by the name column
 * @method array findByCodeSnippetId(int $code_snippet_id) Return RuleCode objects filtered by the code_snippet_id column
 * @method array findByCodeSnippetVariables(string $code_snippet_variables) Return RuleCode objects filtered by the code_snippet_variables column
 * @method array findByRawCode(string $raw_code) Return RuleCode objects filtered by the raw_code column
 */
abstract class BaseRuleCodeQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseRuleCodeQuery object.
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
            $modelName = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\RuleCode';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new RuleCodeQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   RuleCodeQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return RuleCodeQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof RuleCodeQuery) {
            return $criteria;
        }
        $query = new RuleCodeQuery(null, null, $modelAlias);

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
     * @return   RuleCode|RuleCode[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = RuleCodePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(RuleCodePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 RuleCode A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByRuleCodeId($key, $con = null)
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
     * @return                 RuleCode A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT rule_code_id, rule_id, enabled_flag, type, name, code_snippet_id, code_snippet_variables, raw_code FROM core.rule_code WHERE rule_code_id = :p0';
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
            $obj = new RuleCode();
            $obj->hydrate($row);
            RuleCodePeer::addInstanceToPool($obj, (string) $key);
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
     * @return RuleCode|RuleCode[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|RuleCode[]|mixed the list of results, formatted by the current formatter
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
     * @return RuleCode[]
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
     * @return RuleCodeQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(RuleCodePeer::RULE_CODE_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return RuleCodeQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(RuleCodePeer::RULE_CODE_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the rule_code_id column
     *
     * Example usage:
     * <code>
     * $query->filterByRuleCodeId(1234); // WHERE rule_code_id = 1234
     * $query->filterByRuleCodeId(array(12, 34)); // WHERE rule_code_id IN (12, 34)
     * $query->filterByRuleCodeId(array('min' => 12)); // WHERE rule_code_id >= 12
     * $query->filterByRuleCodeId(array('max' => 12)); // WHERE rule_code_id <= 12
     * </code>
     *
     * @param     mixed $ruleCodeId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RuleCodeQuery The current query, for fluid interface
     */
    public function filterByRuleCodeId($ruleCodeId = null, $comparison = null)
    {
        if (is_array($ruleCodeId)) {
            $useMinMax = false;
            if (isset($ruleCodeId['min'])) {
                $this->addUsingAlias(RuleCodePeer::RULE_CODE_ID, $ruleCodeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($ruleCodeId['max'])) {
                $this->addUsingAlias(RuleCodePeer::RULE_CODE_ID, $ruleCodeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RuleCodePeer::RULE_CODE_ID, $ruleCodeId, $comparison);
    }

    /**
     * Filter the query on the rule_id column
     *
     * Example usage:
     * <code>
     * $query->filterByRuleId(1234); // WHERE rule_id = 1234
     * $query->filterByRuleId(array(12, 34)); // WHERE rule_id IN (12, 34)
     * $query->filterByRuleId(array('min' => 12)); // WHERE rule_id >= 12
     * $query->filterByRuleId(array('max' => 12)); // WHERE rule_id <= 12
     * </code>
     *
     * @see       filterByRule()
     *
     * @param     mixed $ruleId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RuleCodeQuery The current query, for fluid interface
     */
    public function filterByRuleId($ruleId = null, $comparison = null)
    {
        if (is_array($ruleId)) {
            $useMinMax = false;
            if (isset($ruleId['min'])) {
                $this->addUsingAlias(RuleCodePeer::RULE_ID, $ruleId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($ruleId['max'])) {
                $this->addUsingAlias(RuleCodePeer::RULE_ID, $ruleId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RuleCodePeer::RULE_ID, $ruleId, $comparison);
    }

    /**
     * Filter the query on the enabled_flag column
     *
     * Example usage:
     * <code>
     * $query->filterByEnabledFlag(true); // WHERE enabled_flag = true
     * $query->filterByEnabledFlag('yes'); // WHERE enabled_flag = true
     * </code>
     *
     * @param     boolean|string $enabledFlag The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RuleCodeQuery The current query, for fluid interface
     */
    public function filterByEnabledFlag($enabledFlag = null, $comparison = null)
    {
        if (is_string($enabledFlag)) {
            $enabledFlag = in_array(strtolower($enabledFlag), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(RuleCodePeer::ENABLED_FLAG, $enabledFlag, $comparison);
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
     * @return RuleCodeQuery The current query, for fluid interface
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

        return $this->addUsingAlias(RuleCodePeer::TYPE, $type, $comparison);
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
     * @return RuleCodeQuery The current query, for fluid interface
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

        return $this->addUsingAlias(RuleCodePeer::NAME, $name, $comparison);
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
     * @see       filterByCodeSnippet()
     *
     * @param     mixed $codeSnippetId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RuleCodeQuery The current query, for fluid interface
     */
    public function filterByCodeSnippetId($codeSnippetId = null, $comparison = null)
    {
        if (is_array($codeSnippetId)) {
            $useMinMax = false;
            if (isset($codeSnippetId['min'])) {
                $this->addUsingAlias(RuleCodePeer::CODE_SNIPPET_ID, $codeSnippetId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($codeSnippetId['max'])) {
                $this->addUsingAlias(RuleCodePeer::CODE_SNIPPET_ID, $codeSnippetId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RuleCodePeer::CODE_SNIPPET_ID, $codeSnippetId, $comparison);
    }

    /**
     * Filter the query on the code_snippet_variables column
     *
     * Example usage:
     * <code>
     * $query->filterByCodeSnippetVariables('fooValue');   // WHERE code_snippet_variables = 'fooValue'
     * $query->filterByCodeSnippetVariables('%fooValue%'); // WHERE code_snippet_variables LIKE '%fooValue%'
     * </code>
     *
     * @param     string $codeSnippetVariables The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RuleCodeQuery The current query, for fluid interface
     */
    public function filterByCodeSnippetVariables($codeSnippetVariables = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($codeSnippetVariables)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $codeSnippetVariables)) {
                $codeSnippetVariables = str_replace('*', '%', $codeSnippetVariables);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(RuleCodePeer::CODE_SNIPPET_VARIABLES, $codeSnippetVariables, $comparison);
    }

    /**
     * Filter the query on the raw_code column
     *
     * Example usage:
     * <code>
     * $query->filterByRawCode('fooValue');   // WHERE raw_code = 'fooValue'
     * $query->filterByRawCode('%fooValue%'); // WHERE raw_code LIKE '%fooValue%'
     * </code>
     *
     * @param     string $rawCode The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RuleCodeQuery The current query, for fluid interface
     */
    public function filterByRawCode($rawCode = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($rawCode)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $rawCode)) {
                $rawCode = str_replace('*', '%', $rawCode);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(RuleCodePeer::RAW_CODE, $rawCode, $comparison);
    }

    /**
     * Filter the query by a related Rule object
     *
     * @param   Rule|PropelObjectCollection $rule The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 RuleCodeQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByRule($rule, $comparison = null)
    {
        if ($rule instanceof Rule) {
            return $this
                ->addUsingAlias(RuleCodePeer::RULE_ID, $rule->getRuleId(), $comparison);
        } elseif ($rule instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(RuleCodePeer::RULE_ID, $rule->toKeyValue('PrimaryKey', 'RuleId'), $comparison);
        } else {
            throw new PropelException('filterByRule() only accepts arguments of type Rule or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Rule relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return RuleCodeQuery The current query, for fluid interface
     */
    public function joinRule($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Rule');

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
            $this->addJoinObject($join, 'Rule');
        }

        return $this;
    }

    /**
     * Use the Rule relation Rule object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\RuleQuery A secondary query class using the current class as primary query
     */
    public function useRuleQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinRule($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Rule', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\RuleQuery');
    }

    /**
     * Filter the query by a related CodeSnippet object
     *
     * @param   CodeSnippet|PropelObjectCollection $codeSnippet The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 RuleCodeQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCodeSnippet($codeSnippet, $comparison = null)
    {
        if ($codeSnippet instanceof CodeSnippet) {
            return $this
                ->addUsingAlias(RuleCodePeer::CODE_SNIPPET_ID, $codeSnippet->getCodeSnippetId(), $comparison);
        } elseif ($codeSnippet instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(RuleCodePeer::CODE_SNIPPET_ID, $codeSnippet->toKeyValue('PrimaryKey', 'CodeSnippetId'), $comparison);
        } else {
            throw new PropelException('filterByCodeSnippet() only accepts arguments of type CodeSnippet or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CodeSnippet relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return RuleCodeQuery The current query, for fluid interface
     */
    public function joinCodeSnippet($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CodeSnippet');

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
            $this->addJoinObject($join, 'CodeSnippet');
        }

        return $this;
    }

    /**
     * Use the CodeSnippet relation CodeSnippet object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\CodeSnippetQuery A secondary query class using the current class as primary query
     */
    public function useCodeSnippetQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCodeSnippet($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CodeSnippet', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\CodeSnippetQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   RuleCode $ruleCode Object to remove from the list of results
     *
     * @return RuleCodeQuery The current query, for fluid interface
     */
    public function prune($ruleCode = null)
    {
        if ($ruleCode) {
            $this->addUsingAlias(RuleCodePeer::RULE_CODE_ID, $ruleCode->getRuleCodeId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

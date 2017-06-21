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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\Rule;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\RuleCode;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\RulePeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\RuleQuery;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\WidgetRule;

/**
 * @method RuleQuery orderByRuleId($order = Criteria::ASC) Order by the rule_id column
 * @method RuleQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method RuleQuery orderByDescription($order = Criteria::ASC) Order by the description column
 * @method RuleQuery orderByCategory($order = Criteria::ASC) Order by the category column
 * @method RuleQuery orderByExpressionType($order = Criteria::ASC) Order by the expression_type column
 * @method RuleQuery orderByExpression($order = Criteria::ASC) Order by the expression column
 *
 * @method RuleQuery groupByRuleId() Group by the rule_id column
 * @method RuleQuery groupByName() Group by the name column
 * @method RuleQuery groupByDescription() Group by the description column
 * @method RuleQuery groupByCategory() Group by the category column
 * @method RuleQuery groupByExpressionType() Group by the expression_type column
 * @method RuleQuery groupByExpression() Group by the expression column
 *
 * @method RuleQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method RuleQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method RuleQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method RuleQuery leftJoinRuleCode($relationAlias = null) Adds a LEFT JOIN clause to the query using the RuleCode relation
 * @method RuleQuery rightJoinRuleCode($relationAlias = null) Adds a RIGHT JOIN clause to the query using the RuleCode relation
 * @method RuleQuery innerJoinRuleCode($relationAlias = null) Adds a INNER JOIN clause to the query using the RuleCode relation
 *
 * @method RuleQuery leftJoinWidgetRule($relationAlias = null) Adds a LEFT JOIN clause to the query using the WidgetRule relation
 * @method RuleQuery rightJoinWidgetRule($relationAlias = null) Adds a RIGHT JOIN clause to the query using the WidgetRule relation
 * @method RuleQuery innerJoinWidgetRule($relationAlias = null) Adds a INNER JOIN clause to the query using the WidgetRule relation
 *
 * @method Rule findOne(PropelPDO $con = null) Return the first Rule matching the query
 * @method Rule findOneOrCreate(PropelPDO $con = null) Return the first Rule matching the query, or a new Rule object populated from the query conditions when no match is found
 *
 * @method Rule findOneByName(string $name) Return the first Rule filtered by the name column
 * @method Rule findOneByDescription(string $description) Return the first Rule filtered by the description column
 * @method Rule findOneByCategory(string $category) Return the first Rule filtered by the category column
 * @method Rule findOneByExpressionType(string $expression_type) Return the first Rule filtered by the expression_type column
 * @method Rule findOneByExpression(string $expression) Return the first Rule filtered by the expression column
 *
 * @method array findByRuleId(int $rule_id) Return Rule objects filtered by the rule_id column
 * @method array findByName(string $name) Return Rule objects filtered by the name column
 * @method array findByDescription(string $description) Return Rule objects filtered by the description column
 * @method array findByCategory(string $category) Return Rule objects filtered by the category column
 * @method array findByExpressionType(string $expression_type) Return Rule objects filtered by the expression_type column
 * @method array findByExpression(string $expression) Return Rule objects filtered by the expression column
 */
abstract class BaseRuleQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseRuleQuery object.
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
            $modelName = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\Rule';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new RuleQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   RuleQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return RuleQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof RuleQuery) {
            return $criteria;
        }
        $query = new RuleQuery(null, null, $modelAlias);

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
     * @return   Rule|Rule[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = RulePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(RulePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Rule A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByRuleId($key, $con = null)
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
     * @return                 Rule A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT rule_id, name, description, category, expression_type, expression FROM core.rule WHERE rule_id = :p0';
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
            $obj = new Rule();
            $obj->hydrate($row);
            RulePeer::addInstanceToPool($obj, (string) $key);
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
     * @return Rule|Rule[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Rule[]|mixed the list of results, formatted by the current formatter
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
     * @return Rule[]
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
     * @return RuleQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(RulePeer::RULE_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return RuleQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(RulePeer::RULE_ID, $keys, Criteria::IN);
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
     * @param     mixed $ruleId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RuleQuery The current query, for fluid interface
     */
    public function filterByRuleId($ruleId = null, $comparison = null)
    {
        if (is_array($ruleId)) {
            $useMinMax = false;
            if (isset($ruleId['min'])) {
                $this->addUsingAlias(RulePeer::RULE_ID, $ruleId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($ruleId['max'])) {
                $this->addUsingAlias(RulePeer::RULE_ID, $ruleId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RulePeer::RULE_ID, $ruleId, $comparison);
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
     * @return RuleQuery The current query, for fluid interface
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

        return $this->addUsingAlias(RulePeer::NAME, $name, $comparison);
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
     * @return RuleQuery The current query, for fluid interface
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

        return $this->addUsingAlias(RulePeer::DESCRIPTION, $description, $comparison);
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
     * @return RuleQuery The current query, for fluid interface
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

        return $this->addUsingAlias(RulePeer::CATEGORY, $category, $comparison);
    }

    /**
     * Filter the query on the expression_type column
     *
     * Example usage:
     * <code>
     * $query->filterByExpressionType('fooValue');   // WHERE expression_type = 'fooValue'
     * $query->filterByExpressionType('%fooValue%'); // WHERE expression_type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $expressionType The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RuleQuery The current query, for fluid interface
     */
    public function filterByExpressionType($expressionType = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($expressionType)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $expressionType)) {
                $expressionType = str_replace('*', '%', $expressionType);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(RulePeer::EXPRESSION_TYPE, $expressionType, $comparison);
    }

    /**
     * Filter the query on the expression column
     *
     * Example usage:
     * <code>
     * $query->filterByExpression('fooValue');   // WHERE expression = 'fooValue'
     * $query->filterByExpression('%fooValue%'); // WHERE expression LIKE '%fooValue%'
     * </code>
     *
     * @param     string $expression The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RuleQuery The current query, for fluid interface
     */
    public function filterByExpression($expression = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($expression)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $expression)) {
                $expression = str_replace('*', '%', $expression);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(RulePeer::EXPRESSION, $expression, $comparison);
    }

    /**
     * Filter the query by a related RuleCode object
     *
     * @param   RuleCode|PropelObjectCollection $ruleCode  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 RuleQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByRuleCode($ruleCode, $comparison = null)
    {
        if ($ruleCode instanceof RuleCode) {
            return $this
                ->addUsingAlias(RulePeer::RULE_ID, $ruleCode->getRuleId(), $comparison);
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
     * @return RuleQuery The current query, for fluid interface
     */
    public function joinRuleCode($relationAlias = null, $joinType = Criteria::INNER_JOIN)
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
    public function useRuleCodeQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinRuleCode($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'RuleCode', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\RuleCodeQuery');
    }

    /**
     * Filter the query by a related WidgetRule object
     *
     * @param   WidgetRule|PropelObjectCollection $widgetRule  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 RuleQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByWidgetRule($widgetRule, $comparison = null)
    {
        if ($widgetRule instanceof WidgetRule) {
            return $this
                ->addUsingAlias(RulePeer::RULE_ID, $widgetRule->getRuleId(), $comparison);
        } elseif ($widgetRule instanceof PropelObjectCollection) {
            return $this
                ->useWidgetRuleQuery()
                ->filterByPrimaryKeys($widgetRule->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByWidgetRule() only accepts arguments of type WidgetRule or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the WidgetRule relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return RuleQuery The current query, for fluid interface
     */
    public function joinWidgetRule($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('WidgetRule');

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
            $this->addJoinObject($join, 'WidgetRule');
        }

        return $this;
    }

    /**
     * Use the WidgetRule relation WidgetRule object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\WidgetRuleQuery A secondary query class using the current class as primary query
     */
    public function useWidgetRuleQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinWidgetRule($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'WidgetRule', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\WidgetRuleQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Rule $rule Object to remove from the list of results
     *
     * @return RuleQuery The current query, for fluid interface
     */
    public function prune($rule = null)
    {
        if ($rule) {
            $this->addUsingAlias(RulePeer::RULE_ID, $rule->getRuleId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

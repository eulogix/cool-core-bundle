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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\WidgetRule;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\WidgetRulePeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\WidgetRuleQuery;

/**
 * @method WidgetRuleQuery orderByWidgetRuleId($order = Criteria::ASC) Order by the widget_rule_id column
 * @method WidgetRuleQuery orderByParentWidgetRuleId($order = Criteria::ASC) Order by the parent_widget_rule_id column
 * @method WidgetRuleQuery orderByWidgetId($order = Criteria::ASC) Order by the widget_id column
 * @method WidgetRuleQuery orderByRuleId($order = Criteria::ASC) Order by the rule_id column
 * @method WidgetRuleQuery orderByEnabledFlag($order = Criteria::ASC) Order by the enabled_flag column
 * @method WidgetRuleQuery orderByEvaluation($order = Criteria::ASC) Order by the evaluation column
 *
 * @method WidgetRuleQuery groupByWidgetRuleId() Group by the widget_rule_id column
 * @method WidgetRuleQuery groupByParentWidgetRuleId() Group by the parent_widget_rule_id column
 * @method WidgetRuleQuery groupByWidgetId() Group by the widget_id column
 * @method WidgetRuleQuery groupByRuleId() Group by the rule_id column
 * @method WidgetRuleQuery groupByEnabledFlag() Group by the enabled_flag column
 * @method WidgetRuleQuery groupByEvaluation() Group by the evaluation column
 *
 * @method WidgetRuleQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method WidgetRuleQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method WidgetRuleQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method WidgetRuleQuery leftJoinRule($relationAlias = null) Adds a LEFT JOIN clause to the query using the Rule relation
 * @method WidgetRuleQuery rightJoinRule($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Rule relation
 * @method WidgetRuleQuery innerJoinRule($relationAlias = null) Adds a INNER JOIN clause to the query using the Rule relation
 *
 * @method WidgetRuleQuery leftJoinWidgetRuleRelatedByParentWidgetRuleIdWidgetId($relationAlias = null) Adds a LEFT JOIN clause to the query using the WidgetRuleRelatedByParentWidgetRuleIdWidgetId relation
 * @method WidgetRuleQuery rightJoinWidgetRuleRelatedByParentWidgetRuleIdWidgetId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the WidgetRuleRelatedByParentWidgetRuleIdWidgetId relation
 * @method WidgetRuleQuery innerJoinWidgetRuleRelatedByParentWidgetRuleIdWidgetId($relationAlias = null) Adds a INNER JOIN clause to the query using the WidgetRuleRelatedByParentWidgetRuleIdWidgetId relation
 *
 * @method WidgetRuleQuery leftJoinWidgetRuleRelatedByWidgetRuleIdWidgetId($relationAlias = null) Adds a LEFT JOIN clause to the query using the WidgetRuleRelatedByWidgetRuleIdWidgetId relation
 * @method WidgetRuleQuery rightJoinWidgetRuleRelatedByWidgetRuleIdWidgetId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the WidgetRuleRelatedByWidgetRuleIdWidgetId relation
 * @method WidgetRuleQuery innerJoinWidgetRuleRelatedByWidgetRuleIdWidgetId($relationAlias = null) Adds a INNER JOIN clause to the query using the WidgetRuleRelatedByWidgetRuleIdWidgetId relation
 *
 * @method WidgetRule findOne(PropelPDO $con = null) Return the first WidgetRule matching the query
 * @method WidgetRule findOneOrCreate(PropelPDO $con = null) Return the first WidgetRule matching the query, or a new WidgetRule object populated from the query conditions when no match is found
 *
 * @method WidgetRule findOneByParentWidgetRuleId(int $parent_widget_rule_id) Return the first WidgetRule filtered by the parent_widget_rule_id column
 * @method WidgetRule findOneByWidgetId(string $widget_id) Return the first WidgetRule filtered by the widget_id column
 * @method WidgetRule findOneByRuleId(int $rule_id) Return the first WidgetRule filtered by the rule_id column
 * @method WidgetRule findOneByEnabledFlag(boolean $enabled_flag) Return the first WidgetRule filtered by the enabled_flag column
 * @method WidgetRule findOneByEvaluation(string $evaluation) Return the first WidgetRule filtered by the evaluation column
 *
 * @method array findByWidgetRuleId(int $widget_rule_id) Return WidgetRule objects filtered by the widget_rule_id column
 * @method array findByParentWidgetRuleId(int $parent_widget_rule_id) Return WidgetRule objects filtered by the parent_widget_rule_id column
 * @method array findByWidgetId(string $widget_id) Return WidgetRule objects filtered by the widget_id column
 * @method array findByRuleId(int $rule_id) Return WidgetRule objects filtered by the rule_id column
 * @method array findByEnabledFlag(boolean $enabled_flag) Return WidgetRule objects filtered by the enabled_flag column
 * @method array findByEvaluation(string $evaluation) Return WidgetRule objects filtered by the evaluation column
 */
abstract class BaseWidgetRuleQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseWidgetRuleQuery object.
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
            $modelName = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\WidgetRule';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new WidgetRuleQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   WidgetRuleQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return WidgetRuleQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof WidgetRuleQuery) {
            return $criteria;
        }
        $query = new WidgetRuleQuery(null, null, $modelAlias);

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
     * @return   WidgetRule|WidgetRule[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = WidgetRulePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(WidgetRulePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 WidgetRule A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByWidgetRuleId($key, $con = null)
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
     * @return                 WidgetRule A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT widget_rule_id, parent_widget_rule_id, widget_id, rule_id, enabled_flag, evaluation FROM core.widget_rule WHERE widget_rule_id = :p0';
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
            $obj = new WidgetRule();
            $obj->hydrate($row);
            WidgetRulePeer::addInstanceToPool($obj, (string) $key);
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
     * @return WidgetRule|WidgetRule[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|WidgetRule[]|mixed the list of results, formatted by the current formatter
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
     * @return WidgetRule[]
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
     * @return WidgetRuleQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(WidgetRulePeer::WIDGET_RULE_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return WidgetRuleQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(WidgetRulePeer::WIDGET_RULE_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the widget_rule_id column
     *
     * Example usage:
     * <code>
     * $query->filterByWidgetRuleId(1234); // WHERE widget_rule_id = 1234
     * $query->filterByWidgetRuleId(array(12, 34)); // WHERE widget_rule_id IN (12, 34)
     * $query->filterByWidgetRuleId(array('min' => 12)); // WHERE widget_rule_id >= 12
     * $query->filterByWidgetRuleId(array('max' => 12)); // WHERE widget_rule_id <= 12
     * </code>
     *
     * @param     mixed $widgetRuleId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return WidgetRuleQuery The current query, for fluid interface
     */
    public function filterByWidgetRuleId($widgetRuleId = null, $comparison = null)
    {
        if (is_array($widgetRuleId)) {
            $useMinMax = false;
            if (isset($widgetRuleId['min'])) {
                $this->addUsingAlias(WidgetRulePeer::WIDGET_RULE_ID, $widgetRuleId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($widgetRuleId['max'])) {
                $this->addUsingAlias(WidgetRulePeer::WIDGET_RULE_ID, $widgetRuleId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(WidgetRulePeer::WIDGET_RULE_ID, $widgetRuleId, $comparison);
    }

    /**
     * Filter the query on the parent_widget_rule_id column
     *
     * Example usage:
     * <code>
     * $query->filterByParentWidgetRuleId(1234); // WHERE parent_widget_rule_id = 1234
     * $query->filterByParentWidgetRuleId(array(12, 34)); // WHERE parent_widget_rule_id IN (12, 34)
     * $query->filterByParentWidgetRuleId(array('min' => 12)); // WHERE parent_widget_rule_id >= 12
     * $query->filterByParentWidgetRuleId(array('max' => 12)); // WHERE parent_widget_rule_id <= 12
     * </code>
     *
     * @see       filterByWidgetRuleRelatedByParentWidgetRuleIdWidgetId()
     *
     * @param     mixed $parentWidgetRuleId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return WidgetRuleQuery The current query, for fluid interface
     */
    public function filterByParentWidgetRuleId($parentWidgetRuleId = null, $comparison = null)
    {
        if (is_array($parentWidgetRuleId)) {
            $useMinMax = false;
            if (isset($parentWidgetRuleId['min'])) {
                $this->addUsingAlias(WidgetRulePeer::PARENT_WIDGET_RULE_ID, $parentWidgetRuleId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($parentWidgetRuleId['max'])) {
                $this->addUsingAlias(WidgetRulePeer::PARENT_WIDGET_RULE_ID, $parentWidgetRuleId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(WidgetRulePeer::PARENT_WIDGET_RULE_ID, $parentWidgetRuleId, $comparison);
    }

    /**
     * Filter the query on the widget_id column
     *
     * Example usage:
     * <code>
     * $query->filterByWidgetId('fooValue');   // WHERE widget_id = 'fooValue'
     * $query->filterByWidgetId('%fooValue%'); // WHERE widget_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $widgetId The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return WidgetRuleQuery The current query, for fluid interface
     */
    public function filterByWidgetId($widgetId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($widgetId)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $widgetId)) {
                $widgetId = str_replace('*', '%', $widgetId);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(WidgetRulePeer::WIDGET_ID, $widgetId, $comparison);
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
     * @return WidgetRuleQuery The current query, for fluid interface
     */
    public function filterByRuleId($ruleId = null, $comparison = null)
    {
        if (is_array($ruleId)) {
            $useMinMax = false;
            if (isset($ruleId['min'])) {
                $this->addUsingAlias(WidgetRulePeer::RULE_ID, $ruleId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($ruleId['max'])) {
                $this->addUsingAlias(WidgetRulePeer::RULE_ID, $ruleId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(WidgetRulePeer::RULE_ID, $ruleId, $comparison);
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
     * @return WidgetRuleQuery The current query, for fluid interface
     */
    public function filterByEnabledFlag($enabledFlag = null, $comparison = null)
    {
        if (is_string($enabledFlag)) {
            $enabledFlag = in_array(strtolower($enabledFlag), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(WidgetRulePeer::ENABLED_FLAG, $enabledFlag, $comparison);
    }

    /**
     * Filter the query on the evaluation column
     *
     * Example usage:
     * <code>
     * $query->filterByEvaluation('fooValue');   // WHERE evaluation = 'fooValue'
     * $query->filterByEvaluation('%fooValue%'); // WHERE evaluation LIKE '%fooValue%'
     * </code>
     *
     * @param     string $evaluation The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return WidgetRuleQuery The current query, for fluid interface
     */
    public function filterByEvaluation($evaluation = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($evaluation)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $evaluation)) {
                $evaluation = str_replace('*', '%', $evaluation);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(WidgetRulePeer::EVALUATION, $evaluation, $comparison);
    }

    /**
     * Filter the query by a related Rule object
     *
     * @param   Rule|PropelObjectCollection $rule The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 WidgetRuleQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByRule($rule, $comparison = null)
    {
        if ($rule instanceof Rule) {
            return $this
                ->addUsingAlias(WidgetRulePeer::RULE_ID, $rule->getRuleId(), $comparison);
        } elseif ($rule instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(WidgetRulePeer::RULE_ID, $rule->toKeyValue('PrimaryKey', 'RuleId'), $comparison);
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
     * @return WidgetRuleQuery The current query, for fluid interface
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
     * Filter the query by a related WidgetRule object
     *
     * @param   WidgetRule $widgetRule The related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 WidgetRuleQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByWidgetRuleRelatedByParentWidgetRuleIdWidgetId($widgetRule, $comparison = null)
    {
        if ($widgetRule instanceof WidgetRule) {
            return $this
                ->addUsingAlias(WidgetRulePeer::PARENT_WIDGET_RULE_ID, $widgetRule->getWidgetRuleId(), $comparison)
                ->addUsingAlias(WidgetRulePeer::WIDGET_ID, $widgetRule->getWidgetId(), $comparison);
        } else {
            throw new PropelException('filterByWidgetRuleRelatedByParentWidgetRuleIdWidgetId() only accepts arguments of type WidgetRule');
        }
    }

    /**
     * Adds a JOIN clause to the query using the WidgetRuleRelatedByParentWidgetRuleIdWidgetId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return WidgetRuleQuery The current query, for fluid interface
     */
    public function joinWidgetRuleRelatedByParentWidgetRuleIdWidgetId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('WidgetRuleRelatedByParentWidgetRuleIdWidgetId');

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
            $this->addJoinObject($join, 'WidgetRuleRelatedByParentWidgetRuleIdWidgetId');
        }

        return $this;
    }

    /**
     * Use the WidgetRuleRelatedByParentWidgetRuleIdWidgetId relation WidgetRule object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\WidgetRuleQuery A secondary query class using the current class as primary query
     */
    public function useWidgetRuleRelatedByParentWidgetRuleIdWidgetIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinWidgetRuleRelatedByParentWidgetRuleIdWidgetId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'WidgetRuleRelatedByParentWidgetRuleIdWidgetId', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\WidgetRuleQuery');
    }

    /**
     * Filter the query by a related WidgetRule object
     *
     * @param   WidgetRule|PropelObjectCollection $widgetRule  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 WidgetRuleQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByWidgetRuleRelatedByWidgetRuleIdWidgetId($widgetRule, $comparison = null)
    {
        if ($widgetRule instanceof WidgetRule) {
            return $this
                ->addUsingAlias(WidgetRulePeer::WIDGET_RULE_ID, $widgetRule->getParentWidgetRuleId(), $comparison)
                ->addUsingAlias(WidgetRulePeer::WIDGET_ID, $widgetRule->getWidgetId(), $comparison);
        } else {
            throw new PropelException('filterByWidgetRuleRelatedByWidgetRuleIdWidgetId() only accepts arguments of type WidgetRule');
        }
    }

    /**
     * Adds a JOIN clause to the query using the WidgetRuleRelatedByWidgetRuleIdWidgetId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return WidgetRuleQuery The current query, for fluid interface
     */
    public function joinWidgetRuleRelatedByWidgetRuleIdWidgetId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('WidgetRuleRelatedByWidgetRuleIdWidgetId');

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
            $this->addJoinObject($join, 'WidgetRuleRelatedByWidgetRuleIdWidgetId');
        }

        return $this;
    }

    /**
     * Use the WidgetRuleRelatedByWidgetRuleIdWidgetId relation WidgetRule object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\WidgetRuleQuery A secondary query class using the current class as primary query
     */
    public function useWidgetRuleRelatedByWidgetRuleIdWidgetIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinWidgetRuleRelatedByWidgetRuleIdWidgetId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'WidgetRuleRelatedByWidgetRuleIdWidgetId', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\WidgetRuleQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   WidgetRule $widgetRule Object to remove from the list of results
     *
     * @return WidgetRuleQuery The current query, for fluid interface
     */
    public function prune($widgetRule = null)
    {
        if ($widgetRule) {
            $this->addUsingAlias(WidgetRulePeer::WIDGET_RULE_ID, $widgetRule->getWidgetRuleId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\ListerConfig;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\ListerConfigColumn;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\ListerConfigColumnPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\ListerConfigColumnQuery;

/**
 * @method ListerConfigColumnQuery orderByListerConfigColumnId($order = Criteria::ASC) Order by the lister_config_column_id column
 * @method ListerConfigColumnQuery orderByListerConfigId($order = Criteria::ASC) Order by the lister_config_id column
 * @method ListerConfigColumnQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method ListerConfigColumnQuery orderBySortableFlag($order = Criteria::ASC) Order by the sortable_flag column
 * @method ListerConfigColumnQuery orderByEditableFlag($order = Criteria::ASC) Order by the editable_flag column
 * @method ListerConfigColumnQuery orderByShowSummaryFlag($order = Criteria::ASC) Order by the show_summary_flag column
 * @method ListerConfigColumnQuery orderByWidth($order = Criteria::ASC) Order by the width column
 * @method ListerConfigColumnQuery orderByCellTemplate($order = Criteria::ASC) Order by the cell_template column
 * @method ListerConfigColumnQuery orderByCellTemplateJs($order = Criteria::ASC) Order by the cell_template_js column
 * @method ListerConfigColumnQuery orderByDijitWidgetTemplate($order = Criteria::ASC) Order by the dijit_widget_template column
 * @method ListerConfigColumnQuery orderByDijitWidgetSetValueJs($order = Criteria::ASC) Order by the dijit_widget_set_value_js column
 * @method ListerConfigColumnQuery orderByColumnStyleCss($order = Criteria::ASC) Order by the column_style_css column
 * @method ListerConfigColumnQuery orderBySortOrder($order = Criteria::ASC) Order by the sort_order column
 * @method ListerConfigColumnQuery orderBySortbyOrder($order = Criteria::ASC) Order by the sortby_order column
 * @method ListerConfigColumnQuery orderBySortbyDirection($order = Criteria::ASC) Order by the sortby_direction column
 * @method ListerConfigColumnQuery orderByTruncateChars($order = Criteria::ASC) Order by the truncate_chars column
 * @method ListerConfigColumnQuery orderByTooltipJsExpression($order = Criteria::ASC) Order by the tooltip_js_expression column
 * @method ListerConfigColumnQuery orderByTooltipUrlJsExpression($order = Criteria::ASC) Order by the tooltip_url_js_expression column
 * @method ListerConfigColumnQuery orderByTooltipMaxWidth($order = Criteria::ASC) Order by the tooltip_max_width column
 * @method ListerConfigColumnQuery orderByTooltipDelayMsec($order = Criteria::ASC) Order by the tooltip_delay_msec column
 *
 * @method ListerConfigColumnQuery groupByListerConfigColumnId() Group by the lister_config_column_id column
 * @method ListerConfigColumnQuery groupByListerConfigId() Group by the lister_config_id column
 * @method ListerConfigColumnQuery groupByName() Group by the name column
 * @method ListerConfigColumnQuery groupBySortableFlag() Group by the sortable_flag column
 * @method ListerConfigColumnQuery groupByEditableFlag() Group by the editable_flag column
 * @method ListerConfigColumnQuery groupByShowSummaryFlag() Group by the show_summary_flag column
 * @method ListerConfigColumnQuery groupByWidth() Group by the width column
 * @method ListerConfigColumnQuery groupByCellTemplate() Group by the cell_template column
 * @method ListerConfigColumnQuery groupByCellTemplateJs() Group by the cell_template_js column
 * @method ListerConfigColumnQuery groupByDijitWidgetTemplate() Group by the dijit_widget_template column
 * @method ListerConfigColumnQuery groupByDijitWidgetSetValueJs() Group by the dijit_widget_set_value_js column
 * @method ListerConfigColumnQuery groupByColumnStyleCss() Group by the column_style_css column
 * @method ListerConfigColumnQuery groupBySortOrder() Group by the sort_order column
 * @method ListerConfigColumnQuery groupBySortbyOrder() Group by the sortby_order column
 * @method ListerConfigColumnQuery groupBySortbyDirection() Group by the sortby_direction column
 * @method ListerConfigColumnQuery groupByTruncateChars() Group by the truncate_chars column
 * @method ListerConfigColumnQuery groupByTooltipJsExpression() Group by the tooltip_js_expression column
 * @method ListerConfigColumnQuery groupByTooltipUrlJsExpression() Group by the tooltip_url_js_expression column
 * @method ListerConfigColumnQuery groupByTooltipMaxWidth() Group by the tooltip_max_width column
 * @method ListerConfigColumnQuery groupByTooltipDelayMsec() Group by the tooltip_delay_msec column
 *
 * @method ListerConfigColumnQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method ListerConfigColumnQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method ListerConfigColumnQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method ListerConfigColumnQuery leftJoinListerConfig($relationAlias = null) Adds a LEFT JOIN clause to the query using the ListerConfig relation
 * @method ListerConfigColumnQuery rightJoinListerConfig($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ListerConfig relation
 * @method ListerConfigColumnQuery innerJoinListerConfig($relationAlias = null) Adds a INNER JOIN clause to the query using the ListerConfig relation
 *
 * @method ListerConfigColumn findOne(PropelPDO $con = null) Return the first ListerConfigColumn matching the query
 * @method ListerConfigColumn findOneOrCreate(PropelPDO $con = null) Return the first ListerConfigColumn matching the query, or a new ListerConfigColumn object populated from the query conditions when no match is found
 *
 * @method ListerConfigColumn findOneByListerConfigId(int $lister_config_id) Return the first ListerConfigColumn filtered by the lister_config_id column
 * @method ListerConfigColumn findOneByName(string $name) Return the first ListerConfigColumn filtered by the name column
 * @method ListerConfigColumn findOneBySortableFlag(boolean $sortable_flag) Return the first ListerConfigColumn filtered by the sortable_flag column
 * @method ListerConfigColumn findOneByEditableFlag(boolean $editable_flag) Return the first ListerConfigColumn filtered by the editable_flag column
 * @method ListerConfigColumn findOneByShowSummaryFlag(boolean $show_summary_flag) Return the first ListerConfigColumn filtered by the show_summary_flag column
 * @method ListerConfigColumn findOneByWidth(string $width) Return the first ListerConfigColumn filtered by the width column
 * @method ListerConfigColumn findOneByCellTemplate(string $cell_template) Return the first ListerConfigColumn filtered by the cell_template column
 * @method ListerConfigColumn findOneByCellTemplateJs(string $cell_template_js) Return the first ListerConfigColumn filtered by the cell_template_js column
 * @method ListerConfigColumn findOneByDijitWidgetTemplate(string $dijit_widget_template) Return the first ListerConfigColumn filtered by the dijit_widget_template column
 * @method ListerConfigColumn findOneByDijitWidgetSetValueJs(string $dijit_widget_set_value_js) Return the first ListerConfigColumn filtered by the dijit_widget_set_value_js column
 * @method ListerConfigColumn findOneByColumnStyleCss(string $column_style_css) Return the first ListerConfigColumn filtered by the column_style_css column
 * @method ListerConfigColumn findOneBySortOrder(int $sort_order) Return the first ListerConfigColumn filtered by the sort_order column
 * @method ListerConfigColumn findOneBySortbyOrder(int $sortby_order) Return the first ListerConfigColumn filtered by the sortby_order column
 * @method ListerConfigColumn findOneBySortbyDirection(string $sortby_direction) Return the first ListerConfigColumn filtered by the sortby_direction column
 * @method ListerConfigColumn findOneByTruncateChars(int $truncate_chars) Return the first ListerConfigColumn filtered by the truncate_chars column
 * @method ListerConfigColumn findOneByTooltipJsExpression(string $tooltip_js_expression) Return the first ListerConfigColumn filtered by the tooltip_js_expression column
 * @method ListerConfigColumn findOneByTooltipUrlJsExpression(string $tooltip_url_js_expression) Return the first ListerConfigColumn filtered by the tooltip_url_js_expression column
 * @method ListerConfigColumn findOneByTooltipMaxWidth(int $tooltip_max_width) Return the first ListerConfigColumn filtered by the tooltip_max_width column
 * @method ListerConfigColumn findOneByTooltipDelayMsec(int $tooltip_delay_msec) Return the first ListerConfigColumn filtered by the tooltip_delay_msec column
 *
 * @method array findByListerConfigColumnId(int $lister_config_column_id) Return ListerConfigColumn objects filtered by the lister_config_column_id column
 * @method array findByListerConfigId(int $lister_config_id) Return ListerConfigColumn objects filtered by the lister_config_id column
 * @method array findByName(string $name) Return ListerConfigColumn objects filtered by the name column
 * @method array findBySortableFlag(boolean $sortable_flag) Return ListerConfigColumn objects filtered by the sortable_flag column
 * @method array findByEditableFlag(boolean $editable_flag) Return ListerConfigColumn objects filtered by the editable_flag column
 * @method array findByShowSummaryFlag(boolean $show_summary_flag) Return ListerConfigColumn objects filtered by the show_summary_flag column
 * @method array findByWidth(string $width) Return ListerConfigColumn objects filtered by the width column
 * @method array findByCellTemplate(string $cell_template) Return ListerConfigColumn objects filtered by the cell_template column
 * @method array findByCellTemplateJs(string $cell_template_js) Return ListerConfigColumn objects filtered by the cell_template_js column
 * @method array findByDijitWidgetTemplate(string $dijit_widget_template) Return ListerConfigColumn objects filtered by the dijit_widget_template column
 * @method array findByDijitWidgetSetValueJs(string $dijit_widget_set_value_js) Return ListerConfigColumn objects filtered by the dijit_widget_set_value_js column
 * @method array findByColumnStyleCss(string $column_style_css) Return ListerConfigColumn objects filtered by the column_style_css column
 * @method array findBySortOrder(int $sort_order) Return ListerConfigColumn objects filtered by the sort_order column
 * @method array findBySortbyOrder(int $sortby_order) Return ListerConfigColumn objects filtered by the sortby_order column
 * @method array findBySortbyDirection(string $sortby_direction) Return ListerConfigColumn objects filtered by the sortby_direction column
 * @method array findByTruncateChars(int $truncate_chars) Return ListerConfigColumn objects filtered by the truncate_chars column
 * @method array findByTooltipJsExpression(string $tooltip_js_expression) Return ListerConfigColumn objects filtered by the tooltip_js_expression column
 * @method array findByTooltipUrlJsExpression(string $tooltip_url_js_expression) Return ListerConfigColumn objects filtered by the tooltip_url_js_expression column
 * @method array findByTooltipMaxWidth(int $tooltip_max_width) Return ListerConfigColumn objects filtered by the tooltip_max_width column
 * @method array findByTooltipDelayMsec(int $tooltip_delay_msec) Return ListerConfigColumn objects filtered by the tooltip_delay_msec column
 */
abstract class BaseListerConfigColumnQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseListerConfigColumnQuery object.
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
            $modelName = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\ListerConfigColumn';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ListerConfigColumnQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   ListerConfigColumnQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return ListerConfigColumnQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ListerConfigColumnQuery) {
            return $criteria;
        }
        $query = new ListerConfigColumnQuery(null, null, $modelAlias);

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
     * @return   ListerConfigColumn|ListerConfigColumn[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ListerConfigColumnPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(ListerConfigColumnPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 ListerConfigColumn A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByListerConfigColumnId($key, $con = null)
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
     * @return                 ListerConfigColumn A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT lister_config_column_id, lister_config_id, name, sortable_flag, editable_flag, show_summary_flag, width, cell_template, cell_template_js, dijit_widget_template, dijit_widget_set_value_js, column_style_css, sort_order, sortby_order, sortby_direction, truncate_chars, tooltip_js_expression, tooltip_url_js_expression, tooltip_max_width, tooltip_delay_msec FROM core.lister_config_column WHERE lister_config_column_id = :p0';
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
            $obj = new ListerConfigColumn();
            $obj->hydrate($row);
            ListerConfigColumnPeer::addInstanceToPool($obj, (string) $key);
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
     * @return ListerConfigColumn|ListerConfigColumn[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|ListerConfigColumn[]|mixed the list of results, formatted by the current formatter
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
     * @return ListerConfigColumn[]
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
     * @return ListerConfigColumnQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ListerConfigColumnPeer::LISTER_CONFIG_COLUMN_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ListerConfigColumnQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ListerConfigColumnPeer::LISTER_CONFIG_COLUMN_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the lister_config_column_id column
     *
     * Example usage:
     * <code>
     * $query->filterByListerConfigColumnId(1234); // WHERE lister_config_column_id = 1234
     * $query->filterByListerConfigColumnId(array(12, 34)); // WHERE lister_config_column_id IN (12, 34)
     * $query->filterByListerConfigColumnId(array('min' => 12)); // WHERE lister_config_column_id >= 12
     * $query->filterByListerConfigColumnId(array('max' => 12)); // WHERE lister_config_column_id <= 12
     * </code>
     *
     * @param     mixed $listerConfigColumnId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ListerConfigColumnQuery The current query, for fluid interface
     */
    public function filterByListerConfigColumnId($listerConfigColumnId = null, $comparison = null)
    {
        if (is_array($listerConfigColumnId)) {
            $useMinMax = false;
            if (isset($listerConfigColumnId['min'])) {
                $this->addUsingAlias(ListerConfigColumnPeer::LISTER_CONFIG_COLUMN_ID, $listerConfigColumnId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($listerConfigColumnId['max'])) {
                $this->addUsingAlias(ListerConfigColumnPeer::LISTER_CONFIG_COLUMN_ID, $listerConfigColumnId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ListerConfigColumnPeer::LISTER_CONFIG_COLUMN_ID, $listerConfigColumnId, $comparison);
    }

    /**
     * Filter the query on the lister_config_id column
     *
     * Example usage:
     * <code>
     * $query->filterByListerConfigId(1234); // WHERE lister_config_id = 1234
     * $query->filterByListerConfigId(array(12, 34)); // WHERE lister_config_id IN (12, 34)
     * $query->filterByListerConfigId(array('min' => 12)); // WHERE lister_config_id >= 12
     * $query->filterByListerConfigId(array('max' => 12)); // WHERE lister_config_id <= 12
     * </code>
     *
     * @see       filterByListerConfig()
     *
     * @param     mixed $listerConfigId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ListerConfigColumnQuery The current query, for fluid interface
     */
    public function filterByListerConfigId($listerConfigId = null, $comparison = null)
    {
        if (is_array($listerConfigId)) {
            $useMinMax = false;
            if (isset($listerConfigId['min'])) {
                $this->addUsingAlias(ListerConfigColumnPeer::LISTER_CONFIG_ID, $listerConfigId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($listerConfigId['max'])) {
                $this->addUsingAlias(ListerConfigColumnPeer::LISTER_CONFIG_ID, $listerConfigId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ListerConfigColumnPeer::LISTER_CONFIG_ID, $listerConfigId, $comparison);
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
     * @return ListerConfigColumnQuery The current query, for fluid interface
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

        return $this->addUsingAlias(ListerConfigColumnPeer::NAME, $name, $comparison);
    }

    /**
     * Filter the query on the sortable_flag column
     *
     * Example usage:
     * <code>
     * $query->filterBySortableFlag(true); // WHERE sortable_flag = true
     * $query->filterBySortableFlag('yes'); // WHERE sortable_flag = true
     * </code>
     *
     * @param     boolean|string $sortableFlag The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ListerConfigColumnQuery The current query, for fluid interface
     */
    public function filterBySortableFlag($sortableFlag = null, $comparison = null)
    {
        if (is_string($sortableFlag)) {
            $sortableFlag = in_array(strtolower($sortableFlag), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(ListerConfigColumnPeer::SORTABLE_FLAG, $sortableFlag, $comparison);
    }

    /**
     * Filter the query on the editable_flag column
     *
     * Example usage:
     * <code>
     * $query->filterByEditableFlag(true); // WHERE editable_flag = true
     * $query->filterByEditableFlag('yes'); // WHERE editable_flag = true
     * </code>
     *
     * @param     boolean|string $editableFlag The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ListerConfigColumnQuery The current query, for fluid interface
     */
    public function filterByEditableFlag($editableFlag = null, $comparison = null)
    {
        if (is_string($editableFlag)) {
            $editableFlag = in_array(strtolower($editableFlag), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(ListerConfigColumnPeer::EDITABLE_FLAG, $editableFlag, $comparison);
    }

    /**
     * Filter the query on the show_summary_flag column
     *
     * Example usage:
     * <code>
     * $query->filterByShowSummaryFlag(true); // WHERE show_summary_flag = true
     * $query->filterByShowSummaryFlag('yes'); // WHERE show_summary_flag = true
     * </code>
     *
     * @param     boolean|string $showSummaryFlag The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ListerConfigColumnQuery The current query, for fluid interface
     */
    public function filterByShowSummaryFlag($showSummaryFlag = null, $comparison = null)
    {
        if (is_string($showSummaryFlag)) {
            $showSummaryFlag = in_array(strtolower($showSummaryFlag), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(ListerConfigColumnPeer::SHOW_SUMMARY_FLAG, $showSummaryFlag, $comparison);
    }

    /**
     * Filter the query on the width column
     *
     * Example usage:
     * <code>
     * $query->filterByWidth('fooValue');   // WHERE width = 'fooValue'
     * $query->filterByWidth('%fooValue%'); // WHERE width LIKE '%fooValue%'
     * </code>
     *
     * @param     string $width The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ListerConfigColumnQuery The current query, for fluid interface
     */
    public function filterByWidth($width = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($width)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $width)) {
                $width = str_replace('*', '%', $width);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ListerConfigColumnPeer::WIDTH, $width, $comparison);
    }

    /**
     * Filter the query on the cell_template column
     *
     * Example usage:
     * <code>
     * $query->filterByCellTemplate('fooValue');   // WHERE cell_template = 'fooValue'
     * $query->filterByCellTemplate('%fooValue%'); // WHERE cell_template LIKE '%fooValue%'
     * </code>
     *
     * @param     string $cellTemplate The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ListerConfigColumnQuery The current query, for fluid interface
     */
    public function filterByCellTemplate($cellTemplate = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($cellTemplate)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $cellTemplate)) {
                $cellTemplate = str_replace('*', '%', $cellTemplate);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ListerConfigColumnPeer::CELL_TEMPLATE, $cellTemplate, $comparison);
    }

    /**
     * Filter the query on the cell_template_js column
     *
     * Example usage:
     * <code>
     * $query->filterByCellTemplateJs('fooValue');   // WHERE cell_template_js = 'fooValue'
     * $query->filterByCellTemplateJs('%fooValue%'); // WHERE cell_template_js LIKE '%fooValue%'
     * </code>
     *
     * @param     string $cellTemplateJs The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ListerConfigColumnQuery The current query, for fluid interface
     */
    public function filterByCellTemplateJs($cellTemplateJs = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($cellTemplateJs)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $cellTemplateJs)) {
                $cellTemplateJs = str_replace('*', '%', $cellTemplateJs);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ListerConfigColumnPeer::CELL_TEMPLATE_JS, $cellTemplateJs, $comparison);
    }

    /**
     * Filter the query on the dijit_widget_template column
     *
     * Example usage:
     * <code>
     * $query->filterByDijitWidgetTemplate('fooValue');   // WHERE dijit_widget_template = 'fooValue'
     * $query->filterByDijitWidgetTemplate('%fooValue%'); // WHERE dijit_widget_template LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dijitWidgetTemplate The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ListerConfigColumnQuery The current query, for fluid interface
     */
    public function filterByDijitWidgetTemplate($dijitWidgetTemplate = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dijitWidgetTemplate)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dijitWidgetTemplate)) {
                $dijitWidgetTemplate = str_replace('*', '%', $dijitWidgetTemplate);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ListerConfigColumnPeer::DIJIT_WIDGET_TEMPLATE, $dijitWidgetTemplate, $comparison);
    }

    /**
     * Filter the query on the dijit_widget_set_value_js column
     *
     * Example usage:
     * <code>
     * $query->filterByDijitWidgetSetValueJs('fooValue');   // WHERE dijit_widget_set_value_js = 'fooValue'
     * $query->filterByDijitWidgetSetValueJs('%fooValue%'); // WHERE dijit_widget_set_value_js LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dijitWidgetSetValueJs The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ListerConfigColumnQuery The current query, for fluid interface
     */
    public function filterByDijitWidgetSetValueJs($dijitWidgetSetValueJs = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dijitWidgetSetValueJs)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dijitWidgetSetValueJs)) {
                $dijitWidgetSetValueJs = str_replace('*', '%', $dijitWidgetSetValueJs);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ListerConfigColumnPeer::DIJIT_WIDGET_SET_VALUE_JS, $dijitWidgetSetValueJs, $comparison);
    }

    /**
     * Filter the query on the column_style_css column
     *
     * Example usage:
     * <code>
     * $query->filterByColumnStyleCss('fooValue');   // WHERE column_style_css = 'fooValue'
     * $query->filterByColumnStyleCss('%fooValue%'); // WHERE column_style_css LIKE '%fooValue%'
     * </code>
     *
     * @param     string $columnStyleCss The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ListerConfigColumnQuery The current query, for fluid interface
     */
    public function filterByColumnStyleCss($columnStyleCss = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($columnStyleCss)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $columnStyleCss)) {
                $columnStyleCss = str_replace('*', '%', $columnStyleCss);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ListerConfigColumnPeer::COLUMN_STYLE_CSS, $columnStyleCss, $comparison);
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
     * @return ListerConfigColumnQuery The current query, for fluid interface
     */
    public function filterBySortOrder($sortOrder = null, $comparison = null)
    {
        if (is_array($sortOrder)) {
            $useMinMax = false;
            if (isset($sortOrder['min'])) {
                $this->addUsingAlias(ListerConfigColumnPeer::SORT_ORDER, $sortOrder['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sortOrder['max'])) {
                $this->addUsingAlias(ListerConfigColumnPeer::SORT_ORDER, $sortOrder['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ListerConfigColumnPeer::SORT_ORDER, $sortOrder, $comparison);
    }

    /**
     * Filter the query on the sortby_order column
     *
     * Example usage:
     * <code>
     * $query->filterBySortbyOrder(1234); // WHERE sortby_order = 1234
     * $query->filterBySortbyOrder(array(12, 34)); // WHERE sortby_order IN (12, 34)
     * $query->filterBySortbyOrder(array('min' => 12)); // WHERE sortby_order >= 12
     * $query->filterBySortbyOrder(array('max' => 12)); // WHERE sortby_order <= 12
     * </code>
     *
     * @param     mixed $sortbyOrder The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ListerConfigColumnQuery The current query, for fluid interface
     */
    public function filterBySortbyOrder($sortbyOrder = null, $comparison = null)
    {
        if (is_array($sortbyOrder)) {
            $useMinMax = false;
            if (isset($sortbyOrder['min'])) {
                $this->addUsingAlias(ListerConfigColumnPeer::SORTBY_ORDER, $sortbyOrder['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sortbyOrder['max'])) {
                $this->addUsingAlias(ListerConfigColumnPeer::SORTBY_ORDER, $sortbyOrder['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ListerConfigColumnPeer::SORTBY_ORDER, $sortbyOrder, $comparison);
    }

    /**
     * Filter the query on the sortby_direction column
     *
     * Example usage:
     * <code>
     * $query->filterBySortbyDirection('fooValue');   // WHERE sortby_direction = 'fooValue'
     * $query->filterBySortbyDirection('%fooValue%'); // WHERE sortby_direction LIKE '%fooValue%'
     * </code>
     *
     * @param     string $sortbyDirection The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ListerConfigColumnQuery The current query, for fluid interface
     */
    public function filterBySortbyDirection($sortbyDirection = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($sortbyDirection)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $sortbyDirection)) {
                $sortbyDirection = str_replace('*', '%', $sortbyDirection);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ListerConfigColumnPeer::SORTBY_DIRECTION, $sortbyDirection, $comparison);
    }

    /**
     * Filter the query on the truncate_chars column
     *
     * Example usage:
     * <code>
     * $query->filterByTruncateChars(1234); // WHERE truncate_chars = 1234
     * $query->filterByTruncateChars(array(12, 34)); // WHERE truncate_chars IN (12, 34)
     * $query->filterByTruncateChars(array('min' => 12)); // WHERE truncate_chars >= 12
     * $query->filterByTruncateChars(array('max' => 12)); // WHERE truncate_chars <= 12
     * </code>
     *
     * @param     mixed $truncateChars The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ListerConfigColumnQuery The current query, for fluid interface
     */
    public function filterByTruncateChars($truncateChars = null, $comparison = null)
    {
        if (is_array($truncateChars)) {
            $useMinMax = false;
            if (isset($truncateChars['min'])) {
                $this->addUsingAlias(ListerConfigColumnPeer::TRUNCATE_CHARS, $truncateChars['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($truncateChars['max'])) {
                $this->addUsingAlias(ListerConfigColumnPeer::TRUNCATE_CHARS, $truncateChars['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ListerConfigColumnPeer::TRUNCATE_CHARS, $truncateChars, $comparison);
    }

    /**
     * Filter the query on the tooltip_js_expression column
     *
     * Example usage:
     * <code>
     * $query->filterByTooltipJsExpression('fooValue');   // WHERE tooltip_js_expression = 'fooValue'
     * $query->filterByTooltipJsExpression('%fooValue%'); // WHERE tooltip_js_expression LIKE '%fooValue%'
     * </code>
     *
     * @param     string $tooltipJsExpression The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ListerConfigColumnQuery The current query, for fluid interface
     */
    public function filterByTooltipJsExpression($tooltipJsExpression = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($tooltipJsExpression)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $tooltipJsExpression)) {
                $tooltipJsExpression = str_replace('*', '%', $tooltipJsExpression);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ListerConfigColumnPeer::TOOLTIP_JS_EXPRESSION, $tooltipJsExpression, $comparison);
    }

    /**
     * Filter the query on the tooltip_url_js_expression column
     *
     * Example usage:
     * <code>
     * $query->filterByTooltipUrlJsExpression('fooValue');   // WHERE tooltip_url_js_expression = 'fooValue'
     * $query->filterByTooltipUrlJsExpression('%fooValue%'); // WHERE tooltip_url_js_expression LIKE '%fooValue%'
     * </code>
     *
     * @param     string $tooltipUrlJsExpression The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ListerConfigColumnQuery The current query, for fluid interface
     */
    public function filterByTooltipUrlJsExpression($tooltipUrlJsExpression = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($tooltipUrlJsExpression)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $tooltipUrlJsExpression)) {
                $tooltipUrlJsExpression = str_replace('*', '%', $tooltipUrlJsExpression);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ListerConfigColumnPeer::TOOLTIP_URL_JS_EXPRESSION, $tooltipUrlJsExpression, $comparison);
    }

    /**
     * Filter the query on the tooltip_max_width column
     *
     * Example usage:
     * <code>
     * $query->filterByTooltipMaxWidth(1234); // WHERE tooltip_max_width = 1234
     * $query->filterByTooltipMaxWidth(array(12, 34)); // WHERE tooltip_max_width IN (12, 34)
     * $query->filterByTooltipMaxWidth(array('min' => 12)); // WHERE tooltip_max_width >= 12
     * $query->filterByTooltipMaxWidth(array('max' => 12)); // WHERE tooltip_max_width <= 12
     * </code>
     *
     * @param     mixed $tooltipMaxWidth The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ListerConfigColumnQuery The current query, for fluid interface
     */
    public function filterByTooltipMaxWidth($tooltipMaxWidth = null, $comparison = null)
    {
        if (is_array($tooltipMaxWidth)) {
            $useMinMax = false;
            if (isset($tooltipMaxWidth['min'])) {
                $this->addUsingAlias(ListerConfigColumnPeer::TOOLTIP_MAX_WIDTH, $tooltipMaxWidth['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($tooltipMaxWidth['max'])) {
                $this->addUsingAlias(ListerConfigColumnPeer::TOOLTIP_MAX_WIDTH, $tooltipMaxWidth['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ListerConfigColumnPeer::TOOLTIP_MAX_WIDTH, $tooltipMaxWidth, $comparison);
    }

    /**
     * Filter the query on the tooltip_delay_msec column
     *
     * Example usage:
     * <code>
     * $query->filterByTooltipDelayMsec(1234); // WHERE tooltip_delay_msec = 1234
     * $query->filterByTooltipDelayMsec(array(12, 34)); // WHERE tooltip_delay_msec IN (12, 34)
     * $query->filterByTooltipDelayMsec(array('min' => 12)); // WHERE tooltip_delay_msec >= 12
     * $query->filterByTooltipDelayMsec(array('max' => 12)); // WHERE tooltip_delay_msec <= 12
     * </code>
     *
     * @param     mixed $tooltipDelayMsec The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ListerConfigColumnQuery The current query, for fluid interface
     */
    public function filterByTooltipDelayMsec($tooltipDelayMsec = null, $comparison = null)
    {
        if (is_array($tooltipDelayMsec)) {
            $useMinMax = false;
            if (isset($tooltipDelayMsec['min'])) {
                $this->addUsingAlias(ListerConfigColumnPeer::TOOLTIP_DELAY_MSEC, $tooltipDelayMsec['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($tooltipDelayMsec['max'])) {
                $this->addUsingAlias(ListerConfigColumnPeer::TOOLTIP_DELAY_MSEC, $tooltipDelayMsec['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ListerConfigColumnPeer::TOOLTIP_DELAY_MSEC, $tooltipDelayMsec, $comparison);
    }

    /**
     * Filter the query by a related ListerConfig object
     *
     * @param   ListerConfig|PropelObjectCollection $listerConfig The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ListerConfigColumnQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByListerConfig($listerConfig, $comparison = null)
    {
        if ($listerConfig instanceof ListerConfig) {
            return $this
                ->addUsingAlias(ListerConfigColumnPeer::LISTER_CONFIG_ID, $listerConfig->getListerConfigId(), $comparison);
        } elseif ($listerConfig instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ListerConfigColumnPeer::LISTER_CONFIG_ID, $listerConfig->toKeyValue('PrimaryKey', 'ListerConfigId'), $comparison);
        } else {
            throw new PropelException('filterByListerConfig() only accepts arguments of type ListerConfig or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ListerConfig relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ListerConfigColumnQuery The current query, for fluid interface
     */
    public function joinListerConfig($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ListerConfig');

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
            $this->addJoinObject($join, 'ListerConfig');
        }

        return $this;
    }

    /**
     * Use the ListerConfig relation ListerConfig object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\ListerConfigQuery A secondary query class using the current class as primary query
     */
    public function useListerConfigQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinListerConfig($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ListerConfig', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\ListerConfigQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ListerConfigColumn $listerConfigColumn Object to remove from the list of results
     *
     * @return ListerConfigColumnQuery The current query, for fluid interface
     */
    public function prune($listerConfigColumn = null)
    {
        if ($listerConfigColumn) {
            $this->addUsingAlias(ListerConfigColumnPeer::LISTER_CONFIG_COLUMN_ID, $listerConfigColumn->getListerConfigColumnId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

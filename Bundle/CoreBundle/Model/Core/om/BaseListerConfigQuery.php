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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\ListerConfigPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\ListerConfigQuery;

/**
 * @method ListerConfigQuery orderByListerConfigId($order = Criteria::ASC) Order by the lister_config_id column
 * @method ListerConfigQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method ListerConfigQuery orderByVariation($order = Criteria::ASC) Order by the variation column
 * @method ListerConfigQuery orderByFilterShowFlag($order = Criteria::ASC) Order by the filter_show_flag column
 * @method ListerConfigQuery orderByFilterServerId($order = Criteria::ASC) Order by the filter_server_id column
 *
 * @method ListerConfigQuery groupByListerConfigId() Group by the lister_config_id column
 * @method ListerConfigQuery groupByName() Group by the name column
 * @method ListerConfigQuery groupByVariation() Group by the variation column
 * @method ListerConfigQuery groupByFilterShowFlag() Group by the filter_show_flag column
 * @method ListerConfigQuery groupByFilterServerId() Group by the filter_server_id column
 *
 * @method ListerConfigQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method ListerConfigQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method ListerConfigQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method ListerConfigQuery leftJoinListerConfigColumn($relationAlias = null) Adds a LEFT JOIN clause to the query using the ListerConfigColumn relation
 * @method ListerConfigQuery rightJoinListerConfigColumn($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ListerConfigColumn relation
 * @method ListerConfigQuery innerJoinListerConfigColumn($relationAlias = null) Adds a INNER JOIN clause to the query using the ListerConfigColumn relation
 *
 * @method ListerConfig findOne(PropelPDO $con = null) Return the first ListerConfig matching the query
 * @method ListerConfig findOneOrCreate(PropelPDO $con = null) Return the first ListerConfig matching the query, or a new ListerConfig object populated from the query conditions when no match is found
 *
 * @method ListerConfig findOneByName(string $name) Return the first ListerConfig filtered by the name column
 * @method ListerConfig findOneByVariation(string $variation) Return the first ListerConfig filtered by the variation column
 * @method ListerConfig findOneByFilterShowFlag(boolean $filter_show_flag) Return the first ListerConfig filtered by the filter_show_flag column
 * @method ListerConfig findOneByFilterServerId(string $filter_server_id) Return the first ListerConfig filtered by the filter_server_id column
 *
 * @method array findByListerConfigId(int $lister_config_id) Return ListerConfig objects filtered by the lister_config_id column
 * @method array findByName(string $name) Return ListerConfig objects filtered by the name column
 * @method array findByVariation(string $variation) Return ListerConfig objects filtered by the variation column
 * @method array findByFilterShowFlag(boolean $filter_show_flag) Return ListerConfig objects filtered by the filter_show_flag column
 * @method array findByFilterServerId(string $filter_server_id) Return ListerConfig objects filtered by the filter_server_id column
 */
abstract class BaseListerConfigQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseListerConfigQuery object.
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
            $modelName = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\ListerConfig';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ListerConfigQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   ListerConfigQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return ListerConfigQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ListerConfigQuery) {
            return $criteria;
        }
        $query = new ListerConfigQuery(null, null, $modelAlias);

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
     * @return   ListerConfig|ListerConfig[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ListerConfigPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(ListerConfigPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 ListerConfig A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByListerConfigId($key, $con = null)
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
     * @return                 ListerConfig A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT lister_config_id, name, variation, filter_show_flag, filter_server_id FROM core.lister_config WHERE lister_config_id = :p0';
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
            $obj = new ListerConfig();
            $obj->hydrate($row);
            ListerConfigPeer::addInstanceToPool($obj, (string) $key);
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
     * @return ListerConfig|ListerConfig[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|ListerConfig[]|mixed the list of results, formatted by the current formatter
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
     * @return ListerConfig[]
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
     * @return ListerConfigQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ListerConfigPeer::LISTER_CONFIG_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ListerConfigQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ListerConfigPeer::LISTER_CONFIG_ID, $keys, Criteria::IN);
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
     * @param     mixed $listerConfigId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ListerConfigQuery The current query, for fluid interface
     */
    public function filterByListerConfigId($listerConfigId = null, $comparison = null)
    {
        if (is_array($listerConfigId)) {
            $useMinMax = false;
            if (isset($listerConfigId['min'])) {
                $this->addUsingAlias(ListerConfigPeer::LISTER_CONFIG_ID, $listerConfigId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($listerConfigId['max'])) {
                $this->addUsingAlias(ListerConfigPeer::LISTER_CONFIG_ID, $listerConfigId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ListerConfigPeer::LISTER_CONFIG_ID, $listerConfigId, $comparison);
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
     * @return ListerConfigQuery The current query, for fluid interface
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

        return $this->addUsingAlias(ListerConfigPeer::NAME, $name, $comparison);
    }

    /**
     * Filter the query on the variation column
     *
     * Example usage:
     * <code>
     * $query->filterByVariation('fooValue');   // WHERE variation = 'fooValue'
     * $query->filterByVariation('%fooValue%'); // WHERE variation LIKE '%fooValue%'
     * </code>
     *
     * @param     string $variation The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ListerConfigQuery The current query, for fluid interface
     */
    public function filterByVariation($variation = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($variation)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $variation)) {
                $variation = str_replace('*', '%', $variation);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ListerConfigPeer::VARIATION, $variation, $comparison);
    }

    /**
     * Filter the query on the filter_show_flag column
     *
     * Example usage:
     * <code>
     * $query->filterByFilterShowFlag(true); // WHERE filter_show_flag = true
     * $query->filterByFilterShowFlag('yes'); // WHERE filter_show_flag = true
     * </code>
     *
     * @param     boolean|string $filterShowFlag The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ListerConfigQuery The current query, for fluid interface
     */
    public function filterByFilterShowFlag($filterShowFlag = null, $comparison = null)
    {
        if (is_string($filterShowFlag)) {
            $filterShowFlag = in_array(strtolower($filterShowFlag), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(ListerConfigPeer::FILTER_SHOW_FLAG, $filterShowFlag, $comparison);
    }

    /**
     * Filter the query on the filter_server_id column
     *
     * Example usage:
     * <code>
     * $query->filterByFilterServerId('fooValue');   // WHERE filter_server_id = 'fooValue'
     * $query->filterByFilterServerId('%fooValue%'); // WHERE filter_server_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $filterServerId The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ListerConfigQuery The current query, for fluid interface
     */
    public function filterByFilterServerId($filterServerId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($filterServerId)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $filterServerId)) {
                $filterServerId = str_replace('*', '%', $filterServerId);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ListerConfigPeer::FILTER_SERVER_ID, $filterServerId, $comparison);
    }

    /**
     * Filter the query by a related ListerConfigColumn object
     *
     * @param   ListerConfigColumn|PropelObjectCollection $listerConfigColumn  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ListerConfigQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByListerConfigColumn($listerConfigColumn, $comparison = null)
    {
        if ($listerConfigColumn instanceof ListerConfigColumn) {
            return $this
                ->addUsingAlias(ListerConfigPeer::LISTER_CONFIG_ID, $listerConfigColumn->getListerConfigId(), $comparison);
        } elseif ($listerConfigColumn instanceof PropelObjectCollection) {
            return $this
                ->useListerConfigColumnQuery()
                ->filterByPrimaryKeys($listerConfigColumn->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByListerConfigColumn() only accepts arguments of type ListerConfigColumn or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ListerConfigColumn relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ListerConfigQuery The current query, for fluid interface
     */
    public function joinListerConfigColumn($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ListerConfigColumn');

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
            $this->addJoinObject($join, 'ListerConfigColumn');
        }

        return $this;
    }

    /**
     * Use the ListerConfigColumn relation ListerConfigColumn object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\ListerConfigColumnQuery A secondary query class using the current class as primary query
     */
    public function useListerConfigColumnQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinListerConfigColumn($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ListerConfigColumn', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\ListerConfigColumnQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ListerConfig $listerConfig Object to remove from the list of results
     *
     * @return ListerConfigQuery The current query, for fluid interface
     */
    public function prune($listerConfig = null)
    {
        if ($listerConfig) {
            $this->addUsingAlias(ListerConfigPeer::LISTER_CONFIG_ID, $listerConfig->getListerConfigId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\Lookup;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\LookupPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\LookupQuery;

/**
 * @method LookupQuery orderByLookupId($order = Criteria::ASC) Order by the lookup_id column
 * @method LookupQuery orderByDomainName($order = Criteria::ASC) Order by the domain_name column
 * @method LookupQuery orderByValue($order = Criteria::ASC) Order by the value column
 * @method LookupQuery orderByDecIt($order = Criteria::ASC) Order by the dec_it column
 * @method LookupQuery orderByDecEn($order = Criteria::ASC) Order by the dec_en column
 * @method LookupQuery orderByDecEs($order = Criteria::ASC) Order by the dec_es column
 * @method LookupQuery orderByDecPt($order = Criteria::ASC) Order by the dec_pt column
 * @method LookupQuery orderByDecEl($order = Criteria::ASC) Order by the dec_el column
 * @method LookupQuery orderBySortOrder($order = Criteria::ASC) Order by the sort_order column
 * @method LookupQuery orderBySchemaFilter($order = Criteria::ASC) Order by the schema_filter column
 * @method LookupQuery orderByFilter($order = Criteria::ASC) Order by the filter column
 * @method LookupQuery orderByExt($order = Criteria::ASC) Order by the ext column
 *
 * @method LookupQuery groupByLookupId() Group by the lookup_id column
 * @method LookupQuery groupByDomainName() Group by the domain_name column
 * @method LookupQuery groupByValue() Group by the value column
 * @method LookupQuery groupByDecIt() Group by the dec_it column
 * @method LookupQuery groupByDecEn() Group by the dec_en column
 * @method LookupQuery groupByDecEs() Group by the dec_es column
 * @method LookupQuery groupByDecPt() Group by the dec_pt column
 * @method LookupQuery groupByDecEl() Group by the dec_el column
 * @method LookupQuery groupBySortOrder() Group by the sort_order column
 * @method LookupQuery groupBySchemaFilter() Group by the schema_filter column
 * @method LookupQuery groupByFilter() Group by the filter column
 * @method LookupQuery groupByExt() Group by the ext column
 *
 * @method LookupQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method LookupQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method LookupQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method Lookup findOne(PropelPDO $con = null) Return the first Lookup matching the query
 * @method Lookup findOneOrCreate(PropelPDO $con = null) Return the first Lookup matching the query, or a new Lookup object populated from the query conditions when no match is found
 *
 * @method Lookup findOneByDomainName(string $domain_name) Return the first Lookup filtered by the domain_name column
 * @method Lookup findOneByValue(string $value) Return the first Lookup filtered by the value column
 * @method Lookup findOneByDecIt(string $dec_it) Return the first Lookup filtered by the dec_it column
 * @method Lookup findOneByDecEn(string $dec_en) Return the first Lookup filtered by the dec_en column
 * @method Lookup findOneByDecEs(string $dec_es) Return the first Lookup filtered by the dec_es column
 * @method Lookup findOneByDecPt(string $dec_pt) Return the first Lookup filtered by the dec_pt column
 * @method Lookup findOneByDecEl(string $dec_el) Return the first Lookup filtered by the dec_el column
 * @method Lookup findOneBySortOrder(int $sort_order) Return the first Lookup filtered by the sort_order column
 * @method Lookup findOneBySchemaFilter(string $schema_filter) Return the first Lookup filtered by the schema_filter column
 * @method Lookup findOneByFilter(string $filter) Return the first Lookup filtered by the filter column
 * @method Lookup findOneByExt(string $ext) Return the first Lookup filtered by the ext column
 *
 * @method array findByLookupId(int $lookup_id) Return Lookup objects filtered by the lookup_id column
 * @method array findByDomainName(string $domain_name) Return Lookup objects filtered by the domain_name column
 * @method array findByValue(string $value) Return Lookup objects filtered by the value column
 * @method array findByDecIt(string $dec_it) Return Lookup objects filtered by the dec_it column
 * @method array findByDecEn(string $dec_en) Return Lookup objects filtered by the dec_en column
 * @method array findByDecEs(string $dec_es) Return Lookup objects filtered by the dec_es column
 * @method array findByDecPt(string $dec_pt) Return Lookup objects filtered by the dec_pt column
 * @method array findByDecEl(string $dec_el) Return Lookup objects filtered by the dec_el column
 * @method array findBySortOrder(int $sort_order) Return Lookup objects filtered by the sort_order column
 * @method array findBySchemaFilter(string $schema_filter) Return Lookup objects filtered by the schema_filter column
 * @method array findByFilter(string $filter) Return Lookup objects filtered by the filter column
 * @method array findByExt(string $ext) Return Lookup objects filtered by the ext column
 */
abstract class BaseLookupQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseLookupQuery object.
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
            $modelName = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\Lookup';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new LookupQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   LookupQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return LookupQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof LookupQuery) {
            return $criteria;
        }
        $query = new LookupQuery(null, null, $modelAlias);

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
     * @return   Lookup|Lookup[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = LookupPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(LookupPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Lookup A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByLookupId($key, $con = null)
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
     * @return                 Lookup A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT lookup_id, domain_name, value, dec_it, dec_en, dec_es, dec_pt, dec_el, sort_order, schema_filter, filter, ext FROM core.lookup WHERE lookup_id = :p0';
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
            $obj = new Lookup();
            $obj->hydrate($row);
            LookupPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Lookup|Lookup[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Lookup[]|mixed the list of results, formatted by the current formatter
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
     * @return Lookup[]
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
     * @return LookupQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(LookupPeer::LOOKUP_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return LookupQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(LookupPeer::LOOKUP_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the lookup_id column
     *
     * Example usage:
     * <code>
     * $query->filterByLookupId(1234); // WHERE lookup_id = 1234
     * $query->filterByLookupId(array(12, 34)); // WHERE lookup_id IN (12, 34)
     * $query->filterByLookupId(array('min' => 12)); // WHERE lookup_id >= 12
     * $query->filterByLookupId(array('max' => 12)); // WHERE lookup_id <= 12
     * </code>
     *
     * @param     mixed $lookupId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LookupQuery The current query, for fluid interface
     */
    public function filterByLookupId($lookupId = null, $comparison = null)
    {
        if (is_array($lookupId)) {
            $useMinMax = false;
            if (isset($lookupId['min'])) {
                $this->addUsingAlias(LookupPeer::LOOKUP_ID, $lookupId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($lookupId['max'])) {
                $this->addUsingAlias(LookupPeer::LOOKUP_ID, $lookupId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LookupPeer::LOOKUP_ID, $lookupId, $comparison);
    }

    /**
     * Filter the query on the domain_name column
     *
     * Example usage:
     * <code>
     * $query->filterByDomainName('fooValue');   // WHERE domain_name = 'fooValue'
     * $query->filterByDomainName('%fooValue%'); // WHERE domain_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $domainName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LookupQuery The current query, for fluid interface
     */
    public function filterByDomainName($domainName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($domainName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $domainName)) {
                $domainName = str_replace('*', '%', $domainName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(LookupPeer::DOMAIN_NAME, $domainName, $comparison);
    }

    /**
     * Filter the query on the value column
     *
     * Example usage:
     * <code>
     * $query->filterByValue('fooValue');   // WHERE value = 'fooValue'
     * $query->filterByValue('%fooValue%'); // WHERE value LIKE '%fooValue%'
     * </code>
     *
     * @param     string $value The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LookupQuery The current query, for fluid interface
     */
    public function filterByValue($value = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($value)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $value)) {
                $value = str_replace('*', '%', $value);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(LookupPeer::VALUE, $value, $comparison);
    }

    /**
     * Filter the query on the dec_it column
     *
     * Example usage:
     * <code>
     * $query->filterByDecIt('fooValue');   // WHERE dec_it = 'fooValue'
     * $query->filterByDecIt('%fooValue%'); // WHERE dec_it LIKE '%fooValue%'
     * </code>
     *
     * @param     string $decIt The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LookupQuery The current query, for fluid interface
     */
    public function filterByDecIt($decIt = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($decIt)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $decIt)) {
                $decIt = str_replace('*', '%', $decIt);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(LookupPeer::DEC_IT, $decIt, $comparison);
    }

    /**
     * Filter the query on the dec_en column
     *
     * Example usage:
     * <code>
     * $query->filterByDecEn('fooValue');   // WHERE dec_en = 'fooValue'
     * $query->filterByDecEn('%fooValue%'); // WHERE dec_en LIKE '%fooValue%'
     * </code>
     *
     * @param     string $decEn The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LookupQuery The current query, for fluid interface
     */
    public function filterByDecEn($decEn = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($decEn)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $decEn)) {
                $decEn = str_replace('*', '%', $decEn);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(LookupPeer::DEC_EN, $decEn, $comparison);
    }

    /**
     * Filter the query on the dec_es column
     *
     * Example usage:
     * <code>
     * $query->filterByDecEs('fooValue');   // WHERE dec_es = 'fooValue'
     * $query->filterByDecEs('%fooValue%'); // WHERE dec_es LIKE '%fooValue%'
     * </code>
     *
     * @param     string $decEs The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LookupQuery The current query, for fluid interface
     */
    public function filterByDecEs($decEs = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($decEs)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $decEs)) {
                $decEs = str_replace('*', '%', $decEs);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(LookupPeer::DEC_ES, $decEs, $comparison);
    }

    /**
     * Filter the query on the dec_pt column
     *
     * Example usage:
     * <code>
     * $query->filterByDecPt('fooValue');   // WHERE dec_pt = 'fooValue'
     * $query->filterByDecPt('%fooValue%'); // WHERE dec_pt LIKE '%fooValue%'
     * </code>
     *
     * @param     string $decPt The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LookupQuery The current query, for fluid interface
     */
    public function filterByDecPt($decPt = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($decPt)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $decPt)) {
                $decPt = str_replace('*', '%', $decPt);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(LookupPeer::DEC_PT, $decPt, $comparison);
    }

    /**
     * Filter the query on the dec_el column
     *
     * Example usage:
     * <code>
     * $query->filterByDecEl('fooValue');   // WHERE dec_el = 'fooValue'
     * $query->filterByDecEl('%fooValue%'); // WHERE dec_el LIKE '%fooValue%'
     * </code>
     *
     * @param     string $decEl The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LookupQuery The current query, for fluid interface
     */
    public function filterByDecEl($decEl = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($decEl)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $decEl)) {
                $decEl = str_replace('*', '%', $decEl);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(LookupPeer::DEC_EL, $decEl, $comparison);
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
     * @return LookupQuery The current query, for fluid interface
     */
    public function filterBySortOrder($sortOrder = null, $comparison = null)
    {
        if (is_array($sortOrder)) {
            $useMinMax = false;
            if (isset($sortOrder['min'])) {
                $this->addUsingAlias(LookupPeer::SORT_ORDER, $sortOrder['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sortOrder['max'])) {
                $this->addUsingAlias(LookupPeer::SORT_ORDER, $sortOrder['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LookupPeer::SORT_ORDER, $sortOrder, $comparison);
    }

    /**
     * Filter the query on the schema_filter column
     *
     * Example usage:
     * <code>
     * $query->filterBySchemaFilter('fooValue');   // WHERE schema_filter = 'fooValue'
     * $query->filterBySchemaFilter('%fooValue%'); // WHERE schema_filter LIKE '%fooValue%'
     * </code>
     *
     * @param     string $schemaFilter The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LookupQuery The current query, for fluid interface
     */
    public function filterBySchemaFilter($schemaFilter = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($schemaFilter)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $schemaFilter)) {
                $schemaFilter = str_replace('*', '%', $schemaFilter);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(LookupPeer::SCHEMA_FILTER, $schemaFilter, $comparison);
    }

    /**
     * Filter the query on the filter column
     *
     * Example usage:
     * <code>
     * $query->filterByFilter('fooValue');   // WHERE filter = 'fooValue'
     * $query->filterByFilter('%fooValue%'); // WHERE filter LIKE '%fooValue%'
     * </code>
     *
     * @param     string $filter The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LookupQuery The current query, for fluid interface
     */
    public function filterByFilter($filter = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($filter)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $filter)) {
                $filter = str_replace('*', '%', $filter);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(LookupPeer::FILTER, $filter, $comparison);
    }

    /**
     * Filter the query on the ext column
     *
     * Example usage:
     * <code>
     * $query->filterByExt('fooValue');   // WHERE ext = 'fooValue'
     * $query->filterByExt('%fooValue%'); // WHERE ext LIKE '%fooValue%'
     * </code>
     *
     * @param     string $ext The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LookupQuery The current query, for fluid interface
     */
    public function filterByExt($ext = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($ext)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $ext)) {
                $ext = str_replace('*', '%', $ext);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(LookupPeer::EXT, $ext, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   Lookup $lookup Object to remove from the list of results
     *
     * @return LookupQuery The current query, for fluid interface
     */
    public function prune($lookup = null)
    {
        if ($lookup) {
            $this->addUsingAlias(LookupPeer::LOOKUP_ID, $lookup->getLookupId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

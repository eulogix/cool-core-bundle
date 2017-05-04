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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AppSetting;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AppSettingPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AppSettingQuery;

/**
 * @method AppSettingQuery orderByAppSettingId($order = Criteria::ASC) Order by the app_setting_id column
 * @method AppSettingQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method AppSettingQuery orderBySpace($order = Criteria::ASC) Order by the space column
 * @method AppSettingQuery orderByValue($order = Criteria::ASC) Order by the value column
 *
 * @method AppSettingQuery groupByAppSettingId() Group by the app_setting_id column
 * @method AppSettingQuery groupByName() Group by the name column
 * @method AppSettingQuery groupBySpace() Group by the space column
 * @method AppSettingQuery groupByValue() Group by the value column
 *
 * @method AppSettingQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method AppSettingQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method AppSettingQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method AppSetting findOne(PropelPDO $con = null) Return the first AppSetting matching the query
 * @method AppSetting findOneOrCreate(PropelPDO $con = null) Return the first AppSetting matching the query, or a new AppSetting object populated from the query conditions when no match is found
 *
 * @method AppSetting findOneByName(string $name) Return the first AppSetting filtered by the name column
 * @method AppSetting findOneBySpace(string $space) Return the first AppSetting filtered by the space column
 * @method AppSetting findOneByValue(string $value) Return the first AppSetting filtered by the value column
 *
 * @method array findByAppSettingId(int $app_setting_id) Return AppSetting objects filtered by the app_setting_id column
 * @method array findByName(string $name) Return AppSetting objects filtered by the name column
 * @method array findBySpace(string $space) Return AppSetting objects filtered by the space column
 * @method array findByValue(string $value) Return AppSetting objects filtered by the value column
 */
abstract class BaseAppSettingQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseAppSettingQuery object.
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
            $modelName = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AppSetting';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new AppSettingQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   AppSettingQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return AppSettingQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof AppSettingQuery) {
            return $criteria;
        }
        $query = new AppSettingQuery(null, null, $modelAlias);

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
     * @return   AppSetting|AppSetting[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = AppSettingPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(AppSettingPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 AppSetting A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByAppSettingId($key, $con = null)
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
     * @return                 AppSetting A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT app_setting_id, name, space, value FROM core.app_setting WHERE app_setting_id = :p0';
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
            $obj = new AppSetting();
            $obj->hydrate($row);
            AppSettingPeer::addInstanceToPool($obj, (string) $key);
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
     * @return AppSetting|AppSetting[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|AppSetting[]|mixed the list of results, formatted by the current formatter
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
     * @return AppSetting[]
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
     * @return AppSettingQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(AppSettingPeer::APP_SETTING_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return AppSettingQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(AppSettingPeer::APP_SETTING_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the app_setting_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAppSettingId(1234); // WHERE app_setting_id = 1234
     * $query->filterByAppSettingId(array(12, 34)); // WHERE app_setting_id IN (12, 34)
     * $query->filterByAppSettingId(array('min' => 12)); // WHERE app_setting_id >= 12
     * $query->filterByAppSettingId(array('max' => 12)); // WHERE app_setting_id <= 12
     * </code>
     *
     * @param     mixed $appSettingId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AppSettingQuery The current query, for fluid interface
     */
    public function filterByAppSettingId($appSettingId = null, $comparison = null)
    {
        if (is_array($appSettingId)) {
            $useMinMax = false;
            if (isset($appSettingId['min'])) {
                $this->addUsingAlias(AppSettingPeer::APP_SETTING_ID, $appSettingId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($appSettingId['max'])) {
                $this->addUsingAlias(AppSettingPeer::APP_SETTING_ID, $appSettingId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AppSettingPeer::APP_SETTING_ID, $appSettingId, $comparison);
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
     * @return AppSettingQuery The current query, for fluid interface
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

        return $this->addUsingAlias(AppSettingPeer::NAME, $name, $comparison);
    }

    /**
     * Filter the query on the space column
     *
     * Example usage:
     * <code>
     * $query->filterBySpace('fooValue');   // WHERE space = 'fooValue'
     * $query->filterBySpace('%fooValue%'); // WHERE space LIKE '%fooValue%'
     * </code>
     *
     * @param     string $space The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AppSettingQuery The current query, for fluid interface
     */
    public function filterBySpace($space = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($space)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $space)) {
                $space = str_replace('*', '%', $space);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AppSettingPeer::SPACE, $space, $comparison);
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
     * @return AppSettingQuery The current query, for fluid interface
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

        return $this->addUsingAlias(AppSettingPeer::VALUE, $value, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   AppSetting $appSetting Object to remove from the list of results
     *
     * @return AppSettingQuery The current query, for fluid interface
     */
    public function prune($appSetting = null)
    {
        if ($appSetting) {
            $this->addUsingAlias(AppSettingPeer::APP_SETTING_ID, $appSetting->getAppSettingId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

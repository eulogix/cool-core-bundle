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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\Translation;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\TranslationPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\TranslationQuery;

/**
 * @method TranslationQuery orderByTranslationId($order = Criteria::ASC) Order by the translation_id column
 * @method TranslationQuery orderByDomainName($order = Criteria::ASC) Order by the domain_name column
 * @method TranslationQuery orderByLocale($order = Criteria::ASC) Order by the locale column
 * @method TranslationQuery orderByToken($order = Criteria::ASC) Order by the token column
 * @method TranslationQuery orderByValue($order = Criteria::ASC) Order by the value column
 * @method TranslationQuery orderByUsedFlag($order = Criteria::ASC) Order by the used_flag column
 * @method TranslationQuery orderByActiveFlag($order = Criteria::ASC) Order by the active_flag column
 * @method TranslationQuery orderByExposeFlag($order = Criteria::ASC) Order by the expose_flag column
 * @method TranslationQuery orderByLastUsageDate($order = Criteria::ASC) Order by the last_usage_date column
 *
 * @method TranslationQuery groupByTranslationId() Group by the translation_id column
 * @method TranslationQuery groupByDomainName() Group by the domain_name column
 * @method TranslationQuery groupByLocale() Group by the locale column
 * @method TranslationQuery groupByToken() Group by the token column
 * @method TranslationQuery groupByValue() Group by the value column
 * @method TranslationQuery groupByUsedFlag() Group by the used_flag column
 * @method TranslationQuery groupByActiveFlag() Group by the active_flag column
 * @method TranslationQuery groupByExposeFlag() Group by the expose_flag column
 * @method TranslationQuery groupByLastUsageDate() Group by the last_usage_date column
 *
 * @method TranslationQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method TranslationQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method TranslationQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method Translation findOne(PropelPDO $con = null) Return the first Translation matching the query
 * @method Translation findOneOrCreate(PropelPDO $con = null) Return the first Translation matching the query, or a new Translation object populated from the query conditions when no match is found
 *
 * @method Translation findOneByDomainName(string $domain_name) Return the first Translation filtered by the domain_name column
 * @method Translation findOneByLocale(string $locale) Return the first Translation filtered by the locale column
 * @method Translation findOneByToken(string $token) Return the first Translation filtered by the token column
 * @method Translation findOneByValue(string $value) Return the first Translation filtered by the value column
 * @method Translation findOneByUsedFlag(boolean $used_flag) Return the first Translation filtered by the used_flag column
 * @method Translation findOneByActiveFlag(boolean $active_flag) Return the first Translation filtered by the active_flag column
 * @method Translation findOneByExposeFlag(boolean $expose_flag) Return the first Translation filtered by the expose_flag column
 * @method Translation findOneByLastUsageDate(string $last_usage_date) Return the first Translation filtered by the last_usage_date column
 *
 * @method array findByTranslationId(int $translation_id) Return Translation objects filtered by the translation_id column
 * @method array findByDomainName(string $domain_name) Return Translation objects filtered by the domain_name column
 * @method array findByLocale(string $locale) Return Translation objects filtered by the locale column
 * @method array findByToken(string $token) Return Translation objects filtered by the token column
 * @method array findByValue(string $value) Return Translation objects filtered by the value column
 * @method array findByUsedFlag(boolean $used_flag) Return Translation objects filtered by the used_flag column
 * @method array findByActiveFlag(boolean $active_flag) Return Translation objects filtered by the active_flag column
 * @method array findByExposeFlag(boolean $expose_flag) Return Translation objects filtered by the expose_flag column
 * @method array findByLastUsageDate(string $last_usage_date) Return Translation objects filtered by the last_usage_date column
 */
abstract class BaseTranslationQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseTranslationQuery object.
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
            $modelName = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\Translation';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new TranslationQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   TranslationQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return TranslationQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof TranslationQuery) {
            return $criteria;
        }
        $query = new TranslationQuery(null, null, $modelAlias);

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
     * @return   Translation|Translation[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = TranslationPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(TranslationPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Translation A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByTranslationId($key, $con = null)
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
     * @return                 Translation A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT translation_id, domain_name, locale, token, value, used_flag, active_flag, expose_flag, last_usage_date FROM core.translation WHERE translation_id = :p0';
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
            $obj = new Translation();
            $obj->hydrate($row);
            TranslationPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Translation|Translation[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Translation[]|mixed the list of results, formatted by the current formatter
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
     * @return Translation[]
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
     * @return TranslationQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(TranslationPeer::TRANSLATION_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return TranslationQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(TranslationPeer::TRANSLATION_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the translation_id column
     *
     * Example usage:
     * <code>
     * $query->filterByTranslationId(1234); // WHERE translation_id = 1234
     * $query->filterByTranslationId(array(12, 34)); // WHERE translation_id IN (12, 34)
     * $query->filterByTranslationId(array('min' => 12)); // WHERE translation_id >= 12
     * $query->filterByTranslationId(array('max' => 12)); // WHERE translation_id <= 12
     * </code>
     *
     * @param     mixed $translationId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TranslationQuery The current query, for fluid interface
     */
    public function filterByTranslationId($translationId = null, $comparison = null)
    {
        if (is_array($translationId)) {
            $useMinMax = false;
            if (isset($translationId['min'])) {
                $this->addUsingAlias(TranslationPeer::TRANSLATION_ID, $translationId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($translationId['max'])) {
                $this->addUsingAlias(TranslationPeer::TRANSLATION_ID, $translationId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TranslationPeer::TRANSLATION_ID, $translationId, $comparison);
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
     * @return TranslationQuery The current query, for fluid interface
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

        return $this->addUsingAlias(TranslationPeer::DOMAIN_NAME, $domainName, $comparison);
    }

    /**
     * Filter the query on the locale column
     *
     * Example usage:
     * <code>
     * $query->filterByLocale('fooValue');   // WHERE locale = 'fooValue'
     * $query->filterByLocale('%fooValue%'); // WHERE locale LIKE '%fooValue%'
     * </code>
     *
     * @param     string $locale The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TranslationQuery The current query, for fluid interface
     */
    public function filterByLocale($locale = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($locale)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $locale)) {
                $locale = str_replace('*', '%', $locale);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(TranslationPeer::LOCALE, $locale, $comparison);
    }

    /**
     * Filter the query on the token column
     *
     * Example usage:
     * <code>
     * $query->filterByToken('fooValue');   // WHERE token = 'fooValue'
     * $query->filterByToken('%fooValue%'); // WHERE token LIKE '%fooValue%'
     * </code>
     *
     * @param     string $token The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TranslationQuery The current query, for fluid interface
     */
    public function filterByToken($token = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($token)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $token)) {
                $token = str_replace('*', '%', $token);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(TranslationPeer::TOKEN, $token, $comparison);
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
     * @return TranslationQuery The current query, for fluid interface
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

        return $this->addUsingAlias(TranslationPeer::VALUE, $value, $comparison);
    }

    /**
     * Filter the query on the used_flag column
     *
     * Example usage:
     * <code>
     * $query->filterByUsedFlag(true); // WHERE used_flag = true
     * $query->filterByUsedFlag('yes'); // WHERE used_flag = true
     * </code>
     *
     * @param     boolean|string $usedFlag The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TranslationQuery The current query, for fluid interface
     */
    public function filterByUsedFlag($usedFlag = null, $comparison = null)
    {
        if (is_string($usedFlag)) {
            $usedFlag = in_array(strtolower($usedFlag), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(TranslationPeer::USED_FLAG, $usedFlag, $comparison);
    }

    /**
     * Filter the query on the active_flag column
     *
     * Example usage:
     * <code>
     * $query->filterByActiveFlag(true); // WHERE active_flag = true
     * $query->filterByActiveFlag('yes'); // WHERE active_flag = true
     * </code>
     *
     * @param     boolean|string $activeFlag The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TranslationQuery The current query, for fluid interface
     */
    public function filterByActiveFlag($activeFlag = null, $comparison = null)
    {
        if (is_string($activeFlag)) {
            $activeFlag = in_array(strtolower($activeFlag), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(TranslationPeer::ACTIVE_FLAG, $activeFlag, $comparison);
    }

    /**
     * Filter the query on the expose_flag column
     *
     * Example usage:
     * <code>
     * $query->filterByExposeFlag(true); // WHERE expose_flag = true
     * $query->filterByExposeFlag('yes'); // WHERE expose_flag = true
     * </code>
     *
     * @param     boolean|string $exposeFlag The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TranslationQuery The current query, for fluid interface
     */
    public function filterByExposeFlag($exposeFlag = null, $comparison = null)
    {
        if (is_string($exposeFlag)) {
            $exposeFlag = in_array(strtolower($exposeFlag), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(TranslationPeer::EXPOSE_FLAG, $exposeFlag, $comparison);
    }

    /**
     * Filter the query on the last_usage_date column
     *
     * Example usage:
     * <code>
     * $query->filterByLastUsageDate('2011-03-14'); // WHERE last_usage_date = '2011-03-14'
     * $query->filterByLastUsageDate('now'); // WHERE last_usage_date = '2011-03-14'
     * $query->filterByLastUsageDate(array('max' => 'yesterday')); // WHERE last_usage_date < '2011-03-13'
     * </code>
     *
     * @param     mixed $lastUsageDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TranslationQuery The current query, for fluid interface
     */
    public function filterByLastUsageDate($lastUsageDate = null, $comparison = null)
    {
        if (is_array($lastUsageDate)) {
            $useMinMax = false;
            if (isset($lastUsageDate['min'])) {
                $this->addUsingAlias(TranslationPeer::LAST_USAGE_DATE, $lastUsageDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($lastUsageDate['max'])) {
                $this->addUsingAlias(TranslationPeer::LAST_USAGE_DATE, $lastUsageDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TranslationPeer::LAST_USAGE_DATE, $lastUsageDate, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   Translation $translation Object to remove from the list of results
     *
     * @return TranslationQuery The current query, for fluid interface
     */
    public function prune($translation = null)
    {
        if ($translation) {
            $this->addUsingAlias(TranslationPeer::TRANSLATION_ID, $translation->getTranslationId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

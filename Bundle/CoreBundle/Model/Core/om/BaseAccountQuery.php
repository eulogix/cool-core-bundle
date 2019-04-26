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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\Account;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountGroupRef;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountProfileRef;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountQuery;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountSetting;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AsyncJob;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\PendingCall;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserNotification;

/**
 * @method AccountQuery orderByAccountId($order = Criteria::ASC) Order by the account_id column
 * @method AccountQuery orderByLoginName($order = Criteria::ASC) Order by the login_name column
 * @method AccountQuery orderByHashedPassword($order = Criteria::ASC) Order by the hashed_password column
 * @method AccountQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method AccountQuery orderByFirstName($order = Criteria::ASC) Order by the first_name column
 * @method AccountQuery orderByLastName($order = Criteria::ASC) Order by the last_name column
 * @method AccountQuery orderBySex($order = Criteria::ASC) Order by the sex column
 * @method AccountQuery orderByEmail($order = Criteria::ASC) Order by the email column
 * @method AccountQuery orderByTelephone($order = Criteria::ASC) Order by the telephone column
 * @method AccountQuery orderByMobile($order = Criteria::ASC) Order by the mobile column
 * @method AccountQuery orderByDefaultLocale($order = Criteria::ASC) Order by the default_locale column
 * @method AccountQuery orderByCompanyName($order = Criteria::ASC) Order by the company_name column
 * @method AccountQuery orderByValidity($order = Criteria::ASC) Order by the validity column
 * @method AccountQuery orderByRoles($order = Criteria::ASC) Order by the roles column
 * @method AccountQuery orderByLastPasswordUpdate($order = Criteria::ASC) Order by the last_password_update column
 * @method AccountQuery orderByValidateMethod($order = Criteria::ASC) Order by the validate_method column
 * @method AccountQuery orderByOffice($order = Criteria::ASC) Order by the office column
 *
 * @method AccountQuery groupByAccountId() Group by the account_id column
 * @method AccountQuery groupByLoginName() Group by the login_name column
 * @method AccountQuery groupByHashedPassword() Group by the hashed_password column
 * @method AccountQuery groupByType() Group by the type column
 * @method AccountQuery groupByFirstName() Group by the first_name column
 * @method AccountQuery groupByLastName() Group by the last_name column
 * @method AccountQuery groupBySex() Group by the sex column
 * @method AccountQuery groupByEmail() Group by the email column
 * @method AccountQuery groupByTelephone() Group by the telephone column
 * @method AccountQuery groupByMobile() Group by the mobile column
 * @method AccountQuery groupByDefaultLocale() Group by the default_locale column
 * @method AccountQuery groupByCompanyName() Group by the company_name column
 * @method AccountQuery groupByValidity() Group by the validity column
 * @method AccountQuery groupByRoles() Group by the roles column
 * @method AccountQuery groupByLastPasswordUpdate() Group by the last_password_update column
 * @method AccountQuery groupByValidateMethod() Group by the validate_method column
 * @method AccountQuery groupByOffice() Group by the office column
 *
 * @method AccountQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method AccountQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method AccountQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method AccountQuery leftJoinAccountSetting($relationAlias = null) Adds a LEFT JOIN clause to the query using the AccountSetting relation
 * @method AccountQuery rightJoinAccountSetting($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AccountSetting relation
 * @method AccountQuery innerJoinAccountSetting($relationAlias = null) Adds a INNER JOIN clause to the query using the AccountSetting relation
 *
 * @method AccountQuery leftJoinAccountProfileRef($relationAlias = null) Adds a LEFT JOIN clause to the query using the AccountProfileRef relation
 * @method AccountQuery rightJoinAccountProfileRef($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AccountProfileRef relation
 * @method AccountQuery innerJoinAccountProfileRef($relationAlias = null) Adds a INNER JOIN clause to the query using the AccountProfileRef relation
 *
 * @method AccountQuery leftJoinAccountGroupRef($relationAlias = null) Adds a LEFT JOIN clause to the query using the AccountGroupRef relation
 * @method AccountQuery rightJoinAccountGroupRef($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AccountGroupRef relation
 * @method AccountQuery innerJoinAccountGroupRef($relationAlias = null) Adds a INNER JOIN clause to the query using the AccountGroupRef relation
 *
 * @method AccountQuery leftJoinPendingCall($relationAlias = null) Adds a LEFT JOIN clause to the query using the PendingCall relation
 * @method AccountQuery rightJoinPendingCall($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PendingCall relation
 * @method AccountQuery innerJoinPendingCall($relationAlias = null) Adds a INNER JOIN clause to the query using the PendingCall relation
 *
 * @method AccountQuery leftJoinAsyncJobRelatedByIssuerUserId($relationAlias = null) Adds a LEFT JOIN clause to the query using the AsyncJobRelatedByIssuerUserId relation
 * @method AccountQuery rightJoinAsyncJobRelatedByIssuerUserId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AsyncJobRelatedByIssuerUserId relation
 * @method AccountQuery innerJoinAsyncJobRelatedByIssuerUserId($relationAlias = null) Adds a INNER JOIN clause to the query using the AsyncJobRelatedByIssuerUserId relation
 *
 * @method AccountQuery leftJoinUserNotificationRelatedByUserId($relationAlias = null) Adds a LEFT JOIN clause to the query using the UserNotificationRelatedByUserId relation
 * @method AccountQuery rightJoinUserNotificationRelatedByUserId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UserNotificationRelatedByUserId relation
 * @method AccountQuery innerJoinUserNotificationRelatedByUserId($relationAlias = null) Adds a INNER JOIN clause to the query using the UserNotificationRelatedByUserId relation
 *
 * @method AccountQuery leftJoinAsyncJobRelatedByCreationUserId($relationAlias = null) Adds a LEFT JOIN clause to the query using the AsyncJobRelatedByCreationUserId relation
 * @method AccountQuery rightJoinAsyncJobRelatedByCreationUserId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AsyncJobRelatedByCreationUserId relation
 * @method AccountQuery innerJoinAsyncJobRelatedByCreationUserId($relationAlias = null) Adds a INNER JOIN clause to the query using the AsyncJobRelatedByCreationUserId relation
 *
 * @method AccountQuery leftJoinAsyncJobRelatedByUpdateUserId($relationAlias = null) Adds a LEFT JOIN clause to the query using the AsyncJobRelatedByUpdateUserId relation
 * @method AccountQuery rightJoinAsyncJobRelatedByUpdateUserId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AsyncJobRelatedByUpdateUserId relation
 * @method AccountQuery innerJoinAsyncJobRelatedByUpdateUserId($relationAlias = null) Adds a INNER JOIN clause to the query using the AsyncJobRelatedByUpdateUserId relation
 *
 * @method AccountQuery leftJoinUserNotificationRelatedByCreationUserId($relationAlias = null) Adds a LEFT JOIN clause to the query using the UserNotificationRelatedByCreationUserId relation
 * @method AccountQuery rightJoinUserNotificationRelatedByCreationUserId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UserNotificationRelatedByCreationUserId relation
 * @method AccountQuery innerJoinUserNotificationRelatedByCreationUserId($relationAlias = null) Adds a INNER JOIN clause to the query using the UserNotificationRelatedByCreationUserId relation
 *
 * @method AccountQuery leftJoinUserNotificationRelatedByUpdateUserId($relationAlias = null) Adds a LEFT JOIN clause to the query using the UserNotificationRelatedByUpdateUserId relation
 * @method AccountQuery rightJoinUserNotificationRelatedByUpdateUserId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UserNotificationRelatedByUpdateUserId relation
 * @method AccountQuery innerJoinUserNotificationRelatedByUpdateUserId($relationAlias = null) Adds a INNER JOIN clause to the query using the UserNotificationRelatedByUpdateUserId relation
 *
 * @method Account findOne(PropelPDO $con = null) Return the first Account matching the query
 * @method Account findOneOrCreate(PropelPDO $con = null) Return the first Account matching the query, or a new Account object populated from the query conditions when no match is found
 *
 * @method Account findOneByLoginName(string $login_name) Return the first Account filtered by the login_name column
 * @method Account findOneByHashedPassword(string $hashed_password) Return the first Account filtered by the hashed_password column
 * @method Account findOneByType(string $type) Return the first Account filtered by the type column
 * @method Account findOneByFirstName(string $first_name) Return the first Account filtered by the first_name column
 * @method Account findOneByLastName(string $last_name) Return the first Account filtered by the last_name column
 * @method Account findOneBySex(string $sex) Return the first Account filtered by the sex column
 * @method Account findOneByEmail(string $email) Return the first Account filtered by the email column
 * @method Account findOneByTelephone(string $telephone) Return the first Account filtered by the telephone column
 * @method Account findOneByMobile(string $mobile) Return the first Account filtered by the mobile column
 * @method Account findOneByDefaultLocale(string $default_locale) Return the first Account filtered by the default_locale column
 * @method Account findOneByCompanyName(string $company_name) Return the first Account filtered by the company_name column
 * @method Account findOneByValidity(string $validity) Return the first Account filtered by the validity column
 * @method Account findOneByRoles(string $roles) Return the first Account filtered by the roles column
 * @method Account findOneByLastPasswordUpdate(string $last_password_update) Return the first Account filtered by the last_password_update column
 * @method Account findOneByValidateMethod(string $validate_method) Return the first Account filtered by the validate_method column
 * @method Account findOneByOffice(string $office) Return the first Account filtered by the office column
 *
 * @method array findByAccountId(int $account_id) Return Account objects filtered by the account_id column
 * @method array findByLoginName(string $login_name) Return Account objects filtered by the login_name column
 * @method array findByHashedPassword(string $hashed_password) Return Account objects filtered by the hashed_password column
 * @method array findByType(string $type) Return Account objects filtered by the type column
 * @method array findByFirstName(string $first_name) Return Account objects filtered by the first_name column
 * @method array findByLastName(string $last_name) Return Account objects filtered by the last_name column
 * @method array findBySex(string $sex) Return Account objects filtered by the sex column
 * @method array findByEmail(string $email) Return Account objects filtered by the email column
 * @method array findByTelephone(string $telephone) Return Account objects filtered by the telephone column
 * @method array findByMobile(string $mobile) Return Account objects filtered by the mobile column
 * @method array findByDefaultLocale(string $default_locale) Return Account objects filtered by the default_locale column
 * @method array findByCompanyName(string $company_name) Return Account objects filtered by the company_name column
 * @method array findByValidity(string $validity) Return Account objects filtered by the validity column
 * @method array findByRoles(string $roles) Return Account objects filtered by the roles column
 * @method array findByLastPasswordUpdate(string $last_password_update) Return Account objects filtered by the last_password_update column
 * @method array findByValidateMethod(string $validate_method) Return Account objects filtered by the validate_method column
 * @method array findByOffice(string $office) Return Account objects filtered by the office column
 */
abstract class BaseAccountQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseAccountQuery object.
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
            $modelName = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\Account';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new AccountQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   AccountQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return AccountQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof AccountQuery) {
            return $criteria;
        }
        $query = new AccountQuery(null, null, $modelAlias);

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
     * @return   Account|Account[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = AccountPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(AccountPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Account A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByAccountId($key, $con = null)
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
     * @return                 Account A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT account_id, login_name, hashed_password, type, first_name, last_name, sex, email, telephone, mobile, default_locale, company_name, validity, roles, last_password_update, validate_method, office FROM core.account WHERE account_id = :p0';
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
            $obj = new Account();
            $obj->hydrate($row);
            AccountPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Account|Account[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Account[]|mixed the list of results, formatted by the current formatter
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
     * @return Account[]
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
     * @return AccountQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(AccountPeer::ACCOUNT_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return AccountQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(AccountPeer::ACCOUNT_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the account_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAccountId(1234); // WHERE account_id = 1234
     * $query->filterByAccountId(array(12, 34)); // WHERE account_id IN (12, 34)
     * $query->filterByAccountId(array('min' => 12)); // WHERE account_id >= 12
     * $query->filterByAccountId(array('max' => 12)); // WHERE account_id <= 12
     * </code>
     *
     * @param     mixed $accountId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountQuery The current query, for fluid interface
     */
    public function filterByAccountId($accountId = null, $comparison = null)
    {
        if (is_array($accountId)) {
            $useMinMax = false;
            if (isset($accountId['min'])) {
                $this->addUsingAlias(AccountPeer::ACCOUNT_ID, $accountId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accountId['max'])) {
                $this->addUsingAlias(AccountPeer::ACCOUNT_ID, $accountId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AccountPeer::ACCOUNT_ID, $accountId, $comparison);
    }

    /**
     * Filter the query on the login_name column
     *
     * Example usage:
     * <code>
     * $query->filterByLoginName('fooValue');   // WHERE login_name = 'fooValue'
     * $query->filterByLoginName('%fooValue%'); // WHERE login_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $loginName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountQuery The current query, for fluid interface
     */
    public function filterByLoginName($loginName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($loginName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $loginName)) {
                $loginName = str_replace('*', '%', $loginName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AccountPeer::LOGIN_NAME, $loginName, $comparison);
    }

    /**
     * Filter the query on the hashed_password column
     *
     * Example usage:
     * <code>
     * $query->filterByHashedPassword('fooValue');   // WHERE hashed_password = 'fooValue'
     * $query->filterByHashedPassword('%fooValue%'); // WHERE hashed_password LIKE '%fooValue%'
     * </code>
     *
     * @param     string $hashedPassword The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountQuery The current query, for fluid interface
     */
    public function filterByHashedPassword($hashedPassword = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($hashedPassword)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $hashedPassword)) {
                $hashedPassword = str_replace('*', '%', $hashedPassword);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AccountPeer::HASHED_PASSWORD, $hashedPassword, $comparison);
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
     * @return AccountQuery The current query, for fluid interface
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

        return $this->addUsingAlias(AccountPeer::TYPE, $type, $comparison);
    }

    /**
     * Filter the query on the first_name column
     *
     * Example usage:
     * <code>
     * $query->filterByFirstName('fooValue');   // WHERE first_name = 'fooValue'
     * $query->filterByFirstName('%fooValue%'); // WHERE first_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $firstName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountQuery The current query, for fluid interface
     */
    public function filterByFirstName($firstName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($firstName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $firstName)) {
                $firstName = str_replace('*', '%', $firstName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AccountPeer::FIRST_NAME, $firstName, $comparison);
    }

    /**
     * Filter the query on the last_name column
     *
     * Example usage:
     * <code>
     * $query->filterByLastName('fooValue');   // WHERE last_name = 'fooValue'
     * $query->filterByLastName('%fooValue%'); // WHERE last_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $lastName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountQuery The current query, for fluid interface
     */
    public function filterByLastName($lastName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($lastName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $lastName)) {
                $lastName = str_replace('*', '%', $lastName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AccountPeer::LAST_NAME, $lastName, $comparison);
    }

    /**
     * Filter the query on the sex column
     *
     * Example usage:
     * <code>
     * $query->filterBySex('fooValue');   // WHERE sex = 'fooValue'
     * $query->filterBySex('%fooValue%'); // WHERE sex LIKE '%fooValue%'
     * </code>
     *
     * @param     string $sex The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountQuery The current query, for fluid interface
     */
    public function filterBySex($sex = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($sex)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $sex)) {
                $sex = str_replace('*', '%', $sex);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AccountPeer::SEX, $sex, $comparison);
    }

    /**
     * Filter the query on the email column
     *
     * Example usage:
     * <code>
     * $query->filterByEmail('fooValue');   // WHERE email = 'fooValue'
     * $query->filterByEmail('%fooValue%'); // WHERE email LIKE '%fooValue%'
     * </code>
     *
     * @param     string $email The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountQuery The current query, for fluid interface
     */
    public function filterByEmail($email = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($email)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $email)) {
                $email = str_replace('*', '%', $email);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AccountPeer::EMAIL, $email, $comparison);
    }

    /**
     * Filter the query on the telephone column
     *
     * Example usage:
     * <code>
     * $query->filterByTelephone('fooValue');   // WHERE telephone = 'fooValue'
     * $query->filterByTelephone('%fooValue%'); // WHERE telephone LIKE '%fooValue%'
     * </code>
     *
     * @param     string $telephone The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountQuery The current query, for fluid interface
     */
    public function filterByTelephone($telephone = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($telephone)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $telephone)) {
                $telephone = str_replace('*', '%', $telephone);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AccountPeer::TELEPHONE, $telephone, $comparison);
    }

    /**
     * Filter the query on the mobile column
     *
     * Example usage:
     * <code>
     * $query->filterByMobile('fooValue');   // WHERE mobile = 'fooValue'
     * $query->filterByMobile('%fooValue%'); // WHERE mobile LIKE '%fooValue%'
     * </code>
     *
     * @param     string $mobile The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountQuery The current query, for fluid interface
     */
    public function filterByMobile($mobile = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($mobile)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $mobile)) {
                $mobile = str_replace('*', '%', $mobile);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AccountPeer::MOBILE, $mobile, $comparison);
    }

    /**
     * Filter the query on the default_locale column
     *
     * Example usage:
     * <code>
     * $query->filterByDefaultLocale('fooValue');   // WHERE default_locale = 'fooValue'
     * $query->filterByDefaultLocale('%fooValue%'); // WHERE default_locale LIKE '%fooValue%'
     * </code>
     *
     * @param     string $defaultLocale The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountQuery The current query, for fluid interface
     */
    public function filterByDefaultLocale($defaultLocale = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($defaultLocale)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $defaultLocale)) {
                $defaultLocale = str_replace('*', '%', $defaultLocale);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AccountPeer::DEFAULT_LOCALE, $defaultLocale, $comparison);
    }

    /**
     * Filter the query on the company_name column
     *
     * Example usage:
     * <code>
     * $query->filterByCompanyName('fooValue');   // WHERE company_name = 'fooValue'
     * $query->filterByCompanyName('%fooValue%'); // WHERE company_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $companyName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountQuery The current query, for fluid interface
     */
    public function filterByCompanyName($companyName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($companyName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $companyName)) {
                $companyName = str_replace('*', '%', $companyName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AccountPeer::COMPANY_NAME, $companyName, $comparison);
    }

    /**
     * Filter the query on the validity column
     *
     * Example usage:
     * <code>
     * $query->filterByValidity('fooValue');   // WHERE validity = 'fooValue'
     * $query->filterByValidity('%fooValue%'); // WHERE validity LIKE '%fooValue%'
     * </code>
     *
     * @param     string $validity The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountQuery The current query, for fluid interface
     */
    public function filterByValidity($validity = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($validity)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $validity)) {
                $validity = str_replace('*', '%', $validity);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AccountPeer::VALIDITY, $validity, $comparison);
    }

    /**
     * Filter the query on the roles column
     *
     * Example usage:
     * <code>
     * $query->filterByRoles('fooValue');   // WHERE roles = 'fooValue'
     * $query->filterByRoles('%fooValue%'); // WHERE roles LIKE '%fooValue%'
     * </code>
     *
     * @param     string $roles The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountQuery The current query, for fluid interface
     */
    public function filterByRoles($roles = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($roles)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $roles)) {
                $roles = str_replace('*', '%', $roles);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AccountPeer::ROLES, $roles, $comparison);
    }

    /**
     * Filter the query on the last_password_update column
     *
     * Example usage:
     * <code>
     * $query->filterByLastPasswordUpdate('2011-03-14'); // WHERE last_password_update = '2011-03-14'
     * $query->filterByLastPasswordUpdate('now'); // WHERE last_password_update = '2011-03-14'
     * $query->filterByLastPasswordUpdate(array('max' => 'yesterday')); // WHERE last_password_update < '2011-03-13'
     * </code>
     *
     * @param     mixed $lastPasswordUpdate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountQuery The current query, for fluid interface
     */
    public function filterByLastPasswordUpdate($lastPasswordUpdate = null, $comparison = null)
    {
        if (is_array($lastPasswordUpdate)) {
            $useMinMax = false;
            if (isset($lastPasswordUpdate['min'])) {
                $this->addUsingAlias(AccountPeer::LAST_PASSWORD_UPDATE, $lastPasswordUpdate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($lastPasswordUpdate['max'])) {
                $this->addUsingAlias(AccountPeer::LAST_PASSWORD_UPDATE, $lastPasswordUpdate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AccountPeer::LAST_PASSWORD_UPDATE, $lastPasswordUpdate, $comparison);
    }

    /**
     * Filter the query on the validate_method column
     *
     * Example usage:
     * <code>
     * $query->filterByValidateMethod('fooValue');   // WHERE validate_method = 'fooValue'
     * $query->filterByValidateMethod('%fooValue%'); // WHERE validate_method LIKE '%fooValue%'
     * </code>
     *
     * @param     string $validateMethod The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountQuery The current query, for fluid interface
     */
    public function filterByValidateMethod($validateMethod = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($validateMethod)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $validateMethod)) {
                $validateMethod = str_replace('*', '%', $validateMethod);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AccountPeer::VALIDATE_METHOD, $validateMethod, $comparison);
    }

    /**
     * Filter the query on the office column
     *
     * Example usage:
     * <code>
     * $query->filterByOffice('fooValue');   // WHERE office = 'fooValue'
     * $query->filterByOffice('%fooValue%'); // WHERE office LIKE '%fooValue%'
     * </code>
     *
     * @param     string $office The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AccountQuery The current query, for fluid interface
     */
    public function filterByOffice($office = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($office)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $office)) {
                $office = str_replace('*', '%', $office);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AccountPeer::OFFICE, $office, $comparison);
    }

    /**
     * Filter the query by a related AccountSetting object
     *
     * @param   AccountSetting|PropelObjectCollection $accountSetting  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AccountQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAccountSetting($accountSetting, $comparison = null)
    {
        if ($accountSetting instanceof AccountSetting) {
            return $this
                ->addUsingAlias(AccountPeer::ACCOUNT_ID, $accountSetting->getAccountId(), $comparison);
        } elseif ($accountSetting instanceof PropelObjectCollection) {
            return $this
                ->useAccountSettingQuery()
                ->filterByPrimaryKeys($accountSetting->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByAccountSetting() only accepts arguments of type AccountSetting or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AccountSetting relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return AccountQuery The current query, for fluid interface
     */
    public function joinAccountSetting($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AccountSetting');

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
            $this->addJoinObject($join, 'AccountSetting');
        }

        return $this;
    }

    /**
     * Use the AccountSetting relation AccountSetting object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountSettingQuery A secondary query class using the current class as primary query
     */
    public function useAccountSettingQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAccountSetting($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AccountSetting', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountSettingQuery');
    }

    /**
     * Filter the query by a related AccountProfileRef object
     *
     * @param   AccountProfileRef|PropelObjectCollection $accountProfileRef  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AccountQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAccountProfileRef($accountProfileRef, $comparison = null)
    {
        if ($accountProfileRef instanceof AccountProfileRef) {
            return $this
                ->addUsingAlias(AccountPeer::ACCOUNT_ID, $accountProfileRef->getAccountId(), $comparison);
        } elseif ($accountProfileRef instanceof PropelObjectCollection) {
            return $this
                ->useAccountProfileRefQuery()
                ->filterByPrimaryKeys($accountProfileRef->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByAccountProfileRef() only accepts arguments of type AccountProfileRef or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AccountProfileRef relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return AccountQuery The current query, for fluid interface
     */
    public function joinAccountProfileRef($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AccountProfileRef');

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
            $this->addJoinObject($join, 'AccountProfileRef');
        }

        return $this;
    }

    /**
     * Use the AccountProfileRef relation AccountProfileRef object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountProfileRefQuery A secondary query class using the current class as primary query
     */
    public function useAccountProfileRefQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAccountProfileRef($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AccountProfileRef', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountProfileRefQuery');
    }

    /**
     * Filter the query by a related AccountGroupRef object
     *
     * @param   AccountGroupRef|PropelObjectCollection $accountGroupRef  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AccountQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAccountGroupRef($accountGroupRef, $comparison = null)
    {
        if ($accountGroupRef instanceof AccountGroupRef) {
            return $this
                ->addUsingAlias(AccountPeer::ACCOUNT_ID, $accountGroupRef->getAccountId(), $comparison);
        } elseif ($accountGroupRef instanceof PropelObjectCollection) {
            return $this
                ->useAccountGroupRefQuery()
                ->filterByPrimaryKeys($accountGroupRef->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByAccountGroupRef() only accepts arguments of type AccountGroupRef or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AccountGroupRef relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return AccountQuery The current query, for fluid interface
     */
    public function joinAccountGroupRef($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AccountGroupRef');

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
            $this->addJoinObject($join, 'AccountGroupRef');
        }

        return $this;
    }

    /**
     * Use the AccountGroupRef relation AccountGroupRef object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountGroupRefQuery A secondary query class using the current class as primary query
     */
    public function useAccountGroupRefQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAccountGroupRef($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AccountGroupRef', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountGroupRefQuery');
    }

    /**
     * Filter the query by a related PendingCall object
     *
     * @param   PendingCall|PropelObjectCollection $pendingCall  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AccountQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPendingCall($pendingCall, $comparison = null)
    {
        if ($pendingCall instanceof PendingCall) {
            return $this
                ->addUsingAlias(AccountPeer::ACCOUNT_ID, $pendingCall->getCallerUserId(), $comparison);
        } elseif ($pendingCall instanceof PropelObjectCollection) {
            return $this
                ->usePendingCallQuery()
                ->filterByPrimaryKeys($pendingCall->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByPendingCall() only accepts arguments of type PendingCall or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PendingCall relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return AccountQuery The current query, for fluid interface
     */
    public function joinPendingCall($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PendingCall');

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
            $this->addJoinObject($join, 'PendingCall');
        }

        return $this;
    }

    /**
     * Use the PendingCall relation PendingCall object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\PendingCallQuery A secondary query class using the current class as primary query
     */
    public function usePendingCallQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinPendingCall($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PendingCall', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\PendingCallQuery');
    }

    /**
     * Filter the query by a related AsyncJob object
     *
     * @param   AsyncJob|PropelObjectCollection $asyncJob  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AccountQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAsyncJobRelatedByIssuerUserId($asyncJob, $comparison = null)
    {
        if ($asyncJob instanceof AsyncJob) {
            return $this
                ->addUsingAlias(AccountPeer::ACCOUNT_ID, $asyncJob->getIssuerUserId(), $comparison);
        } elseif ($asyncJob instanceof PropelObjectCollection) {
            return $this
                ->useAsyncJobRelatedByIssuerUserIdQuery()
                ->filterByPrimaryKeys($asyncJob->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByAsyncJobRelatedByIssuerUserId() only accepts arguments of type AsyncJob or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AsyncJobRelatedByIssuerUserId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return AccountQuery The current query, for fluid interface
     */
    public function joinAsyncJobRelatedByIssuerUserId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AsyncJobRelatedByIssuerUserId');

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
            $this->addJoinObject($join, 'AsyncJobRelatedByIssuerUserId');
        }

        return $this;
    }

    /**
     * Use the AsyncJobRelatedByIssuerUserId relation AsyncJob object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\AsyncJobQuery A secondary query class using the current class as primary query
     */
    public function useAsyncJobRelatedByIssuerUserIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinAsyncJobRelatedByIssuerUserId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AsyncJobRelatedByIssuerUserId', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\AsyncJobQuery');
    }

    /**
     * Filter the query by a related UserNotification object
     *
     * @param   UserNotification|PropelObjectCollection $userNotification  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AccountQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByUserNotificationRelatedByUserId($userNotification, $comparison = null)
    {
        if ($userNotification instanceof UserNotification) {
            return $this
                ->addUsingAlias(AccountPeer::ACCOUNT_ID, $userNotification->getUserId(), $comparison);
        } elseif ($userNotification instanceof PropelObjectCollection) {
            return $this
                ->useUserNotificationRelatedByUserIdQuery()
                ->filterByPrimaryKeys($userNotification->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByUserNotificationRelatedByUserId() only accepts arguments of type UserNotification or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the UserNotificationRelatedByUserId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return AccountQuery The current query, for fluid interface
     */
    public function joinUserNotificationRelatedByUserId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('UserNotificationRelatedByUserId');

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
            $this->addJoinObject($join, 'UserNotificationRelatedByUserId');
        }

        return $this;
    }

    /**
     * Use the UserNotificationRelatedByUserId relation UserNotification object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserNotificationQuery A secondary query class using the current class as primary query
     */
    public function useUserNotificationRelatedByUserIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinUserNotificationRelatedByUserId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'UserNotificationRelatedByUserId', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserNotificationQuery');
    }

    /**
     * Filter the query by a related AsyncJob object
     *
     * @param   AsyncJob|PropelObjectCollection $asyncJob  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AccountQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAsyncJobRelatedByCreationUserId($asyncJob, $comparison = null)
    {
        if ($asyncJob instanceof AsyncJob) {
            return $this
                ->addUsingAlias(AccountPeer::ACCOUNT_ID, $asyncJob->getCreationUserId(), $comparison);
        } elseif ($asyncJob instanceof PropelObjectCollection) {
            return $this
                ->useAsyncJobRelatedByCreationUserIdQuery()
                ->filterByPrimaryKeys($asyncJob->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByAsyncJobRelatedByCreationUserId() only accepts arguments of type AsyncJob or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AsyncJobRelatedByCreationUserId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return AccountQuery The current query, for fluid interface
     */
    public function joinAsyncJobRelatedByCreationUserId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AsyncJobRelatedByCreationUserId');

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
            $this->addJoinObject($join, 'AsyncJobRelatedByCreationUserId');
        }

        return $this;
    }

    /**
     * Use the AsyncJobRelatedByCreationUserId relation AsyncJob object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\AsyncJobQuery A secondary query class using the current class as primary query
     */
    public function useAsyncJobRelatedByCreationUserIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinAsyncJobRelatedByCreationUserId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AsyncJobRelatedByCreationUserId', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\AsyncJobQuery');
    }

    /**
     * Filter the query by a related AsyncJob object
     *
     * @param   AsyncJob|PropelObjectCollection $asyncJob  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AccountQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAsyncJobRelatedByUpdateUserId($asyncJob, $comparison = null)
    {
        if ($asyncJob instanceof AsyncJob) {
            return $this
                ->addUsingAlias(AccountPeer::ACCOUNT_ID, $asyncJob->getUpdateUserId(), $comparison);
        } elseif ($asyncJob instanceof PropelObjectCollection) {
            return $this
                ->useAsyncJobRelatedByUpdateUserIdQuery()
                ->filterByPrimaryKeys($asyncJob->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByAsyncJobRelatedByUpdateUserId() only accepts arguments of type AsyncJob or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AsyncJobRelatedByUpdateUserId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return AccountQuery The current query, for fluid interface
     */
    public function joinAsyncJobRelatedByUpdateUserId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AsyncJobRelatedByUpdateUserId');

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
            $this->addJoinObject($join, 'AsyncJobRelatedByUpdateUserId');
        }

        return $this;
    }

    /**
     * Use the AsyncJobRelatedByUpdateUserId relation AsyncJob object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\AsyncJobQuery A secondary query class using the current class as primary query
     */
    public function useAsyncJobRelatedByUpdateUserIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinAsyncJobRelatedByUpdateUserId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AsyncJobRelatedByUpdateUserId', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\AsyncJobQuery');
    }

    /**
     * Filter the query by a related UserNotification object
     *
     * @param   UserNotification|PropelObjectCollection $userNotification  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AccountQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByUserNotificationRelatedByCreationUserId($userNotification, $comparison = null)
    {
        if ($userNotification instanceof UserNotification) {
            return $this
                ->addUsingAlias(AccountPeer::ACCOUNT_ID, $userNotification->getCreationUserId(), $comparison);
        } elseif ($userNotification instanceof PropelObjectCollection) {
            return $this
                ->useUserNotificationRelatedByCreationUserIdQuery()
                ->filterByPrimaryKeys($userNotification->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByUserNotificationRelatedByCreationUserId() only accepts arguments of type UserNotification or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the UserNotificationRelatedByCreationUserId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return AccountQuery The current query, for fluid interface
     */
    public function joinUserNotificationRelatedByCreationUserId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('UserNotificationRelatedByCreationUserId');

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
            $this->addJoinObject($join, 'UserNotificationRelatedByCreationUserId');
        }

        return $this;
    }

    /**
     * Use the UserNotificationRelatedByCreationUserId relation UserNotification object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserNotificationQuery A secondary query class using the current class as primary query
     */
    public function useUserNotificationRelatedByCreationUserIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinUserNotificationRelatedByCreationUserId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'UserNotificationRelatedByCreationUserId', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserNotificationQuery');
    }

    /**
     * Filter the query by a related UserNotification object
     *
     * @param   UserNotification|PropelObjectCollection $userNotification  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 AccountQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByUserNotificationRelatedByUpdateUserId($userNotification, $comparison = null)
    {
        if ($userNotification instanceof UserNotification) {
            return $this
                ->addUsingAlias(AccountPeer::ACCOUNT_ID, $userNotification->getUpdateUserId(), $comparison);
        } elseif ($userNotification instanceof PropelObjectCollection) {
            return $this
                ->useUserNotificationRelatedByUpdateUserIdQuery()
                ->filterByPrimaryKeys($userNotification->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByUserNotificationRelatedByUpdateUserId() only accepts arguments of type UserNotification or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the UserNotificationRelatedByUpdateUserId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return AccountQuery The current query, for fluid interface
     */
    public function joinUserNotificationRelatedByUpdateUserId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('UserNotificationRelatedByUpdateUserId');

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
            $this->addJoinObject($join, 'UserNotificationRelatedByUpdateUserId');
        }

        return $this;
    }

    /**
     * Use the UserNotificationRelatedByUpdateUserId relation UserNotification object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserNotificationQuery A secondary query class using the current class as primary query
     */
    public function useUserNotificationRelatedByUpdateUserIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinUserNotificationRelatedByUpdateUserId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'UserNotificationRelatedByUpdateUserId', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserNotificationQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Account $account Object to remove from the list of results
     *
     * @return AccountQuery The current query, for fluid interface
     */
    public function prune($account = null)
    {
        if ($account) {
            $this->addUsingAlias(AccountPeer::ACCOUNT_ID, $account->getAccountId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

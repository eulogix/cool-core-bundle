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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\FormConfig;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\FormConfigField;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\FormConfigFieldPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\FormConfigFieldQuery;

/**
 * @method FormConfigFieldQuery orderByFormConfigFieldId($order = Criteria::ASC) Order by the form_config_field_id column
 * @method FormConfigFieldQuery orderByFormConfigId($order = Criteria::ASC) Order by the form_config_id column
 * @method FormConfigFieldQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method FormConfigFieldQuery orderByReadOnlyFlag($order = Criteria::ASC) Order by the read_only_flag column
 * @method FormConfigFieldQuery orderByHiddenFlag($order = Criteria::ASC) Order by the hidden_flag column
 * @method FormConfigFieldQuery orderByWidth($order = Criteria::ASC) Order by the width column
 * @method FormConfigFieldQuery orderByHeight($order = Criteria::ASC) Order by the height column
 *
 * @method FormConfigFieldQuery groupByFormConfigFieldId() Group by the form_config_field_id column
 * @method FormConfigFieldQuery groupByFormConfigId() Group by the form_config_id column
 * @method FormConfigFieldQuery groupByName() Group by the name column
 * @method FormConfigFieldQuery groupByReadOnlyFlag() Group by the read_only_flag column
 * @method FormConfigFieldQuery groupByHiddenFlag() Group by the hidden_flag column
 * @method FormConfigFieldQuery groupByWidth() Group by the width column
 * @method FormConfigFieldQuery groupByHeight() Group by the height column
 *
 * @method FormConfigFieldQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method FormConfigFieldQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method FormConfigFieldQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method FormConfigFieldQuery leftJoinFormConfig($relationAlias = null) Adds a LEFT JOIN clause to the query using the FormConfig relation
 * @method FormConfigFieldQuery rightJoinFormConfig($relationAlias = null) Adds a RIGHT JOIN clause to the query using the FormConfig relation
 * @method FormConfigFieldQuery innerJoinFormConfig($relationAlias = null) Adds a INNER JOIN clause to the query using the FormConfig relation
 *
 * @method FormConfigField findOne(PropelPDO $con = null) Return the first FormConfigField matching the query
 * @method FormConfigField findOneOrCreate(PropelPDO $con = null) Return the first FormConfigField matching the query, or a new FormConfigField object populated from the query conditions when no match is found
 *
 * @method FormConfigField findOneByFormConfigId(int $form_config_id) Return the first FormConfigField filtered by the form_config_id column
 * @method FormConfigField findOneByName(string $name) Return the first FormConfigField filtered by the name column
 * @method FormConfigField findOneByReadOnlyFlag(boolean $read_only_flag) Return the first FormConfigField filtered by the read_only_flag column
 * @method FormConfigField findOneByHiddenFlag(boolean $hidden_flag) Return the first FormConfigField filtered by the hidden_flag column
 * @method FormConfigField findOneByWidth(string $width) Return the first FormConfigField filtered by the width column
 * @method FormConfigField findOneByHeight(string $height) Return the first FormConfigField filtered by the height column
 *
 * @method array findByFormConfigFieldId(int $form_config_field_id) Return FormConfigField objects filtered by the form_config_field_id column
 * @method array findByFormConfigId(int $form_config_id) Return FormConfigField objects filtered by the form_config_id column
 * @method array findByName(string $name) Return FormConfigField objects filtered by the name column
 * @method array findByReadOnlyFlag(boolean $read_only_flag) Return FormConfigField objects filtered by the read_only_flag column
 * @method array findByHiddenFlag(boolean $hidden_flag) Return FormConfigField objects filtered by the hidden_flag column
 * @method array findByWidth(string $width) Return FormConfigField objects filtered by the width column
 * @method array findByHeight(string $height) Return FormConfigField objects filtered by the height column
 */
abstract class BaseFormConfigFieldQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseFormConfigFieldQuery object.
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
            $modelName = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\FormConfigField';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new FormConfigFieldQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   FormConfigFieldQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return FormConfigFieldQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof FormConfigFieldQuery) {
            return $criteria;
        }
        $query = new FormConfigFieldQuery(null, null, $modelAlias);

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
     * @return   FormConfigField|FormConfigField[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = FormConfigFieldPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(FormConfigFieldPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 FormConfigField A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByFormConfigFieldId($key, $con = null)
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
     * @return                 FormConfigField A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT form_config_field_id, form_config_id, name, read_only_flag, hidden_flag, width, height FROM core.form_config_field WHERE form_config_field_id = :p0';
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
            $obj = new FormConfigField();
            $obj->hydrate($row);
            FormConfigFieldPeer::addInstanceToPool($obj, (string) $key);
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
     * @return FormConfigField|FormConfigField[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|FormConfigField[]|mixed the list of results, formatted by the current formatter
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
     * @return FormConfigField[]
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
     * @return FormConfigFieldQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(FormConfigFieldPeer::FORM_CONFIG_FIELD_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return FormConfigFieldQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(FormConfigFieldPeer::FORM_CONFIG_FIELD_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the form_config_field_id column
     *
     * Example usage:
     * <code>
     * $query->filterByFormConfigFieldId(1234); // WHERE form_config_field_id = 1234
     * $query->filterByFormConfigFieldId(array(12, 34)); // WHERE form_config_field_id IN (12, 34)
     * $query->filterByFormConfigFieldId(array('min' => 12)); // WHERE form_config_field_id >= 12
     * $query->filterByFormConfigFieldId(array('max' => 12)); // WHERE form_config_field_id <= 12
     * </code>
     *
     * @param     mixed $formConfigFieldId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FormConfigFieldQuery The current query, for fluid interface
     */
    public function filterByFormConfigFieldId($formConfigFieldId = null, $comparison = null)
    {
        if (is_array($formConfigFieldId)) {
            $useMinMax = false;
            if (isset($formConfigFieldId['min'])) {
                $this->addUsingAlias(FormConfigFieldPeer::FORM_CONFIG_FIELD_ID, $formConfigFieldId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($formConfigFieldId['max'])) {
                $this->addUsingAlias(FormConfigFieldPeer::FORM_CONFIG_FIELD_ID, $formConfigFieldId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FormConfigFieldPeer::FORM_CONFIG_FIELD_ID, $formConfigFieldId, $comparison);
    }

    /**
     * Filter the query on the form_config_id column
     *
     * Example usage:
     * <code>
     * $query->filterByFormConfigId(1234); // WHERE form_config_id = 1234
     * $query->filterByFormConfigId(array(12, 34)); // WHERE form_config_id IN (12, 34)
     * $query->filterByFormConfigId(array('min' => 12)); // WHERE form_config_id >= 12
     * $query->filterByFormConfigId(array('max' => 12)); // WHERE form_config_id <= 12
     * </code>
     *
     * @see       filterByFormConfig()
     *
     * @param     mixed $formConfigId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FormConfigFieldQuery The current query, for fluid interface
     */
    public function filterByFormConfigId($formConfigId = null, $comparison = null)
    {
        if (is_array($formConfigId)) {
            $useMinMax = false;
            if (isset($formConfigId['min'])) {
                $this->addUsingAlias(FormConfigFieldPeer::FORM_CONFIG_ID, $formConfigId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($formConfigId['max'])) {
                $this->addUsingAlias(FormConfigFieldPeer::FORM_CONFIG_ID, $formConfigId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FormConfigFieldPeer::FORM_CONFIG_ID, $formConfigId, $comparison);
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
     * @return FormConfigFieldQuery The current query, for fluid interface
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

        return $this->addUsingAlias(FormConfigFieldPeer::NAME, $name, $comparison);
    }

    /**
     * Filter the query on the read_only_flag column
     *
     * Example usage:
     * <code>
     * $query->filterByReadOnlyFlag(true); // WHERE read_only_flag = true
     * $query->filterByReadOnlyFlag('yes'); // WHERE read_only_flag = true
     * </code>
     *
     * @param     boolean|string $readOnlyFlag The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FormConfigFieldQuery The current query, for fluid interface
     */
    public function filterByReadOnlyFlag($readOnlyFlag = null, $comparison = null)
    {
        if (is_string($readOnlyFlag)) {
            $readOnlyFlag = in_array(strtolower($readOnlyFlag), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(FormConfigFieldPeer::READ_ONLY_FLAG, $readOnlyFlag, $comparison);
    }

    /**
     * Filter the query on the hidden_flag column
     *
     * Example usage:
     * <code>
     * $query->filterByHiddenFlag(true); // WHERE hidden_flag = true
     * $query->filterByHiddenFlag('yes'); // WHERE hidden_flag = true
     * </code>
     *
     * @param     boolean|string $hiddenFlag The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FormConfigFieldQuery The current query, for fluid interface
     */
    public function filterByHiddenFlag($hiddenFlag = null, $comparison = null)
    {
        if (is_string($hiddenFlag)) {
            $hiddenFlag = in_array(strtolower($hiddenFlag), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(FormConfigFieldPeer::HIDDEN_FLAG, $hiddenFlag, $comparison);
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
     * @return FormConfigFieldQuery The current query, for fluid interface
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

        return $this->addUsingAlias(FormConfigFieldPeer::WIDTH, $width, $comparison);
    }

    /**
     * Filter the query on the height column
     *
     * Example usage:
     * <code>
     * $query->filterByHeight('fooValue');   // WHERE height = 'fooValue'
     * $query->filterByHeight('%fooValue%'); // WHERE height LIKE '%fooValue%'
     * </code>
     *
     * @param     string $height The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FormConfigFieldQuery The current query, for fluid interface
     */
    public function filterByHeight($height = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($height)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $height)) {
                $height = str_replace('*', '%', $height);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(FormConfigFieldPeer::HEIGHT, $height, $comparison);
    }

    /**
     * Filter the query by a related FormConfig object
     *
     * @param   FormConfig|PropelObjectCollection $formConfig The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 FormConfigFieldQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByFormConfig($formConfig, $comparison = null)
    {
        if ($formConfig instanceof FormConfig) {
            return $this
                ->addUsingAlias(FormConfigFieldPeer::FORM_CONFIG_ID, $formConfig->getFormConfigId(), $comparison);
        } elseif ($formConfig instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(FormConfigFieldPeer::FORM_CONFIG_ID, $formConfig->toKeyValue('PrimaryKey', 'FormConfigId'), $comparison);
        } else {
            throw new PropelException('filterByFormConfig() only accepts arguments of type FormConfig or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the FormConfig relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return FormConfigFieldQuery The current query, for fluid interface
     */
    public function joinFormConfig($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('FormConfig');

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
            $this->addJoinObject($join, 'FormConfig');
        }

        return $this;
    }

    /**
     * Use the FormConfig relation FormConfig object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\FormConfigQuery A secondary query class using the current class as primary query
     */
    public function useFormConfigQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinFormConfig($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'FormConfig', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\FormConfigQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   FormConfigField $formConfigField Object to remove from the list of results
     *
     * @return FormConfigFieldQuery The current query, for fluid interface
     */
    public function prune($formConfigField = null)
    {
        if ($formConfigField) {
            $this->addUsingAlias(FormConfigFieldPeer::FORM_CONFIG_FIELD_ID, $formConfigField->getFormConfigFieldId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

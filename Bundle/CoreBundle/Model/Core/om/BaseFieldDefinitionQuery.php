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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\FieldDefinition;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\FieldDefinitionPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\FieldDefinitionQuery;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\FileProperty;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtensionField;

/**
 * @method FieldDefinitionQuery orderByFieldDefinitionId($order = Criteria::ASC) Order by the field_definition_id column
 * @method FieldDefinitionQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method FieldDefinitionQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method FieldDefinitionQuery orderByControlType($order = Criteria::ASC) Order by the control_type column
 * @method FieldDefinitionQuery orderByLookupType($order = Criteria::ASC) Order by the lookup_type column
 * @method FieldDefinitionQuery orderByLookup($order = Criteria::ASC) Order by the lookup column
 * @method FieldDefinitionQuery orderByDescription($order = Criteria::ASC) Order by the description column
 *
 * @method FieldDefinitionQuery groupByFieldDefinitionId() Group by the field_definition_id column
 * @method FieldDefinitionQuery groupByName() Group by the name column
 * @method FieldDefinitionQuery groupByType() Group by the type column
 * @method FieldDefinitionQuery groupByControlType() Group by the control_type column
 * @method FieldDefinitionQuery groupByLookupType() Group by the lookup_type column
 * @method FieldDefinitionQuery groupByLookup() Group by the lookup column
 * @method FieldDefinitionQuery groupByDescription() Group by the description column
 *
 * @method FieldDefinitionQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method FieldDefinitionQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method FieldDefinitionQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method FieldDefinitionQuery leftJoinTableExtensionField($relationAlias = null) Adds a LEFT JOIN clause to the query using the TableExtensionField relation
 * @method FieldDefinitionQuery rightJoinTableExtensionField($relationAlias = null) Adds a RIGHT JOIN clause to the query using the TableExtensionField relation
 * @method FieldDefinitionQuery innerJoinTableExtensionField($relationAlias = null) Adds a INNER JOIN clause to the query using the TableExtensionField relation
 *
 * @method FieldDefinitionQuery leftJoinFileProperty($relationAlias = null) Adds a LEFT JOIN clause to the query using the FileProperty relation
 * @method FieldDefinitionQuery rightJoinFileProperty($relationAlias = null) Adds a RIGHT JOIN clause to the query using the FileProperty relation
 * @method FieldDefinitionQuery innerJoinFileProperty($relationAlias = null) Adds a INNER JOIN clause to the query using the FileProperty relation
 *
 * @method FieldDefinition findOne(PropelPDO $con = null) Return the first FieldDefinition matching the query
 * @method FieldDefinition findOneOrCreate(PropelPDO $con = null) Return the first FieldDefinition matching the query, or a new FieldDefinition object populated from the query conditions when no match is found
 *
 * @method FieldDefinition findOneByName(string $name) Return the first FieldDefinition filtered by the name column
 * @method FieldDefinition findOneByType(string $type) Return the first FieldDefinition filtered by the type column
 * @method FieldDefinition findOneByControlType(string $control_type) Return the first FieldDefinition filtered by the control_type column
 * @method FieldDefinition findOneByLookupType(string $lookup_type) Return the first FieldDefinition filtered by the lookup_type column
 * @method FieldDefinition findOneByLookup(string $lookup) Return the first FieldDefinition filtered by the lookup column
 * @method FieldDefinition findOneByDescription(string $description) Return the first FieldDefinition filtered by the description column
 *
 * @method array findByFieldDefinitionId(int $field_definition_id) Return FieldDefinition objects filtered by the field_definition_id column
 * @method array findByName(string $name) Return FieldDefinition objects filtered by the name column
 * @method array findByType(string $type) Return FieldDefinition objects filtered by the type column
 * @method array findByControlType(string $control_type) Return FieldDefinition objects filtered by the control_type column
 * @method array findByLookupType(string $lookup_type) Return FieldDefinition objects filtered by the lookup_type column
 * @method array findByLookup(string $lookup) Return FieldDefinition objects filtered by the lookup column
 * @method array findByDescription(string $description) Return FieldDefinition objects filtered by the description column
 */
abstract class BaseFieldDefinitionQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseFieldDefinitionQuery object.
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
            $modelName = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\FieldDefinition';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new FieldDefinitionQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   FieldDefinitionQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return FieldDefinitionQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof FieldDefinitionQuery) {
            return $criteria;
        }
        $query = new FieldDefinitionQuery(null, null, $modelAlias);

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
     * @return   FieldDefinition|FieldDefinition[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = FieldDefinitionPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(FieldDefinitionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 FieldDefinition A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByFieldDefinitionId($key, $con = null)
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
     * @return                 FieldDefinition A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT field_definition_id, name, type, control_type, lookup_type, lookup, description FROM core.field_definition WHERE field_definition_id = :p0';
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
            $obj = new FieldDefinition();
            $obj->hydrate($row);
            FieldDefinitionPeer::addInstanceToPool($obj, (string) $key);
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
     * @return FieldDefinition|FieldDefinition[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|FieldDefinition[]|mixed the list of results, formatted by the current formatter
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
     * @return FieldDefinition[]
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
     * @return FieldDefinitionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(FieldDefinitionPeer::FIELD_DEFINITION_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return FieldDefinitionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(FieldDefinitionPeer::FIELD_DEFINITION_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the field_definition_id column
     *
     * Example usage:
     * <code>
     * $query->filterByFieldDefinitionId(1234); // WHERE field_definition_id = 1234
     * $query->filterByFieldDefinitionId(array(12, 34)); // WHERE field_definition_id IN (12, 34)
     * $query->filterByFieldDefinitionId(array('min' => 12)); // WHERE field_definition_id >= 12
     * $query->filterByFieldDefinitionId(array('max' => 12)); // WHERE field_definition_id <= 12
     * </code>
     *
     * @param     mixed $fieldDefinitionId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FieldDefinitionQuery The current query, for fluid interface
     */
    public function filterByFieldDefinitionId($fieldDefinitionId = null, $comparison = null)
    {
        if (is_array($fieldDefinitionId)) {
            $useMinMax = false;
            if (isset($fieldDefinitionId['min'])) {
                $this->addUsingAlias(FieldDefinitionPeer::FIELD_DEFINITION_ID, $fieldDefinitionId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($fieldDefinitionId['max'])) {
                $this->addUsingAlias(FieldDefinitionPeer::FIELD_DEFINITION_ID, $fieldDefinitionId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FieldDefinitionPeer::FIELD_DEFINITION_ID, $fieldDefinitionId, $comparison);
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
     * @return FieldDefinitionQuery The current query, for fluid interface
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

        return $this->addUsingAlias(FieldDefinitionPeer::NAME, $name, $comparison);
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
     * @return FieldDefinitionQuery The current query, for fluid interface
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

        return $this->addUsingAlias(FieldDefinitionPeer::TYPE, $type, $comparison);
    }

    /**
     * Filter the query on the control_type column
     *
     * Example usage:
     * <code>
     * $query->filterByControlType('fooValue');   // WHERE control_type = 'fooValue'
     * $query->filterByControlType('%fooValue%'); // WHERE control_type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $controlType The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FieldDefinitionQuery The current query, for fluid interface
     */
    public function filterByControlType($controlType = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($controlType)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $controlType)) {
                $controlType = str_replace('*', '%', $controlType);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(FieldDefinitionPeer::CONTROL_TYPE, $controlType, $comparison);
    }

    /**
     * Filter the query on the lookup_type column
     *
     * Example usage:
     * <code>
     * $query->filterByLookupType('fooValue');   // WHERE lookup_type = 'fooValue'
     * $query->filterByLookupType('%fooValue%'); // WHERE lookup_type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $lookupType The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FieldDefinitionQuery The current query, for fluid interface
     */
    public function filterByLookupType($lookupType = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($lookupType)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $lookupType)) {
                $lookupType = str_replace('*', '%', $lookupType);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(FieldDefinitionPeer::LOOKUP_TYPE, $lookupType, $comparison);
    }

    /**
     * Filter the query on the lookup column
     *
     * Example usage:
     * <code>
     * $query->filterByLookup('fooValue');   // WHERE lookup = 'fooValue'
     * $query->filterByLookup('%fooValue%'); // WHERE lookup LIKE '%fooValue%'
     * </code>
     *
     * @param     string $lookup The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FieldDefinitionQuery The current query, for fluid interface
     */
    public function filterByLookup($lookup = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($lookup)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $lookup)) {
                $lookup = str_replace('*', '%', $lookup);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(FieldDefinitionPeer::LOOKUP, $lookup, $comparison);
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
     * @return FieldDefinitionQuery The current query, for fluid interface
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

        return $this->addUsingAlias(FieldDefinitionPeer::DESCRIPTION, $description, $comparison);
    }

    /**
     * Filter the query by a related TableExtensionField object
     *
     * @param   TableExtensionField|PropelObjectCollection $tableExtensionField  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 FieldDefinitionQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByTableExtensionField($tableExtensionField, $comparison = null)
    {
        if ($tableExtensionField instanceof TableExtensionField) {
            return $this
                ->addUsingAlias(FieldDefinitionPeer::FIELD_DEFINITION_ID, $tableExtensionField->getFieldDefinitionId(), $comparison);
        } elseif ($tableExtensionField instanceof PropelObjectCollection) {
            return $this
                ->useTableExtensionFieldQuery()
                ->filterByPrimaryKeys($tableExtensionField->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByTableExtensionField() only accepts arguments of type TableExtensionField or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the TableExtensionField relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return FieldDefinitionQuery The current query, for fluid interface
     */
    public function joinTableExtensionField($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('TableExtensionField');

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
            $this->addJoinObject($join, 'TableExtensionField');
        }

        return $this;
    }

    /**
     * Use the TableExtensionField relation TableExtensionField object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtensionFieldQuery A secondary query class using the current class as primary query
     */
    public function useTableExtensionFieldQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinTableExtensionField($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'TableExtensionField', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtensionFieldQuery');
    }

    /**
     * Filter the query by a related FileProperty object
     *
     * @param   FileProperty|PropelObjectCollection $fileProperty  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 FieldDefinitionQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByFileProperty($fileProperty, $comparison = null)
    {
        if ($fileProperty instanceof FileProperty) {
            return $this
                ->addUsingAlias(FieldDefinitionPeer::FIELD_DEFINITION_ID, $fileProperty->getFieldDefinitionId(), $comparison);
        } elseif ($fileProperty instanceof PropelObjectCollection) {
            return $this
                ->useFilePropertyQuery()
                ->filterByPrimaryKeys($fileProperty->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByFileProperty() only accepts arguments of type FileProperty or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the FileProperty relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return FieldDefinitionQuery The current query, for fluid interface
     */
    public function joinFileProperty($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('FileProperty');

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
            $this->addJoinObject($join, 'FileProperty');
        }

        return $this;
    }

    /**
     * Use the FileProperty relation FileProperty object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\FilePropertyQuery A secondary query class using the current class as primary query
     */
    public function useFilePropertyQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinFileProperty($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'FileProperty', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\FilePropertyQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   FieldDefinition $fieldDefinition Object to remove from the list of results
     *
     * @return FieldDefinitionQuery The current query, for fluid interface
     */
    public function prune($fieldDefinition = null)
    {
        if ($fieldDefinition) {
            $this->addUsingAlias(FieldDefinitionPeer::FIELD_DEFINITION_ID, $fieldDefinition->getFieldDefinitionId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

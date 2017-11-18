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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\FileProperty;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\FilePropertyPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\FilePropertyQuery;

/**
 * @method FilePropertyQuery orderByFilePropertyId($order = Criteria::ASC) Order by the file_property_id column
 * @method FilePropertyQuery orderByFieldDefinitionId($order = Criteria::ASC) Order by the field_definition_id column
 * @method FilePropertyQuery orderByContextSchema($order = Criteria::ASC) Order by the context_schema column
 * @method FilePropertyQuery orderByContextActualSchema($order = Criteria::ASC) Order by the context_actual_schema column
 * @method FilePropertyQuery orderByContextTable($order = Criteria::ASC) Order by the context_table column
 * @method FilePropertyQuery orderByContextCategory($order = Criteria::ASC) Order by the context_category column
 * @method FilePropertyQuery orderByShowInListFlag($order = Criteria::ASC) Order by the show_in_list_flag column
 *
 * @method FilePropertyQuery groupByFilePropertyId() Group by the file_property_id column
 * @method FilePropertyQuery groupByFieldDefinitionId() Group by the field_definition_id column
 * @method FilePropertyQuery groupByContextSchema() Group by the context_schema column
 * @method FilePropertyQuery groupByContextActualSchema() Group by the context_actual_schema column
 * @method FilePropertyQuery groupByContextTable() Group by the context_table column
 * @method FilePropertyQuery groupByContextCategory() Group by the context_category column
 * @method FilePropertyQuery groupByShowInListFlag() Group by the show_in_list_flag column
 *
 * @method FilePropertyQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method FilePropertyQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method FilePropertyQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method FilePropertyQuery leftJoinFieldDefinition($relationAlias = null) Adds a LEFT JOIN clause to the query using the FieldDefinition relation
 * @method FilePropertyQuery rightJoinFieldDefinition($relationAlias = null) Adds a RIGHT JOIN clause to the query using the FieldDefinition relation
 * @method FilePropertyQuery innerJoinFieldDefinition($relationAlias = null) Adds a INNER JOIN clause to the query using the FieldDefinition relation
 *
 * @method FileProperty findOne(PropelPDO $con = null) Return the first FileProperty matching the query
 * @method FileProperty findOneOrCreate(PropelPDO $con = null) Return the first FileProperty matching the query, or a new FileProperty object populated from the query conditions when no match is found
 *
 * @method FileProperty findOneByFieldDefinitionId(int $field_definition_id) Return the first FileProperty filtered by the field_definition_id column
 * @method FileProperty findOneByContextSchema(string $context_schema) Return the first FileProperty filtered by the context_schema column
 * @method FileProperty findOneByContextActualSchema(string $context_actual_schema) Return the first FileProperty filtered by the context_actual_schema column
 * @method FileProperty findOneByContextTable(string $context_table) Return the first FileProperty filtered by the context_table column
 * @method FileProperty findOneByContextCategory(string $context_category) Return the first FileProperty filtered by the context_category column
 * @method FileProperty findOneByShowInListFlag(boolean $show_in_list_flag) Return the first FileProperty filtered by the show_in_list_flag column
 *
 * @method array findByFilePropertyId(int $file_property_id) Return FileProperty objects filtered by the file_property_id column
 * @method array findByFieldDefinitionId(int $field_definition_id) Return FileProperty objects filtered by the field_definition_id column
 * @method array findByContextSchema(string $context_schema) Return FileProperty objects filtered by the context_schema column
 * @method array findByContextActualSchema(string $context_actual_schema) Return FileProperty objects filtered by the context_actual_schema column
 * @method array findByContextTable(string $context_table) Return FileProperty objects filtered by the context_table column
 * @method array findByContextCategory(string $context_category) Return FileProperty objects filtered by the context_category column
 * @method array findByShowInListFlag(boolean $show_in_list_flag) Return FileProperty objects filtered by the show_in_list_flag column
 */
abstract class BaseFilePropertyQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseFilePropertyQuery object.
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
            $modelName = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\FileProperty';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new FilePropertyQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   FilePropertyQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return FilePropertyQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof FilePropertyQuery) {
            return $criteria;
        }
        $query = new FilePropertyQuery(null, null, $modelAlias);

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
     * @return   FileProperty|FileProperty[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = FilePropertyPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(FilePropertyPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 FileProperty A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByFilePropertyId($key, $con = null)
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
     * @return                 FileProperty A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT file_property_id, field_definition_id, context_schema, context_actual_schema, context_table, context_category, show_in_list_flag FROM core.file_property WHERE file_property_id = :p0';
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
            $obj = new FileProperty();
            $obj->hydrate($row);
            FilePropertyPeer::addInstanceToPool($obj, (string) $key);
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
     * @return FileProperty|FileProperty[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|FileProperty[]|mixed the list of results, formatted by the current formatter
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
     * @return FileProperty[]
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
     * @return FilePropertyQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(FilePropertyPeer::FILE_PROPERTY_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return FilePropertyQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(FilePropertyPeer::FILE_PROPERTY_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the file_property_id column
     *
     * Example usage:
     * <code>
     * $query->filterByFilePropertyId(1234); // WHERE file_property_id = 1234
     * $query->filterByFilePropertyId(array(12, 34)); // WHERE file_property_id IN (12, 34)
     * $query->filterByFilePropertyId(array('min' => 12)); // WHERE file_property_id >= 12
     * $query->filterByFilePropertyId(array('max' => 12)); // WHERE file_property_id <= 12
     * </code>
     *
     * @param     mixed $filePropertyId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FilePropertyQuery The current query, for fluid interface
     */
    public function filterByFilePropertyId($filePropertyId = null, $comparison = null)
    {
        if (is_array($filePropertyId)) {
            $useMinMax = false;
            if (isset($filePropertyId['min'])) {
                $this->addUsingAlias(FilePropertyPeer::FILE_PROPERTY_ID, $filePropertyId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($filePropertyId['max'])) {
                $this->addUsingAlias(FilePropertyPeer::FILE_PROPERTY_ID, $filePropertyId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FilePropertyPeer::FILE_PROPERTY_ID, $filePropertyId, $comparison);
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
     * @see       filterByFieldDefinition()
     *
     * @param     mixed $fieldDefinitionId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FilePropertyQuery The current query, for fluid interface
     */
    public function filterByFieldDefinitionId($fieldDefinitionId = null, $comparison = null)
    {
        if (is_array($fieldDefinitionId)) {
            $useMinMax = false;
            if (isset($fieldDefinitionId['min'])) {
                $this->addUsingAlias(FilePropertyPeer::FIELD_DEFINITION_ID, $fieldDefinitionId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($fieldDefinitionId['max'])) {
                $this->addUsingAlias(FilePropertyPeer::FIELD_DEFINITION_ID, $fieldDefinitionId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FilePropertyPeer::FIELD_DEFINITION_ID, $fieldDefinitionId, $comparison);
    }

    /**
     * Filter the query on the context_schema column
     *
     * Example usage:
     * <code>
     * $query->filterByContextSchema('fooValue');   // WHERE context_schema = 'fooValue'
     * $query->filterByContextSchema('%fooValue%'); // WHERE context_schema LIKE '%fooValue%'
     * </code>
     *
     * @param     string $contextSchema The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FilePropertyQuery The current query, for fluid interface
     */
    public function filterByContextSchema($contextSchema = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($contextSchema)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $contextSchema)) {
                $contextSchema = str_replace('*', '%', $contextSchema);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(FilePropertyPeer::CONTEXT_SCHEMA, $contextSchema, $comparison);
    }

    /**
     * Filter the query on the context_actual_schema column
     *
     * Example usage:
     * <code>
     * $query->filterByContextActualSchema('fooValue');   // WHERE context_actual_schema = 'fooValue'
     * $query->filterByContextActualSchema('%fooValue%'); // WHERE context_actual_schema LIKE '%fooValue%'
     * </code>
     *
     * @param     string $contextActualSchema The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FilePropertyQuery The current query, for fluid interface
     */
    public function filterByContextActualSchema($contextActualSchema = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($contextActualSchema)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $contextActualSchema)) {
                $contextActualSchema = str_replace('*', '%', $contextActualSchema);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(FilePropertyPeer::CONTEXT_ACTUAL_SCHEMA, $contextActualSchema, $comparison);
    }

    /**
     * Filter the query on the context_table column
     *
     * Example usage:
     * <code>
     * $query->filterByContextTable('fooValue');   // WHERE context_table = 'fooValue'
     * $query->filterByContextTable('%fooValue%'); // WHERE context_table LIKE '%fooValue%'
     * </code>
     *
     * @param     string $contextTable The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FilePropertyQuery The current query, for fluid interface
     */
    public function filterByContextTable($contextTable = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($contextTable)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $contextTable)) {
                $contextTable = str_replace('*', '%', $contextTable);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(FilePropertyPeer::CONTEXT_TABLE, $contextTable, $comparison);
    }

    /**
     * Filter the query on the context_category column
     *
     * Example usage:
     * <code>
     * $query->filterByContextCategory('fooValue');   // WHERE context_category = 'fooValue'
     * $query->filterByContextCategory('%fooValue%'); // WHERE context_category LIKE '%fooValue%'
     * </code>
     *
     * @param     string $contextCategory The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FilePropertyQuery The current query, for fluid interface
     */
    public function filterByContextCategory($contextCategory = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($contextCategory)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $contextCategory)) {
                $contextCategory = str_replace('*', '%', $contextCategory);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(FilePropertyPeer::CONTEXT_CATEGORY, $contextCategory, $comparison);
    }

    /**
     * Filter the query on the show_in_list_flag column
     *
     * Example usage:
     * <code>
     * $query->filterByShowInListFlag(true); // WHERE show_in_list_flag = true
     * $query->filterByShowInListFlag('yes'); // WHERE show_in_list_flag = true
     * </code>
     *
     * @param     boolean|string $showInListFlag The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FilePropertyQuery The current query, for fluid interface
     */
    public function filterByShowInListFlag($showInListFlag = null, $comparison = null)
    {
        if (is_string($showInListFlag)) {
            $showInListFlag = in_array(strtolower($showInListFlag), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(FilePropertyPeer::SHOW_IN_LIST_FLAG, $showInListFlag, $comparison);
    }

    /**
     * Filter the query by a related FieldDefinition object
     *
     * @param   FieldDefinition|PropelObjectCollection $fieldDefinition The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 FilePropertyQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByFieldDefinition($fieldDefinition, $comparison = null)
    {
        if ($fieldDefinition instanceof FieldDefinition) {
            return $this
                ->addUsingAlias(FilePropertyPeer::FIELD_DEFINITION_ID, $fieldDefinition->getFieldDefinitionId(), $comparison);
        } elseif ($fieldDefinition instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(FilePropertyPeer::FIELD_DEFINITION_ID, $fieldDefinition->toKeyValue('PrimaryKey', 'FieldDefinitionId'), $comparison);
        } else {
            throw new PropelException('filterByFieldDefinition() only accepts arguments of type FieldDefinition or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the FieldDefinition relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return FilePropertyQuery The current query, for fluid interface
     */
    public function joinFieldDefinition($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('FieldDefinition');

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
            $this->addJoinObject($join, 'FieldDefinition');
        }

        return $this;
    }

    /**
     * Use the FieldDefinition relation FieldDefinition object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\FieldDefinitionQuery A secondary query class using the current class as primary query
     */
    public function useFieldDefinitionQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinFieldDefinition($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'FieldDefinition', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\FieldDefinitionQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   FileProperty $fileProperty Object to remove from the list of results
     *
     * @return FilePropertyQuery The current query, for fluid interface
     */
    public function prune($fileProperty = null)
    {
        if ($fileProperty) {
            $this->addUsingAlias(FilePropertyPeer::FILE_PROPERTY_ID, $fileProperty->getFilePropertyId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

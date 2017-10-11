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
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\FormConfigPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\FormConfigQuery;

/**
 * @method FormConfigQuery orderByFormConfigId($order = Criteria::ASC) Order by the form_config_id column
 * @method FormConfigQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method FormConfigQuery orderByVariation($order = Criteria::ASC) Order by the variation column
 * @method FormConfigQuery orderByLayout($order = Criteria::ASC) Order by the layout column
 * @method FormConfigQuery orderByWikiHelpPage($order = Criteria::ASC) Order by the wiki_help_page column
 *
 * @method FormConfigQuery groupByFormConfigId() Group by the form_config_id column
 * @method FormConfigQuery groupByName() Group by the name column
 * @method FormConfigQuery groupByVariation() Group by the variation column
 * @method FormConfigQuery groupByLayout() Group by the layout column
 * @method FormConfigQuery groupByWikiHelpPage() Group by the wiki_help_page column
 *
 * @method FormConfigQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method FormConfigQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method FormConfigQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method FormConfigQuery leftJoinFormConfigField($relationAlias = null) Adds a LEFT JOIN clause to the query using the FormConfigField relation
 * @method FormConfigQuery rightJoinFormConfigField($relationAlias = null) Adds a RIGHT JOIN clause to the query using the FormConfigField relation
 * @method FormConfigQuery innerJoinFormConfigField($relationAlias = null) Adds a INNER JOIN clause to the query using the FormConfigField relation
 *
 * @method FormConfig findOne(PropelPDO $con = null) Return the first FormConfig matching the query
 * @method FormConfig findOneOrCreate(PropelPDO $con = null) Return the first FormConfig matching the query, or a new FormConfig object populated from the query conditions when no match is found
 *
 * @method FormConfig findOneByName(string $name) Return the first FormConfig filtered by the name column
 * @method FormConfig findOneByVariation(string $variation) Return the first FormConfig filtered by the variation column
 * @method FormConfig findOneByLayout(string $layout) Return the first FormConfig filtered by the layout column
 * @method FormConfig findOneByWikiHelpPage(string $wiki_help_page) Return the first FormConfig filtered by the wiki_help_page column
 *
 * @method array findByFormConfigId(int $form_config_id) Return FormConfig objects filtered by the form_config_id column
 * @method array findByName(string $name) Return FormConfig objects filtered by the name column
 * @method array findByVariation(string $variation) Return FormConfig objects filtered by the variation column
 * @method array findByLayout(string $layout) Return FormConfig objects filtered by the layout column
 * @method array findByWikiHelpPage(string $wiki_help_page) Return FormConfig objects filtered by the wiki_help_page column
 */
abstract class BaseFormConfigQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseFormConfigQuery object.
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
            $modelName = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\FormConfig';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new FormConfigQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   FormConfigQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return FormConfigQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof FormConfigQuery) {
            return $criteria;
        }
        $query = new FormConfigQuery(null, null, $modelAlias);

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
     * @return   FormConfig|FormConfig[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = FormConfigPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(FormConfigPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 FormConfig A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneByFormConfigId($key, $con = null)
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
     * @return                 FormConfig A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT form_config_id, name, variation, layout, wiki_help_page FROM core.form_config WHERE form_config_id = :p0';
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
            $obj = new FormConfig();
            $obj->hydrate($row);
            FormConfigPeer::addInstanceToPool($obj, (string) $key);
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
     * @return FormConfig|FormConfig[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|FormConfig[]|mixed the list of results, formatted by the current formatter
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
     * @return FormConfig[]
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
     * @return FormConfigQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(FormConfigPeer::FORM_CONFIG_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return FormConfigQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(FormConfigPeer::FORM_CONFIG_ID, $keys, Criteria::IN);
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
     * @param     mixed $formConfigId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FormConfigQuery The current query, for fluid interface
     */
    public function filterByFormConfigId($formConfigId = null, $comparison = null)
    {
        if (is_array($formConfigId)) {
            $useMinMax = false;
            if (isset($formConfigId['min'])) {
                $this->addUsingAlias(FormConfigPeer::FORM_CONFIG_ID, $formConfigId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($formConfigId['max'])) {
                $this->addUsingAlias(FormConfigPeer::FORM_CONFIG_ID, $formConfigId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FormConfigPeer::FORM_CONFIG_ID, $formConfigId, $comparison);
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
     * @return FormConfigQuery The current query, for fluid interface
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

        return $this->addUsingAlias(FormConfigPeer::NAME, $name, $comparison);
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
     * @return FormConfigQuery The current query, for fluid interface
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

        return $this->addUsingAlias(FormConfigPeer::VARIATION, $variation, $comparison);
    }

    /**
     * Filter the query on the layout column
     *
     * Example usage:
     * <code>
     * $query->filterByLayout('fooValue');   // WHERE layout = 'fooValue'
     * $query->filterByLayout('%fooValue%'); // WHERE layout LIKE '%fooValue%'
     * </code>
     *
     * @param     string $layout The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FormConfigQuery The current query, for fluid interface
     */
    public function filterByLayout($layout = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($layout)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $layout)) {
                $layout = str_replace('*', '%', $layout);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(FormConfigPeer::LAYOUT, $layout, $comparison);
    }

    /**
     * Filter the query on the wiki_help_page column
     *
     * Example usage:
     * <code>
     * $query->filterByWikiHelpPage('fooValue');   // WHERE wiki_help_page = 'fooValue'
     * $query->filterByWikiHelpPage('%fooValue%'); // WHERE wiki_help_page LIKE '%fooValue%'
     * </code>
     *
     * @param     string $wikiHelpPage The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FormConfigQuery The current query, for fluid interface
     */
    public function filterByWikiHelpPage($wikiHelpPage = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($wikiHelpPage)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $wikiHelpPage)) {
                $wikiHelpPage = str_replace('*', '%', $wikiHelpPage);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(FormConfigPeer::WIKI_HELP_PAGE, $wikiHelpPage, $comparison);
    }

    /**
     * Filter the query by a related FormConfigField object
     *
     * @param   FormConfigField|PropelObjectCollection $formConfigField  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 FormConfigQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByFormConfigField($formConfigField, $comparison = null)
    {
        if ($formConfigField instanceof FormConfigField) {
            return $this
                ->addUsingAlias(FormConfigPeer::FORM_CONFIG_ID, $formConfigField->getFormConfigId(), $comparison);
        } elseif ($formConfigField instanceof PropelObjectCollection) {
            return $this
                ->useFormConfigFieldQuery()
                ->filterByPrimaryKeys($formConfigField->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByFormConfigField() only accepts arguments of type FormConfigField or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the FormConfigField relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return FormConfigQuery The current query, for fluid interface
     */
    public function joinFormConfigField($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('FormConfigField');

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
            $this->addJoinObject($join, 'FormConfigField');
        }

        return $this;
    }

    /**
     * Use the FormConfigField relation FormConfigField object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Eulogix\Cool\Bundle\CoreBundle\Model\Core\FormConfigFieldQuery A secondary query class using the current class as primary query
     */
    public function useFormConfigFieldQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinFormConfigField($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'FormConfigField', '\Eulogix\Cool\Bundle\CoreBundle\Model\Core\FormConfigFieldQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   FormConfig $formConfig Object to remove from the list of results
     *
     * @return FormConfigQuery The current query, for fluid interface
     */
    public function prune($formConfig = null)
    {
        if ($formConfig) {
            $this->addUsingAlias(FormConfigPeer::FORM_CONFIG_ID, $formConfig->getFormConfigId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

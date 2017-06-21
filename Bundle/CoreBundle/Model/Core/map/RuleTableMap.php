<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core\map;

use \RelationMap;


/**
 * This class defines the structure of the 'core.rule' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.vendor.eulogix.cool-core-bundle.Bundle.CoreBundle.Model.Core.map
 */
class RuleTableMap extends \Eulogix\Cool\Lib\Database\Propel\CoolTableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'vendor.eulogix.cool-core-bundle.Bundle.CoreBundle.Model.Core.map.RuleTableMap';

    /**
     * Initialize the table attributes, columns and validators
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('core.rule');
        $this->setPhpName('Rule');
        $this->setClassname('Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\Rule');
        $this->setPackage('vendor.eulogix.cool-core-bundle.Bundle.CoreBundle.Model.Core');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('core.rule_rule_id_seq');
        // columns
        $this->addPrimaryKey('rule_id', 'RuleId', 'INTEGER', true, null, null);
        $this->addColumn('name', 'Name', 'LONGVARCHAR', true, null, null);
        $this->addColumn('description', 'Description', 'LONGVARCHAR', false, null, null);
        $this->addColumn('category', 'Category', 'LONGVARCHAR', true, null, null);
        $this->addColumn('expression_type', 'ExpressionType', 'LONGVARCHAR', true, null, 'HOA');
        $this->addColumn('expression', 'Expression', 'LONGVARCHAR', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('RuleCode', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\RuleCode', RelationMap::ONE_TO_MANY, array('rule_id' => 'rule_id', ), 'RESTRICT', null, 'RuleCodes');
        $this->addRelation('WidgetRule', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\WidgetRule', RelationMap::ONE_TO_MANY, array('rule_id' => 'rule_id', ), 'RESTRICT', null, 'WidgetRules');
    } // buildRelations()

} // RuleTableMap

<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core\map;

use \RelationMap;


/**
 * This class defines the structure of the 'core.widget_rule' table.
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
class WidgetRuleTableMap extends \Eulogix\Cool\Lib\Database\Propel\CoolTableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'vendor.eulogix.cool-core-bundle.Bundle.CoreBundle.Model.Core.map.WidgetRuleTableMap';

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
        $this->setName('core.widget_rule');
        $this->setPhpName('WidgetRule');
        $this->setClassname('Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\WidgetRule');
        $this->setPackage('vendor.eulogix.cool-core-bundle.Bundle.CoreBundle.Model.Core');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('core.widget_rule_widget_rule_id_seq');
        // columns
        $this->addPrimaryKey('widget_rule_id', 'WidgetRuleId', 'INTEGER', true, null, null);
        $this->addColumn('widget_id', 'WidgetId', 'LONGVARCHAR', true, null, null);
        $this->addForeignKey('rule_id', 'RuleId', 'INTEGER', 'core.rule', 'rule_id', true, null, null);
        $this->addColumn('evaluation', 'Evaluation', 'LONGVARCHAR', true, null, 'BEFORE_DEFINITION');
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Rule', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\Rule', RelationMap::MANY_TO_ONE, array('rule_id' => 'rule_id', ), 'RESTRICT', null);
    } // buildRelations()

} // WidgetRuleTableMap

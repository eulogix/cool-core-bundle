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
        $this->addForeignKey('parent_widget_rule_id', 'ParentWidgetRuleId', 'INTEGER', 'core.widget_rule', 'widget_rule_id', false, null, null);
        $this->addForeignKey('widget_id', 'WidgetId', 'LONGVARCHAR', 'core.widget_rule', 'widget_id', true, null, null);
        $this->addForeignKey('rule_id', 'RuleId', 'INTEGER', 'core.rule', 'rule_id', true, null, null);
        $this->addColumn('enabled_flag', 'EnabledFlag', 'BOOLEAN', false, null, true);
        $this->addColumn('evaluation', 'Evaluation', 'LONGVARCHAR', true, null, 'BEFORE_DEFINITION');
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Rule', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\Rule', RelationMap::MANY_TO_ONE, array('rule_id' => 'rule_id', ), 'RESTRICT', null);
        $this->addRelation('WidgetRuleRelatedByParentWidgetRuleIdWidgetId', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\WidgetRule', RelationMap::MANY_TO_ONE, array('parent_widget_rule_id' => 'widget_rule_id', 'widget_id' => 'widget_id', ), 'RESTRICT', null);
        $this->addRelation('WidgetRuleRelatedByWidgetRuleIdWidgetId', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\WidgetRule', RelationMap::ONE_TO_MANY, array('widget_rule_id' => 'parent_widget_rule_id', 'widget_id' => 'widget_id', ), 'RESTRICT', null, 'WidgetRulesRelatedByWidgetRuleIdWidgetId');
    } // buildRelations()

    /**
     *
     * Gets the list of behaviors registered for this table
     *
     * @return array Associative array (name => parameters) of behaviors
     */
    public function getBehaviors()
    {
        return array(
            'notifier' =>  array (
  'channel' => NULL,
  'per_row' => false,
  'schema' => 'core',
  'target' => 'EulogixCoolCoreBundle/core',
),
        );
    } // getBehaviors()

} // WidgetRuleTableMap

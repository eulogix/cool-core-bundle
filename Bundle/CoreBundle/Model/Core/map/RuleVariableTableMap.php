<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core\map;

use \RelationMap;


/**
 * This class defines the structure of the 'core.rule_variable' table.
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
class RuleVariableTableMap extends \Eulogix\Cool\Lib\Database\Propel\CoolTableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'vendor.eulogix.cool-core-bundle.Bundle.CoreBundle.Model.Core.map.RuleVariableTableMap';

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
        $this->setName('core.rule_variable');
        $this->setPhpName('RuleVariable');
        $this->setClassname('Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\RuleVariable');
        $this->setPackage('vendor.eulogix.cool-core-bundle.Bundle.CoreBundle.Model.Core');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('core.rule_variable_rule_variable_id_seq');
        // columns
        $this->addPrimaryKey('rule_variable_id', 'RuleVariableId', 'INTEGER', true, null, null);
        $this->addForeignKey('rule_id', 'RuleId', 'INTEGER', 'core.rule', 'rule_id', true, null, null);
        $this->addColumn('name', 'Name', 'LONGVARCHAR', true, null, null);
        $this->addForeignKey('code_snippet_id', 'CodeSnippetId', 'INTEGER', 'core.code_snippet', 'code_snippet_id', false, null, null);
        $this->addColumn('code_snippet_variables', 'CodeSnippetVariables', 'LONGVARCHAR', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Rule', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\Rule', RelationMap::MANY_TO_ONE, array('rule_id' => 'rule_id', ), 'RESTRICT', null);
        $this->addRelation('CodeSnippet', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\CodeSnippet', RelationMap::MANY_TO_ONE, array('code_snippet_id' => 'code_snippet_id', ), 'RESTRICT', null);
    } // buildRelations()

} // RuleVariableTableMap

<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core\map;

use \RelationMap;


/**
 * This class defines the structure of the 'core.code_snippet' table.
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
class CodeSnippetTableMap extends \Eulogix\Cool\Lib\Database\Propel\CoolTableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'vendor.eulogix.cool-core-bundle.Bundle.CoreBundle.Model.Core.map.CodeSnippetTableMap';

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
        $this->setName('core.code_snippet');
        $this->setPhpName('CodeSnippet');
        $this->setClassname('Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\CodeSnippet');
        $this->setPackage('vendor.eulogix.cool-core-bundle.Bundle.CoreBundle.Model.Core');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('core.code_snippet_code_snippet_id_seq');
        // columns
        $this->addPrimaryKey('code_snippet_id', 'CodeSnippetId', 'INTEGER', true, null, null);
        $this->addColumn('category', 'Category', 'LONGVARCHAR', true, null, null);
        $this->addColumn('language', 'Language', 'LONGVARCHAR', true, null, 'PHP');
        $this->addColumn('type', 'Type', 'LONGVARCHAR', true, null, 'EXPRESSION');
        $this->addColumn('return_type', 'ReturnType', 'LONGVARCHAR', true, null, 'NONE');
        $this->addColumn('name', 'Name', 'LONGVARCHAR', true, null, null);
        $this->addColumn('description', 'Description', 'LONGVARCHAR', true, null, null);
        $this->addColumn('snippet', 'Snippet', 'LONGVARCHAR', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('RuleCode', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\RuleCode', RelationMap::ONE_TO_MANY, array('code_snippet_id' => 'code_snippet_id', ), 'RESTRICT', null, 'RuleCodes');
        $this->addRelation('CodeSnippetVariable', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\CodeSnippetVariable', RelationMap::ONE_TO_MANY, array('code_snippet_id' => 'code_snippet_id', ), 'RESTRICT', null, 'CodeSnippetVariables');
    } // buildRelations()

} // CodeSnippetTableMap

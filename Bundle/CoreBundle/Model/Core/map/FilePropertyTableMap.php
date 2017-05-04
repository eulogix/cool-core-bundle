<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core\map;

use \RelationMap;


/**
 * This class defines the structure of the 'core.file_property' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.src.Eulogix.Cool.Bundle.CoreBundle.Model.Core.map
 */
class FilePropertyTableMap extends \Eulogix\Cool\Lib\Database\Propel\CoolTableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Eulogix.Cool.Bundle.CoreBundle.Model.Core.map.FilePropertyTableMap';

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
        $this->setName('core.file_property');
        $this->setPhpName('FileProperty');
        $this->setClassname('Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\FileProperty');
        $this->setPackage('src.Eulogix.Cool.Bundle.CoreBundle.Model.Core');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('core.file_property_file_property_id_seq');
        // columns
        $this->addPrimaryKey('file_property_id', 'FilePropertyId', 'INTEGER', true, null, null);
        $this->addForeignKey('field_definition_id', 'FieldDefinitionId', 'INTEGER', 'core.field_definition', 'field_definition_id', false, null, null);
        $this->addColumn('context_schema', 'ContextSchema', 'LONGVARCHAR', true, null, null);
        $this->addColumn('context_actual_schema', 'ContextActualSchema', 'LONGVARCHAR', false, null, null);
        $this->addColumn('context_table', 'ContextTable', 'LONGVARCHAR', false, null, null);
        $this->addColumn('context_category', 'ContextCategory', 'LONGVARCHAR', false, null, null);
        $this->addColumn('show_in_list_flag', 'ShowInListFlag', 'BOOLEAN', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('FieldDefinition', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\FieldDefinition', RelationMap::MANY_TO_ONE, array('field_definition_id' => 'field_definition_id', ), 'RESTRICT', null);
    } // buildRelations()

} // FilePropertyTableMap

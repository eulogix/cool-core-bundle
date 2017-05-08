<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core\map;

use \RelationMap;


/**
 * This class defines the structure of the 'core.field_definition' table.
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
class FieldDefinitionTableMap extends \Eulogix\Cool\Lib\Database\Propel\CoolTableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'vendor.eulogix.cool-core-bundle.Bundle.CoreBundle.Model.Core.map.FieldDefinitionTableMap';

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
        $this->setName('core.field_definition');
        $this->setPhpName('FieldDefinition');
        $this->setClassname('Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\FieldDefinition');
        $this->setPackage('vendor.eulogix.cool-core-bundle.Bundle.CoreBundle.Model.Core');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('core.field_definition_field_definition_id_seq');
        // columns
        $this->addPrimaryKey('field_definition_id', 'FieldDefinitionId', 'INTEGER', true, null, null);
        $this->addColumn('name', 'Name', 'VARCHAR', false, 50, null);
        $this->addColumn('type', 'Type', 'LONGVARCHAR', false, null, null);
        $this->addColumn('control_type', 'ControlType', 'LONGVARCHAR', false, null, null);
        $this->addColumn('lookup_type', 'LookupType', 'LONGVARCHAR', false, null, null);
        $this->addColumn('lookup', 'Lookup', 'LONGVARCHAR', false, null, null);
        $this->addColumn('description', 'Description', 'LONGVARCHAR', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('TableExtensionField', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\TableExtensionField', RelationMap::ONE_TO_MANY, array('field_definition_id' => 'field_definition_id', ), 'RESTRICT', null, 'TableExtensionFields');
        $this->addRelation('FileProperty', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\FileProperty', RelationMap::ONE_TO_MANY, array('field_definition_id' => 'field_definition_id', ), 'RESTRICT', null, 'FileProperties');
    } // buildRelations()

} // FieldDefinitionTableMap

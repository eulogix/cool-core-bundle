<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core\map;

use \RelationMap;


/**
 * This class defines the structure of the 'core.table_extension' table.
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
class TableExtensionTableMap extends \Eulogix\Cool\Lib\Database\Propel\CoolTableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'vendor.eulogix.cool-core-bundle.Bundle.CoreBundle.Model.Core.map.TableExtensionTableMap';

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
        $this->setName('core.table_extension');
        $this->setPhpName('TableExtension');
        $this->setClassname('Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\TableExtension');
        $this->setPackage('vendor.eulogix.cool-core-bundle.Bundle.CoreBundle.Model.Core');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('core.table_extension_table_extension_id_seq');
        // columns
        $this->addPrimaryKey('table_extension_id', 'TableExtensionId', 'INTEGER', true, null, null);
        $this->addColumn('db_schema', 'DbSchema', 'LONGVARCHAR', false, null, null);
        $this->addColumn('db_table', 'DbTable', 'LONGVARCHAR', false, null, null);
        $this->addColumn('active_flag', 'ActiveFlag', 'BOOLEAN', false, null, true);
        $this->addColumn('description', 'Description', 'LONGVARCHAR', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('TableExtensionField', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\TableExtensionField', RelationMap::ONE_TO_MANY, array('table_extension_id' => 'table_extension_id', ), 'CASCADE', null, 'TableExtensionFields');
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

} // TableExtensionTableMap

<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core\map;

use \RelationMap;


/**
 * This class defines the structure of the 'core.lister_config' table.
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
class ListerConfigTableMap extends \Eulogix\Cool\Lib\Database\Propel\CoolTableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'vendor.eulogix.cool-core-bundle.Bundle.CoreBundle.Model.Core.map.ListerConfigTableMap';

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
        $this->setName('core.lister_config');
        $this->setPhpName('ListerConfig');
        $this->setClassname('Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\ListerConfig');
        $this->setPackage('vendor.eulogix.cool-core-bundle.Bundle.CoreBundle.Model.Core');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('core.lister_config_lister_config_id_seq');
        // columns
        $this->addPrimaryKey('lister_config_id', 'ListerConfigId', 'INTEGER', true, null, null);
        $this->addColumn('name', 'Name', 'LONGVARCHAR', true, null, null);
        $this->addColumn('variation', 'Variation', 'LONGVARCHAR', false, null, null);
        $this->addColumn('filter_show_flag', 'FilterShowFlag', 'BOOLEAN', false, null, null);
        $this->addColumn('filter_server_id', 'FilterServerId', 'LONGVARCHAR', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('ListerConfigColumn', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\ListerConfigColumn', RelationMap::ONE_TO_MANY, array('lister_config_id' => 'lister_config_id', ), 'CASCADE', null, 'ListerConfigColumns');
    } // buildRelations()

} // ListerConfigTableMap

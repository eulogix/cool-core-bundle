<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core\map;

use \RelationMap;


/**
 * This class defines the structure of the 'core.app_lock' table.
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
class AppLockTableMap extends \Eulogix\Cool\Lib\Database\Propel\CoolTableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'vendor.eulogix.cool-core-bundle.Bundle.CoreBundle.Model.Core.map.AppLockTableMap';

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
        $this->setName('core.app_lock');
        $this->setPhpName('AppLock');
        $this->setClassname('Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AppLock');
        $this->setPackage('vendor.eulogix.cool-core-bundle.Bundle.CoreBundle.Model.Core');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('core.app_lock_app_lock_id_seq');
        // columns
        $this->addPrimaryKey('app_lock_id', 'AppLockId', 'INTEGER', true, null, null);
        $this->addColumn('reason', 'Reason', 'LONGVARCHAR', false, null, null);
        $this->addColumn('message', 'Message', 'LONGVARCHAR', false, null, null);
        $this->addColumn('from_date', 'FromDate', 'TIMESTAMP', false, null, null);
        $this->addColumn('to_date', 'ToDate', 'TIMESTAMP', false, null, null);
        $this->addColumn('active_flag', 'ActiveFlag', 'BOOLEAN', false, null, null);
        $this->addColumn('meta', 'Meta', 'LONGVARCHAR', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
    } // buildRelations()

} // AppLockTableMap

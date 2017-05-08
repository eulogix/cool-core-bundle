<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core\map;

use \RelationMap;


/**
 * This class defines the structure of the 'core.account_group_ref' table.
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
class AccountGroupRefTableMap extends \Eulogix\Cool\Lib\Database\Propel\CoolTableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'vendor.eulogix.cool-core-bundle.Bundle.CoreBundle.Model.Core.map.AccountGroupRefTableMap';

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
        $this->setName('core.account_group_ref');
        $this->setPhpName('AccountGroupRef');
        $this->setClassname('Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountGroupRef');
        $this->setPackage('vendor.eulogix.cool-core-bundle.Bundle.CoreBundle.Model.Core');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('core.account_group_ref_account_group_ref_id_seq');
        // columns
        $this->addPrimaryKey('account_group_ref_id', 'AccountGroupRefId', 'INTEGER', true, null, null);
        $this->addForeignKey('account_id', 'AccountId', 'INTEGER', 'core.account', 'account_id', true, null, null);
        $this->addForeignKey('account_group_id', 'AccountGroupId', 'INTEGER', 'core.account_group', 'account_group_id', true, null, null);
        $this->addColumn('role', 'Role', 'LONGVARCHAR', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Account', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\Account', RelationMap::MANY_TO_ONE, array('account_id' => 'account_id', ), 'CASCADE', null);
        $this->addRelation('AccountGroup', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountGroup', RelationMap::MANY_TO_ONE, array('account_group_id' => 'account_group_id', ), 'CASCADE', null);
    } // buildRelations()

} // AccountGroupRefTableMap

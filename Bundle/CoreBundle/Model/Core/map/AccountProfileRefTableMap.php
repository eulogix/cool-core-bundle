<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core\map;

use \RelationMap;


/**
 * This class defines the structure of the 'core.account_profile_ref' table.
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
class AccountProfileRefTableMap extends \Eulogix\Cool\Lib\Database\Propel\CoolTableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'vendor.eulogix.cool-core-bundle.Bundle.CoreBundle.Model.Core.map.AccountProfileRefTableMap';

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
        $this->setName('core.account_profile_ref');
        $this->setPhpName('AccountProfileRef');
        $this->setClassname('Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountProfileRef');
        $this->setPackage('vendor.eulogix.cool-core-bundle.Bundle.CoreBundle.Model.Core');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('core.account_profile_ref_account_profile_ref_id_seq');
        // columns
        $this->addPrimaryKey('account_profile_ref_id', 'AccountProfileRefId', 'INTEGER', true, null, null);
        $this->addForeignKey('account_id', 'AccountId', 'INTEGER', 'core.account', 'account_id', true, null, null);
        $this->addForeignKey('account_profile_id', 'AccountProfileId', 'INTEGER', 'core.account_profile', 'account_profile_id', true, null, null);
        $this->addColumn('sort_order', 'SortOrder', 'INTEGER', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Account', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\Account', RelationMap::MANY_TO_ONE, array('account_id' => 'account_id', ), 'CASCADE', null);
        $this->addRelation('AccountProfile', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountProfile', RelationMap::MANY_TO_ONE, array('account_profile_id' => 'account_profile_id', ), 'CASCADE', null);
    } // buildRelations()

} // AccountProfileRefTableMap

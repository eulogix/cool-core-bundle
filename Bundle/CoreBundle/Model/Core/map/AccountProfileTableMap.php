<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core\map;

use \RelationMap;


/**
 * This class defines the structure of the 'core.account_profile' table.
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
class AccountProfileTableMap extends \Eulogix\Cool\Lib\Database\Propel\CoolTableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'vendor.eulogix.cool-core-bundle.Bundle.CoreBundle.Model.Core.map.AccountProfileTableMap';

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
        $this->setName('core.account_profile');
        $this->setPhpName('AccountProfile');
        $this->setClassname('Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountProfile');
        $this->setPackage('vendor.eulogix.cool-core-bundle.Bundle.CoreBundle.Model.Core');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('core.account_profile_account_profile_id_seq');
        // columns
        $this->addPrimaryKey('account_profile_id', 'AccountProfileId', 'INTEGER', true, null, null);
        $this->addColumn('name', 'Name', 'LONGVARCHAR', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('AccountProfileSetting', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountProfileSetting', RelationMap::ONE_TO_MANY, array('account_profile_id' => 'account_profile_id', ), 'CASCADE', null, 'AccountProfileSettings');
        $this->addRelation('AccountProfileRef', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountProfileRef', RelationMap::ONE_TO_MANY, array('account_profile_id' => 'account_profile_id', ), 'CASCADE', null, 'AccountProfileRefs');
    } // buildRelations()

} // AccountProfileTableMap

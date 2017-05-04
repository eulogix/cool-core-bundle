<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core\map;

use \RelationMap;


/**
 * This class defines the structure of the 'core.account' table.
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
class AccountTableMap extends \Eulogix\Cool\Lib\Database\Propel\CoolTableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Eulogix.Cool.Bundle.CoreBundle.Model.Core.map.AccountTableMap';

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
        $this->setName('core.account');
        $this->setPhpName('Account');
        $this->setClassname('Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\Account');
        $this->setPackage('src.Eulogix.Cool.Bundle.CoreBundle.Model.Core');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('core.account_account_id_seq');
        // columns
        $this->addPrimaryKey('account_id', 'AccountId', 'INTEGER', true, null, null);
        $this->addColumn('login_name', 'LoginName', 'LONGVARCHAR', true, null, null);
        $this->addColumn('password', 'Password', 'LONGVARCHAR', false, null, null);
        $this->addColumn('hashed_password', 'HashedPassword', 'LONGVARCHAR', false, null, null);
        $this->addColumn('type', 'Type', 'LONGVARCHAR', false, null, null);
        $this->addColumn('first_name', 'FirstName', 'LONGVARCHAR', false, null, null);
        $this->addColumn('last_name', 'LastName', 'LONGVARCHAR', false, null, null);
        $this->addColumn('sex', 'Sex', 'LONGVARCHAR', false, null, null);
        $this->addColumn('email', 'Email', 'LONGVARCHAR', false, null, null);
        $this->addColumn('telephone', 'Telephone', 'LONGVARCHAR', false, null, null);
        $this->addColumn('mobile', 'Mobile', 'LONGVARCHAR', false, null, null);
        $this->addColumn('default_locale', 'DefaultLocale', 'LONGVARCHAR', false, null, null);
        $this->addColumn('company_name', 'CompanyName', 'LONGVARCHAR', false, null, null);
        $this->addColumn('validity', 'Validity', 'LONGVARCHAR', false, null, null);
        $this->addColumn('roles', 'Roles', 'LONGVARCHAR', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('AccountSetting', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountSetting', RelationMap::ONE_TO_MANY, array('account_id' => 'account_id', ), 'CASCADE', null, 'AccountSettings');
        $this->addRelation('AccountProfileRef', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountProfileRef', RelationMap::ONE_TO_MANY, array('account_id' => 'account_id', ), 'CASCADE', null, 'AccountProfileRefs');
        $this->addRelation('AccountGroupRef', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AccountGroupRef', RelationMap::ONE_TO_MANY, array('account_id' => 'account_id', ), 'CASCADE', null, 'AccountGroupRefs');
        $this->addRelation('PendingCall', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\PendingCall', RelationMap::ONE_TO_MANY, array('account_id' => 'caller_user_id', ), 'RESTRICT', null, 'PendingCalls');
        $this->addRelation('AsyncJobRelatedByIssuerUserId', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AsyncJob', RelationMap::ONE_TO_MANY, array('account_id' => 'issuer_user_id', ), 'RESTRICT', null, 'AsyncJobsRelatedByIssuerUserId');
        $this->addRelation('UserNotificationRelatedByUserId', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\UserNotification', RelationMap::ONE_TO_MANY, array('account_id' => 'user_id', ), 'RESTRICT', null, 'UserNotificationsRelatedByUserId');
        $this->addRelation('AsyncJobRelatedByCreationUserId', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AsyncJob', RelationMap::ONE_TO_MANY, array('account_id' => 'creation_user_id', ), 'RESTRICT', null, 'AsyncJobsRelatedByCreationUserId');
        $this->addRelation('AsyncJobRelatedByUpdateUserId', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AsyncJob', RelationMap::ONE_TO_MANY, array('account_id' => 'update_user_id', ), 'RESTRICT', null, 'AsyncJobsRelatedByUpdateUserId');
        $this->addRelation('UserNotificationRelatedByCreationUserId', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\UserNotification', RelationMap::ONE_TO_MANY, array('account_id' => 'creation_user_id', ), 'RESTRICT', null, 'UserNotificationsRelatedByCreationUserId');
        $this->addRelation('UserNotificationRelatedByUpdateUserId', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\UserNotification', RelationMap::ONE_TO_MANY, array('account_id' => 'update_user_id', ), 'RESTRICT', null, 'UserNotificationsRelatedByUpdateUserId');
    } // buildRelations()

} // AccountTableMap

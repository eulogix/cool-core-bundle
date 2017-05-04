<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core\map;

use \RelationMap;


/**
 * This class defines the structure of the 'core.pending_call' table.
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
class PendingCallTableMap extends \Eulogix\Cool\Lib\Database\Propel\CoolTableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Eulogix.Cool.Bundle.CoreBundle.Model.Core.map.PendingCallTableMap';

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
        $this->setName('core.pending_call');
        $this->setPhpName('PendingCall');
        $this->setClassname('Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\PendingCall');
        $this->setPackage('src.Eulogix.Cool.Bundle.CoreBundle.Model.Core');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('core.pending_call_pending_call_id_seq');
        // columns
        $this->addPrimaryKey('pending_call_id', 'PendingCallId', 'INTEGER', true, null, null);
        $this->addColumn('sid', 'Sid', 'LONGVARCHAR', false, null, null);
        $this->addColumn('recording_url', 'RecordingUrl', 'LONGVARCHAR', false, null, null);
        $this->addColumn('client_sid', 'ClientSid', 'LONGVARCHAR', false, null, null);
        $this->addColumn('creation_date', 'CreationDate', 'TIMESTAMP', false, null, null);
        $this->addForeignKey('caller_user_id', 'CallerUserId', 'INTEGER', 'core.account', 'account_id', false, null, null);
        $this->addColumn('target', 'Target', 'LONGVARCHAR', true, null, null);
        $this->addColumn('serialized_call', 'SerializedCall', 'LONGVARCHAR', false, null, null);
        $this->addColumn('properties', 'Properties', 'LONGVARCHAR', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Account', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\Account', RelationMap::MANY_TO_ONE, array('caller_user_id' => 'account_id', ), 'RESTRICT', null);
    } // buildRelations()

} // PendingCallTableMap

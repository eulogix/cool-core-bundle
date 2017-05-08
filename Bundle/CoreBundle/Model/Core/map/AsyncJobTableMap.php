<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core\map;

use \RelationMap;


/**
 * This class defines the structure of the 'core.async_job' table.
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
class AsyncJobTableMap extends \Eulogix\Cool\Lib\Database\Propel\CoolTableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'vendor.eulogix.cool-core-bundle.Bundle.CoreBundle.Model.Core.map.AsyncJobTableMap';

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
        $this->setName('core.async_job');
        $this->setPhpName('AsyncJob');
        $this->setClassname('Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\AsyncJob');
        $this->setPackage('vendor.eulogix.cool-core-bundle.Bundle.CoreBundle.Model.Core');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('core.async_job_async_job_id_seq');
        // columns
        $this->addPrimaryKey('async_job_id', 'AsyncJobId', 'INTEGER', true, null, null);
        $this->addForeignKey('issuer_user_id', 'IssuerUserId', 'INTEGER', 'core.account', 'account_id', false, null, null);
        $this->addColumn('context', 'Context', 'LONGVARCHAR', false, null, null);
        $this->addColumn('executor_type', 'ExecutorType', 'LONGVARCHAR', false, null, null);
        $this->addColumn('execution_id', 'ExecutionId', 'LONGVARCHAR', false, null, null);
        $this->addColumn('job_path', 'JobPath', 'LONGVARCHAR', false, null, null);
        $this->addColumn('parameters', 'Parameters', 'LONGVARCHAR', false, null, null);
        $this->addColumn('start_date', 'StartDate', 'TIMESTAMP', false, null, null);
        $this->addColumn('completion_date', 'CompletionDate', 'TIMESTAMP', false, null, null);
        $this->addColumn('completion_percentage', 'CompletionPercentage', 'INTEGER', false, null, null);
        $this->addColumn('outcome', 'Outcome', 'LONGVARCHAR', false, null, null);
        $this->addColumn('job_output', 'JobOutput', 'LONGVARCHAR', false, null, null);
        $this->addColumn('creation_date', 'CreationDate', 'TIMESTAMP', false, null, null);
        $this->addColumn('update_date', 'UpdateDate', 'TIMESTAMP', false, null, null);
        $this->addForeignKey('creation_user_id', 'CreationUserId', 'INTEGER', 'core.account', 'account_id', false, null, null);
        $this->addForeignKey('update_user_id', 'UpdateUserId', 'INTEGER', 'core.account', 'account_id', false, null, null);
        $this->addColumn('record_version', 'RecordVersion', 'INTEGER', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('AccountRelatedByIssuerUserId', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\Account', RelationMap::MANY_TO_ONE, array('issuer_user_id' => 'account_id', ), 'RESTRICT', null);
        $this->addRelation('AccountRelatedByCreationUserId', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\Account', RelationMap::MANY_TO_ONE, array('creation_user_id' => 'account_id', ), 'RESTRICT', null);
        $this->addRelation('AccountRelatedByUpdateUserId', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\Account', RelationMap::MANY_TO_ONE, array('update_user_id' => 'account_id', ), 'RESTRICT', null);
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
            'auditable' =>  array (
  'create_column' => 'creation_date',
  'created_by_column' => 'creation_user_id',
  'update_column' => 'update_date',
  'updated_by_column' => 'update_user_id',
  'version_column' => 'record_version',
  'target' => 'EulogixCoolCoreBundle/core',
),
        );
    } // getBehaviors()

} // AsyncJobTableMap

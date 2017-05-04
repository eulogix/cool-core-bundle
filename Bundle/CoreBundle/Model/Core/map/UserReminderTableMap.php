<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core\map;

use \RelationMap;


/**
 * This class defines the structure of the 'core.user_reminder' table.
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
class UserReminderTableMap extends \Eulogix\Cool\Lib\Database\Propel\CoolTableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Eulogix.Cool.Bundle.CoreBundle.Model.Core.map.UserReminderTableMap';

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
        $this->setName('core.user_reminder');
        $this->setPhpName('UserReminder');
        $this->setClassname('Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\UserReminder');
        $this->setPackage('src.Eulogix.Cool.Bundle.CoreBundle.Model.Core');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('core.user_reminder_user_reminder_id_seq');
        // columns
        $this->addPrimaryKey('user_reminder_id', 'UserReminderId', 'INTEGER', true, null, null);
        $this->addColumn('name', 'Name', 'LONGVARCHAR', true, null, null);
        $this->addColumn('type', 'Type', 'LONGVARCHAR', true, null, 'SIMPLE');
        $this->addColumn('category', 'Category', 'LONGVARCHAR', false, null, null);
        $this->addColumn('sort_order', 'SortOrder', 'INTEGER', false, null, null);
        $this->addColumn('lister', 'Lister', 'LONGVARCHAR', false, null, null);
        $this->addColumn('lister_translation_domain', 'ListerTranslationDomain', 'LONGVARCHAR', false, null, null);
        $this->addColumn('parent_tables', 'ParentTables', 'LONGVARCHAR', false, null, null);
        $this->addColumn('context_schema', 'ContextSchema', 'LONGVARCHAR', true, null, 'core');
        $this->addColumn('sql_query', 'SqlQuery', 'LONGVARCHAR', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
    } // buildRelations()

} // UserReminderTableMap

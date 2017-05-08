<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core\map;

use \RelationMap;


/**
 * This class defines the structure of the 'core.lister_config_column' table.
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
class ListerConfigColumnTableMap extends \Eulogix\Cool\Lib\Database\Propel\CoolTableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'vendor.eulogix.cool-core-bundle.Bundle.CoreBundle.Model.Core.map.ListerConfigColumnTableMap';

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
        $this->setName('core.lister_config_column');
        $this->setPhpName('ListerConfigColumn');
        $this->setClassname('Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\ListerConfigColumn');
        $this->setPackage('vendor.eulogix.cool-core-bundle.Bundle.CoreBundle.Model.Core');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('core.lister_config_column_lister_config_column_id_seq');
        // columns
        $this->addPrimaryKey('lister_config_column_id', 'ListerConfigColumnId', 'INTEGER', true, null, null);
        $this->addForeignKey('lister_config_id', 'ListerConfigId', 'INTEGER', 'core.lister_config', 'lister_config_id', true, null, null);
        $this->addColumn('name', 'Name', 'LONGVARCHAR', true, null, null);
        $this->addColumn('sortable_flag', 'SortableFlag', 'BOOLEAN', false, null, null);
        $this->addColumn('editable_flag', 'EditableFlag', 'BOOLEAN', false, null, null);
        $this->addColumn('show_summary_flag', 'ShowSummaryFlag', 'BOOLEAN', false, null, null);
        $this->addColumn('width', 'Width', 'LONGVARCHAR', false, null, null);
        $this->addColumn('cell_template', 'CellTemplate', 'LONGVARCHAR', false, null, null);
        $this->addColumn('cell_template_js', 'CellTemplateJs', 'LONGVARCHAR', false, null, null);
        $this->addColumn('column_style_css', 'ColumnStyleCss', 'LONGVARCHAR', false, null, null);
        $this->addColumn('sort_order', 'SortOrder', 'INTEGER', false, null, null);
        $this->addColumn('sortby_order', 'SortbyOrder', 'INTEGER', false, null, null);
        $this->addColumn('sortby_direction', 'SortbyDirection', 'LONGVARCHAR', false, null, null);
        $this->addColumn('truncate_chars', 'TruncateChars', 'INTEGER', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('ListerConfig', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\ListerConfig', RelationMap::MANY_TO_ONE, array('lister_config_id' => 'lister_config_id', ), 'CASCADE', null);
    } // buildRelations()

} // ListerConfigColumnTableMap

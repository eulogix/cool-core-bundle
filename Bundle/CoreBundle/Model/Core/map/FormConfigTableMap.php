<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core\map;

use \RelationMap;


/**
 * This class defines the structure of the 'core.form_config' table.
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
class FormConfigTableMap extends \Eulogix\Cool\Lib\Database\Propel\CoolTableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Eulogix.Cool.Bundle.CoreBundle.Model.Core.map.FormConfigTableMap';

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
        $this->setName('core.form_config');
        $this->setPhpName('FormConfig');
        $this->setClassname('Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\FormConfig');
        $this->setPackage('src.Eulogix.Cool.Bundle.CoreBundle.Model.Core');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('core.form_config_form_config_id_seq');
        // columns
        $this->addPrimaryKey('form_config_id', 'FormConfigId', 'INTEGER', true, null, null);
        $this->addColumn('name', 'Name', 'LONGVARCHAR', true, null, null);
        $this->addColumn('variation', 'Variation', 'LONGVARCHAR', false, null, null);
        $this->addColumn('layout', 'Layout', 'LONGVARCHAR', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('FormConfigField', 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\FormConfigField', RelationMap::ONE_TO_MANY, array('form_config_id' => 'form_config_id', ), 'CASCADE', null, 'FormConfigFields');
    } // buildRelations()

} // FormConfigTableMap

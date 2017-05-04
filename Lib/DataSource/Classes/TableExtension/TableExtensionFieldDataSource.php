<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\DataSource\Classes\TableExtension;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\FieldDefinition;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\TableExtensionField;
use Eulogix\Cool\Lib\Database\Propel\CoolPropelObject;
use Eulogix\Cool\Lib\DataSource\CoolCrudDataSource as CD;
use Eulogix\Cool\Lib\DataSource\CoolCrudTableRelation as Rel;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class TableExtensionFieldDataSource extends CD {

    public function __construct()
    {
        parent::__construct('core');
        $rels = [
            Rel::build()
                ->setTable('core.table_extension_field')
                ->setIsRequired(true)
                ->setDeleteFlag(true)
                ->setUpdateOrder(2),

            Rel::build()
                ->setTable('core.field_definition')
                ->setIsRequired(true)
                ->setJoinCondition('(table_extension_field.field_definition_id = field_definition.field_definition_id)')
                ->setUpdateOrder(1)

        ];

        foreach($rels as $r)
            $this->addRelation($r);
    }


    /**
     * @inheritdoc
     */
    protected function updateHook($tableQualifier, $currentObj, $savedObjects, $tableFillData) {
        switch($tableQualifier) {
            case 'core.table_extension_field': {
                /** @var TableExtensionField $currentObj */
                /** @var FieldDefinition[] $savedObjects */
                $currentObj->setFieldDefinitionId( $savedObjects['core.field_definition']->getPrimaryKey() );
                break;
            }
        }
        return true;
    }

}

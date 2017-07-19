<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\DataSource\Classes\Rules;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\Rule;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\WidgetRule;
use Eulogix\Cool\Lib\DataSource\CoolCrudDataSource as CD;
use Eulogix\Cool\Lib\DataSource\CoolCrudTableRelation as Rel;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class WidgetRulesDataSource extends CD {

    public function __construct()
    {
        $dsTables = [
            CD::PARAM_TABLE_RELATIONS=>[

                Rel::build()
                    ->setTable('core.widget_rule')
                    ->setDeleteFlag(true)
                    ->setUpdateOrder(2),

                Rel::build()
                    ->setTable('core.rule')
                    ->setDeleteFlag(false)
                    ->setUpdateOrder(1)
                    ->setJoinCondition('widget_rule.rule_id = rule.rule_id')
            ]
        ];

        $ret = parent::__construct('core', $dsTables);

        $this->addField('valid')->setType(\PropelTypes::BOOLEAN);
        $this->addField(Rule::REPORT_EXECUTION_TIME)->setType(\PropelTypes::INTEGER);
        $this->addField(Rule::REPORT_MEMORY_USAGE)->setType(\PropelTypes::INTEGER);

        return $ret;
    }

    /**
     * @inheritdoc
     */
    protected function updateHook($tableQualifier, $currentObj, $savedObjects, $tableFillData) {
        switch($tableQualifier) {
            case 'core.widget_rule': {
                /** @var WidgetRule $currentObj */
                /** @var Rule[] $savedObjects */
                $currentObj->setRuleId( $savedObjects['core.rule']->getPrimaryKey() );
                break;
            }
        }
        return true;
    }
}
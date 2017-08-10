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
use Eulogix\Cool\Lib\DataSource\DSRequest;

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
                    ->setJoinCondition('widget_rule.rule_id = rule.rule_id'),

                Rel::build()
                    ->setView('core.widget_rule_calc')
                    ->setIsRequired(false)
                    ->setAlias('calc')
                    ->setJoinCondition('calc.widget_rule_id = core.widget_rule.widget_rule_id')
            ]
        ];

        $ret = parent::__construct('core', $dsTables);

        $this->addField('valid')->setType(\PropelTypes::BOOLEAN);
        $this->addField(Rule::REPORT_EXECUTION_TIME)->setType(\PropelTypes::INTEGER);
        $this->addField(Rule::REPORT_MEMORY_USAGE)->setType(\PropelTypes::INTEGER);

        return $ret;
    }

    /**
     * we add the leaf column (bool) as the parameter which tells whether this record has or not children
     * @inheritdoc
     */
    public function getSqlSelect($parameters = array())
    {
        $p = parent::getSqlSelect($parameters);
        $s = ", calc.children_nr > 0 AS ".self::HAS_CHILDREN_IDENTIFIER;
        return $p.$s;
    }

    /**
     * @inheritdoc
     */
    public function getSqlWhere($parameters = array(), $query=null) {

        $ret = parent::getSqlWhere($parameters, $query);

        if(@$parameters[self::RECORD_IDENTIFIER])
            return $ret;

        $parentId = @$parameters[DSRequest::PARAM_PARENT_ID];

        if($parentId) {
            $ret['statement'].=" AND (parent_widget_rule_id = :parent_id) ";
            $ret['parameters'][':parent_id'] = (int) $parentId;
        } else $ret['statement'].=" AND (parent_widget_rule_id IS NULL) ";

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
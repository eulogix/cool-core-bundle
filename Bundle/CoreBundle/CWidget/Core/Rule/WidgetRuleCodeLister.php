<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\CWidget\Core\Rule;

use Eulogix\Cool\Lib\DataSource\Classes\Rules\WidgetRuleCodeDataSource;
use Eulogix\Cool\Lib\Lister\Lister;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class WidgetRuleCodeLister extends Lister {

    private $ruleLog = [];

    public function __construct($parameters = [])
    {
        parent::__construct($parameters);

        $this->ruleLog = json_decode(@$parameters['_ruleLog'], true) ?? [];
        $ds = new WidgetRuleCodeDataSource();
        $this->setDataSource($ds->build());
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return "COOL_WIDGET_RULE_CODE_LISTER";
    }

    public function processRows(&$rows) {
        parent::processRows($rows);

        $log = @$this->ruleLog['report']['codes'];

        foreach($rows as &$row) {
            $logRow = @$log[ $row['name'] ] ?? [];

           // $row['valid'] = $logRow['valid'] ?? false;
           // $row['logRow'] = json_encode($logRow);
            $row['return_value'] = @$logRow['return_value'];
            $row['execution_time'] = @$logRow['execution_time'];
            $row['evaluation_order'] = @$logRow['evaluation_order'];
        }

        return $this;
    }

}
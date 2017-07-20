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

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\DataSource\Classes\Rules\WidgetRuleCodeDataSource;
use Eulogix\Cool\Lib\Lister\Lister;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class WidgetRuleCodeLister extends Lister {

    private $executionLog = [];

    public function __construct($parameters = [])
    {
        parent::__construct($parameters);

        if($logKey = @$parameters['_logKey']) {
            $this->executionLog = json_decode(Cool::getInstance()->getFactory()->getSharedCacher()->fetch($logKey), true) ?? [];
        }

        $ds = new WidgetRuleCodeDataSource();
        $this->setDataSource($ds->build());
    }

    /**
     * @inheritdoc
     */
    public function build() {
        parent::build();
        $this->addAction('new Code')->setOnClick("widget.openNewRecordEditor();");
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultEditorServerId() {
        return 'EulogixCoolCore/Core/Rule/RuleCodeEditorForm';
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return "COOL_WIDGET_RULE_CODE_LISTER";
    }

    public function processRows(&$rows) {
        parent::processRows($rows);

        $globalLog = $this->executionLog;

        $ruleLog = @$globalLog[ $this->getParameters()->get('_rule_name') ] ?? [];

        $codesLog = @$ruleLog['report']['codes'];

        foreach($rows as &$row) {
            $logRow = @$codesLog[ $row['name'] ] ?? [];

           // $row['valid'] = $logRow['valid'] ?? false;
           // $row['logRow'] = json_encode($logRow);
            $row['return_value'] = @$logRow['return_value'];
            $row['execution_time'] = @$logRow['execution_time'];
            $row['evaluation_order'] = @$logRow['evaluation_order'];
        }

        return $this;
    }

}
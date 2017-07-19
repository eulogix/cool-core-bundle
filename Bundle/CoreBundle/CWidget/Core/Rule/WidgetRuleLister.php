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

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\Rule;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\DataSource\Classes\Rules\WidgetRulesDataSource;
use Eulogix\Cool\Lib\Lister\Lister;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class WidgetRuleLister extends Lister {

    private $executionLog = [];

    public function __construct($parameters = [])
    {
        parent::__construct($parameters);

        if($logKey = @$parameters['_logKey']) {
            $this->executionLog = json_decode(Cool::getInstance()->getFactory()->getCacher()->fetch($logKey), true) ?? [];
        }

        $ds = new WidgetRulesDataSource();
        $this->setDataSource($ds->build());
    }

    /**
     * @inheritdoc
     */
    public function build() {
        parent::build();
        $this->addAction('new Rule')->setOnClick("widget.openNewRecordEditor();");
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultEditorServerId() {
        return 'EulogixCoolCore/Core/Rule/WidgetRuleEditorForm';
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return "COOL_WIDGET_RULE_LISTER";
    }

    public function processRows(&$rows) {
        parent::processRows($rows);

        $log = $this->executionLog;

        foreach($rows as &$row) {
            $logRow = @$log[ $row['name'] ] ?? [];

            $row['valid'] = $logRow['valid'] ?? false;
            $row[Rule::REPORT_EXECUTION_TIME] = $logRow['report'][Rule::REPORT_EXECUTION_TIME] ?? null;
            $row[Rule::REPORT_MEMORY_USAGE] = $logRow['report'][Rule::REPORT_MEMORY_USAGE] ?? null;
        }

        return $this;
    }

}
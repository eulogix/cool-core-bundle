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

use Eulogix\Cool\Lib\DataSource\Classes\Rules\WidgetRulesDataSource;
use Eulogix\Cool\Lib\Lister\Lister;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class WidgetRuleLister extends Lister {

    public function __construct($parameters = [])
    {
        parent::__construct($parameters);
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

}
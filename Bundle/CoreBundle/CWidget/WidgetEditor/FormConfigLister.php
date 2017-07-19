<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\CWidget\WidgetEditor;

use Eulogix\Cool\Lib\DataSource\Classes\WidgetEditor\FormConfigDataSource;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class FormConfigLister extends WidgetConfigLister {

    public function __construct($parameters = [])
    {
        parent::__construct($parameters);
        $ds = new FormConfigDataSource();
        $this->setDataSource($ds->build());
    }

    /**
     * @inheritdoc
     */
    public function getDefaultEditorServerId() {
        return 'EulogixCoolCore/WidgetEditor/FormConfigEditorForm';
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return "COOL_FORM_CONFIG_LISTER";
    }

}
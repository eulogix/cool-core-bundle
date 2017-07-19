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

use Eulogix\Cool\Lib\DataSource\Classes\WidgetEditor\ListerConfigDataSource;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class ListerConfigLister extends WidgetConfigLister {

    public function __construct($parameters = [])
    {
        parent::__construct($parameters);
        $ds = new ListerConfigDataSource();
        $this->setDataSource($ds->build());
    }

    /**
     * @inheritdoc
     */
    public function getDefaultEditorServerId() {
        return 'EulogixCoolCore/WidgetEditor/ListerConfigEditorForm';
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return "COOL_LISTER_CONFIG_LISTER";
    }

}
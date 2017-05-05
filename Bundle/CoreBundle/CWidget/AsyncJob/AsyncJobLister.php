<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\CWidget\AsyncJob;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

use Eulogix\Cool\Lib\DataSource\Classes\AsyncJob\AsyncJobDataSource;
use Eulogix\Cool\Lib\Lister\Lister;

class AsyncJobLister extends Lister {

    public function __construct($parameters = [])
    {
        parent::__construct($parameters);
        $ds = new AsyncJobDataSource();
        $this->setDataSource( $ds->build() );
        $this->getAttributes()->set(self::ATTR_SHOW_TOOLS_COLUMN, false);
    }

    public function getDefaultEditorServerId() {
        return 'EulogixCoolCore/AsyncJob/AsyncJobEditorForm';
    }

    public function getId() {
        return "COOL_ASYNC_JOB_LISTER";
    }

}
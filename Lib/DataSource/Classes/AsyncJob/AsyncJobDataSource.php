<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\DataSource\Classes\AsyncJob;

use Eulogix\Cool\Lib\DataSource\CoolCrudDataSource as CD;
use Eulogix\Cool\Lib\DataSource\CoolCrudTableRelation as Rel;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class AsyncJobDataSource extends CD {

    public function __construct()
    {
        return parent::__construct('core', [
            CD::PARAM_TABLE_RELATIONS=>[

                Rel::build()
                    ->setTable('core.async_job')
            ]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getRowMeta($row) {
        return [
            self::META_RECORD_CAN_DELETE => false,
            self::META_RECORD_CAN_EDIT => true
        ];
    }
}
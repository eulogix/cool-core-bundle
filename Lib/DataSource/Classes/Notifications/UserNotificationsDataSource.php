<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\DataSource\Classes\Notifications;

use Eulogix\Lib\Database\Postgres\PgUtils;
use Eulogix\Cool\Lib\DataSource\CoolCrudDataSource as CD;
use Eulogix\Cool\Lib\DataSource\CoolCrudTableRelation as Rel;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class UserNotificationsDataSource extends CD {

    public function __construct()
    {
        return parent::__construct('core', [
            CD::PARAM_TABLE_RELATIONS=>[

                Rel::build()
                    ->setTable('core.user_notification')
                    ->setDeleteFlag(true),

            ]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getSqlSelect($parameters = []) {
        return parent::getSqlSelect($parameters).", creation_date";
    }

    /**
     * @inheritdoc
     */
    public function getSqlWhere($parameters = array(), $query=null) {
        $ret = parent::getSqlWhere($parameters, $query);

        if($contexts = explode(',',@$parameters['contexts'])) {
            $ret['statement'].=" AND context IN (".PgUtils::quoteStringsArray($contexts).")";
        }

        return $ret;
    }
}

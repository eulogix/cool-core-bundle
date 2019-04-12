<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core;


use \Propel;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\om\BaseUserReminderQuery;

class UserReminderQuery extends BaseUserReminderQuery
{
    public function orderByCategorySortOrder(){
        $columns = 'rem.*';
        $sql = "select $columns ".
            "   from core.user_reminder as rem inner join ".
            "   lookups.core_user_reminder_category as cat on rem.category=cat.value ".
            " order by cat.sort_order asc,rem.sort_order;";

        $con = Propel::getConnection(UserReminderPeer::DATABASE_NAME);
        $stmt = $con->prepare($sql);
        $stmt->execute();
        $formatter = new \PropelObjectFormatter();
        $formatter->setClass('UserReminder');
        $criteria = new UserReminderQuery();
        $formatter->init($criteria);
        return $formatter->format($stmt);
    }
}

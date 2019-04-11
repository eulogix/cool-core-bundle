<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Reminder;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\map\UserReminderTableMap;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserReminder;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserReminderPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserReminderQuery;
use Propel;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class CoolRemindersManager extends RemindersManager {

    /**
     * @var bool
     */
    private $initialized = false;

    /**
     * @inheritdoc
     */
    public function initialize()
    {
        if(!$this->initialized) {

            $tableMap = new UserReminderTableMap();
            $columns = $tableMap->getColumns();
            $columns = array_keys($columns);

            foreach ($columns as &$column){
                $column = "rem.".$column;
            }
            $columns = implode(", ",$columns);
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
            $userReminders = $formatter->format($stmt);

            foreach($userReminders as $ur) {
                /**
                 * @var UserReminder $ur
                 */
                $ds = new UserReminderDS($ur->getUserReminderId());
                $dsProvider = new DSReminderProvider($ds->build(),[]);
                $dsProvider->setType($ur->getType());
                $dsProvider->setCategory($ur->getCategory());

                if($lister = $ur->getLister())
                    $dsProvider->setDetailsLister($lister);
                $this->addProvider($ur->getName(), $dsProvider, $ur->getListerTranslationDomain());
            }

            $this->initialized = true;
        }
        return $this;
    }

}
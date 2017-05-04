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

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserReminder;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserReminderQuery;

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

            $userReminders = UserReminderQuery::create()->orderBySortOrder(\Criteria::ASC)->find();
            foreach($userReminders as $ur) {
                /**
                 * @var UserReminder $ur
                 */
                $ds = new UserReminderDS($ur->getUserReminderId());
                $dsProvider = new DSReminderProvider($ds->build(),[]);
                $dsProvider->setType($ur->getType());

                if($lister = $ur->getLister())
                    $dsProvider->setDetailsLister($lister);
                $this->addProvider($ur->getName(), $dsProvider, $ur->getListerTranslationDomain());
            }

            $this->initialized = true;
        }
        return $this;
    }

}
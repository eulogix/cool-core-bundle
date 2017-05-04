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

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

Interface ReminderInterface {

    /**
     * Returns a name that identifies the reminder
     * @return string
     */
    public function getName();

}
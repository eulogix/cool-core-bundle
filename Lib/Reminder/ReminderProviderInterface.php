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

use Eulogix\Cool\Lib\DataSource\DataSourceInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

Interface ReminderProviderInterface {

    const TYPE_SIMPLE = "SIMPLE";
    const TYPE_DATED = "DATED";

    /**
     * @return ParameterBag
     */
    public function getParameters();

    /**
     * returns either SIMPLE or DATED
     * @return string
     */
    public function getType();

    /**
     * returns all the pending reminders
     * @return integer
     */
    public function countAll();

    /**
     * returns the number of reminders at a given date
     * @param \DateTime $date
     * @return integer
     */
    public function countAtDate(\DateTime $date);

    /**
     * returns the number of reminders before a given date
     * @param \DateTime $date
     * @return integer
     */
    public function countBeforeDate(\DateTime $date);

    /**
     * returns the number of reminders after a given date
     * @param \DateTime $date
     * @return integer
     */
    public function countAfterDate(\DateTime $date);

    /**
     * returns the DataSource used to display the details for a given date
     * @return DataSourceInterface
     */
    public function getDetailsDataSource();

    /**
     * returns the serverId of the lister used to show the details
     * @return string
     */
    public function getDetailsLister();

}
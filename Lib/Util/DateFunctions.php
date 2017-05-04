<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Util;

use Eulogix\Cool\Lib\Cool;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class DateFunctions
{

    /**
     * returns the difference between two dates in minutes
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return int|mixed
     */
    public static function differenceInMinutes(\DateTime $startDate, \DateTime $endDate) {
        $sinceStart = $startDate->diff($endDate);
        $minutes = $sinceStart->days * 24 * 60;
        $minutes += $sinceStart->h * 60;
        $minutes += $sinceStart->i;
        return $minutes;
    }

    /**
     * @param \DateTime $date
     * @return boolean
     */
    public static function isWeekend(\DateTime $date) {
        $core = Cool::getInstance()->getCoreSchema();
        return $core->fetch("SELECT weekend FROM lookups._date WHERE \"date\"=:date", [':date'=>$date->format('c')]);
    }

    /**
     * @param \DateTime $date
     * @param string $country
     * @return bool
     */
    public static function isHoliday(\DateTime $date, $country) {
        $core = Cool::getInstance()->getCoreSchema();
        $country = in_array($country, ['us','it','es','pt','gr']) ? $country : 'us';
        return $core->fetch("SELECT holiday_{$country} FROM lookups._date WHERE \"date\"=:date", [':date'=>$date->format('c')]);
    }

    /**
     * @param \DateTime $date
     * @return bool
     */
    public static function isToday(\DateTime $date) {
        return self::daysDiff(new \DateTime('now',$date->getTimezone()), $date) == 0;
    }

    /**
     * @param \DateTime $date
     * @return bool
     */
    public static function isTomorrow(\DateTime $date) {
        return self::daysDiff(new \DateTime('now',$date->getTimezone()), $date) == 1;
    }

    /**
     * @param \DateTime $date
     * @return bool
     */
    public static function isYesterday(\DateTime $date) {
        return self::daysDiff(new \DateTime('now',$date->getTimezone()), $date) == -1;
    }

    /**
     * @param \DateTime $date1
     * @param \DateTime $date2
     * @return int
     */
    public static function daysDiff(\DateTime $date1, \DateTime $date2) {
        $workDate1 = clone $date1;
        $workDate1->setTime( 0, 0, 0 );

        $workDate2 = clone $date2;
        $workDate2->setTime( 0, 0, 0 );

        $diff = $workDate1->diff( $workDate2 );
        return (integer) $diff->format( "%R%a" ); // Extract days count in interval
    }
}
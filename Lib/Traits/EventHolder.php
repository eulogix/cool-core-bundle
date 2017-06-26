<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Traits;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

trait EventHolder {

    /**
     * @var array
     */
    private $events;

    /**
     * @param string $eventName
     * @param array $payload
     */
    public function addEvent($eventName, $payload=[]) {
        $this->events[] = ['event'=>$eventName, 'payload'=>$payload];
    }

    /**
     * @return array
     */
    public function getEvents() {
        return $this->events;
    }

    /**
     * @return bool
     */
    public function hasEvents() {
        return !empty($this->events);
    }

    /**
     * @param string $eventName
     * @return bool
     */
    public function hasEvent($eventName) {
        foreach($this->getEvents() as $evt)
            if($evt['event'] == $eventName)
                return true;
        return false;
    }
    
}
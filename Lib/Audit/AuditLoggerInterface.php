<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Audit;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

interface AuditLoggerInterface {

    /**
     * attaches an event to whatever head may be current at the moment.
     * Use this method when the calling code may come from an event chain (usually anything but UI interactions)
     *
     * @param AuditEventInterface $event
     * @param AuditEventInterface|null $father
     * @return $this
     */
    public function stackEvent(AuditEventInterface $event, AuditEventInterface $father=null);

    /**
     * Pushes the event as a new root. Call this method when the calling code wants to ensure
     * that the event is the root of the chain.
     * This is particularly useful for UI interactions
     *
     * @param AuditEventInterface $event
     * @return $this
     */
    public function rootEvent(AuditEventInterface $event);

    /**
     * Persists the events to a durable store
     *
     * @return bool
     */
    public function saveEvents();
} 
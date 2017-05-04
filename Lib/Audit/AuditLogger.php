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

class AuditLogger implements  AuditLoggerInterface {

    /**
     * @var AuditEventInterface[]
     */
    private $rootEvents = [];

    /**
     * @var AuditEventInterface
     */
    private $head = null;

    /**
     * @var AuditEventInterface
     */
    private $headRoot = null;

    /**
     * @inheritdoc
     */
    public function stackEvent(AuditEventInterface $event, AuditEventInterface $father=null) {
        if($father == null) {
            if($this->head)
                $this->stackEvent($event, $this->head);
            else {
                $this->rootEvents[] = $event;
                $this->head = $event;
                $this->headRoot = $event;
            }
        } elseif($father->isClosed()) {
            if(!$father->getFather()) {
                $this->head = $this->headRoot = null;
            }
            $this->stackEvent($event, $father->getFather());
        } else {
            $father->addChild($event);
            if($this->headRoot)
                $event->setRoot($this->headRoot);
            $this->head = $event;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function rootEvent(AuditEventInterface $event)
    {
        $this->head = null;
        $this->stackEvent($event);
    }

    /**
     * Persists the events to a durable store
     *
     * @return bool
     */
    public function saveEvents()
    {
        foreach($this->rootEvents as $event) {
            if(!$event->save())
                return false;
        }
        return true;
    }

}
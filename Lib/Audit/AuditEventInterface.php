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

interface AuditEventInterface {
    /**
     * @param AuditEventInterface $root
     * @return $this
     */
    public function setRoot(AuditEventInterface $root);

    /**
     * @return AuditEventInterface|null
     */
    public function getRoot();
    
    /**
     * @param AuditEventInterface $father
     * @return $this
     */
    public function setFather(AuditEventInterface $father);

    /**
     * @return AuditEventInterface|null
     */
    public function getFather();

    /**
     * @param AuditEventInterface $event
     * @return mixed
     */
    public function addChild(AuditEventInterface $event);

    /**
     * @return AuditEventInterface[]|null
     */
    public function getChildren();

    public function close();

    public function isClosed();

    public function discard();

    public function isDiscarded();

    /**
     * generic getters and setters for event properties
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setProperty($name, $value);

    /**
     * @param string $name
     * @return mixed
     */
    public function getProperty($name);

    /**
     * returns a hash propertyName => propertyValue
     * @return []
     */
    public function getProperties();


    /**
     * persists the event to a durable store (log file..)
     * @return bool
     */
    public function save();

    /**
     * returns an array that maps object (table) names to numeric ids
     * @return []
     */
    public static function getObjectTypes();

}
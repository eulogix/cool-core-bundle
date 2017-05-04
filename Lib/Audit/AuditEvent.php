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

abstract class AuditEvent implements AuditEventInterface {

    /**
     * @var AuditEventInterface
     */
    private $father, $root;

    /**
     * @var AuditEventInterface[]
     */
    private $children;

    /**
     * @var bool
     */
    private $closed = false;

    /**
     * @var bool
     */
    private $discarded = false;

    /**
     * @var []
     */
    private $properties = [];


    /**
     * @inheritdoc
     */
    public function setFather(AuditEventInterface $father)
    {
        $this->father = $father;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFather()
    {
        return $this->father;
    }

    /**
     * @inheritdoc
     */
    public function setRoot(AuditEventInterface $root)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @inheritdoc
     */
    public function close()
    {
        $this->closed = true;
    }

    /**
     * @inheritdoc
     */
    public function discard()
    {
        $this->discarded = true;
    }

    /**
     * @inheritdoc
     */
    public function isClosed()
    {
        return $this->closed;
    }

    /**
     * @inheritdoc
     */
    public function isDiscarded()
    {
        return $this->discarded;
    }

    /**
     * @inheritdoc
     */
    public function addChild(AuditEventInterface $event)
    {
        $event->setFather($this);
        $this->children[] = $event;
    }

    /**
     * @return AuditEventInterface[]|null
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @inheritdoc
     */
    public function setProperty($name, $value)
    {
        $this->properties[ $name ] = $value;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getProperty($name)
    {
        return $this->properties[ $name ];
    }


    /**
     * @inheritdoc
     */
    public function getProperties()
    {
        return $this->properties;
    }

}
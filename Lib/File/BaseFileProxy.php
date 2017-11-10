<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\File;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

abstract class BaseFileProxy implements FileProxyInterface {

    /**
     * @var string
     */
    public $name, $basename, $extension, $id, $parentId, $hash;

    /**
     * @var boolean
     */
    public $isDirectory;

    /**
     * @var array
     */
    public $properties = [];

    /**
     * @var \DateTime
     */
    public $creationDate, $lastModificationDate;

    /**
     * @var int
     */
    public $size;

    /**
     * safe defaults
     */
    public function __construct() {
        $this->setCreationDate( new \DateTime() );
        $this->setLastModificationDate( new \DateTime() );
    }

    /**
     * @return string
     */
    public function getBasename()
    {
        return $this->basename;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash ? $this->hash : $this->hash = sha1($this->getContent());
    }

    /**
     * @param string $hash
     * @return $this
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize() {
        return $this->size ? $this->size : strlen($this->getContent());
    }

    /**
     * @inheritdoc
     */
    public function isEmpty()
    {
        return $this->getSize() == 0;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param array $properties
     * @return $this
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @return boolean
     */
    public function isDirectory()
    {
        return $this->isDirectory;
    }

    /**
     * @param $propertyName
     * @return mixed
     */
    public function getProperty($propertyName)
    {
        return @$this->getProperties()[$propertyName];
    }

    /**
     * @return \DateTime
     */
    public function getLastModificationDate()
    {
        return $this->lastModificationDate;
    }

    /**
     * @param $propertyName
     * @param $propertValue
     * @return $this
     */
    public function setProperty($propertyName, $propertValue)
    {
        $this->properties[$propertyName] = $propertValue;
    }


    /**
     * @param boolean $isDirectory
     * @return $this
     */
    public function setIsDirectory($isDirectory)
    {
        $this->isDirectory = $isDirectory;
        return $this;
    }

    /**
     * @param \DateTime $lastModificationDate
     * @return $this
     */
    public function setLastModificationDate($lastModificationDate)
    {
        $this->lastModificationDate = $lastModificationDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * @param \DateTime $creationDate
     * @return $this
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
        return $this;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setParentId($id)
    {
        $this->parentId = $id;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        $pi = mb_pathinfo($name);
        if(!$this->isDirectory()) {
            $this->extension = @$pi[ 'extension' ];
        }
        $this->basename = @$pi[ 'basename' ];

        return $this;
    }

    /**
     * @param int $size
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

 }
<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Dictionary;

use Eulogix\Cool\Lib\DataSource\Bean;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class FileCategory extends Bean {

    /**
     * @var string
     */
    private $name;

    /**
     * @var string[]
     */
    private $extensions;

    /**
     * @var integer
     */
    private $maxSizeMb, $maxCount;

    /**
     * @var boolean
     */
    private $default, $hidden;

    /**
     * @param boolean $default
     * @return $this
     */
    public function setDefault($default)
    {
        $this->default = $default;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isDefault()
    {
        return $this->default;
    }

    /**
     * @return boolean
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     * @param boolean $hidden
     * @return FileCategory
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
        return $this;
    }

    /**
     * @param \string[] $extensions
     * @return $this
     */
    public function setExtensions($extensions)
    {
        $this->extensions = is_array($extensions) ? $extensions : explode(',',$extensions);
        return $this;
    }

    /**
     * @return \string[]
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * @param int $maxCount
     * @return $this
     */
    public function setMaxCount($maxCount)
    {
        $this->maxCount = $maxCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxCount()
    {
        return $this->maxCount ? $this->maxCount : PHP_INT_MAX;
    }

    /**
     * @param int $maxSizeMb
     * @return $this
     */
    public function setMaxSizeMb($maxSizeMb)
    {
        $this->maxSizeMb = $maxSizeMb;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxSizeMb()
    {
        return $this->maxSizeMb;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

}
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

use Eulogix\Cool\Lib\DataSource\ValueMapInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class FileProperty
{
    /**
     * @var string
     */
    private $name, $controlType;

    /**
     * @var ValueMapInterface
     */
    private $valueMap;

    /**
     * @var boolean
     */
    private $showInList;

    /**
     * FileProperty constructor.
     * @param string $name
     * @param string $controlType
     * @param ValueMapInterface $valueMap
     * @param bool $showInList
     */
    public function __construct($name, $controlType, $showInList, ValueMapInterface $valueMap = null)
    {
        $this->name = $name;
        $this->controlType = $controlType;
        $this->valueMap = $valueMap;
        $this->showInList = $showInList;
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
    public function getControlType()
    {
        return $this->controlType;
    }

    /**
     * @return boolean
     */
    public function isShowInList()
    {
        return $this->showInList;
    }

    /**
     * @return ValueMapInterface
     */
    public function getValueMap()
    {
        return $this->valueMap;
    }

    /**
     * @return array
     */
    public function toArray() {
        return [
            'name'              => $this->getName(),
            'controlType'       => $this->getControlType(),
            'showInList'        => $this->showInList
        ];
    }
}
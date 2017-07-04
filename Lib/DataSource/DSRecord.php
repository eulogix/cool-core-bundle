<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\DataSource;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class DSRecord {
    
    private $values = [];

    /**
     * @var DataSourceInterface
     */
    private $dataSource;

    public function __construct(DataSourceInterface $ds = null) {
        $this->dataSource = $ds;
    }
    
    /**
    * @return boolean
    */
    public function isNew() {
        return $this->getRecordId() === null;
    }
    
    /**
    * @param mixed $values
    * @return DSRecord
    */
    public function setValues($values) {
        $this->values = $values;        
        return $this;
    }
    
    /**
    * @return mixed
    */
    public function getValues() {
        return $this->values;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name) {
        return isset($this->values[$name]) ? $this->values[$name] : null;
    }

    /**
     * @return mixed|null
     */
    public function getRecordId() {
        if(!$this->dataSource)
            return null;

        return isset($this->values[ $this->dataSource->getPrimaryKey() ]) ? $this->values[ $this->dataSource->getPrimaryKey() ] : null;
    }

}
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

use Eulogix\Lib\Error\ErrorReport;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class DSResponse extends Bean
{

    const STATUS_TRANSACTION_FAILED = 't_fail';
    const STATUS_TRANSACTION_SUCCESS = 't_ok';
    const STATUS_VALIDATION_ERROR = 't_validation_fail';

    /**
     * @var array
     */
    protected $data, $summary;

    /**
     * @var int
     */
    var $startRow, $endRow, $totalRows;

    /**
     * @var string
     */
    var $status;

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @var DataSourceInterface
     */
    private $dataSource;

    /**
     * @var ErrorReport
     */
    private $errors;

    public function __construct(DataSourceInterface $ds)
    {
        $this->dataSource = $ds;
        $this->errors = new ErrorReport();
    }

    /**
     * @param array $value
     * @return $this
     */
    public function setData($value)
    {
        $this->data = empty( $value ) ? null : (Array)$this->_cleanData($value);
        return $this;
    }

    /**
     * @param array $data
     * @return array mixed
     */
    protected function _cleanData(&$data)
    {
        if (is_array($data)) {
            foreach ($data as &$delem) {
                $this->_cleanData($delem);
            }
        } else {
            $this->_cleanLeaf($data);
        }

        return $data;
    }

    /**
     * @param mixed $leaf
     */
    protected function _cleanLeaf(&$leaf)
    {
        if ($leaf instanceof \DateTime) {
            $leaf = $leaf->format('c'); //formats datetime objects in the format expected by smartclient
        } elseif ($leaf === '0') {
            $leaf = 0;
        }
    }

    /**
     * @param array $summary
     * @return $this
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * @return array
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setStartRow($value)
    {
        $this->startRow = (int)$value;
        return $this;
    }

    /**
     * @return int
     */
    public function getStartRow()
    {
        return $this->startRow;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setEndRow($value)
    {
        $this->endRow = (int)$value;
        return $this;
    }

    /**
     * @return int
     */
    public function getEndRow()
    {
        return $this->endRow;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setTotalRows($value)
    {
        $this->totalRows = (int)$value;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalRows()
    {
        return $this->totalRows;
    }

    /**
     * @param string $groupField
     * @param string $groupValue
     * @return int
     */
    public function getCount($groupField="", $groupValue="") {
        if($data = $this->getData())
            foreach($data as $row)
                if(@$row[$groupField] == $groupValue)
                    return $row['count'];
        return 0;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setStatus($value)
    {
        switch ($value) {
            case false :
                $this->status = self::STATUS_TRANSACTION_FAILED;
                break;
            case true :
                $this->status = self::STATUS_TRANSACTION_SUCCESS;
                break;
            default :
                $this->status = $value;
                break;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $field
     * @param string $message
     */
    public function addError($field, $message)
    {
        $this->errors->addError($field, $message);
    }

    /**
     * @param string $message
     */
    public function addGeneralError($message)
    {
        $this->errors->addGeneralError($message);
    }

    /**
     * @return ErrorReport
     */
    public function getErrorReport()
    {
        return $this->errors;
    }

    /**
     * @param ErrorReport $errorReport
     * @return DSResponse
     */
    public function setErrorReport($errorReport)
    {
        $this->errors = $errorReport;
    }

    /**
     * @return array
     */
    public function getArray()
    {
        return array('response' => (Array)$this);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return DSResponse
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[ $name ] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getAttribute($name)
    {
        return @$this->attributes[ $name ];
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return DSRecord
     */
    public function getDSRecord()
    {
        $record = new DSRecord($this->dataSource);
        $record->setValues($this->getData());

        return $record;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getRows() {
        $data = $this->getData();
        $ret = $this->getTotalRows() == 1 && array_key_exists($this->dataSource->getPrimaryKey(), $data) ? [$this->getData()] : $this->getData();
        return $ret;
    }

    /**
     * @return bool
     */
    public function isSuccess() {
        return $this->getStatus() == self::STATUS_TRANSACTION_SUCCESS;
    }

}
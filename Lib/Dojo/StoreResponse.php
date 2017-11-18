<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Dojo;

use Eulogix\Lib\Error\ErrorReport;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class StoreResponse {

    const STATUS_TRANSACTION_FAILED = 't_fail';
    const STATUS_TRANSACTION_SUCCESS = 't_ok';
    const STATUS_VALIDATION_ERROR  = 't_validation_fail';

    /**
     * @var mixed
     */
    private $data;

    /**
     * @var mixed
     */
    private $summary;

    /**
     * @var integer
     */
    private $startRow, $endRow, $totalRows;

    /**
     * @var string
     */
    var $status;

    /**
     * @var ErrorReport
     */
    private $errors;

    public function __construct() {
        $this->errors = new ErrorReport();
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data ? $this->data : [];
    }

    /**
     * @param int $endRow
     * @return $this
     */
    public function setEndRow($endRow)
    {
        $this->endRow = $endRow;
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
     * @param int $startRow
     * @return $this
     */
    public function setStartRow($startRow)
    {
        $this->startRow = $startRow;
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
     * @param mixed $summary
     * @return $this
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param int $totalRows
     * @return $this
     */
    public function setTotalRows($totalRows)
    {
        $this->totalRows = $totalRows;
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
     * @param mixed $value
     * @return $this
     */
    public function setStatus($value) {
        if($value === false)
            $this->status = self::STATUS_TRANSACTION_FAILED;
        elseif($value === true)
            $this->status = self::STATUS_TRANSACTION_SUCCESS;
        else $this->status = $value;
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
    public function addError($field, $message) {
        $this->errors->addError($field, $message);
    }

    /**
     * @return ErrorReport
     */
    public function getErrorReport() {
        return $this->errors;
    }

    /**
     * @param ErrorReport $errorReport
     * @return $this
     */
    public function setErrorReport( $errorReport ) {
        $this->errors = $errorReport;
        return $this;
    }

    /**
     * returns a formatted response that represents the object
     * @return array
     */
    public function getResponseData() {
        $responseData = [];

        if($this->getSummary())
            $responseData['_summary'] = $this->getSummary();

        if($this->errors->hasErrors())
            $responseData['_errors'] = $this->errors->getGeneralErrors();

        if($this->getStatus() != self::STATUS_TRANSACTION_SUCCESS) {
            $responseData['_success'] = false;
            $responseData['_status'] = $this->getStatus();
        }

        $responseData = array_merge($this->getData(), $responseData);

        return $responseData;
    }
}
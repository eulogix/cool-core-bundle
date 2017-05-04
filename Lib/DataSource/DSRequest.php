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

class DSRequest {
        
    const OPERATION_TYPE_COUNT = 'count';
    const OPERATION_TYPE_FETCH = 'fetch';
    const OPERATION_TYPE_ADD = 'add';
    const OPERATION_TYPE_UPDATE = 'update';
    const OPERATION_TYPE_REMOVE = 'remove';

    const SORT_ASC = 'A';
    const SORT_DESC = 'D';

    const SUMMARY_TOTAL = 'total';

    const PARAM_PARENT_ID = '_parent_id';

    /**
    * @var string
    */
    //private $datasourceName, $operationType;
    
    private $starRow, $endRow;
    private $operationId, $operationType;
    private $sortBy;

    /**
     * @var array[]
     */
    private $query;

    private $parameters = [];
    
    private $oldValues, $newValues;

    /**
     * wether or not to include _dec_* fields in the response
     * @var boolean
     */
    private $includeDecodings;

    /**
     * wether or not to include meta information (canDelete, canEdit...) in the response
     * @var boolean
     */
    private $includeMeta;

    /**
     * wether or not to include 1:1 files
     * @var boolean
     */
    private $includeFiles = true;

    /**
     * wether or not to include an additional string field that describes each record
     * @var boolean
     */
    private $includeRecordDescriptions = false;

    /**
     * array of field names for which we want to return a summary value
     * @var string[]
     */
    private $summaryFields;

    /**
     * for count grouped, the fields we want to get grouped counts for
     * @var string[]
     */
    private $groupCountFields;

    /**
    * Returns the index of the last requested record
    * @return integer
    */
    public function getEndRow() {
        return $this->endRow;
    }

    /**
     * @param $fieldName
     * @return $this
     */
    public function addSummaryFor($fieldName) {
        $this->summaryFields[$fieldName] = self::SUMMARY_TOTAL;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getSummaryFields() {
        return $this->summaryFields;
    }

    /**
     * @return \string[]
     */
    public function getGroupCountFields()
    {
        return $this->groupCountFields;
    }

    /**
     * @param \string[] $groupCountFields
     * @return $this
     */
    public function setGroupCountFields($groupCountFields)
    {
        $this->groupCountFields = $groupCountFields;
        return $this;
    }

    /**
     * @param \array[] $query
     * @return $this
     */
    public function setQuery($query)
    {
        $this->query = $query;
        return $this;
    }

    /**
     * @return \array[]
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
    * Returns the value for a particular fieldName
    * @param mixed $fieldName
    * @return mixed
    */
    public function getFieldValue($fieldName) {
        return $this->newValues[$fieldName];
    }

    /**
    * For an "update" or "remove" operation, returns the complete original record as it was delivered to the client, as a set of key-value pairs where the keys are field names and the values are field values.
    * @return mixed
    */
    public function getOldValues() {
        return $this->oldValues;
    }

    /**
    * Optional operationId passed by the client
    * @return string
    */
    public function getOperationId() {
        return $this->operationId;
    }

    /**
    * eturns the type of this DataSource operation
    * @return string
    */
    public function getOperationType() {
      return $this->operationType;
    }

    /**
    * The sortBy specification is only valid for the fetch operation since it specifies the sort order for the returned data.
    * @return mixed
    */
    public function getSortByFields() {
        return $this->sortBy;
    }

    /**
    * When components that are capable or showing multiple records at once are bound to datasources with large datasets, it becomes important to only send those records that are currently visible in the component (or can become visible with a typical user action).
    * @return integer
    */
    public function getStartRow() {
        return $this->starRow;
    }

    /**
    * Returns a list of uploaded files
    * @return mixed
    */
    public function getUploadedFiles() {}

    /**
    * Returns the values for this operation as a set of key-value pairs where the keys are field names and the values are field values.
    * @return mixed
    */
    public function getValues() {
        return $this->newValues;
    }

    /**
    * Returns the values in the request as a List, even if singular.
    * @return mixed[]
    */
    public function getValueSets() {}

    /**
    * Returns true if the current request is requesting a partial set of data using startRow/endRow parameters.
    * @return boolean
    */
    public function isPaged() {}

    /**
    * Sets the index of the last requested record
    * 
    * @param integer $endRow
    * @return DSRequest
    */
    public function setEndRow($endRow) {
        $this->endRow = $endRow; 
        return $this;   
    }

    /**
    *  Sets the value for a particular fieldName, in criteria or values according to the operation type.
    * 
    * @param string $fieldName
    * @param mixed $value
    * @return DSRequest
    */
    public function setFieldValue($fieldName, $value) {
        
    }

    /**
     * @return boolean
     */
    public function getIncludeRecordDescriptions()
    {
        return $this->includeRecordDescriptions;
    }

    /**
     * @param boolean $includeRecordDescriptions
     * @return $this
     */
    public function setIncludeRecordDescriptions($includeRecordDescriptions)
    {
        $this->includeRecordDescriptions = $includeRecordDescriptions;
        return $this;
    }

    /**
     * @param boolean $includeDecodings
     * @return $this
     */
    public function setIncludeDecodings($includeDecodings)
    {
        $this->includeDecodings = $includeDecodings;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getIncludeDecodings()
    {
        return $this->includeDecodings;
    }

    /**
     * @param boolean $includeMeta
     * @return $this
     */
    public function setIncludeMeta($includeMeta)
    {
        $this->includeMeta = $includeMeta;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getIncludeMeta()
    {
        return $this->includeMeta;
    }

    /**
     * @param boolean $includeFiles
     * @return $this
     */
    public function setIncludeFiles($includeFiles)
    {
        $this->includeFiles = $includeFiles;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getIncludeFiles()
    {
        return $this->includeFiles;
    }

    /**
    *  Sets the "old values" for this DSRequest (ie, the values as they were before the set of changes represented by this request)
    * 
    * @param mixed $oldValues
    * @return DSRequest
    */
    public function setOldValues($oldValues) {
        $this->oldValues = $oldValues;  
        return $this;  
    }

    /**
    * Sets the operation id
    * @param string $operationId
    * @return DSRequest
    */
    public function setOperationId($operationId) {
        $this->operationId = $operationId;
        return $this;
    }

    /**
    * Sets the operation type
    * @param string $operationType
    * @return DSRequest
    */
    public function setOperationType($operationType) {
        $this->operationType = $operationType;
        return $this;
    }
         
    /**
    * Sets the field or fields to sort by
    * @param mixed $sortBy
    * @return DSRequest
    */
    public function setSortBy($sortBy) {
        $this->sortBy = $sortBy;  
        return $this;
    }

    /**
    * Sets the index of the first requested record.
    * @param integer $startRow
    * @return DSRequest
    */
    public function setStartRow($startRow) {
        $this->starRow = $startRow;
        return $this;
    }

    /**
    * Sets the values for this DSRequest.
    * @param mixed $values
    * @return DSRequest
    */
    public function setValues($values) {
        $this->newValues = $values;
        return $this;
    }
    
    /**
    * sets raw parameters that can be used by the datasource to filter records, or parametrize any logic 
    * currently avoids the implementation of criteria
    * 
    * @param mixed $parameters
    * @return DSRequest
    */
    public function setParameters($parameters) {
        $this->parameters = $parameters ? $parameters : [];
        return $this;
    }

    /**
     * @return array
     */
    public function getParameters() {
        return $this->parameters;
    }
}
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

use Eulogix\Cool\Lib\Factory\Factory;
use Eulogix\Cool\Lib\Form\Field\FieldInterface;
use Eulogix\Lib\Progress\ProgressTracker;
use Eulogix\Cool\Lib\Traits\ParametersHolder;
use Eulogix\Cool\Lib\Util\DataFormatter;
use Eulogix\Lib\Error\ErrorReport;
use Eulogix\Lib\Traits\ProgressTrackerHolder;
use Eulogix\Lib\Validation\BeanValidatorInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

abstract class BaseDataSource implements DataSourceInterface {

    use ParametersHolder, ProgressTrackerHolder;

    /**
     * @var BeanValidatorInterface
     */
    protected $validator;

    /**
     * @var array
     */
    private $baseQuery;

    /**
     * @var \DateTime
     */
    private $instant;

    /**
    * @var DSField[]
    */
    private $fields = [];

    /**
     * @var bool
     */
    private $readOnly = false;

    /**
     * @inheritdoc
     */
    public function build($parameters=[])
    {
        return $this;
    }

    /**
    * @inheritdoc
    */
    public function addField($fieldName, DSField $field = null) {
        $this->fields[$fieldName] = $field ? $field : new DSField($fieldName);
        return $this->getField($fieldName);
    }

    /**
    * @inheritdoc
    */
    public function addFields($fields) {
        foreach($fields as $field)
            if(!$this->hasField($field->getName()))
                $this->addField($field->getName(), $field);
    }

    /**
    * @inheritdoc
    */
    public function hasField($fieldName) {
        return isset($this->fields[$fieldName]);
    }

    /**
    * @inheritdoc
    */
    public function removeField($fieldName) {
        if($this->hasField($fieldName)) {
            unset($this->fields[$fieldName]);
            return true;
        }
        return false;
    }

    /**
    * @inheritdoc
    */
    public function getField($fieldName) {
        return $this->hasField($fieldName) ? $this->fields[$fieldName] : false;
    }

    /**
    * @inheritdoc
    */
    public function getFields() {
        return $this->fields;
    }
    
    /**
    * @inheritdoc
    */
    public function getFieldNames() {
        return array_keys($this->fields);
    }
    
    /**
    * @inheritdoc 
    */
    public function getPrimaryKey() {
        foreach($this->fields as $fieldName => $field) {
            if($field->isPrimaryKey()) {
                return $fieldName;
            }
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getValidator() {
        if(!$this->validator) {
            $this->validator = Factory::getNewBeanValidator();
        }
        return $this->validator;
    }

    /**
    * @inheritdoc
    */
    public function validate($data, $reportMissingRequiredFields=true) {
        $errorReport = new ErrorReport();
        $errorList = $this->getValidator()->validateHash( $data );
        if($errorList->getFlatViolations()->count() > 0) {
            $allViolations = $errorList->getViolations();
            foreach($allViolations as $fieldName => $fieldViolations) {
                foreach($fieldViolations as $violation) {
                    $errorReport->addError($fieldName, $violation->getMessage());
                }
            }
        }
        return $errorReport;
    }

    /**
     * @inheritdoc
     */
    public function execute(DSRequest $req) {

        if($this->isInAuditMode() && !in_array( $req->getOperationType(), [$req::OPERATION_TYPE_COUNT, $req::OPERATION_TYPE_FETCH] )) {
            $dsresponse = new DSResponse($this);
            $dsresponse->setStatus(false);
            $dsresponse->addGeneralError("DATASOURCE IS IN AUDIT MODE");
            return $dsresponse;
        }

        switch( $req->getOperationType() ) {
            case $req::OPERATION_TYPE_COUNT : {
                return $this->executeCount($req);
            }
            case $req::OPERATION_TYPE_FETCH : {
                if($bq = $this->getBaseQuery()) {
                    if($rq = $req->getQuery())
                        $req->setQuery($cq = $this->getDSQuery()->_AND([$bq, $rq]));
                    else $req->setQuery($bq);
                }
                return $this->executeFetch($req);
            }
            case $req::OPERATION_TYPE_REMOVE : {
                return $this->executeRemove($req);
            }
            case $req::OPERATION_TYPE_UPDATE : {
                return $this->executeUpdate($req);
            }
            case $req::OPERATION_TYPE_ADD : {
                return $this->executeAdd($req);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function count(DSRequest $dsRequest) {
        $wkRequest = clone $dsRequest;
        $wkRequest->setOperationType(DSRequest::OPERATION_TYPE_COUNT);
        $wkRequest->setIncludeMeta(false);
        $wkRequest->setIncludeDecodings(false);

        $dsResponse = $this->execute($wkRequest);
        if($dsResponse->getStatus() == DSResponse::STATUS_TRANSACTION_SUCCESS) {
            return $dsResponse->getTotalRows();
        }

        return null;
    }

    /**
     * @param array $rows
     * @return mixed
     */
    protected function addDecodedValuesToRows( array $rows ) {
        $ret = $rows;
        $i = 0; $rowsNr = count($rows);
        foreach($ret as &$row) {
            $row = $this->addDecodedValuesToHash($row);
            $this->getProgressTracker()->logProgress(100*$i++/$rowsNr);
        }
        return $ret;
    }

    /**
     * @param array $hash
     * @return array
     */
    public function getDecodedHash($hash) {
        $decodifications = [];
        foreach($hash as $fieldName => $rawValue)
            if( !in_array($fieldName, [self::RECORD_IDENTIFIER]) ) {
                $decodifications[$fieldName] = $this->getDecodedValue($fieldName, $rawValue, $hash[self::RECORD_IDENTIFIER] ?? null);
            }
        return $decodifications;
    }

    /**
     * @inheritdoc
     */
    protected function addDecodedValuesToHash( $hash ) {
        return array_merge($hash, [self::DECODIFICATIONS_IDENTIFIER=>$this->getDecodedHash($hash)]);
    }

    /**
     * @inheritdoc
     */
    protected function addDescriptionsToRows( $rows ) {
        $ret = $rows;
        foreach($ret as &$row) {
            $row = $this->addDescriptionToRow($row);
        }
        return $ret;
    }

    /**
     * @inheritdoc
     */
    protected function addDescriptionToRow( $row ) {
        return array_merge($row, [self::RECORD_DESCRIPTION_IDENTIFIER=>$this->getRowDescription($row)]);
    }

    /**
     * @inheritdoc
     */
    public function getRowDescription(array $row) {
        return $row[ $this->getPrimaryKey() ] ?? null;
    }

    /**
     * @inheritdoc
     */
    protected function addMetaToRows( $rows ) {
        $ret = $rows;
        foreach($ret as &$row) {
            $row = $this->addMetaToRow($row);
        }
        return $ret;
    }

    /**
     * @inheritdoc
     */
    protected function addMetaToRow( $row ) {
        return array_merge($row, [self::META_IDENTIFIER=>$this->getRowMeta($row)]);
    }

    public function getRowMeta($row) {
        return [
            self::META_RECORD_CAN_DELETE => !$this->isReadOnly(),
            self::META_RECORD_CAN_EDIT => true
        ];
    }

    /**
     * @inheritdoc
     */
    protected function addFilesToRows( $rows ) {
        $ret = $rows;
        foreach($ret as &$row) {
            $row = $this->addFilesToRow($row);
        }
        return $ret;
    }

    /**
     * @inheritdoc
     */
    protected function addFilesToRow( $row ) {
        return array_merge($row, $this->getRowFiles($row));
    }

    public function getRowFiles($row, $hydrate=false) {
        return [];
    }

    public function getMeta() {
        return [
            self::META_CAN_DELETE_MULTIPLE => !$this->isReadOnly(),
            self::META_CAN_EXPORT_XLSX => true,
            self::META_CAN_ADD => !$this->isReadOnly()
        ];
    }

    /**
     * @inheritdoc
     * TODO: cache results and implement other decodings
     */
    public function getDecodedValue($fieldName, $value, $recordid=null) {
        //when this function is called after a PUT lister operation, we get dates populated by Propel as DateTime objects instead of strings
        if($value instanceof \DateTime)
            $value = $value->format('c'); //ISO 8601 string representation

        $dsField = $this->getField($fieldName);

        if($dsField) {
            //field is value mapped, so the obvious decoding is the label of the map
            if($map = $dsField->getValueMap()) {
                return $map->mapValue($value);
            }

            if($value === null)
                return '-';

            switch($dsField->getControlType()) {
                /*case FieldInterface::TYPE_FILE      : {
                    if($value && ($file = $this->getFileRepository($recordid)->get($value))) {
                        return $file->getName();
                    } else return '-';
                }*/
                case FieldInterface::TYPE_CURRENCY  : return DataFormatter::formatCurrency($value);
                case FieldInterface::TYPE_NUMBER  : {
                    if( in_array($dsField->getType(), [\PropelTypes::INTEGER, \PropelTypes::BIGINT, \PropelTypes::SMALLINT]) )
                        return $value;
                    else return DataFormatter::formatFloat($value);
                }

                //case FieldInterface::TYPE_REPOFILE  :
                case FieldInterface::TYPE_DATETIME  : return DataFormatter::formatDateTime($value);
                case FieldInterface::TYPE_DATE      : return DataFormatter::formatDate($value);
                case FieldInterface::TYPE_TIME      : return DataFormatter::formatTime($value);
            }
        }

        return $value;
    }

    /**
     * @param $recordId
     * @param array $requestParameters
     * @param \Closure|null $DSRequestHook
     * @return DSResponse
     */
    public function getSingleRecordResponse($recordId, $requestParameters = [], \Closure $DSRequestHook = null) {
        $dsr = new DSRequest();

        if($DSRequestHook)
            $DSRequestHook($dsr);

        $dsr ->setOperationType($dsr::OPERATION_TYPE_FETCH)
             ->setParameters(array_merge($requestParameters, [$this->getPrimaryKey() => $recordId]));

        return $this->execute($dsr);
    }

    /**
     * @inheritdoc
     */
    public function getDSRecord($recordId, $requestParameters = [], \Closure $DSRequestHook = null) {
        if($recordId !== null) {
            return $this->getSingleRecordResponse($recordId, $requestParameters, $DSRequestHook)->getDSRecord();
        } else return new DSRecord($this);
    }

    /**
     * @param \DateTime $instant
     * @return $this
     */
    public function setInstant(\DateTime $instant)
    {
        $this->instant = $instant;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getInstant()
    {
        return $this->instant;
    }

    /**
     * @return boolean
     */
    public function isInAuditMode() {
        return $this->getInstant() instanceof \DateTime;
    }

    /**
     * @param $bool
     * @return $this
     */
    public function setReadOnly($bool) {
        $this->readOnly = $bool ? true : false;
    }

    /**
     * @return bool
     */
    public function isReadOnly() {
        return $this->readOnly;
    }

    /**
     * @return DSQuery
     */
    public function getDSQuery() {
        return new DSQuery($this);
    }

    /**
     * @param array $baseQuery
     * @return $this
     */
    public function setBaseQuery($baseQuery)
    {
        $this->baseQuery = $baseQuery;
        return $this;
    }

    /**
     * @return array
     */
    public function getBaseQuery()
    {
        return $this->baseQuery;
    }

    /**
     * @inheritdoc
     */
    public function getQueryExpression($query) {
        $ret = false;
        if($statements = $this->transformQueryInEvaluableStatements($query)) {
            eval("\$ret = $statements;");
        }
        return $ret;
    }

    /**
     * @param $query
     * @return bool|string
     */
    private function transformQueryInEvaluableStatements($query) {
        if(!$query) { return false; }
        if(!is_array($query['data'])) {
            if(isset($query['isCol']) && $query['isCol']==1){
                return "\$this->_f_column(\"{$query['data']}\")";
            }
            return "\"{$query['data']}\"";
        }else{
            $exprs = [];
            foreach($query['data'] as $e){
                $exprs[] = $this->transformQueryInEvaluableStatements($e);
            }
            return "\$this->_f_{$query['op']}(".implode($exprs,', ').")";
        }
    }

    /**
     * gridx enumerates columns starting from 1, and the first one is reserved for tools.
     * TODO: refactor this, having the client resolve column offsets to actual field names
     * TODO: get rid of the dependancy with gridx and cool lister (offset 2)
     *
     * @param $colNr
     * @return string
     */
    protected function _f_column($colNr) {
        return $this->getFieldNames()[$colNr-2];
    }

    abstract protected function _f_and();
    abstract protected function _f_or();

    abstract protected function _f_isEmpty($fieldName);
    abstract protected function _f_contain($fieldName, $arg);
    abstract protected function _f_equal($fieldName, $arg);
    abstract protected function _f_different($fieldName, $arg);

}

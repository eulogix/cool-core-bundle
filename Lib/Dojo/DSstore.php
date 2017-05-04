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

use Eulogix\Cool\Lib\DataSource\DataSourceInterface;
use Eulogix\Cool\Lib\DataSource\DSRequest;
use Eulogix\Cool\Lib\DataSource\DSResponse;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class DSstore implements StoreInterface {

    /**
     * @var DataSourceInterface
     */
    protected $dataSource;

    public function __construct(DataSourceInterface $dataSource) {
        $this->dataSource = $dataSource;
    }

    /**
     * @inheritdoc
     */
    public function execute(StoreRequestInterface $request)
    {
        switch($request->getOperation()) {
            case StoreRequestInterface::OPERATION_TYPE_QUERY    : return $this->executeQuery($request);
            case StoreRequestInterface::OPERATION_TYPE_PUT      : return $this->executePut($request);
            case StoreRequestInterface::OPERATION_TYPE_REMOVE   : return $this->executeRemove($request);
        }
    }

    /**
     * returns the DSRequest object for a QUERY operation
     * @param StoreRequestInterface $request
     * @return DSRequest
     */
    public function getQueryDSRequest(StoreRequestInterface $request) {
        $dsr = new DSRequest();

        $dsr->setOperationType($dsr::OPERATION_TYPE_FETCH)
            ->setStartRow($request->getRangeFrom())
            ->setSortBy($request->getSortArray())
            ->setQuery($request->getGridxQuery())
            ->setIncludeDecodings(true)
            ->setIncludeRecordDescriptions($request->getIncludeDescriptions())
            ->setParameters($request->getParameters());

        if($request->getRangeTo()!==null)
            $dsr->setEndRow($request->getRangeTo());

        return $dsr;
    }

    /**
     * @param DSResponse $DSResponse
     */
    protected function processDSResponse(DSResponse $DSResponse) { }

    /**
     * @inheritdoc
     */
    public function executeQuery(StoreRequestInterface $dojoRequest)
    {
        $DSRequest = $this->getQueryDSRequest($dojoRequest);
        $DSResponse = $this->dataSource->execute($DSRequest);
        $this->processDSResponse( $DSResponse );

        $storeResponse = new StoreResponse();
        $storeResponse->setData( $DSResponse->getData() );
        $storeResponse->setSummary($DSResponse->getSummary());
        $storeResponse->setStartRow($dojoRequest->getRangeFrom());
        $storeResponse->setTotalRows($DSResponse->getTotalRows());

        return $storeResponse;
    }

    /**
     * @inheritdoc
     */
    public function executePut(StoreRequestInterface $dojoRequest)
    {
        $postedRecord = $dojoRequest->getPostedRecord();

        $primaryKeyField = $this->dataSource->getPrimaryKey();

        $DSRecord = $this->dataSource->getDSRecord(@$postedRecord[ $primaryKeyField ]);

        $dsr = new DSRequest();
        $dsr->setOperationType( $DSRecord->isNew() ? $dsr::OPERATION_TYPE_ADD : $dsr::OPERATION_TYPE_UPDATE )
            ->setOldValues( $DSRecord->getValues() )
            ->setValues( $postedRecord )
            ->setIncludeDecodings(true)
            ->setParameters( array_merge(
                $dojoRequest->getParameters(),
                [$primaryKeyField => @$postedRecord[ $primaryKeyField ]]
            ));

        $dsresponse = $this->dataSource->execute($dsr);

        $storeResponse = new StoreResponse();

        switch($dsresponse->getStatus()) {

            case $dsresponse::STATUS_TRANSACTION_SUCCESS : {
                $storeResponse->setStatus(StoreResponse::STATUS_TRANSACTION_SUCCESS);
                $data = $dsresponse->getData() ? $dsresponse->getData() : [];
                $retData = @$postedRecord[ $primaryKeyField ]!==null ?
                    array_merge([$primaryKeyField => $postedRecord[ $primaryKeyField ]], $data ) :
                    $data;
                $storeResponse->setData( $retData );
                break;
            }
            case $dsresponse::STATUS_TRANSACTION_FAILED :
            case $dsresponse::STATUS_VALIDATION_ERROR : {
                $storeResponse->setErrorReport($dsresponse->getErrorReport());
                $storeResponse->setData( $DSRecord->getValues() );
                break;
            }
        }

        return $storeResponse;
    }

    /**
     * @inheritdoc
     */
    public function executeRemove(StoreRequestInterface $dojoRequest)
    {
        $recordId = $dojoRequest->getPostedObjectId();
        $primaryKeyField = $this->dataSource->getPrimaryKey();

        $dsr = new DSRequest();

        $dsr->setOperationType($dsr::OPERATION_TYPE_REMOVE)
            ->setParameters([ $primaryKeyField => $recordId ]);

        $dsresponse = $this->dataSource->execute($dsr);

        $storeResponse = new StoreResponse();
        $storeResponse->setStatus(StoreResponse::STATUS_TRANSACTION_SUCCESS);
        return $storeResponse;
    }

}
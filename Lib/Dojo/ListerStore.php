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

use Eulogix\Cool\Lib\DataSource\DataSourceInterface as D;
use Eulogix\Cool\Lib\DataSource\DataSourceInterface;
use Eulogix\Cool\Lib\DataSource\DSRequest;
use Eulogix\Cool\Lib\DataSource\DSResponse;
use Eulogix\Cool\Lib\Lister\ListerInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class ListerStore extends DSstore implements StoreInterface {

    /**
     * @var ListerInterface $lister
     */
    private $lister;

    public function __construct(ListerInterface $lister) {
        parent::__construct($lister->getDataSource());
        $this->lister = $lister;
    }

    /**
     * @inheritdoc
     */
    public function getQueryDSRequest(StoreRequestInterface $request) {

        $DSr = parent::getQueryDSRequest($request);

        // if the client is asking for descendants of a given record, we do not recalculate totals
        // as they are likely already displayed from the parent request (the one that originated from top-level rows)
        if(($listerCols = $this->lister->getColumns()) && !@$DSr->getParameters()[DSRequest::PARAM_PARENT_ID]) {
            foreach($listerCols as $colName => $col) {
                if($col->getHasSummary())
                    $DSr->addSummaryFor($colName);
            }
        }

        $DSr->setIncludeMeta(true);

        return $DSr;
    }

    /**
     * @param DSResponse $DSResponse
     */
    protected function processDSResponse(DSResponse $DSResponse) {

        $workData = $DSResponse->getData();
        if($singleRecordFetch = $DSResponse->getAttribute(DataSourceInterface::RECORD_IDENTIFIER))
            $workData = [$workData];

        //we make sure that the record id is always textual as this is what the lister.js widget expects
        if($workData) {
            foreach($workData as &$row) {
                if(isset($row[D::RECORD_IDENTIFIER])) {
                    $row[D::RECORD_IDENTIFIER].='';
                }
            }
            //add some metadata to rows that is provided by the lister, this usually is for cosmetic / UI stuff
            $this->lister->processRows($workData);
        }

        if($singleRecordFetch)
            $DSResponse->setData($workData[0]);
        else $DSResponse->setData($workData);
    }

    /**
     * @param $responseData
     * @return array
     */
    protected function processQueryResponseData($responseData) {

    }

}
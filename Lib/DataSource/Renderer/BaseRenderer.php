<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\DataSource\Renderer;

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\DataSource\DataSourceInterface;
use Eulogix\Cool\Lib\DataSource\DSRequest;
use Eulogix\Cool\Lib\DataSource\DSResponse;
use Eulogix\Cool\Lib\File\FileUtil;
use Eulogix\Cool\Lib\Traits\DataSourceHolder;
use Eulogix\Lib\Progress\Event\ProgressEvent;
use Eulogix\Lib\Progress\ProgressTracker;
use Eulogix\Lib\Traits\ProgressTrackerHolder;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

abstract class BaseRenderer implements RendererInterface {

    use DataSourceHolder, ProgressTrackerHolder;

    /**
     * @param DataSourceInterface $ds
     */
    public function __construct(DataSourceInterface $ds) {
        $this->setDataSource($ds);
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function render(DSRequest $request, $raw = false, array $listerColumnsDefinitions = null, $asyncIfMoreThanRows = null) {

        $tracker = $this->getProgressTracker();

        $nrOfRowsToExport = $this->getDataSource()->count($request);
        if($asyncIfMoreThanRows && $nrOfRowsToExport > $asyncIfMoreThanRows) {
            $rd = Cool::getInstance()->getFactory()->getRundeck();
            if( $jobId = $rd->getJobIdByName('cool:dataSource:render') ) {

                $cacher = Cool::getInstance()->getFactory()->getSharedCacher();
                $inputKey = md5(microtime());

                $cacher->store($inputKey, [
                    'executionEnvironment' => serialize(Cool::getInstance()->getExcutionEnvironment()),

                    'raw' => $raw,
                    'listerColumnsDefinitions' => serialize($listerColumnsDefinitions),
                    'DSRequest' => serialize($request),
                    'Renderer' => serialize($this),
                    'outputFile' => $outputFile = FileUtil::getTempFileName()
                ]);

                if ($execution = $rd->runJob( $jobId, [
                        'input_key' => $inputKey
                    ])) {
                        $exec = array_pop($execution);
                        return array_merge($exec, ['outputFile' => $outputFile]);
                    }
            } else throw new \Exception("missing cool:dataSource:render Rundeck job");
        } else {
            $tracker->logProgress(0);

            $tracker->openSub(70);
            $data = $this->getExportData($request);
            $tracker->closeSub();

            $tracker->logProgress(70);

            if(count($data) > 0) {
                $tracker->openSub(30);
                $ret =  $this->renderData($data, $raw, $listerColumnsDefinitions);
                $tracker->closeSub();
            } else throw new \Exception("NOTHING TO EXPORT");

            $tracker->logProgress(100);
            return $ret;
        }
    }

    /**
     * @param DSRequest $dsRequest
     * @return array
     */
    protected function getExportData(DSRequest $dsRequest) {

        $self = $this;
        $wkDsRequest = clone $dsRequest;
        $wkDsRequest->setIncludeMeta(false);
        $wkDsRequest->setStartRow(0);
        $wkDsRequest->setEndRow(null);

        if( $ds = $this->getDataSource() ) {

            $ds->getProgressTracker()->getDispatcher()->addListener( ProgressTracker::EVENT_PROGRESS,
                function(ProgressEvent $e) use ($self) {
                    $self->getProgressTracker()->logProgress($e->getProgressPercentage());
                }
            );

            $dsResponse = $ds->execute($wkDsRequest);
            if($dsResponse->getStatus() == DSResponse::STATUS_TRANSACTION_SUCCESS) {
                return $dsResponse->getData();
            }
        }

        return [];
    }

    /**
     * @param array $data
     * @return array
     */
    protected function getDecodedRows(array $data) {
        $ret = [];
        foreach($data as $row)
            $ret[] = $row[DataSourceInterface::DECODIFICATIONS_IDENTIFIER];
        return $ret;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function getRawRows(array $data) {
        $ret = $data;
        foreach($ret as &$row) {
            unset( $row[DataSourceInterface::RECORD_IDENTIFIER] );
            unset( $row[DataSourceInterface::DECODIFICATIONS_IDENTIFIER] );
        }
        return $ret;
    }

    /**
     * returns a filtered array of rows with only the columns specified in $columnNames, in the same order
     * @param array $data
     * @param string[] $columnNames
     * @return array
     */
    protected function filterData(array $data, array $columnNames) {
        return array_map(function($row) use($columnNames) {
            $ret = [DataSourceInterface::DECODIFICATIONS_IDENTIFIER => []];
            foreach($columnNames as $c) {
                $ret[$c] = @$row[$c];
                $ret[DataSourceInterface::DECODIFICATIONS_IDENTIFIER][$c] = @$row[DataSourceInterface::DECODIFICATIONS_IDENTIFIER][$c];;
            }
            return $ret;
        }, $data);
    }

}
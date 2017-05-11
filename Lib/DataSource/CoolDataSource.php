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

use Eulogix\Cool\Lib\Traits\CoolCacheShimmed;
use Eulogix\Lib\Cache\Shimmable;
use Eulogix\Cool\Lib\Cool;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

abstract class CoolDataSource extends SqlDataSource implements Shimmable {

    use CoolCacheShimmed;

    var $schemaName = "";

    /**
     * @param string $schemaName
     * @param array $parameters
     */
    public function __construct($schemaName, array $parameters = [])
    {
        $this->schemaName = $schemaName;
        $this->getParameters()->replace($parameters);
    }

    /**
     * @return string
     */
    public function getShimUID() {
        return $this->schemaName;
    }

    /**
     * returns the schema for this lister
     * @returns \Eulogix\Cool\Lib\Database\Schema
     */
    public function getCoolSchema() {
        return Cool::getInstance()->getSchema($this->schemaName);
    }

    /**
     * This method is called by DataSource.execute() for "clientExport" operations.
     * @param DSRequest $req
     * @return DSResponse
     */
    public function executeClientExport(DSRequest $req) {}

    /**
     * This method is called by DataSource.execute() for "custom" operations.
     * @param DSRequest $req
     * @return DSResponse
     */
    public function executeCustom(DSRequest $req) {}


    public function hydrateFileFields(&$row) {

    }

    /**
     * This method is called by DataSource.execute() for "fetch" operations.
     * @param DSRequest $req
     * @throws \Exception
     * @return DSResponse
     */
    public function executeFetch(DSRequest $req) {
        $success = false;
        $dsresponse = new DSResponse($this);

        if($db = $this->getCoolSchema()) {
            if($this->isInAuditMode())
                $db->setInstant($this->getInstant());

            if(array_key_exists(self::RECORD_IDENTIFIER, $req->getParameters())) {
                //single record fetch
                $recordId = $req->getParameters()[self::RECORD_IDENTIFIER];
                $sql = $this->getSql(0, 0, [], $req->getParameters(), []);
                if( $row = $db->fetch( $sql['statement'], $sql['parameters'] ) ) {

                    if($req->getIncludeDecodings()) {
                        $row = $this->addDecodedValuesToHash($row);
                    }

                    if($req->getIncludeMeta()) {
                        $row = $this->addMetaToRow($row);
                    }

                    if($req->getIncludeRecordDescriptions()) {
                        $row = $this->addDescriptionToRow($row);
                    }

                    if($req->getIncludeFiles()) {
                        $row = $this->addFilesToRow($row);
                    }

                    $this->hydrateFileFields($row);
                    $dsresponse->setTotalRows(1);
                    //we use that to distinguish this response from a multiple record fetch with 1 row!
                    $dsresponse->setAttribute(self::RECORD_IDENTIFIER, $recordId);

                    $success = true;
                } else {
                    $dsresponse->setTotalRows(0);
                    $success = false;
                }

                $dsresponse->setData($row);

            } else {
                //multiple record fetch
                $sql = $this->getSql($req->getStartRow(), $req->getEndRow(), $req->getSortByFields(), $req->getParameters(), $req->getQuery());
                $rows = $db->fetchArray( $sql['statement'], $sql['parameters'] );

                $errorCode = $db->getConnection()->errorCode();
                if($errorCode != '00000') {
                    $lastQuery = $db->getConnection()->getLastExecutedQuery();
                    $errorInfo = $db->getConnection()->errorInfo();
                    throw new \Exception($errorCode.' -> '.$lastQuery . var_export($errorInfo) );
                }

                if($req->getIncludeDecodings()) {
                    $rows = $this->addDecodedValuesToRows($rows);
                }

                if($req->getIncludeMeta()) {
                    $rows = $this->addMetaToRows($rows);
                }

                if($req->getIncludeRecordDescriptions()) {
                    $rows = $this->addDescriptionsToRows($rows);
                }

                if($req->getIncludeFiles()) {
                    $rows = $this->addFilesToRows($rows);
                }

                $dsresponse->setData($rows);
                $dsresponse->setStartRow($req->getStartRow());
                $dsresponse->setEndRow($req->getEndRow());
                $dsresponse->setTotalRows($this->getTotalRows($req->getParameters(), $req->getQuery()));

                if($sf = $req->getSummaryFields()) {
                    if( $sql = $this->getSqlSummary($sf, $req->getParameters(), $req->getQuery()) ) {
                        $summary = $db->fetchArray( $sql['statement'], $sql['parameters'] );
                        $summary = array_pop($summary);
                        //by default, summary is decoded
                        $dsresponse->setSummary( $this->getDecodedHash($summary) );
                    }
                }

                $success = true;
            }

            if($this->isInAuditMode())
                $db->exitAuditMode();

        }


        $dsresponse->setStatus($success);
        return $dsresponse;
    }

    /**
     * This method is called by DataSource.execute() for "fetch" operations.
     * @param DSRequest $req
     * @return DSResponse
     */
    public function executeCount(DSRequest $req) {
        $success = false;
        $dsresponse = new DSResponse($this);

        if($db = $this->getCoolSchema()) {
            //multiple record fetch
            $dsresponse->setTotalRows($this->getTotalRows($req->getParameters(), $req->getQuery()));

            if(!empty($req->getGroupCountFields()))
                $dsresponse->setData($this->countRowsGrouped($req->getParameters(), $req->getQuery(), $req->getGroupCountFields()));

            $success = true;
        }

        $dsresponse->setStatus($success);
        return $dsresponse;
    }

    /**
     * This method is called by DataSource.execute() for "remove" operations.
     * @param DSRequest $req
     * @return DSResponse
     */
    public function executeRemove(DSRequest $req) {
        $success = false;
        $dsresponse = new DSResponse($this);
        $dsresponse->setStatus($success);
        return $dsresponse;
    }

    /**
     * This method is called by DataSource.execute() for "update" operations.
     * @param DSRequest $req
     * @return DSResponse
     */
    public function executeUpdate(DSRequest $req) {
        $success = false;
        $dsresponse = new DSResponse($this);
        $dsresponse->setStatus($success);
        return $dsresponse;
    }

    /**
     * This method is called by DataSource.execute() for "add" operations.
     * @param DSRequest $req
     * @return DSResponse
     */
    public function executeAdd(DSRequest $req) {
        return $this->executeUpdate($req);
    }

    public function getTotalRows($parameters = array(), $query=null) {
        if($db = $this->getCoolSchema()) {
            $where = $this->getSqlWhere($parameters, $query);
            return $db->fetch("SELECT COUNT(*) FROM ({$this->getSqlSelect($parameters)} {$this->getSqlFrom($parameters)} {$where['statement']} {$this->getSqlGroupBy($parameters)}) as _tmp_", $where['parameters']);
        }
        return 0;
    }

    public function countRowsGrouped($parameters = array(), $query=null, array $groupFields=[]) {
        if($db = $this->getCoolSchema()) {
            $where = $this->getSqlWhere($parameters, $query);
            $fieldsExpr = implode(',',$groupFields);
            return $db->fetchArray("SELECT {$fieldsExpr},COUNT(*) FROM ({$this->getSqlSelect($parameters)} {$this->getSqlFrom($parameters)} {$where['statement']} {$this->getSqlGroupBy($parameters)}) as _tmp_ GROUP BY {$fieldsExpr}", $where['parameters']);
        }
        return [];
    }

    //TODO: review this
    public function getFileRepository($recordid = null)
    {
        return null;
    }

}

<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Activiti\dataSource;

use Eulogix\Cool\Lib\DataSource\DSQuery;
use Eulogix\Cool\Lib\DataSource\DSRequest;
use Eulogix\Cool\Lib\DataSource\DSResponse;
use Eulogix\Cool\Lib\File\FileRepositoryInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class ProcessDefinitionDataSource extends ActivitiDataSource {

    /**
     * @inheritdoc
     */
    public function build($parameters = [])
    {
        $this->addField('id')->setType(\PropelTypes::VARCHAR)
                             ->setIsPrimaryKey(true);
        $this->addField('url')->setType(\PropelTypes::VARCHAR);
        $this->addField('key')->setType(\PropelTypes::VARCHAR);
        $this->addField('version')->setType(\PropelTypes::VARCHAR);
        $this->addField('name')->setType(\PropelTypes::VARCHAR);
        $this->addField('description')->setType(\PropelTypes::VARCHAR);
        $this->addField('deploymentId')->setType(\PropelTypes::VARCHAR);
        $this->addField('deploymentUrl')->setType(\PropelTypes::VARCHAR);
        $this->addField('resource')->setType(\PropelTypes::VARCHAR);
        $this->addField('diagramResource')->setType(\PropelTypes::VARCHAR);
        $this->addField('category')->setType(\PropelTypes::VARCHAR);
        $this->addField('graphicalNotationDefined')->setType(\PropelTypes::VARCHAR);
        $this->addField('suspended')->setType(\PropelTypes::VARCHAR);
        $this->addField('startFormDefined')->setType(\PropelTypes::VARCHAR);

        $this->setReadOnly(true);

        return $this;
    }

    /**
     * This method is called by DataSource.execute() for "add" operations.
     * @param DSRequest $req
     * @return DSResponse
     */
    public function executeAdd(DSRequest $req)
    {

    }

    /**
     * This method is called by DataSource.execute() for "clientExport" operations.
     * @param DSRequest $req
     * @return DSResponse
     */
    public function executeClientExport(DSRequest $req)
    {
        // TODO: Implement executeClientExport() method.
    }

    /**
     * This method is called by DataSource.execute() for "custom" operations.
     * @param DSRequest $req
     * @return DSResponse
     */
    public function executeCustom(DSRequest $req)
    {
        // TODO: Implement executeCustom() method.
    }

    /**
     * This method is called by DataSource.execute() for "fetch" operations.
     * @param DSRequest $req
     * @return DSResponse
     */
    public function executeFetch(DSRequest $req)
    {
        $dsresponse = new DSResponse($this);


        if($recordId = @$req->getParameters()[self::RECORD_IDENTIFIER]) {

             $processDefinition = $this->client->getProcessDefinition($recordId);
             $processDefinition[self::RECORD_IDENTIFIER] = $recordId;

             $dsresponse->setData($processDefinition);
             $dsresponse->setTotalRows(1);

             $success = true;

        } else {
            $params = [
                'start' => $req->getStartRow(),
                'size' => $req->getEndRow()-$req->getStartRow(),
                'latest' => 'true'
            ];

            if($sort = $req->getSortByFields()) {
                $params['sort'] = $sk = array_keys($sort)[0];
                $params['order'] = $sort[$sk] == DSRequest::SORT_ASC ? 'asc':'desc';
            }

            $reqParameters = $req->getParameters();
            if($filter = json_decode(@$reqParameters['_filter_raw_values'], true)) {
                foreach($filter as $filterField => $filterValue) {
                    switch($filterField) {
                        case 'involvedUser' :
                        case 'processDefinitionKeyLike' : $params[$filterField] = $filterValue;
                    }
                }
            }

            $params = array_merge($params, $this->getParamsFromQuery($req->getQuery()));

            $tasks = $this->client->getListOfProcessDefinitions($params);

            $rows = $tasks->getData();
            foreach($rows as &$row) {
                $row[self::RECORD_IDENTIFIER] = $row['id'];
            }

            if($req->getIncludeMeta()) {
                $rows = $this->addMetaToRows($rows);
            }

            $dsresponse->setData($rows);
            $dsresponse->setStartRow($tasks->getStart());
            $dsresponse->setEndRow($req->getEndRow());
            $dsresponse->setTotalRows($tasks->getTotal());

            $success = true;
        }

        $dsresponse->setStatus($success);
        return $dsresponse;
    }

    /**
     * This method is called by DataSource.execute() for "count" operations.
     * @param DSRequest $req
     * @return DSResponse
     */
    public function executeCount(DSRequest $req)
    {
        // TODO: Implement executeCount() method.
    }

    /**
     * This method is called by DataSource.execute() for "remove" operations.
     * @param DSRequest $req
     * @return DSResponse
     */
    public function executeRemove(DSRequest $req)
    {
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
    public function executeUpdate(DSRequest $req)
    {
        // TODO: Implement executeUpdate() method.
    }

    /**
     * returns the default file repository instance that retrieves and stores files, for a given fieldname or recordid
     *
     * @param mixed $recordid
     * @return FileRepositoryInterface
     */
    public function getFileRepository($recordid = null)
    {
        // TODO: Implement getFileRepository() method.
    }

    /**
     * @param DSQuery $query
     * @return array|bool|mixed
     */
    private function getParamsFromQuery($query)
    {
        if($expr = $this->getQueryExpression($query)) {
            return($expr);
        }
        return [];
    }

    protected function _f_equal($fieldName, $arg)
    {
        return [$fieldName => $arg];
    }

    protected function _f_and()
    {
        $args = func_get_args();
        return call_user_func_array('array_merge', $args);
    }

    protected function _f_or()
    {
        // TODO: Implement _f_or() method.
    }

    protected function _f_isEmpty($fieldName)
    {
        // TODO: Implement _f_isEmpty() method.
    }

    protected function _f_contain($fieldName, $arg)
    {
        // TODO: Implement _f_contain() method.
    }

    protected function _f_different($fieldName, $arg)
    {
        // TODO: Implement _f_different() method.
    }
}

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

use Eulogix\Cool\Lib\Activiti\om\Task;
use Eulogix\Cool\Lib\DataSource\DataSourceInterface;
use Eulogix\Cool\Lib\DataSource\DSField;
use Eulogix\Cool\Lib\DataSource\DSQuery;
use Eulogix\Cool\Lib\DataSource\DSRecord;
use Eulogix\Cool\Lib\DataSource\DSRequest;
use Eulogix\Cool\Lib\DataSource\DSResponse;
use Eulogix\Cool\Lib\File\FileRepositoryInterface;
use Eulogix\Lib\Validation\BeanValidatorInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class TaskDataSource extends ActivitiDataSource {

    /**
     * method that must be called after instantiation
     * @param array $parameters
     * @return self
     */
    public function build($parameters = [])
    {
        $this->addField('id')->setType(\PropelTypes::VARCHAR)
                             ->setIsPrimaryKey(true);
        $this->addField('url')->setType(\PropelTypes::VARCHAR);
        $this->addField('owner')->setType(\PropelTypes::VARCHAR);
        $this->addField('assignee')->setType(\PropelTypes::VARCHAR);
        $this->addField('delegationState')->setType(\PropelTypes::VARCHAR);
        $this->addField('name')->setType(\PropelTypes::VARCHAR);
        $this->addField('description')->setType(\PropelTypes::VARCHAR);
        $this->addField('createTime')->setType(\PropelTypes::VARCHAR);
        $this->addField('dueDate')->setType(\PropelTypes::VARCHAR);
        $this->addField('priority')->setType(\PropelTypes::VARCHAR);
        $this->addField('suspended')->setType(\PropelTypes::VARCHAR);
        $this->addField('taskDefinitionKey')->setType(\PropelTypes::VARCHAR);
        $this->addField('tenantId')->setType(\PropelTypes::VARCHAR);
        $this->addField('category')->setType(\PropelTypes::VARCHAR);
        $this->addField('formKey')->setType(\PropelTypes::VARCHAR);
        $this->addField('parentTaskId')->setType(\PropelTypes::VARCHAR);
        $this->addField('parentTaskUrl')->setType(\PropelTypes::VARCHAR);
        $this->addField('executionId')->setType(\PropelTypes::VARCHAR);
        $this->addField('executionUrl')->setType(\PropelTypes::VARCHAR);
        $this->addField('processInstanceId')->setType(\PropelTypes::VARCHAR);
        $this->addField('processInstanceUrl')->setType(\PropelTypes::VARCHAR);
        $this->addField('processDefinitionId')->setType(\PropelTypes::VARCHAR);
        $this->addField('processDefinitionUrl')->setType(\PropelTypes::VARCHAR);
//        $this->addField('variables')->setType(\PropelTypes::VARCHAR);

        $this->addField('businessKey')->setType(\PropelTypes::VARCHAR);
        $this->addField('processDefinitionKey')->setType(\PropelTypes::VARCHAR);

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
        $success = false;
        $dsresponse = new DSResponse($this);


        if($recordId = @$req->getParameters()[self::RECORD_IDENTIFIER]) {

        } else {
            //multiple record fetch
            $params = [
                'start' => $req->getStartRow(),
                'size' => $req->getEndRow()-$req->getStartRow()
            ];

            if($sort = $req->getSortByFields()) {
                $params['sort'] = $sk = array_keys($sort)[0];
                $params['order'] = $sort[$sk] == DSRequest::SORT_ASC ? 'asc':'desc';
            }

            $reqParameters = $req->getParameters();
            if($filter = json_decode(@$reqParameters['_filter_raw_values'], true)) {

                $bkeyLike = @$filter['baseProcessNamespace'];
                if($cluster = @$filter['cluster'])
                    $bkeyLike.='/'.$cluster;
                $bkeyLike.='%';
                $params['processInstanceBusinessKeyLike'] = $bkeyLike;

                if($bkl = json_decode( @$filter['processInstanceBusinessKeyLikes'], true ) ) {
                    $params['processInstanceBusinessKeyLike'] = $bkl;
                }

                if($groups = json_decode( @$filter['candidateGroups'], true ) ) {
                    $params['candidateGroup'] = $groups;
                }

                foreach($filter as $filterField => $filterValue)
                if($filterValue != '') {
                    switch($filterField) {
                        case 'assignee' :
                        case 'candidateUser' :
                        case 'candidateGroup' :
                        case 'involvedUser' :
                        case 'processDefinitionKeyLike' :
                            $params[$filterField] = $filterValue;
                            break;
                    }
                }
            }

            $params = array_merge($params, $this->getParamsFromQuery($req->getQuery() ?? []));

            $tasks = $this->client->getFlatListOfTasks($params);

            $rows = $tasks->getData();
            foreach($rows as &$row) {
                $row[self::RECORD_IDENTIFIER] = $row['id'];
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
        $dsresponse = new DSResponse($this);

        $recordId = $req->getParameters()[self::RECORD_IDENTIFIER];
        try {
            $ret = $this->getClient()->deleteTask($recordId, 'true', 'Direct deletion');
            $success = true;
        } catch(\Exception $e) {
            $dsresponse->addGeneralError($e->getMessage());
            $success = false;
        }

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
     * @param array $query
     * @return array|bool|mixed
     */
    private function getParamsFromQuery(array $query)
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

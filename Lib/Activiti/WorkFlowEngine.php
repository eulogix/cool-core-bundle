<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Activiti;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AccountQuery;
use Eulogix\Lib\Activiti\ActivitiClient;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Lib\Activiti\om\ProcessInstance;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class WorkFlowEngine {

    /**
     * @var ActivitiClient
     */
    private $client;

    /**
     * @var string
     */
    private $tenantId;

    /**
     * The Id of the system user whose credentials are supplied to activiti so that it can connect back to the app
     * TODO: this would be better managed with expiring tokens (one timers or with a limited lifespan/context)
     * @var integer
     */
    private $systemUserId;

    public function __construct(ActivitiClient $client, $tenantId=null, $systemUserId=null) {
        $this->client = $client;
        $this->tenantId = $tenantId;
        $this->systemUserId = $systemUserId;
        if(!$this->systemUserId) {
            throw new \Exception("I need a system user to pass credentials to the wf engine!");
        }
    }

    /**
     * @return ActivitiClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return string
     */
    public function getTenantId()
    {
        return $this->tenantId;
    }

    /**
     * @param string $definitionKey
     * @param array $parameters a hash
     * @param string $businessKey
     * @throws \Exception
     * @return ProcessInstance
     */
    public function startProcessByKey($definitionKey, $parameters, $businessKey="") {
        $processDefinitions = $this->getClient()->getListOfProcessDefinitions([
                'key'       =>  $definitionKey,
                'latest'    =>  'true'
            ]);
        if($processDefinitions['total'] == 1)
            return $this->startProcessById($processDefinitions['data'][0]['id'], $parameters, $businessKey);
        if($processDefinitions['total'] == 0)
            throw new \Exception("PROCESS KEY $definitionKey IS NOT FOUND");
        if($processDefinitions['total'] > 1)
            throw new \Exception("PROCESS KEY $definitionKey IS AMBIGUOUS");
    }

    /**
     * @param string $definitionId
     * @param array $parameters a hash
     * @param string $businessKey
     * @return ProcessInstance
     */
    public function startProcessById($definitionId, $parameters, $businessKey="") {
        $variables = [];
        $allParams = array_merge($this->getDefaultVariables($parameters), $parameters);
        foreach($allParams as $pk => $pv)
            $variables[] = ["name"=>$pk, "value"=> $pv ];

        $clientParameters =[
            "processDefinitionId" => $definitionId,
            "variables" => $variables,
            "businessKey" => $businessKey
        ];

        $clientResponse = $this->getClient()->startProcessInstance($clientParameters);

        return new ProcessInstance($clientResponse, $this->getClient());
    }

    /**
     * @param $parameters
     * @throws \Exception
     * @return array
     */
    public function getDefaultVariables($parameters)
    {
        $user = AccountQuery::create()->findPk($this->systemUserId);
        if(!$user) {
            throw new \Exception("Bad user id: {$this->systemUserId}");
        }

        $ret = [
            'username'  => $user->getLoginName(),
            'password'  => $user->getPassword(),
        ];

        $schemaNames = Cool::getInstance()->getAvailableSchemaNames();
        $schemas = [];
        foreach($schemaNames as $schemaName) {
            $schema = Cool::getInstance()->getSchema($schemaName);
            $schemas[$schemaName] = [
                'current_schema' => $schema->getCurrentSchema(),
                'audit_schema' => $schema->getCurrentAuditSchemaName(),
                'instant' => $schema->getInstant() ? $schema->getInstant()->getTimestamp() : null,
                'name' => $schema->getName()
            ];
        }
        $ret['schemas'] = $schemas;

        if($u = Cool::getInstance()->getLoggedUser()) {
            $ret['logged_user'] = $u->getAccount()->toArraySafe();
        }

        $ret['base_url'] = Cool::getInstance()->getContainer()->getParameter('activiti_propagated_base_url');

        return ['_cool'=>$ret];
    }

} 
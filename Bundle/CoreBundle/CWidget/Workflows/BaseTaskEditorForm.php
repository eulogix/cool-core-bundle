<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\CWidget\Workflows;

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\DataSource\DataSourceInterface;
use Eulogix\Cool\Lib\Form\Form;
use Eulogix\Cool\Lib\Translation\Translator;
use Eulogix\Cool\Lib\Translation\TranslatorInterface;
use Eulogix\Cool\Lib\Widget\Message;
use Eulogix\Lib\Activiti\om\ProcessInstance;
use Eulogix\Lib\Activiti\om\Task;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

abstract class BaseTaskEditorForm extends Form {

    const COMMON_TRANSLATOR_DOMAIN = "WORKFLOW_FORM_COMMON";

    /**
     * @var TranslatorInterface
     */
    protected $commonTranslator = null;

    protected $taskVariables = false;

    public function build() {

        $this->getServerAttributes()->set('commonTranslator', $this->getCommonTranslator());

        return parent::build();
    }

    public function onSubmit() {
        $parameters = $this->request->all();
        $this->rawFill( $parameters );

        $taskId = $this->getTaskId();

        if($this->validate( array_keys($parameters) ) ) {

            $activiti = Cool::getInstance()->getFactory()->getActiviti();
            $wfEngine = Cool::getInstance()->getFactory()->getWorkflowEngine();

            try {
                $taskDef = $activiti->getTask($taskId);
                $activitiGroupValues = $this->getRawValues($this->getFieldNames('activiti'));
                $ret = $activiti->submitTaskFormData($taskId, $activitiGroupValues);

                try {
                    $processInstance = new ProcessInstance($activiti->getProcessInstance($taskDef['processInstanceId']), $wfEngine->getClient());
                    if($pendingTask = $wfEngine->getFirstPendingTaskForUser($processInstance)) {
                        $this->addEvent("taskFlow", ['task_id' => $pendingTask->getId()] );
                    }
                } catch(\Exception $e) {
                    $this->addMessage(Message::TYPE_INFO, $this->getCommonTranslator()->trans("WORKFLOW COMPLETED") );
                }

                $this->setReadOnly(true);
                $this->addEvent("recordSaved");
                $this->addMessage(Message::TYPE_INFO, $this->getCommonTranslator()->trans("TASK COMPLETED"));
            } catch(\Exception $e) {
                $this->addMessage(Message::TYPE_ERROR, $e->getMessage());
            }
        } else {
            $this->addMessage(Message::TYPE_ERROR, $this->getCommonTranslator()->trans("NOT VALIDATED"));
        }
    }

    /**
     * @return array
     */
    protected function getTaskFormData()
    {
        $activiti = $activiti = Cool::getInstance()->getFactory()->getActiviti();
        $taskId = $this->getTaskId();
        $formData = $activiti->getFormData($taskId);
        return $formData;
    }

    /**
     * @return array
     */
    protected function getTaskDefinition()
    {
        $activiti = $activiti = Cool::getInstance()->getFactory()->getActiviti();
        $taskId = $this->getTaskId();
        $taskDef = $activiti->getTask($taskId);
        return $taskDef;
    }

    /**
     * @return Task
     */
    protected function getTaskObject() {
        $activiti = Cool::getInstance()->getFactory()->getActiviti();
        return new Task($this->getTaskDefinition(), $activiti);
    }

    /**
     * @param string $variableName
     * @return mixed
     */
    protected function getTaskVariable($variableName) {
        if(!$this->taskVariables)
            $this->taskVariables = $this->getTaskVariables();
        return @$this->taskVariables[$variableName];
    }

    /**
     * @return array
     */
    protected function getTaskVariables()
    {
        if($this->taskVariables)
            return $this->taskVariables;

        $activiti = Cool::getInstance()->getFactory()->getActiviti();
        $taskId = $this->getTaskId();
        $globalVars = $activiti->getAllVariablesForTask($taskId, "global");

        $bridge = Cool::getInstance()->getFactory()->getJavaBridge();
        $jser = $bridge->instanceJavaClass("com.eulogix.cool.bridge.serializer");

        $ret = [];
        $oe = error_reporting();
        error_reporting(E_ERROR);
        foreach($globalVars as $v) {
            if($v['type']=='serializable') {
                $var = $activiti->fetch('GET', $v['valueUrl']);
                $ret[$v['name']] = json_decode($jser->toJSON( $jser->deserializeBase64String( base64_encode($var) )), true);
            } else $ret[$v['name']] = $v['value'];
        }
        error_reporting($oe);

        return $ret;
    }

    /**
     * returns the pk of the record currently edited
     * @return mixed
     */
    public function getTaskId() {
        return $this->parameters->get(DataSourceInterface::RECORD_IDENTIFIER);
    }

    /**
     * @return TranslatorInterface
     */
    public function getCommonTranslator() {
        if(!$this->commonTranslator) {
            $this->commonTranslator = Translator::fromDomain( self::COMMON_TRANSLATOR_DOMAIN );
        }
        return $this->commonTranslator;
    }

    public function getLayout() {
        $this->getServerAttributes()->set("formLayout", parent::getLayout());
        return "{% include '@CoolWidgets/Workflows/templates/TaskForm.html.twig' %}";
    }
}
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

use Eulogix\Cool\Lib\DataSource\SimpleValueMap;
use Eulogix\Cool\Lib\Form\Field\FieldInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class PropertiesTaskEditorForm extends BaseTaskEditorForm {

    /**
     * @var string
     */
    private $embeddedFormsLayout;

    protected $id = "COOL_ACTIVITI_TASK_FORM";

    public function build() {
        $this->buildActivitiForm();
        $td = $this->getTaskDefinition();
        $this->id = "USER_TASK_FORM_".preg_replace('/:[0-9]+:[0-9]+$/sim', '', $td['processDefinitionId']).'/'.$td['taskDefinitionKey'];
        return parent::build();
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Builds the visual representation of an activiti form using the task form data
     */
    protected function buildActivitiForm() {

        $formData = $this->getTaskFormData();

        $formProperties = $formData['formProperties'];
        foreach($formProperties as $formElement) {
            switch($formElement['type']) {
                case 'enum': $this->buildActivitiEnum($formElement); break;
                case 'string': $this->buildActivitiString($formElement); break;
                case 'long': $this->buildActivitiLong($formElement); break;
                //non standard fields (cool extensions)
                case 'json':
                case 'textarea': $this->buildActivitiTextArea($formElement); break;
                case 'embeddedForm': $this->buildActivitiEmbeddedForm($formElement); break;
                case 'boolean': $this->buildActivitiBoolean($formElement); break;
            }
        }

        if(!$this->getReadOnly())
            $this->addFieldSubmit("proceed");
    }

    /**
     * @param array $formElement
     */
    protected function buildActivitiEnum($formElement)
    {
        $field = $this->addFieldSelect($formElement['id']);

        $map = [];
        foreach($formElement['enumValues'] as $ev)
            $map[] = ['value'=>$ev['id'], 'label'=>$ev['name']];
        $field->setValueMap(new SimpleValueMap($map));

        $this->setUpActivitiField($field, $formElement);
    }

    /**
     * @param array $formElement
     */
    protected function buildActivitiString($formElement)
    {
        $field = $this->addFieldTextBox($formElement['id']);
        $this->setUpActivitiField($field, $formElement);
    }

    /**
     * @param array $formElement
     */
    protected function buildActivitiLong($formElement)
    {
        $field = $this->addFieldNumber($formElement['id']);
        $this->setUpActivitiField($field, $formElement);
    }

    protected  function buildActivitiBoolean($formElement)
    {
        $field = $this->addFieldCheckbox($formElement['id']);
        $this->setUpActivitiField($field, $formElement);
    }

    /**
     * @param array $formElement
     */
    protected function buildActivitiTextArea($formElement)
    {
        $field = $this->addFieldTextArea($formElement['id']);
        $this->setUpActivitiField($field, $formElement);
    }

    protected function setUpActivitiField(FieldInterface $field, $formElement) {
        $field->setLabel($formElement['name'])
              ->setValue($formElement['value'])
              ->setReadOnly(!$formElement['writable']);

        //submit the field only if it is writable
        if($formElement['writable']) {
              $field->setGroup('activiti');
        }

        if($formElement['datePattern']) {}
        if($formElement['readable']) {}
    }

    private function buildActivitiEmbeddedForm($formElement)
    {
        //we use this string to pass the definition, as activiti does not allow passing custom key value pairs like ENUM controls
        if(!preg_match('/(.+?)($|:(.+?))$/sim', $formElement['value'], $m))
            return;

        $serverId = $m[1];
        $parameters = @$m[3] ? json_decode($m[3],true) : [];

        if($formElement['writable']===false)
            $parameters['readOnly'] = true;

        $this->embeddedFormsLayout.="<h2>{{ '{$formElement['name']}'|t }}</h2><br>".
        "{{ coolWidget('$serverId', ".json_encode($parameters).",
         { onlyContent:true }
         ) }}";
    }

    public function getDefaultLayout() {
        return parent::getDefaultLayout().$this->embeddedFormsLayout;
    }
}
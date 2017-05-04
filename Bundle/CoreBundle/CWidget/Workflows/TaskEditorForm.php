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
use Eulogix\Cool\Lib\Widget\Widget;
use Eulogix\Cool\Lib\Widget\WidgetSlot;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class TaskEditorForm extends Widget {

    public function build() {

        $formServerId = "Cool/Workflows/PropertiesTaskEditorForm";

        if($taskId = $this->getTaskId()) {
            $activiti = Cool::getInstance()->getFactory()->getActiviti();
            $formData = $activiti->getFormData($taskId);
            if($formKey = @$formData['formKey'])
                $formServerId = $formKey;
        }

        $this->getAttributes()->set(self::ATTRIBUTE_ONLY_CONTENT, true);

        $this->setSlot('actualForm', new WidgetSlot($formServerId, $p = $this->getParameters()->all()));

        //propagate the recordSaved and close event from the inner form so that the lister is notified
        $this->addCommandJs("widget.widgetSlots.actualForm.on('recordSaved', function() { widget.emit('recordSaved'); });");
        $this->addCommandJs("widget.widgetSlots.actualForm.on('close', function() { widget.emit('close'); widget.destroyRecursive(); });");

        //when the inner form signals that a new task is available for the user as a result of the previous submit, we reload it with the new task id to display the task to the user
        //the 3 seconds delay gives the user a chance of seeing that the submission was successful
        //the inner taskFlow event signals the lister that it has to update its visuals too
        $this->addCommandJs("

        widget.widgetSlots.actualForm.on('taskFlow', function(payload) {
            setTimeout(function(){
                widget.definition.parameters._recordid = payload.task_id; widget.reBind();
                widget.emit('taskFlow', payload);
            },3000);
        });

        ");

        return parent::build();
    }

    /**
     * returns the pk of the record currently edited
     * @return mixed
     */
    public function getTaskId() {
        return $this->parameters->get(DataSourceInterface::RECORD_IDENTIFIER);
    }

    /**
     * specifies the dojo widget that will be instantiated by the client
     * @return string
     */
    public static function getClientWidget()
    {
        return "cool/workflow/taskEditor";
    }
}
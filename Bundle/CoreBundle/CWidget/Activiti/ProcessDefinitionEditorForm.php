<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\CWidget\Activiti;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

use Eulogix\Cool\Lib\Activiti\dataSource\ProcessDefinitionDataSource;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Form\DSCRUDForm;

class ProcessDefinitionEditorForm extends DSCRUDForm  {

    public function __construct($parameters = [])
    {
        parent::__construct($parameters);
        $ds = new ProcessDefinitionDataSource(Cool::getInstance()->getFactory()->getActiviti());
        $this->setDataSource($ds->build());
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return "COOL_ACTIVITI_PROCESS_DEFINITION_EDITOR_FORM";
    }

}
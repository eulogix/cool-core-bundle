<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Form\Configurator;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\FormConfigPeer;
use Eulogix\Cool\Lib\Form\FormInterface;
use Eulogix\Cool\Lib\Widget\Configurator\WidgetConfigurator;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class FormConfigurator extends WidgetConfigurator {

    /**
     * @var FormInterface
     */
    protected $widget;

    /**
     * @var array
     */
    protected $config;

    /**
     * @inheritdoc
     */
    protected function getTable() {
        return 'form_config';
    }

    /**
     * @inheritdoc
     */
    protected function getWidgetId() {
        return $this->widget->getId();
    }

    /**
    * @inheritdoc
    */
    public function load() {
        $id = $this->getBestMatchingStoredId();
        if($id) {
            $obj = FormConfigPeer::retrieveByPK($id);
            $this->config['layout'] = $obj->getLayout();
            return true;            
        } 
        return false;
    }

    /**
    * @inheritdoc
    */
    public function apply() {
        if($this->config) {
            $this->widget->setLayout($this->config['layout']);
        }        
    }
    
}
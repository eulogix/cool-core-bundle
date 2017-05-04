<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\CWidget\WidgetEditor;

use Eulogix\Cool\Lib\DataSource\DataSourceInterface;
use Eulogix\Cool\Lib\Form\CoolForm;
use Eulogix\Cool\Lib\Widget\Message;
use Eulogix\Cool\Lib\DataSource\DSRequest;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class WidgetEditor extends CoolForm {

    /**
     * used when instancing the edited widget, so that it knows that, for instance,
     * the editor is asking it the default configuration
     */
    const WIDGET_EDITOR_TOKEN = '_widgetEditor';

    protected $editedWidget;
    
    /**
    * returns a variation array using the value of the cat_* fields of the form
    * @return mixed
    */
    protected function getVariationFromFields() {
        $widget = $this->getEditedWidget();
        $a = [];
        if($vl = $widget->getVariationLevels()) {
            foreach($vl as $cat=>$levels) {
                $a[$cat] = $this->getField('cat_'.$cat)->getValue();
            }
        }    
        return $a; 
    }
    
    /**
    * we want this form to have a unique config, independent from its ancestor (the CoolForm form for form_config)
    */
    public function getId() {
        return get_class($this);
    }
    
    public function build() {
        
        $widget = $this->getEditedWidget();
        $configurator = $widget->getConfigurator();
        
        //add the tabs for the variation levels allowed by the edited widget
        $tabOptions = [];
        $selectDefaultJs = [];
        if($vl = $widget->getVariationLevels()) {
            foreach($vl as $cat=>$levels) {
                array_unshift($levels, 'default');
                $options = [];
                foreach($levels as $l) {
                    $options[] = ['label'=>$l, 'value'=>$l];
                }
                $fieldName = 'cat_'.$cat;
                //if the form is submitted, we look in the request, otherwise we pick the current state of the edited form
                $value = $this->actionCalled() ? $this->request->get($fieldName) : $widget->getActiveLevelVariant($cat);
                
                $tabOptions[$cat] = $options;    
                $this->addFieldTab($fieldName)
                    ->setOptions($options)
                    ->setValue($value)
                    ->setOnChange('container.callAction("switch");');
                $selectDefaultJs[] = "widget.getField('$fieldName').set('value','default', true);";
            }
        }

        if(!empty($selectDefaultJs))
            $this->addAction("select_default")->setOnClick(implode(";",$selectDefaultJs)." setTimeout(function(){ widget.callAction('switch'); }, 300);");

        //we use the tabs state to determine the variant of the form that we try to load
        $fieldVariation = $this->getVariationFromFields();
        $storedId = $configurator->getStoredId( $fieldVariation );
        $this->setRecordId($storedId);
        
        //if a combination exists, we add the possibility to remove it
        if($storedId) {
            $this->addAction('remove_combination')->setOnClick("widget.callAction('removeCombination');");    
        }
        
        //retrieve the stored variation to decorate tabs with hints about which variation are stored in the db
        $storedVariations = $configurator->getStoredVariations( $fieldVariation );
        foreach($storedVariations as $level=>$values) {
            $options = [];
            foreach($tabOptions[$level] as $item) {
               $options[]= ['label'=> in_array($item['value'], array_keys($values)) ? '<b>'.$item['label'].' ('.$values[$item['value']].')</b>' : $item['label'], 
                            'value'=> $item['value']];
            }    
            $this->getField('cat_'.$level)->setOptions($options);
        }
        
        //add the fields from the form_config table, name and variation must be hidden
        parent::build();

        //$this->getField('name')->setReadOnly();

        //name does never change
        $name = $widget->getId();
        $this->getField('name')->setValue( $name );
        
        //variation string is calculated from the tab state
        $variation = $configurator->getVariationString( $this->getVariationFromFields() );
        $this->getField('variation')->setValue( $variation );
        
        //$this->parameters->set('_debug', true);
        return $this;
    }  
    
    public function onSwitch() {
        $this->fill( $this->request->all() );
        $this->clear()->build();    
    }
    
    public function onRemoveCombination() {
        if($ds = $this->getDataSource()) {
            $dsr = new DSRequest();
            $dsr->setOperationType($dsr::OPERATION_TYPE_REMOVE)
                ->setParameters([DataSourceInterface::RECORD_IDENTIFIER=>$this->getDSRecord()->getRecordId()]);
            $dsresponse = $ds->execute($dsr);
            $success = ($dsresponse->getStatus() == $dsresponse::STATUS_TRANSACTION_SUCCESS);
            if($success) {
                $this->clear()->build();
                $this->addMessage(Message::TYPE_INFO, "DELETED");
                return;
            } else $this->addMessage(Message::TYPE_ERROR, "ERROR");
        } $this->addMessage(Message::TYPE_ERROR, "ERROR2");
    }
    
    /**
    * returns the instance of the widget being edited, configured with its request parameters
    * @return \Eulogix\Cool\Lib\Widget\WidgetInterface
    */
    protected function getEditedWidget() {
        if($this->editedWidget) 
            return $this->editedWidget;
        $p = json_decode($this->parameters->get('edit_parameters'), true);
        $p[self::WIDGET_EDITOR_TOKEN] = '1';
        $widget = $this->getWidgetFactory()->getWidget($this->parameters->get('edit_serverid'), $p);  
        $widget->build();  
        return $this->editedWidget = $widget;
    }
    
    /**
    * this form is not configurable as the layout is composed
    */
    public function isConfigurable() {
        return false;
    }

    /**
     * @return string
     */
    protected function getBaseLayout() {
        $l = '';
        $widget = $this->getEditedWidget();
        if($vl = $widget->getVariationLevels()) {
            foreach($vl as $cat=>$levels) {
                $l.="cat_$cat@!\n";
            }
        }
        return $l."name:300, variation:300\n";
    }

}
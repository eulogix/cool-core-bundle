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

use Eulogix\Cool\Lib\Form\DSCRUDForm;
use Eulogix\Cool\Lib\Widget\WidgetInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

abstract class BaseConfigEditorForm extends DSCRUDForm {

    /**
     * used when instancing the edited widget, so that it can know that, for instance,
     * the editor is asking it the default configuration
     */
    const WIDGET_EDITOR_TOKEN = '_widgetEditor';

    protected $editedWidget;

    /**
     * returns the instance of the widget being edited, configured with its request parameters
     * @return WidgetInterface
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
}
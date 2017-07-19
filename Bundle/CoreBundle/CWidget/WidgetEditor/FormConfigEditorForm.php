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

use Eulogix\Cool\Lib\DataSource\Classes\WidgetEditor\FormConfigDataSource;
use Eulogix\Cool\Lib\Form\FormInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class FormConfigEditorForm extends BaseConfigEditorForm {

    public function __construct($parameters = [])
    {
        parent::__construct($parameters);
        $ds = new FormConfigDataSource();
        $this->setDataSource($ds->build());
    }

    public function build() {
        parent::build();
        $this->addAction('fetch_defaults')->setOnClick("widget.callAction('fetchDefaults');");
        return $this;
    }

    public function onFetchDefaults() {
        /** @var FormInterface $widget */
        $widget = $this->getEditedWidget();
        $this->addFieldTextArea('layout');
        $this->getField('layout')->setValue( $widget->getDefaultLayout() );
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return "COOL_FORM_CONFIG_EDITOR_FORM";
    }
                                                                                                                                        
}
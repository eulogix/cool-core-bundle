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

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class FormEditor extends WidgetEditor {

    public function build() {
        parent::build();
        //add misc actions
        $this->addAction('fetch_defaults')->setOnClick("widget.callAction('fetchDefaults');");
        return $this;
    }

    public function onFetchDefaults() {
        $widget = $this->getEditedWidget();
        $this->addFieldTextArea('layout');
        $this->getField('layout')->setValue( $widget->getDefaultLayout() );
    }

    public function getLayout() {
        $layout="<fields>".
            $this->getBaseLayout().
            "layout:100%:200px@!
            save|align=center@!</fields>";
        return $layout;
    }

}
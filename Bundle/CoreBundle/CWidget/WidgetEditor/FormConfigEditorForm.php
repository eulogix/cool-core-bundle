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
use Eulogix\Cool\Lib\Widget\Help\WikimediaHelpProvider;

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

        /** @var FormInterface $widget */
        $widget = $this->getEditedWidget();

        $this->addAction('fetch_defaults')->setOnClick("widget.callAction('fetchDefaults');");

        $helpProvider = $widget->getHelpProvider();
        if($helpProvider instanceof WikimediaHelpProvider) {
            $wikiPageTpl = $this->getField('wiki_help_page')->getValue();
            $this->addAction('populate_wiki')->setConfirmedOnClick("widget.callAction('populateWikiPage');", "Create/update page {$helpProvider->getProcessedWikiPageTitle($wikiPageTpl)} ?");
        }

        return $this;
    }

    public function onFetchDefaults() {
        /** @var FormInterface $widget */
        $widget = $this->getEditedWidget();
        $this->addFieldTextArea('layout');
        $this->getField('layout')->setValue( $widget->getDefaultLayout() );

        if($wikiHelpPage = $this->getField('wiki_help_page'))
            $wikiHelpPage->setValue( $widget->getDefaultWikiHelpPage() );
    }

    public function onPopulateWikiPage() {
        /** @var FormInterface $widget */
        $widget = $this->getEditedWidget();
        $helpProvider = $widget->getHelpProvider();
        if($helpProvider instanceof WikimediaHelpProvider) {
            try {
                $helpProvider->populateFormFieldsPage( $this->getField('wiki_help_page')->getValue() );
                $this->addMessageInfo("Wiki page populated successfully");
            } catch(\Exception $e) {
                $this->addMessageError($e->getMessage());
            }
        } else $this->addMessageError("This form does not have a WikimediaHelpProvider");
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return "COOL_FORM_CONFIG_EDITOR_FORM";
    }
                                                                                                                                        
}
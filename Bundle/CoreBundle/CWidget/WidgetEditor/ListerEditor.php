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

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\ListerConfigColumnPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\ListerConfigQuery;
use Eulogix\Cool\Lib\Lister\ListerInterface;
use Eulogix\Cool\Lib\Widget\Message;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\ListerConfigColumn;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class ListerEditor extends WidgetEditor {

    public function build() {
        parent::build();
        $this->addAction('fetch_defaults')->setOnClick("widget.callAction('fetchDefaults');");
        return $this;
    }

    public function onFetchDefaults() {
        if($this->getDSRecord()->isNew()) {
            $this->addMessage(Message::TYPE_ERROR, 'SAVE THE RECORD FIRST');
        } else {
        
            $configId = $this->getDSRecord()->get('lister_config_id');
            $config = ListerConfigQuery::create()->findPk($configId);

            /** @var ListerInterface $widget */
            $widget = $this->getEditedWidget();
            $columns = $widget->getColumns();
            $defaultSort = $widget->getInitialSort();

            $i = 1;

            foreach($columns as $listerCol) {

                $fieldName = $listerCol->getName();
                $crit = new \Criteria();
                $crit->add(ListerConfigColumnPeer::NAME, $fieldName);

                if($config->getListerConfigColumns($crit)->count() == 0) {

                    $configColumn = new ListerConfigColumn();
                    $configColumn->setListerConfigId( $configId );
                    $configColumn->setName($fieldName);
                    $configColumn->setSortOrder( $listerCol->getSortOrder() ? $listerCol->getSortOrder() : 100*$i );

                    $configColumn->setWidth( $listerCol->getWidth() )
                        ->setSortableFlag( $listerCol->getSortable() )
                        ->setEditableFlag( $listerCol->getEditable() )
                        ->setCellTemplate( $listerCol->getCellTemplate() )
                        ->setCellTemplateJs( $listerCol->getCellTemplateJs() )
                        ->setColumnStyleCss( $listerCol->getColumnStyleCss() )
                        ->setShowSummaryFlag( $listerCol->getHasSummary() )
                        ->setTruncateChars( $listerCol->getMaxChars() );

                    $idx = array_search($fieldName, array_keys($defaultSort));
                    if($idx !== false) {
                        $configColumn->setSortbyOrder($idx+1)
                                     ->setSortbyDirection(strtoupper($defaultSort[$fieldName]));
                    }

                    $configColumn->save();
                } else {
                    $this->addMessageWarning("column '$fieldName' already exists. Skipped");
                }

                $i++;
            }
            $this->addMessage(Message::TYPE_INFO, "DONE");

            $this->forceRedraw();
        }
    }

    public function getLayout() {
        $layout="<fields>".
            $this->getBaseLayout().
            "filter_show_flag, filter_server_id:300
            save|align=center@!</fields>";
        return $layout;
    }

}
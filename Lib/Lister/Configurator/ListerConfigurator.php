<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Lister\Configurator;

use Eulogix\Cool\Lib\Lister\ListerInterface;
use Eulogix\Cool\Lib\Widget\Configurator\WidgetConfigurator;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\ListerConfig;
use Eulogix\Cool\Lib\Widget\WidgetSlot;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class ListerConfigurator extends WidgetConfigurator {

    /**
     * @var ListerInterface
     */
    protected $widget;

    /**
     * @var ListerConfig
     */
    protected $config;

    /**
     * @inheritdoc
     */
    protected function getTable() {
        return 'lister_config';
    }

    /**
     * @inheritdoc
     */
    protected function getWidgetId() {
        return $this->widget->getId();
    }

    /**
     * @param int $id
     * @return ListerConfig
     */
    protected function getConfigObj($id) {
        return  $this->getCoolSchema()->getPropelObject($this->getTable(), $id);
    }

    /**
     * @inheritdoc
     */
    public function load() {
        $id = $this->getBestMatchingStoredId();
        if($id) {
            $this->config = $this->getConfigObj($id);
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function apply() {
        if($this->config) {
            if($this->config->getFilterShowFlag()) {
                $serverId = $this->config->getFilterServerId() ? $this->config->getFilterServerId() : $this->widget->getDefaultFilterWidget();
                $params = $this->widget->getParameters()->all();
                $params['_parent'] = get_class($this->widget);
                $this->widget->setFilterSlot( new WidgetSlot($serverId, $params) );
            }

            $configColumns = $this->config->getListerConfigColumns();
            $configColumnNames = [];

            $tempInitialSort = [];

            //configure existing columns using configuration parameters
            foreach($configColumns as $configColumn) {
                $configColumnNames[] = $configColumn->getName();

                $widgetColumn = $this->widget->getColumn($configColumn->getName());
                if(!$widgetColumn)
                    $widgetColumn = $this->widget->addNewColumn($configColumn->getName());

                $widgetColumn->setWidth( $configColumn->getWidth() )
                        ->setSortable( $configColumn->getSortableFlag() )
                        ->setEditable( $configColumn->getEditableFlag() )
                        ->setCellTemplate( $configColumn->getCellTemplate() )
                        ->setCellTemplateJs( $configColumn->getCellTemplateJs() )
                        ->setColumnStyleCss( $configColumn->getColumnStyleCss() )
                        ->setHasSummary( $configColumn->getShowSummaryFlag() )
                        ->setTooltipMaxWidth( $configColumn->getTooltipMaxWidth() )
                        ->setTooltipJsExpression( $configColumn->getTooltipJsExpression() )
                        ->setTooltipUrlJsExpression( $configColumn->getTooltipUrlJsExpression() )
                        ->setTooltipDelay( $configColumn->getTooltipDelayMsec() )
                        ->setMaxChars( $configColumn->getTruncateChars() );

                if($so = $configColumn->getSortOrder())
                    $widgetColumn->setSortOrder( $so );

                if($configColumn->getSortbyOrder()) {
                    $widgetColumn->setSortable(true);
                    $tempInitialSort[$configColumn->getSortbyOrder()] = [
                        'field' => $configColumn->getName(),
                        'dir' => $configColumn->getSortbyDirection() == 'ASC' ? ListerInterface::SORT_ASC : ListerInterface::SORT_DESC
                    ];
                }
            }

            //build the initial sort
            ksort($tempInitialSort);
            $initialSort = [];
            foreach($tempInitialSort as $ts)
                $initialSort[$ts['field']] = $ts['dir'];

            if(!empty($initialSort))
                $this->widget->setInitialSort($initialSort);

            //then remove columns not specified in the configuration
            if(count($configColumnNames)>0) {
                $ds = $this->widget->getDataSource();
                $listerColumns = $this->widget->getColumnNames();
                foreach($listerColumns as $listerColumn)
                    if(!in_array($listerColumn, $configColumnNames)) {
                        if($ds->hasField($listerColumn)) {
                            if(!$this->widget->isColumnPropagated($listerColumn))
                                $this->widget->getDataSource()->getField($listerColumn)->setLazyFetch(true);
                            $this->widget->removeColumn($listerColumn);
                        } else $this->widget->addMessageWarning("The lister definition contains a column ({1}) not defined in the datasource ({2}). Check the PHP code!", $listerColumn, get_class($ds));
                    }
            }
        }
    }

}
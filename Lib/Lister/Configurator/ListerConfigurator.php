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

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\ListerConfigColumn;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\ListerConfigQuery;
use Eulogix\Cool\Lib\Lister\ListerInterface;
use Eulogix\Cool\Lib\Widget\Configurator\WidgetConfigurator;

use Eulogix\Cool\Lib\Widget\WidgetSlot;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class ListerConfigurator extends WidgetConfigurator {

    /**
     * @inheritdoc
     */
    public function configurationExists()
    {
        return $this->getConfigQuery()->count() >= 1;
    }

    /**
     * applies the stored configuration for the widget in its current state
     */
    public function applyConfiguration()
    {
        if($this->configurationExists()) {
            $config = $this->getConfigQuery()->findOne();
            
            /** @var ListerInterface $widget */
            $widget = $this->widget;

            if($minHeight = $config->getMinHeight())
                $widget->getAttributes()->set(ListerInterface::ATTR_MIN_HEIGHT, $minHeight);

            if($maxHeight = $config->getMaxHeight())
                $widget->getAttributes()->set(ListerInterface::ATTR_MAX_HEIGHT, $maxHeight);

            if($config->getFilterShowFlag()) {
                $serverId = $config->getFilterServerId() ? $config->getFilterServerId() : $widget->getDefaultFilterWidget();
                $params = $widget->getParameters()->all();
                $params['_parent'] = get_class($widget);
                $widget->setFilterSlot( new WidgetSlot($serverId, $params) );
            }

            $configColumns = $config->getListerConfigColumns();
            /**
             * @var ListerConfigColumn[] $configColumnsArray
             */
            $configColumnsArray = [];

            $tempInitialSort = [];

            //configure existing columns using configuration parameters
            foreach($configColumns as $configColumn) {
                $configColumnsArray[ $configColumn->getName() ] = $configColumn;

                $widgetColumn = $widget->getColumn($configColumn->getName());
                if(!$widgetColumn)
                    $widgetColumn = $widget->addNewColumn($configColumn->getName());

                $widgetColumn->setWidth( $configColumn->getWidth() )
                    ->setSortable( $configColumn->getSortableFlag() )
                    ->setEditable( $configColumn->getEditableFlag() )
                    ->setCellTemplate( $configColumn->getCellTemplate() )
                    ->setCellTemplateJs( $configColumn->getCellTemplateJs() )
                    ->setColumnStyleCss( $configColumn->getColumnStyleCss() )
                    ->setDijitWidgetTemplate( $configColumn->getDijitWidgetTemplate() )
                    ->setSetValueJs( $configColumn->getDijitWidgetSetValueJs() )
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
                $widget->setInitialSort($initialSort);

            //then remove columns not specified in the configuration
            if(count($configColumnsArray)>0) {
                $ds = $widget->getDataSource();
                $listerColumns = $widget->getColumnNames();
                foreach($listerColumns as $listerColumn) {

                    $columnConfigured = isset( $configColumnsArray[ $listerColumn ] );
                    $columnVisible = $columnConfigured && !$configColumnsArray[ $listerColumn ]->getHiddenFlag();

                    if ($ds->hasField($listerColumn) && !$columnConfigured && !$widget->isColumnPropagated($listerColumn))
                            $widget->getDataSource()->getField($listerColumn)->setLazyFetch(true);

                    if(!$columnVisible)
                        $widget->removeColumn($listerColumn);
                    
                }
            }

        }
        return $this;
    }

    /**
     * @return ListerConfigQuery
     */
    private function getConfigQuery() {
        $currentVariation = $this->widget->getCurrentVariation();
        return ListerConfigQuery::create()
            ->filterByName( $this->widget->getId() )
            ->filterByVariation( $currentVariation );
    }
    
}
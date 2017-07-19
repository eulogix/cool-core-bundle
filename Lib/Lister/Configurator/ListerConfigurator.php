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
            
            if($config->getFilterShowFlag()) {
                $serverId = $config->getFilterServerId() ? $config->getFilterServerId() : $widget->getDefaultFilterWidget();
                $params = $widget->getParameters()->all();
                $params['_parent'] = get_class($widget);
                $widget->setFilterSlot( new WidgetSlot($serverId, $params) );
            }

            $configColumns = $config->getListerConfigColumns();
            $configColumnNames = [];

            $tempInitialSort = [];

            //configure existing columns using configuration parameters
            foreach($configColumns as $configColumn) {
                $configColumnNames[] = $configColumn->getName();

                $widgetColumn = $widget->getColumn($configColumn->getName());
                if(!$widgetColumn)
                    $widgetColumn = $widget->addNewColumn($configColumn->getName());

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
                $widget->setInitialSort($initialSort);

            //then remove columns not specified in the configuration
            if(count($configColumnNames)>0) {
                $ds = $widget->getDataSource();
                $listerColumns = $widget->getColumnNames();
                foreach($listerColumns as $listerColumn)
                    if(!in_array($listerColumn, $configColumnNames)) {
                        if($ds->hasField($listerColumn)) {
                            if(!$widget->isColumnPropagated($listerColumn))
                                $widget->getDataSource()->getField($listerColumn)->setLazyFetch(true);
                            $widget->removeColumn($listerColumn);
                        } else $widget->addMessageWarning("The lister definition contains a column ({1}) not defined in the datasource ({2}). Check the PHP code!", $listerColumn, get_class($ds));
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
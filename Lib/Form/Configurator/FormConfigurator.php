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

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\FormConfigQuery;
use Eulogix\Cool\Lib\Widget\Configurator\WidgetConfigurator;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class FormConfigurator extends WidgetConfigurator {

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
            $this->widget->setLayout( $config->getLayout() );
        }
        return $this;
    }

    /**
     * @return FormConfigQuery
     */
    private function getConfigQuery() {
        $currentVariation = $this->widget->getCurrentVariation();
        return FormConfigQuery::create()
            ->filterByName( $this->widget->getId() )
            ->filterByVariation( $currentVariation );
    }
}
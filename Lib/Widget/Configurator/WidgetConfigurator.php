<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Widget\Configurator;

use Eulogix\Cool\Lib\Widget\WidgetInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

abstract class WidgetConfigurator {

    /**
     * @var WidgetInterface
     */
    protected $widget;

    /**
     * @param WidgetInterface $widget
     */
    public function __construct($widget) {
        $this->widget = $widget;
    }

    /**
     * @return string
     */
    protected function getWidgetId() {
        return $this->widget->getId();
    }

    /**
     * returns true if a configuration exists for the widget in its current state
     * @return boolean
     */
    abstract public function configurationExists();

    /**
     * applies the stored configuration for the widget in its current state
     * @return $this
     */
    abstract public function applyConfiguration();

} 
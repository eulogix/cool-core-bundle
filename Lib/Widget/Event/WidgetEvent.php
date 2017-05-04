<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Widget\Event;

use Eulogix\Cool\Lib\Widget\WidgetInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class WidgetEvent extends Event
{
    /**
     * @var WidgetInterface
     */
    protected $widget;

    /**
     * @param WidgetInterface $widget
     */
    public function __construct(WidgetInterface $widget)
    {
        $this->widget = $widget;
    }

    /**
     * @return WidgetInterface
     */
    public function getWidget()
    {
        return $this->widget;
    }
}
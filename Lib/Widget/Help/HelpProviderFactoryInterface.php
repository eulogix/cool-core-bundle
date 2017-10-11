<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Widget\Help;

use Eulogix\Cool\Lib\Widget\WidgetInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

interface HelpProviderFactoryInterface
{
    /**
     * @param WidgetInterface $widget
     * @return WidgetHelpProviderInterface
     */
    public function getHelperFor(WidgetInterface $widget);
}
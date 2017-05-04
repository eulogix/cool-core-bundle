<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Widget\Factory;

use Eulogix\Cool\Lib\Widget\WidgetInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

interface WidgetFactoryInterface {

    /**
     * @param string $serverId
     * @param array $parameters
     * @return WidgetInterface|bool
     */
    public function getWidget($serverId, $parameters=null);

}
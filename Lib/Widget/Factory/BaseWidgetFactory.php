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

class BaseWidgetFactory implements WidgetFactoryInterface {

    /**
     * tries to resolve the path as a complete namespace, and loads the corresponding Widget
     *
     * @param mixed $path
     * @param array $parameters
     * @return WidgetInterface|bool
     */
    protected function getWidgetByNamespace($path, $parameters=null) {
        $nameSpace = str_replace("/","\\",$path);
        if(class_exists($nameSpace)) {
            return new $nameSpace( $parameters );  //GET  data as init parameters
        }
        return false;    
    }

    /**
     * @inheritdoc
     */
    public function getWidget($serverId, $parameters=null) {
        if($WidgetInstance = $this->getWidgetByNamespace($serverId, $parameters)) {
            //propagate the factory
            $WidgetInstance->setWidgetFactory($this);
            return $WidgetInstance;
        }
        return false;
    }

}
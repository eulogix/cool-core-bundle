<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\DataSource;

use Eulogix\Cool\Lib\Cool;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class DataSourceManager {

    /**
     * this method maps an Id, or a string, to a datasource instance.
     *
     * @param string $id
     * @throws \Exception
     * @return DataSourceInterface
     */
    public function getDataSource($id) {

        $container = Cool::getInstance()->getContainer();
        $serviceName = "ds.$id";

        if($container->has($serviceName)) {
            $serviceObj = $container->get($serviceName);
            if($serviceObj instanceof DataSourceInterface)
                return $serviceObj;
            throw new \Exception("service $serviceName does not implement DataSourceInterface");
        }

        if(class_exists($id)) {
            $ds = new $id();
            if($ds instanceof DataSourceInterface)
                return $ds;
            else throw new \Exception("namespace $id does not implement DataSourceInterface");
        }

        throw new \Exception("DataSource $id not registered");
    }

}
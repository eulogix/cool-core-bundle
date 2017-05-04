<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\File;

use Eulogix\Cool\Lib\Cool;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class FileRepositoryFactory {

    /**
     * @param string $id
     * @return FileRepositoryInterface
     * @throws \Exception
     */
    public static function fromId($id) {
        if(!$id) {
            throw new \Exception("missing repository service id");
        }
        $container = Cool::getInstance()->getContainer();
        $serviceName = "file.repository.$id";

        if($container->has($serviceName)) {
            $serviceObj = $container->get($serviceName);
            if($serviceObj instanceof FileRepositoryInterface)
                return $serviceObj;
            else throw new \Exception("service $serviceName does not implement FileRepositoryInterface");
        } else throw new \Exception("service $serviceName does not exist");
    }

} 
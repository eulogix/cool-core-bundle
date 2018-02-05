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

        if(preg_match('/^TMP/sim', $id)) {
            $cacher = Cool::getInstance()->getFactory()->getSharedCacher();
            return unserialize($cacher->fetch($id));
        } else {
            $container = Cool::getInstance()->getContainer();
            $serviceName = "file.repository.$id";

            if ($container->has($serviceName)) {
                $serviceObj = $container->get($serviceName);
                if ($serviceObj instanceof FileRepositoryInterface) {
                    return $serviceObj;
                } else {
                    throw new \Exception("service $serviceName does not implement FileRepositoryInterface");
                }
            } else {
                throw new \Exception("service $serviceName does not exist");
            }
        }
    }

    /**
     * @param FileRepositoryInterface $repository
     * @return string a temporary id
     */
    public static function register(FileRepositoryInterface $repository) {
        $cacher = Cool::getInstance()->getFactory()->getSharedCacher();
        $serializedClass = serialize($repository);
        $key = 'TMP'.sha1($serializedClass);
        $cacher->store($key, $serializedClass);
        return $key;
    }

    /**
     * @param string $temporaryId
     */
    public static function unregister($temporaryId) {
        $cacher = Cool::getInstance()->getFactory()->getSharedCacher();
        $cacher->delete($temporaryId);
    }
} 
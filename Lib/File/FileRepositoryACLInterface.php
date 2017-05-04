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

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

interface FileRepositoryACLInterface {

    /**
     * @return FileRepositoryInterface
     */
    public function getRepository();

    /**
     * @param $path
     * @return bool
     */
    public function canBrowse($path);

    /**
     * @param $path
     * @param FileProxyInterface|null $file
     * @return bool
     */
    public function canCreateFileIn($path, $file=null);

    /**
     * @param $path
     * @param FileProxyInterface|null $dir
     * @return bool
     */
    public function canCreateDirIn($path, $dir=null);

    /**
     * @param $path
     * @return bool
     */
    public function canDownloadFile($path);

    /**
     * @param $path
     * @return bool
     */
    public function canDelete($path);

    /**
     * @param $path
     * @return bool
     */
    public function canOverwrite($path);

    /**
     * @param $path
     * @param $targetDir
     * @return bool
     */
    public function canMove($path, $targetDir);

    /**
     * @param $path
     * @param null $newName
     * @return bool
     */
    public function canRename($path, $newName=null);

}
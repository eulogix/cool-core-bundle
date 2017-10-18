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

abstract class BaseFileRepositoryPermissions {

    /**
     * @var FileRepositoryInterface
     */
    protected $repository;

    /**
     * @param FileRepositoryInterface $repository
     */
    public function __construct(FileRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * @return FileRepositoryInterface
     */
    public function getRepository() {
        return $this->repository;
    }

    /**
     * @param $path
     * @return bool
     */
    public abstract function canBrowse($path);

    /**
     * @param $path
     * @param FileProxyInterface|null $file
     * @return bool
     */
    public abstract function canCreateFileIn($path, $file=null);

    /**
     * @param $path
     * @param FileProxyInterface|null $dir
     * @return bool
     */
    public abstract function canCreateDirIn($path, $dir=null);

    /**
     * @param $path
     * @return bool
     */
    public abstract function canDownloadFile($path);

    /**
     * @param $path
     * @return bool
     */
    public abstract function canDelete($path);

    /**
     * @param $path
     * @return bool
     */
    public abstract function canOverwrite($path);

    /**
     * @param $path
     * @param $targetDir
     * @return bool
     */
    public abstract function canMove($path, $targetDir);

    /**
     * @param $path
     * @param null $newName
     * @return bool
     */
    public abstract function canRename($path, $newName=null);

    /**
     * @param string $path
     * @return array
     */
    public function getAllFor($path) {
        return [
            'canBrowse' => $this->canBrowse($path),
            'canCreateDirIn' => $this->canCreateDirIn($path),
            'canCreateFileIn' => $this->canCreateFileIn($path),
            'canDelete' => $this->canDelete($path),
            'canOverwrite' => $this->canOverwrite($path),
            'canRename' => $this->canRename($path)
        ];
    }

}
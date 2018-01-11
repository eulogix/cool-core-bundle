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
 * user permissions for file and folders, relies on settings
 *
 *
 *
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class FileSystemFileRepositoryPermissions extends BaseFileRepositoryPermissions {

    /**
     * @inheritdoc
     */
    public function canBrowse($path)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function canCreateFileIn($path, $file = null)
    {
        return $this->isDir($path);
    }

    /**
     * @inheritdoc
     */
    public function canCreateDirIn($path, $dir = null)
    {
        return $this->isDir($path);
    }

    /**
     * @inheritdoc
     */
    public function canDownloadFile($path)
    {
        return $this->isFile($path);
    }

    /**
     * @inheritdoc
     */
    public function canDelete($path)
    {
        return $this->getRepository()->exists($path);
    }

    /**
     * @inheritdoc
     */
    public function canOverwrite($path)
    {
        return $this->isFile($path);
    }

    /**
     * @inheritdoc
     */
    public function canMove($path, $targetDir)
    {
        return $this->isDir($targetDir);
    }

    /**
     * @inheritdoc
     */
    public function canRename($path, $newName = null)
    {
        return $this->getRepository()->exists($path);
    }

    /**
     * @return FileSystemFileRepository
     */
    public function getRepository() {
        return parent::getRepository();
    }

    /**
     * @inheritdoc
     */
    public function canSetProperties($path)
    {
        return $this->isFile($path);
    }

    /**
     * @param string $path
     * @return bool
     */
    private function isFile($path) {
        $p = $this->getRepository()->get($path);
        return !$p->isDirectory();
    }

    /**
     * @param string $path
     * @return bool
     */
    private function isDir($path) {
        $p = $this->getRepository()->get($path);
        return $p->isDirectory();
    }

}
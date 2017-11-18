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
use Eulogix\Cool\Lib\Database\Propel\CoolPropelObject;
use Eulogix\Cool\Lib\Database\Schema;
use Eulogix\Cool\Lib\Translation\Translator;
use Eulogix\Cool\Lib\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * user permissions for file and folders, relies on settings
 *
 *
 *
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class CoolTableFileRepositoryPermissions extends BaseFileRepositoryPermissions {

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
        return false;
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
        return $this->isFile($path);
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
        return $this->isFile($path) && $this->isDir($targetDir);
    }

    /**
     * @inheritdoc
     */
    public function canRename($path, $newName = null)
    {
        return $this->isFile($path);
    }

    /**
     * @inheritdoc
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
        $p = $this->getRepository()->parsePathId($path);
        return !empty($p['file_id']);

    }

    /**
     * @param string $path
     * @return bool
     */
    private function isDir($path) {
        $p = $this->getRepository()->parsePathId($path);
        return($p['category'] || $p['pk']);
    }

}
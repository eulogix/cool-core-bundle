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
 * Base permissions for table file repositories.
 * Folders are fixed and can not be modified
 *
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class FileRepositoryPermissionsSet extends BaseFileRepositoryPermissions {

    /**
     * @var BaseFileRepositoryPermissions[]
     */
    protected $permissions = [];

    public function addPermissions(BaseFileRepositoryPermissions $permissions) {
        $this->permissions[] = $permissions;
    }

    /**
     * @inheritdoc
     */
    public function canBrowse($path)
    {
        foreach($this->permissions as $permissions)
            if(!$permissions->canBrowse($path))
                return false;
        return true;
    }

    /**
     * @inheritdoc
     */
    public function canCreateFileIn($path, $file = null)
    {
        foreach($this->permissions as $permissions)
            if(!$permissions->canCreateFileIn($path, $file))
                return false;
        return true;
    }

    /**
     * @inheritdoc
     */
    public function canCreateDirIn($path, $dir = null)
    {
        foreach($this->permissions as $permissions)
            if(!$permissions->canCreateDirIn($path, $dir))
                return false;
        return true;
    }

    /**
     * @inheritdoc
     */
    public function canDownloadFile($path)
    {
        foreach($this->permissions as $permissions)
            if(!$permissions->canDownloadFile($path))
                return false;
        return true;
    }

    /**
     * @inheritdoc
     */
    public function canDelete($path)
    {
        foreach($this->permissions as $permissions)
            if(!$permissions->canDelete($path))
                return false;
        return true;
    }

    /**
     * @inheritdoc
     */
    public function canOverwrite($path)
    {
        foreach($this->permissions as $permissions)
            if(!$permissions->canOverwrite($path))
                return false;
        return true;
    }

    /**
     * @inheritdoc
     */
    public function canMove($path, $targetDir)
    {
        foreach($this->permissions as $permissions)
            if(!$permissions->canMove($path, $targetDir))
                return false;
        return true;
    }

    /**
     * @inheritdoc
     */
    public function canRename($path, $newName = null)
    {
        foreach($this->permissions as $permissions)
            if(!$permissions->canRename($path, $newName))
                return false;
        return true;
    }

    /**
     * @inheritdoc
     */
    public function canSetProperties($path)
    {
        foreach($this->permissions as $permissions)
            if(!$permissions->canSetProperties($path))
                return false;
        return true;
    }
}
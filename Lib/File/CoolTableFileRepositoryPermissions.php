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
 * this repository mimics a hierarchical file repository
 * ids are composed as follows
 *
 * /<schemaName>/<tableName>/<recordPk>[/category|optional]/numericFileId
 *
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class CoolTableFileRepositoryPermissions extends BaseFileRepositoryPermissions {

    /**
     * @param $path
     * @return bool
     */
    public function canBrowse($path)
    {
        return true;
    }

    /**
     * @param $path
     * @param FileProxyInterface|null $file
     * @return bool
     */
    public function canCreateFileIn($path, $file = null)
    {
        $p = $this->getRepository()->parsePathId($path);
        return($p['category'] || $p['pk']);
    }

    /**
     * @param $path
     * @param FileProxyInterface|null $dir
     * @return bool
     */
    public function canCreateDirIn($path, $dir = null)
    {
        return false;
    }

    /**
     * @param $path
     * @return bool
     */
    public function canDownloadFile($path)
    {
        return true;
    }

    /**
     * @param $path
     * @return bool
     */
    public function canDelete($path)
    {
        return true;
    }

    /**
     * @param $path
     * @return bool
     */
    public function canOverwrite($path)
    {
        return true;
    }

    /**
     * @param $path
     * @param $targetDir
     * @return bool
     */
    public function canMove($path, $targetDir)
    {
        return true;
    }

    /**
     * @param $path
     * @param null $newName
     * @return bool
     */
    public function canRename($path, $newName = null)
    {
        return true;
    }

    /**
     * @return CoolTableFileRepository
     */
    public function getRepository() {
        return parent::getRepository();
    }
}
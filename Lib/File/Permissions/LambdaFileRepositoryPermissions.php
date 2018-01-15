<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\File\Permissions;

use Eulogix\Cool\Lib\File\BaseFileRepositoryPermissions;

/**
 *
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class LambdaFileRepositoryPermissions extends BaseFileRepositoryPermissions {

    const LAMBDA_CAN_BROWSE = 'canBrowse';
    const LAMBDA_CAN_CREATE_DIRS = 'canCreateDirs';
    const LAMBDA_CAN_CREATE_FILES = 'canCreate';
    const LAMBDA_CAN_DOWNLOAD_FILES = 'canDownload';
    const LAMBDA_CAN_DELETE = 'canDelete';
    const LAMBDA_CAN_OVERWRITE = 'canOverwrite';
    const LAMBDA_CAN_MOVE = 'canMove';
    const LAMBDA_CAN_SET_PROPERTIES = 'canSetProperties';

    /**
     * @var callable[]
     */
    protected $lambdas = [];

    /**
     * @param string $lambdaName
     * @param callable $lambda
     */
    public function setLambda($lambdaName, callable $lambda) {
        $this->lambdas[$lambdaName] = $lambda;
    }

    /**
     * @param string $path
     * @param string $lambdaName
     *
     * @param array $arguments
     * @return bool
     */
    protected function checkLambda($path, $lambdaName, array $arguments) {
        if(isset($this->lambdas[$lambdaName])) {
            return call_user_func($this->lambdas[$lambdaName], $arguments);
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function canBrowse($path)
    {
        return $this->checkLambda($path, self::LAMBDA_CAN_BROWSE, func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function canCreateFileIn($path, $file = null)
    {
        return $this->checkLambda($path, self::LAMBDA_CAN_CREATE_FILES, func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function canCreateDirIn($path, $dir = null)
    {
        return $this->checkLambda($path, self::LAMBDA_CAN_CREATE_DIRS, func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function canDownloadFile($path)
    {
        return $this->checkLambda($path, self::LAMBDA_CAN_DOWNLOAD_FILES, func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function canDelete($path)
    {
        return $this->checkLambda($path, self::LAMBDA_CAN_DELETE, func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function canOverwrite($path)
    {
        return $this->canCreateFileIn($path) &&
               $this->checkLambda($path, self::LAMBDA_CAN_OVERWRITE, func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function canMove($path, $targetDir)
    {
        return $this->checkLambda($path, self::LAMBDA_CAN_MOVE, func_get_args()) &&
               $this->canOverwrite($targetDir);
    }

    /**
     * @inheritdoc
     */
    public function canRename($path, $newName = null)
    {
        return $this->canOverwrite($path);
    }

    /**
     * @inheritdoc
     */
    public function canSetProperties($path)
    {
        return $this->checkLambda($path, self::LAMBDA_CAN_SET_PROPERTIES, func_get_args());
    }
}
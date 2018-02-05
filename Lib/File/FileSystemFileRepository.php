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

use Eulogix\Cool\Lib\File\Exception\ForbiddenException;
use Eulogix\Lib\File\Proxy\FileProxyInterface;
use Eulogix\Lib\File\Proxy\SimpleFileProxy;
use Eulogix\Lib\File\Proxy\SimpleFileProxyCollection;
use RecursiveIteratorIterator;

/***
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class FileSystemFileRepository extends BaseFileRepository {

    /**
     * @var string
     */
    protected $baseFolder = "";

    public function __construct($baseFolder) {
        if(!file_exists($baseFolder) || !is_dir($baseFolder))
            throw new \Exception("$baseFolder does not exist or is not a directory");
        $this->baseFolder = $baseFolder;
        $this->setPermissions( new FileSystemFileRepositoryPermissions($this));
    }

    /**
     * @return string
     */
    public function getBaseFolder()
    {
        return $this->baseFolder;
    }

    /**
     * @inheritdoc
     */
    public function getUid()
    {
        return 'FS:'.$this->baseFolder;
    }

    /**
     * @inheritdoc
     */
    public function exists($path)
    {
        $fsPath = $this->toFsPath($path);
        return file_exists($fsPath);
    }

    /**
     * @inheritdoc
     */
    public function get($path)
    {
        if(!$this->exists($path))
            throw new \Exception("$path does not exist");
        $fsPath = $this->toFsPath($path);
        return $this->getFileProxy($fsPath);
    }

    /**
     * @inheritdoc
     */
    public function delete($path)
    {
        if(!$this->exists($path))
            throw new \Exception("$path does not exist");
        $fsPath = $this->toFsPath($path);
        if(is_dir($fsPath))
            rmdir($fsPath);
        else unlink($fsPath);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function move($path, $target)
    {
        if(!$this->exists($path))
            throw new \Exception("$path does not exist");
        if(!$this->exists($target))
            throw new \Exception("$target does not exist");

        $source = $this->get($path);
        $fsPath = $this->toFsPath($path);
        $fsPathTarget = $this->toFsPath($target);
        rename($fsPath, $fsPathTarget.DIRECTORY_SEPARATOR.$source->getName());
    }

    /**
     * @inheritdoc
     */
    public function rename($path, $newName)
    {
        if(!$this->exists($path))
            throw new \Exception("$path does not exist");
        if(!$this->validFileName($newName))
            throw new ForbiddenException("$newName is not a valid file or folder name");

        $source = $this->get($path);
        $fsPath = $this->toFsPath($path);
        $fsPathTarget = $this->toFsPath($newId = $source->getParentId().DIRECTORY_SEPARATOR.$newName);
        rename($fsPath, $fsPathTarget);
        return $newId;
    }

    /**
     * @inheritdoc
     */
    public function getChildrenOf($path = null, $recursive = false, $includeHidden = false)
    {
        $ret = new SimpleFileProxyCollection([]);

        if($recursive) {
            $dirIterator = new \RecursiveDirectoryIterator( $this->toFsPath($path) );
            $iterator = new \RecursiveIteratorIterator($dirIterator);
        } else {
            $dirIterator = new \DirectoryIterator( $this->toFsPath($path) );
            $iterator = new \IteratorIterator($dirIterator);
        }

        $rootFs = $this->toFsPath($path);

        foreach ($iterator as $file) {
            /**
             * @var \SplFileInfo $file
             */
            if($file->getRealPath() != $rootFs && $file->getFilename() != '..' && ($recursive || $file->getFilename() != '.') ) {
                $ret->add($this->getFileProxy($file));
            }
        }

        return $ret;
    }

    /**
     * @inheritdoc
     */
    public function storeFileAt(FileProxyInterface $file, $path = null, $collisionStrategy = self::COLLISION_STRATEGY_OVERWRITE)
    {
        if(!$this->exists($path))
            throw new ForbiddenException("$path does not exist");
        if(!$this->validFileName($file->getName()))
            throw new ForbiddenException("{$file->getName()} is not a valid file or folder name");

        $newName = $file->getName();

        if($this->exists($path.DIRECTORY_SEPARATOR.$file->getName())) {
            switch($collisionStrategy) {
                case self::COLLISION_STRATEGY_SKIP: {
                    throw new \Exception("File already exists", -1);
                    break;
                }
                case self::COLLISION_STRATEGY_RENAME: {
                    $newName = $this->getNextUncollidedName($path, $file);
                    break;
                }
            }
        }

        $FQP = $this->getFQPath($path);
        $fsPath = $this->toFsPath($rpath = $FQP.DIRECTORY_SEPARATOR.$newName);
        $file->toFile($fsPath);
        return $this->get($rpath);
    }

    /**
     * @inheritdoc
     */
    public function createFolder($path, $folderName = null)
    {
        if(!$folderName && preg_match('%^(.*?)/([^/]*)$%sim', $path, $m)) {
            $path = $m[1];
            $folderName = $m[2];
        }

        if(!$this->exists($path))
            throw new ForbiddenException("$path does not exist");
        if(!$this->validFileName($folderName))
            throw new ForbiddenException("$folderName is not a valid file or folder name");

        $FQP = $this->getFQPath($path);
        if(!$this->exists($rpath = $FQP.DIRECTORY_SEPARATOR.$folderName)) {
            $fsPath = $this->toFsPath($rpath);
            mkdir($fsPath);
        }

        return $this->get($rpath);
    }

    /**
     * @inheritdoc
     */
    public function setFileProperties($path, array $properties, $merge = false)
    {
        throw new ForbiddenException();
    }

    /**
     * @inheritdoc
     */
    public function getFileProperties($path, $decodingsPrefix = null)
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAvailableFileProperties($path, $recursive = false)
    {
        return [];
    }

    /**
     * @param \SplFileInfo|string $f
     * @return SimpleFileProxy
     * @throws \Exception
     */
    private function getFileProxy($f) {

        if(!($f instanceof \SplFileInfo))
            $f = new \SplFileInfo($f);

        if($f->getFilename() == '.') {
            $filePath = substr($f->getPathname(), 0, strlen($f->getPathname())-2);
            $info = mb_pathinfo($filePath);
            $parentPath = $info['dirname'];
        } else {
            $filePath = $f->getPathname();
            $parentPath = $f->getPath();
        }

        $fp = SimpleFileProxy::fromFileSystem( $filePath );
        $fp->setId(str_replace($this->baseFolder, '', $filePath))
           ->setParentId(str_replace($this->baseFolder, '', $parentPath));
        return $fp;
    }

    /**
     * @param $path
     * @return string
     */
    private function toFsPath($path) {
        return $this->baseFolder.$this->getFQPath($path);
    }

    /**
     * @param string $name
     * @return bool
     */
    private function validFileName($name) {
        return preg_match('/^[ a-zA-Z_\-0-9\.()]+$/sim', $name) > 0;
    }
}
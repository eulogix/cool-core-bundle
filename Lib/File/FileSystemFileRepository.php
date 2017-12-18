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
use Eulogix\Lib\File\Proxy\FileProxyCollectionInterface;
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
    }

    /**
     * returns a unique identifier for the repository, used to differentiate caches..
     * @return string
     */
    public function getUid()
    {
        return 'FS:'.$this->baseFolder;
    }

    /**
     * @param string $path
     * @return boolean
     */
    public function exists($path)
    {
        $fsPath = $this->toFsPath($path);
        return file_exists($fsPath);
    }

    /**
     * @param string $path
     * @return FileProxyInterface
     * @throws \Exception
     */
    public function get($path)
    {
        if(!$this->exists($path))
            throw new \Exception("$path does not exist");
        $fsPath = $this->toFsPath($path);
        return $this->getFileProxy($fsPath);
    }

    /**
     * @param string $path
     * @return $this
     * @throws \Exception
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
     * @param string $path
     * @param string $target
     * @return string The new file path
     * @throws \Exception
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
     * @param string $path
     * @param string $newName
     * @return $this
     * @throws \Exception
     */
    public function rename($path, $newName)
    {
        if(!$this->exists($path))
            throw new \Exception("$path does not exist");
        if(!$this->validFileName($newName))
            throw new ForbiddenException("$newName is not a valid file or folder name");

        $source = $this->get($path);
        $fsPath = $this->toFsPath($path);
        $fsPathTarget = $this->toFsPath($source->getParentId().DIRECTORY_SEPARATOR.$newName);
        rename($fsPath, $fsPathTarget);
    }

    /**
     * @param string $path
     * @param bool $recursive
     * @param bool $includeHidden
     * @return FileProxyCollectionInterface
     * @throws \Exception
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
            if($file->getRealPath() != $rootFs && $file->getFilename() != '..') {
                $ret->add($this->getFileProxy($file));
            }
        }

        return $ret;
    }

    /**
     * @param FileProxyInterface $file
     * @param string $path
     * @param string $collisionStrategy overwrite|skip|append
     * @return FileProxyInterface a fileProxy representing the inserted file
     * @throws ForbiddenException
     */
    public function storeFileAt(FileProxyInterface $file, $path = null, $collisionStrategy = 'overwrite')
    {
        if(!$this->exists($path))
            throw new ForbiddenException("$path does not exist");
        if(!$this->validFileName($file->getName()))
            throw new ForbiddenException("{$file->getName()} is not a valid file or folder name");

        $fsPath = $this->toFsPath($rpath = $path.DIRECTORY_SEPARATOR.$file->getName());
        $file->toFile($fsPath);
        return $this->get($rpath);
    }

    /**
     * @param string $path
     * @param string $folderName
     * @return $this
     * @throws ForbiddenException
     */
    public function createFolder($path, $folderName)
    {
        if(!$this->exists($path))
            throw new ForbiddenException("$path does not exist");
        if(!$this->validFileName($folderName))
            throw new ForbiddenException("$folderName is not a valid file or folder name");

        if(!$this->exists($rpath = $path.DIRECTORY_SEPARATOR.$folderName)) {
            $fsPath = $this->toFsPath($rpath);
            mkdir($fsPath);
        }

        return $this;
    }

    /**
     * @param string $path
     * @param array $properties
     * @param bool $merge
     * @return $this
     * @throws ForbiddenException
     */
    public function setFileProperties($path, array $properties, $merge = false)
    {
        throw new ForbiddenException();
    }

    /**
     * @param string $path
     * @param string $decodingsPrefix if set, includes all the decodifications of the properties prefixing them
     * @return array
     */
    public function getFileProperties($path, $decodingsPrefix = null)
    {
        return [];
    }

    /**
     * @param string $path
     * @param bool $recursive
     * @return FileProperty[]
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
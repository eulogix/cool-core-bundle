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


use Eulogix\Cool\Lib\File\Action\FileAction;
use Eulogix\Cool\Lib\Widget\Menu;
use Eulogix\Lib\File\Proxy\FileProxyCollectionInterface;
use Eulogix\Lib\File\Proxy\FileProxyInterface;
use Eulogix\Lib\File\Proxy\SimpleFileProxyCollection;
use Symfony\Component\HttpFoundation\ParameterBag;

abstract class BaseFileRepository implements FileRepositoryInterface
{
    /**
     * general purpose parameters bag, can be used to further configure a file repository with stuff that comes from request
     *
     * @var ParameterBag
     */
    private $parameters;

    /**
     * @var BaseFileRepositoryPermissions
     */
    protected $permissions, $userPermissions;

    /**
     * @var string
     */
    protected $workingDir = '/';

    /**
     * @var FileAction[]
     */
    protected $fileActions = [];

    /**
     * @inheritdoc
     */
    public function getParameters() {
        if(!$this->parameters)
            $this->parameters = new ParameterBag();
        return $this->parameters;
    }

    /**
     * @inheritdoc
     */
    public function setParameters(array $parameters = []) {
        $this->getParameters()->replace($parameters);

        if( isset($parameters['workingDir']) )
            $this->setWorkingDir($parameters['workingDir']);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMergedFileProperties($paths, $decodingsPrefix = null)
    {
        $ret = [];
        foreach($paths as $path) {
            $properties = $this->getFileProperties($path, $decodingsPrefix);
            foreach($properties as $k=>$v) {
                if(!isset($ret[$k]))
                    $ret[$k] = $v;
                else if($ret[$k] != $v)
                    $ret[$k] = null;
            }
        }
        return $ret;
    }

    /**
     * @inheritdoc
     */
    public function search($startPath = null, array $query = [], $recursive = true, $offset = 0, $limit = 100)
    {
        $ret = new SimpleFileProxyCollection([]);
        $this->searchRecursive($startPath, $query, $ret);
        return $ret;
    }

    /**
     * @inheritdoc
     */
    public function parseExtendedQuery($eq) {
        $ret = ['properties'=>[]];
        if(preg_match('%^([^/]*)%im', $eq, $matches))
            $ret['name'] = trim($matches[1]);
        if(preg_match_all('%/([^=]+?)=["\']?([^\'"/]+)(["\']?| ?/|$)%im', $eq, $matches, PREG_SET_ORDER)) {
            foreach($matches as $m)
                $ret['properties'][trim($m[1])] = trim($m[2]);
        }
        return $ret;
    }

    /**
     * clears the content of a folder, deleting all the files it contains
     * @param string $path
     * @param callable $callBack
     * @throws \Exception
     */
    public function wipeFolder($path, callable $callBack = null) {
        $files = $this->getChildrenOf($path);

        foreach($files->getIterator() as $file) {
            /**
             * @var FileProxyInterface $file
             */
            if(!$file->isDirectory()) {
                $this->delete($file->getId());
                if($callBack)
                    call_user_func($callBack, $file);
            }
        }
    }

    /**
     * @param $startPath
     * @param array $query
     * @param FileProxyCollectionInterface $results
     * @return FileProxyCollectionInterface
     */
    private function searchRecursive($startPath, array $query, FileProxyCollectionInterface &$results) {
        $children = $this->getChildrenOf($startPath, false, false)->fetch();
        $name = @$query[self::QUERY_NAME];
        $eq = ($extendedQuery = @$query[self::QUERY_EXTENDED]) ? $this->parseExtendedQuery($extendedQuery) : null;
        if($eq) {
            $name = $eq['name'];
        }

        foreach($children as $child) {
            /**
             * @var FileProxyInterface $child
             */
            $add = !$child->isDirectory();

            if($add && $eq) {
                if(count($eq['properties'])>0)
                    foreach($eq['properties'] as $propName=>$propValue) {
                        $add = $add && ($child->getProperty($propName)==$propValue);
                    }
            }

            if($name) {
                $add = $add && preg_match("/{$name}/sim", $child->getName());
            }

            if($add)
                $results->add($child);

            if($child->isDirectory())
                $this->searchRecursive($child->getId(), $query, $results);
        }
        return $results;
    }

    /**
     * @return BaseFileRepositoryPermissions
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @param BaseFileRepositoryPermissions $permissions
     * @return BaseFileRepository
     */
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;
        return $this;
    }

    /**
     * @return BaseFileRepositoryPermissions
     */
    public function getUserPermissions()
    {
        return $this->userPermissions ?? $this->permissions;
    }

    /**
     * @param BaseFileRepositoryPermissions $userPermissions
     * @return BaseFileRepository
     */
    public function setUserPermissions($userPermissions)
    {
        $this->userPermissions = $userPermissions;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setWorkingDir($folderId) {
        $this->workingDir = $this->cleanPath($folderId);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getWorkingDir() {
        return $this->workingDir;
    }

    /**
     * @inheritdoc
     */
    public function getFQPath($path)
    {
        $path = $path ? $path : $this->getWorkingDir(); //null means root (/)

        if($path[0] == '/')
            return $this->cleanPath($path); //$p is an absolute path

        return $this->cleanPath($this->getWorkingDir().'/'.$path);
    }

    /**
     * @inheritdoc
     */
    public function getContextualMenuFor($path) {

        $file = $this->get($path);
        $menu = new Menu();

        foreach($this->getFileActions() as $fileAction) {
            $fileAction->setFileRepository($this);
            if($fileAction->appliesTo($file)) {
                $fileAction->populateContextualMenu($menu);
            }
        }

        return $menu;
    }

    /**
     * @inheritdoc
     */
    public function getFileActions() {
        return $this->fileActions;
    }

    /**
     * @inheritdoc
     */
    public function registerFileAction(FileAction $action) {
        $this->fileActions[] = $action;
    }

    /**
     * @param string $path
     * @return string
     */
    private function cleanPath($path) {
        $ret = str_replace('//', '/', preg_replace('%^[/]*(.+?)[/]*$%im', '/$1', $path));
        return $ret == '/' ? null : $ret;
    }

    /**
     * returns the next available name for a file, when it already exists
     *
     * @param string $path
     * @param FileProxyInterface $file
     * @return string
     */
    protected function getNextUncollidedName($path, FileProxyInterface $file) {
        $i = 0;
        do {
            $newName = $file->getBaseName().($i?" ($i).":".").$file->getCompleteExtension();
            $i++;
        } while($this->exists($path.DIRECTORY_SEPARATOR.$newName));
        return $newName;
    }

    /**
     * @inheritdoc
     */
    public function getContextFor($path) : string {
        return $this->getUid();
    }
}
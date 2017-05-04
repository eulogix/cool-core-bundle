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

        if(@$parameters['workingDir'])
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
}
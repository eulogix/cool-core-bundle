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

class SimpleFileProxyCollection implements FileProxyCollectionInterface
{
    /**
     * @var FileProxyInterface[]
     */
    private $fpArray = [];

    /**
     * @param FileProxyInterface[] $fpArray
     */
    public function __construct($fpArray) {
        $this->fpArray = $fpArray;
    }

    /**
     * @return integer
     */
    public function count() {
        return count($this->fpArray);
    }

    /**
     * @param integer $start
     * @param integer $end
     * @return FileProxyInterface[]
     */
    public function fetch($start=null, $end=null) {
        return $this->fpArray;
    }

    /**
     * @param FileProxyInterface $f
     * @return FileProxyCollectionInterface|void
     */
    public function add(FileProxyInterface $f) {
        $this->fpArray[] = $f;
        return $this;
    }

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->fpArray);
    }

    /**
     * merges the collection with another
     * @param FileProxyCollectionInterface $otherCollection
     * @return self
     */
    public function mergeWith(FileProxyCollectionInterface $otherCollection)
    {
        foreach($otherCollection->getIterator() as $fileProxy) {
            /** @var FileProxyInterface $fileProxy */
            $this->add($fileProxy);
        }
    }

    /**
     * filters the content by a lambda that accept as argument a fileProxyInterface
     * @param callable $lambda
     * @return self
     */
    public function filter(callable $lambda)
    {
        $filteredArray = [];
        foreach($this->fpArray as $fp) {
            if(call_user_func($lambda, $fp))
                $filteredArray[] = $fp;
        }
        $this->fpArray = $filteredArray;
        return $this;
    }
}
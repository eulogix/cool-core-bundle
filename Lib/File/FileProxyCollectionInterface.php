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

interface FileProxyCollectionInterface
{
    /**
     * @return integer
     */
    public function count();

    /**
     * @param integer $start
     * @param integer $end
     * @return FileProxyInterface[]
     */
    public function fetch($start=null, $end=null);

    /**
     * @return \Iterator
     */
    public function getIterator();

    /**
     * merges the collection with another
     * @param FileProxyCollectionInterface $otherCollection
     * @return self
     */
    public function mergeWith(FileProxyCollectionInterface $otherCollection);

    /**
     * filters the content by a lambda that accept as argument a fileProxyInterface
     * @param callable $lambda
     * @return self
     */
    public function filter(callable $lambda);

    /**
     * adds a file to the collection
     * @param FileProxyInterface $f
     * @return FileProxyCollectionInterface
     */
    public function add(FileProxyInterface $f);

}
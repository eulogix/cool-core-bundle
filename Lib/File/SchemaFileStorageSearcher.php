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

use Eulogix\Lib\File\Proxy\SimpleFileProxyCollection;
use Eulogix\Lib\Util\AbstractPagedIterator;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class SchemaFileStorageSearcher extends AbstractPagedIterator
{
    protected $totalSize = 0;

    /**
     * @var string
     */
    protected  $table, $pk, $category, $name;

    /**
     * @var SchemaFileStorage
     */
    protected $storage;

    /**
     * @var bool
     */
    protected $fetchContent = false;

    /**
     * @param SchemaFileStorage $sf
     * @param string $table
     * @param string $pk
     * @param string $category
     * @param $name
     * @param bool $fetchContent
     */
    public function __construct(SchemaFileStorage $sf, $table, $pk, $category, $name, $fetchContent = false)
    {
        $this->storage = $sf;
        $this->table = $table;
        $this->pk = $pk;
        $this->category = $category;
        $this->name = $name;

        $this->fetchContent = $fetchContent;

        // this will make sure totalSize is set before we try and access the data
        $this->cachePage(0);
    }

    /**
     * @inheritdoc
     */
    public function getTotalSize()
    {
        return $this->totalSize;
    }

    /**
     * @inheritdoc
     */
    public function getPageSize()
    {
        return 100;
    }

    /**
     * @inheritdoc
     */
    public function doGetPage($pageNumber)
    {
        $result = $this->storage->rawQuery($this->table, $this->pk, $this->category, $this->name, $pageNumber, $this->getPageSize(), $this->fetchContent);
        $this->totalSize = $result['total_count'];
        return $result['items'];
    }

    /**
     * @return SimpleFileProxyCollection
     */
    public function getAllAsFileProxyCollection() {
        $c = new SimpleFileProxyCollection([]);
        foreach($this as $fileRecord) {
            $fp = $this->storage->compileFileProxy($fileRecord, true);
            $c->add($fp);
        }

        return $c;
    }
}
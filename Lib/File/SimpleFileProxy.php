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

class SimpleFileProxy extends BaseFileProxy
{
    /**
     * @param string $filePath
     * @return SimpleFileProxy
     * @throws \Exception
     */
    public static function fromFileSystem($filePath) {
        if(!file_exists($filePath))
            throw new \Exception("file does not exist");
        $pi = mb_pathinfo($filePath);
        $f = new self();
        $f->setName($pi['basename']);
        $f->setContent( file_get_contents($filePath) );
        $f->setCreationDate( new \DateTime(filectime($filePath)) );
        $f->setLastModificationDate( new \DateTime(filemtime($filePath)) );
        $f->setIsDirectory(is_dir($filePath));
        return $f;
    }

    /**
     * @param string $httpUrl
     * @return SimpleFileProxy
     */
    public static function fromHTTPRemoteFile($httpUrl) {
        $path = parse_url($httpUrl, PHP_URL_PATH);
        $arr = explode("/", $path);
        $pi = mb_pathinfo(array_pop($arr));

        $f = new self();
        $f->setName($pi['basename']);
        $f->setContent( file_get_contents($httpUrl) );
        $f->setIsDirectory(false);
        return $f;
    }

    public static function fromValues($name, $id, $parentId, $isDir = false) {
        $f = new self();
        $f  ->setIsDirectory($isDir)
            ->setName( $name )
            ->setId( $id )
            ->setParentId( $parentId );
        return $f;
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        return [
            'id' => $this->getId(),
            'parentId' => $this->getParentId(),
            'name' => $this->getName(),
            'extension' => $this->getExtension(),
            'hash' => $this->getHash(),
            'size' => $this->getSize(),
            'lastModificationDate' => $this->getLastModificationDate(),
            'properties' => $this->getProperties(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function isEmpty()
    {
        return sizeof($this->getContent()) == 0;
    }
}
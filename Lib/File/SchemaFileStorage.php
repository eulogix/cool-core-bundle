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

use Eulogix\Cool\Lib\Database\Schema;
use Eulogix\Lib\File\Proxy\FileProxyInterface;
use Eulogix\Lib\File\Proxy\SimpleFileProxy;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class SchemaFileStorage {

    /**
     * @var Schema
     */
    private $schema;

    /**
     * @var string
     */
    private $storagePath, $tableName;



    public function __construct(Schema $schema, $storagePath) {
        $this->schema = $schema;
        $this->tableName = $schema->getFilesIndexTableName();
        $this->storagePath = $storagePath;
    }

    /**
     * @param string $table
     * @param string $pk
     * @param string $category
     * @param string $name
     * @param bool $fetchContent
     * @return SchemaFileStorageSearcher
     */
    public function query($table=null, $pk=null, $category=null, $name=null, $fetchContent = false)
    {
        return new SchemaFileStorageSearcher($this, $table, $pk, $category, $name, $fetchContent);
    }

    /**
     * @param string $table
     * @param string $pk
     * @param string $category
     * @param string $name
     * @param int $page
     * @param int $pageSize
     * @param bool $fetchContent
     * @return array
     */
    public function rawQuery($table=null, $pk=null, $category=null, $name=null, $page = 0, $pageSize = 100, $fetchContent = false)
    {
        $repo = CoolTableFileRepository::fromSchema($this->schema);

        $parameters = [];
        $sql = "select * from {$this->tableName} WHERE TRUE";

        if($table) {
            $sql.= " AND (source_table=:table)";
            $parameters[':table'] = $table;
        }

        if($pk) {
            $sql.= " AND (source_table_id=:pk)";
            $parameters[':pk'] = $pk;
        }

        if($category !== null) {
            $sql.=" AND (category=:category)";
            $parameters[':category'] = $category;
        }// else $sql.=" AND category=''";

        if($name) {
            if(preg_match("/%/sim",$name))
                $sql.=" AND (file_name SIMILAR TO :name)";
            else $sql.=" AND (file_name=:name)";
            $parameters[':name'] = $name;
        }

        $sql.=" ORDER BY source_table ASC, category ASC, file_name ASC";

        $totalResults = $this->schema->fetch("SELECT COUNT(file_id) FROM ($sql) as tmp", $parameters);
        $offset = $page * $pageSize;
        $data = $this->schema->fetchArray($sql." OFFSET {$offset} LIMIT {$pageSize}", $parameters);

        //used in the REST api
        foreach($data as &$file) {
            $file[ 'path' ] = $repo->buildPath(
                $file[ 'source_table' ],
                $file[ 'source_table_id' ],
                $file[ 'category' ],
                $file[ 'file_id' ]
            );
            if($fetchContent) {
                $storedFile = $this->getFileContent($file['checksum_sha1']);
                $file['content'] = file_exists($storedFile) ? @file_get_contents($storedFile) : null;
            }
        }

        return [
            'total_count' => $totalResults,
            'page' => $page,
            'page_size' => $pageSize,
            'page_count' => ceil($totalResults/$pageSize),
            'items' => $data
        ];
    }

    /**
     * @param string $id
     * @return FileProxyInterface|bool
     */
    public function getById($id)
    {
        $data = $this->schema->fetch("select * from {$this->tableName} a
        where a.file_id=:file_id",[':file_id'=>$id]);
        if($data) {
            return $this->compileFileProxy($data);
        }
        return false;
    }

    /**
     * @param string $id
     */
    public function removeById($id)
    {
        $this->schema->query("DELETE FROM {$this->tableName} WHERE file_id=:file_id", [':file_id'=>$id]);
    }

    /**
     * @param int $id
     * @param array $properties
     * @param bool $merge
     */
    public function setProperties($id, array $properties, $merge = false)
    {
        $finalProperties = $merge ? array_merge($this->getProperties($id), $properties) : $properties;
        $jsonProperties = json_encode($finalProperties);
        $this->schema->query("UPDATE {$this->tableName} SET properties=:props WHERE file_id=:file_id", [':file_id'=>$id, ':props'=>$jsonProperties]);
    }

    /**
     * @param int $id
     * @return array
     */
    public function getProperties($id)
    {
        $jsonString = $this->schema->fetch("SELECT properties FROM {$this->tableName} WHERE file_id=:file_id", [':file_id'=>$id]);
        $ret = json_decode($jsonString, true);
        return $ret ? $ret : [];
    }

    /**
     * @param int $id
     * @return boolean
     */
    public function exists($id)
    {
        return $this->schema->fetch("SELECT file_id FROM {$this->tableName} WHERE file_id=:file_id", [':file_id'=>$id]) > 0;
    }

    /**
     * @param FileProxyInterface $file
     * @param $table
     * @param $pk
     * @param null $category
     * @return integer
     */
    public function store(FileProxyInterface $file, $table, $pk, $category=null)
    {
        $db = $this->schema;

        $hash = $file->getHash();
        $schemaName = $this->schema->getName();

        $id = false;
        $previous = $this->query($table, $pk, $category, $file->getName());
        if($previous->count()==1)
            $id = $previous->current()["file_id"];
        elseif($category) {
            $tableMap = $this->schema->getDictionary()->getPropelTableMap($table);

            if(!$tableMap)
                $tableMap = $this->schema->getDictionary()->getPropelTableMap($schemaName.'.'.$table);

            $cat = $tableMap->getFileCategory($category);
            if($cat->getMaxCount()==1) {
                $previous = $this->query($table, $pk, $category);
                if($previous->count()==1)
                    $id = $previous->current()["file_id"];
            }
        }

        if($id) {
            $db->query("UPDATE {$this->tableName} SET checksum_sha1=:sha1, file_name=:fname WHERE file_id=:file_id",[':file_id'=>$id, ':sha1'=>$hash,
                    ':fname'=>$file->getName()]);
            $this->storeContent($hash, $file);
            return $id;
        } else {
            $newId = $db->fetch(
                "INSERT INTO {$this->tableName} ( file_id, file_name, file_size, checksum_sha1, category, source_table, source_table_id, upload_date, last_modification_date, uploaded_by_user)
                            VALUES (DEFAULT, :fname, :size, :sha1::text, :category, :tbl_name, :pk, now(), :lastmdate, core.get_logged_user()) RETURNING file_id",
                [
                    ':sha1'=>$hash,
                    ':fname'=>$file->getName(),
                    ':size'=>$file->getSize(),
                    ':pk'=>$pk,
                    ':tbl_name'=>$table,
                    ':category'=>$category,
                    ':lastmdate'=>$file->getLastModificationDate()->format('c')
                ]);
            $this->storeContent($hash, $file);
            return $newId;
        }
    }

    /**
     * @param string $id
     * @param string $table
     * @param string $pk
     * @param string $category
     */
    public function move($id, $table, $pk, $category=null) {
        $db = $this->schema;

        $db->query("UPDATE {$this->tableName} SET source_table=:table, source_table_id=:pk, category=:category WHERE file_id=:file_id",
            [
                ':file_id'=>$id,
                ':table'=>$table,
                ':pk'=>$pk,
                ':category'=>$category
            ]);
    }

    /**
     * @param string $id
     * @param string $newName
     */
    public function rename($id, $newName) {
        $db = $this->schema;

        $db->query("UPDATE {$this->tableName} SET file_name=:newName WHERE file_id=:file_id",
            [
                ':file_id'=>$id,
                ':newName'=>$newName
            ]);
    }

    /**
     * @param string $sha1
     * @param FileProxyInterface $file
     * @return bool
     */
    private function storeContent($sha1, FileProxyInterface $file) {
        $path = $this->storagePath.'/'.substr($sha1,0,2).'/'.substr($sha1,2,2).'/';
        @mkdir($path, 0777, true);
        $file->toFile($path.$sha1);
        return true;
    }

    /**
     * @param string $sha1
     * @param SimpleFileProxy $file
     */
    private function setStoredContentToFileProxy($sha1, SimpleFileProxy $file) {
        $storedFile = $this->getFileContent($sha1);
        if(file_exists($storedFile))
            $file->setContentFile($storedFile, $sha1);
    }

    /**
     * @param string $sha1
     * @return string
     */
    private function getFileContent($sha1) {
        $path = $this->storagePath.'/'.substr($sha1,0,2).'/'.substr($sha1,2,2).'/';
        $storedFile = $path.$sha1;
        return $storedFile;
    }

    /**
     * @param $data
     * @param bool $lazyContent
     * @return FileProxyInterface
     */
    public function compileFileProxy($data, $lazyContent=false)
    {
        $f = new SimpleFileProxy();

        $this->setStoredContentToFileProxy($data[ 'checksum_sha1' ], $f);

        $f->setId($data[ 'file_id' ])
          ->setName($data[ 'file_name' ])
          ->setSize($data[ 'file_size' ])
          ->setCreationDate(new \DateTime(@$data['upload_date']))
          ->setLastModificationDate(new \DateTime(@$data['last_modification_date']))
          ->setProperties(json_decode(@$data['properties'], true));

        return $f;
    }

}
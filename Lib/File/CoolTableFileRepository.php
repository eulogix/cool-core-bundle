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

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Database\Propel\CoolPropelObject;
use Eulogix\Cool\Lib\Database\Schema;
use Eulogix\Cool\Lib\File\Exception\ForbiddenException;
use Eulogix\Cool\Lib\Translation\Translator;
use Eulogix\Cool\Lib\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * this repository mimics a hierarchical file repository
 * ids are composed as follows
 *
 * /<schemaName>/<tableName>/<recordPk>[/category|optional]/numericFileId
 *
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class CoolTableFileRepository extends BaseFileRepository {

    /**
     * @var string
     */
    private $workingDir = '/';

    /**
     * @var Schema
     */
    private $schema;

    /**
     * @var string
     */
    private $sourceTable, $pk, $pkField;

    /**
     * @var CoolPropelObject
     */
    private $propelObj;

    /**
     * @var SchemaFileStorage
     */
    private $storage;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @inheritdoc
     */
    public function getUid() {
        return $this->schema->getCurrentSchema();
    }

    /**
     * @param CoolPropelObject $obj
     * @return CoolTableFileRepository
     */
    public static function fromCoolPropelObject($obj) {
        //strip the schema qualifier, this is done only to have cleaner data stored in the files table
        $strippedTableName = $obj->getCoolTableMap()->getCoolRawName();
        $repo = new self($obj->getCoolDatabase(), $strippedTableName, $obj);
        return $repo;
    }

    /**
     * @param Schema $schema
     * @param $tableName
     * @return CoolTableFileRepository
     */
    public static function fromSchemaAndTableName(Schema $schema, $tableName) {
        $repo = new self($schema, $tableName);
        return $repo;
    }

    /**
     * @param Schema $schema
     * @return CoolTableFileRepository
     */
    public static function fromSchema(Schema $schema) {
        $repo = new self($schema);
        return $repo;
    }

    /**
     * @param RequestStack $requestStack
     * @return CoolTableFileRepository
     * @throws \Exception
     * @internal param Request $request
     */
    public static function fromRequest(RequestStack $requestStack) {
        $p = $requestStack->getCurrentRequest()->query->all();

        if(isset($p['schema'])) {
            $schema = Cool::getInstance()->getSchema($p['schema']);
            if(isset($p['actualSchema']))
                $schema->setCurrentSchema($p['actualSchema']);
            if(isset($p['table'])) {
                if(isset($p['pk']))
                    return self::fromCoolPropelObject($schema->getPropelObject($p['table'], $p['pk']));
                else return self::fromSchemaAndTableName($schema, $p['table']);
            } else return self::fromSchema($schema);
        } else if($filePath = @$p['filePath']) {
            // allows things like
            // {{ path('frepoGetPreviewImage', {'width': 100, 'repositoryId':'schema', 'filePath': '/core/account/cat_AVATAR/1' }) }}
            if( $parsedPath = self::parseFQPathId($filePath) ) {
                $schema = Cool::getInstance()->getSchema($parsedPath['schema']);
                return self::fromCoolPropelObject($schema->getPropelObject($parsedPath['table'], $parsedPath['pk']));
            }
        }
        //initialize an empty repository that will derive its schema and table from paths
        else return new self();
        throw new \Exception("Bad request parameters");
    }

    /**
     * @param Schema $schema
     * @param string $tableName
     * @param CoolPropelObject $propelObj
     */
    private function __construct(Schema $schema=null, $tableName=null, CoolPropelObject $propelObj=null) {
        if($schema)
            $this->setSchemaAndTable($schema->getName(), $tableName);
        if($propelObj){
            $this->propelObj = $propelObj;
            $this->pk = $propelObj->getPrimaryKeyAsString();
            $this->pkField = $propelObj->getCoolTableMap()->getPkFields()[0];
            $this->setWorkingDir( $this->buildPath($tableName, $this->pk) );
        }
        $this->setPermissions( new CoolTableFileRepositoryPermissions($this) );
        $set = new FileRepositoryPermissionsSet($this);
        $set->addPermissions($this->getPermissions());
        $set->addPermissions(new CoolTableFileRepositoryUserPermissions($this));
        $this->setUserPermissions( $set );
    }

    /**
     * @param string $schemaName
     * @param string $tableName
     */
    protected function setSchemaAndTable($schemaName, $tableName=null) {
        if(!$this->schema || $this->schema->getName() != $schemaName) {
            $this->schema = Cool::getInstance()->getSchema($schemaName);
            $this->storage = Cool::getInstance()->getFactory()->getSchemaFileStorage($this->schema);
        }
        if($tableName != $this->sourceTable) {
            $this->sourceTable = $tableName;
            $this->translator = Translator::fromDomain('COOL_FREPO_'.$this->schema->getName().'@'.$this->sourceTable);
        }
    }

    /**
     * @inheritdoc
     */
    public function setWorkingDir($folderId) {
        $this->workingDir = $folderId;
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
    public function storeFileAt(FileProxyInterface $file, $path=null, $collisionStrategy='overwrite') {
        if($this->getPermissions()->canCreateFileIn($path)) {

            $p = $this->parsePathId($path);

            if($p['category'] || $p['pk']) {
                $newFileName = $file->getName();
                $baseName = $file->getBaseName();
                $ext = $file->getExtension();

                if($collisionStrategy != 'overwrite') {
                    $i = 1;
                    do {
                        $existingFilesCount = $this->storage->query($p['table'], $p['pk'], $p['category'], $newFileName)->count();

                        if($existingFilesCount > 0) {
                            if($collisionStrategy == 'skip')
                                throw new \Exception("File already exists", -1);
                            $newFileName = $baseName.'_('.$i++.')'.($ext ? ".$ext" : "");
                            $file->setName($newFileName);
                        }

                    } while($existingFilesCount > 0);
                }

                $storageId = $this->storage->store($file, $p['table'], $p['pk'], $p['category']);

                return $this->get( $this->buildPath($p['table'], $p['pk'], $p['category'], $storageId) );
            }

        } else throw new ForbiddenException();
    }

    /**
     * @inheritdoc
     */
    public function getChildrenOf($path, $recursive = false, $includeHidden = false) {
        $p = $this->parsePathId($path);
        $this->configFromPath($path);

        $files = [];

        if($p['file_id'])
            return new SimpleFileProxyCollection($files);

        if($p['category'] || $p['pk']) {

            $collection = $this->storage->query($p['table'], $p['pk'], $p['category'])->getAllAsFileProxyCollection();

            $i = $collection->getIterator();
            foreach($i as $file)
                if($file instanceof FileProxyInterface) {
                    /**
                     * @var $file SimpleFileProxy
                     */
                    $file->setId( $this->buildPath($p['table'], $p['pk'], $p['category'], $file->getId()) );
                    $file->setParentId( $this->buildPath($p['table'], $p['pk'], $p['category']) );
                }

            if(!$p['category']) {

                //find the categories
                $tableMap = $this->schema->getDictionary()->getPropelTableMap($p['table']);

                $fileCategories = $tableMap->getFileCategories();
                foreach($fileCategories as $cat) {
                    if($includeHidden || !$cat->isHidden()) {
                        $f = new SimpleFileProxy();
                        $f  ->setIsDirectory(true)
                            ->setName( $this->translator->trans( 'cat_'.$cat->getName() ) )
                            ->setId( $this->buildPath($p['table'], $p['pk'], $cat->getName()) )
                            ->setParentId( $this->buildPath($p['table'], $p['pk']) );
                        $collection->add($f);

                        if($recursive)
                            $collection->mergeWith( $this->getChildrenOf($f->getId(), true, $includeHidden) );
                   }
                }
            }

            return($collection);
        }

        //TODO manage higher nest levels
        return new SimpleFileProxyCollection($files);
    }

    /**
     * @inheritdoc
     */
    public function getFQPath($path)
    {
        $path = $path ? $path : $this->getWorkingDir(); //null means root (/)

        $p = str_replace('//','/',$path);
        if($p[0]=='/')
            return $p; //$p is an absolute path
        return $this->getWorkingDir().'/'.$p;
    }

    /**
     * @inheritdoc
     */
    public function exists($path)
    {
        $p = $this->parsePathId($path);

        if($p['file_id']) {
            return $this->storage->exists($p['file_id']);
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function get($path)
    {
        $p = $this->parsePathId($path);

        if($p['file_id']) {
            //$path like /bookstore/book/3/cat_TEMPORARY/1 OR /bookstore/book/3/2
            if($f = $this->storage->getById($p['file_id'])) {
                $f->setId($path)
                  ->setParentId( $this->buildPath($p['table'], $p['pk'], $p['category']) );
                return $f;
            } else return null;
        }

        if($p['pk']) {
            $f = new SimpleFileProxy();
            $f  ->setIsDirectory(true);

            if($p['category']) {
                //$path like /bookstore/book/3/cat_TEMPORARY
                $f->setName($p['category'])
                  ->setId($this->buildPath($p[ 'table' ], $p[ 'pk' ], $p['category']))
                  ->setParentId( $this->buildPath($p['table'], $p['pk']) );
            } else {
                //$path like /bookstore/book/3/
                $f->setName($p[ 'table' ] . $p[ 'pk' ])
                  ->setId( $this->buildPath($p[ 'table' ], $p[ 'pk' ]) )
                  ->setParentId( $this->buildPath($p['table']) );
            }
            return $f;
        }
    }

    /**
     * @inheritdoc
     */
    public function delete($path)
    {
        if($this->getPermissions()->canDelete($path)) {
            $p = $this->parsePathId($path);

            if ($p[ 'file_id' ]) {
                $this->storage->removeById($p[ 'file_id' ]);

                return;
            }
        } else throw new ForbiddenException();
    }

    /**
     * @inheritdoc
     */
    public function getFileProperties($path, $decodingsPrefix=null)
    {
        $p = $this->parsePathId($path);

        if($p['file_id']) {
            $ret = $this->storage->getProperties($p['file_id']);
            $availableProperties = $this->getAvailableFileProperties($path);
            if($decodingsPrefix) {
                $decodings = [];
                foreach($ret as $prop => $value) {
                    foreach($availableProperties as $definedProperty) {
                        if($definedProperty->getName() == $prop) {
                            if($vm = $definedProperty->getValueMap()) {
                                $decodings[$decodingsPrefix.$prop] = $vm->mapValue($value);
                            } else $decodings[$decodingsPrefix.$prop] = $value;
                        }
                    }
                }
                return array_merge($ret, $decodings);
            }
            return $ret;
        }

        return [];
    }

    /**
     * @inheritdoc
     */
    public function setFileProperties($path, array $properties, $merge=false) {
        if($this->getPermissions()->canSetProperties($path)) {

            $p = $this->parsePathId($path);

            if($p['file_id']) {
                //$path like /bookstore/book/3/cat_TEMPORARY/1 OR /bookstore/book/3/2
                $this->storage->setProperties($p['file_id'], $properties, $merge);
            }
            return $this;

        } else throw new ForbiddenException();
    }

    /**
     * @inheritdoc
     */
    public function move($path, $target)
    {
        if($this->getPermissions()->canMove($path, $target)) {
            $p = $this->parsePathId($path);
            $tp = $this->parsePathId($target);
            $this->storage->move($p['file_id'], $tp['table'], $tp['pk'], @$tp['category']);
            return $this->buildPath($tp['table'], $tp['pk'], @$tp['category'], $p['file_id']);
        } else throw new ForbiddenException();
    }

    /**
     * @inheritdoc
     */
    public function rename($path, $newName)
    {
        if($this->getPermissions()->canRename($path, $newName)) {
            $p = $this->parsePathId($path);
            $this->storage->rename($p[ 'file_id' ], $newName);
            return $this;
        } else throw new ForbiddenException();
    }

    /**
     * @inheritdoc
     */
    public function createFolder($path, $folderName) {
        throw new ForbiddenException();
    }

    /**
     * @param $path
     * @return array|bool
     */
    public function parsePathId($path) {
        $p = $this->getFQPath($path);
        return self::parseFQPathId($p);
    }

    /**
     * @param string $table
     * @param string $pk
     * @param string $category
     * @param string $file_id
     * @return string
     */
    public function buildPath($table=null, $pk=null, $category=null, $file_id=null) {
        $p = '/'.$this->schema->getName();
        $p.= $table ? '/'.$table : '';
        $p.= $pk !== null ? '/'.$pk : '';
        $p.= $category ? '/cat_'.$category : '';
        $p.= $file_id ? '/'.$file_id : '';

        return $p;
    }

    /**
     * @param string $p
     * @return array|bool
     */
    private static function parseFQPathId($p) {
        if(preg_match('%^/(\w*)/*([^/]*)/*(\w*)/*(cat_(\w*)|)/*([0-9]*)$%im', $p, $m))
            return [
                'schema' => $m[1],
                'table' => $m[2],
                'pk' => $m[3],
                'category' => $m[5],
                'file_id' => $m[6]
            ];
        return false;
    }

    /**
     * derives the schema and table from path
     * @param $path
     */
    protected function configFromPath($path)
    {
        $pp = $this->parseFQPathId( $this->getFQPath($path) );
        $this->setSchemaAndTable(@$pp['schema'], @$pp['table']);
    }

    /**
     * @inheritdoc
     */
    public function getAvailableFileProperties($path, $recursive=false)
    {
        $p = $this->parsePathId($path);
        return Cool::getInstance()->getCoreSchema()->getAvailableFileProperties(
            $p['schema'],
            Cool::getInstance()->getSchema($p['schema'])->getCurrentSchema(),
            $p['table'],
            $p['category'],
            $recursive);
    }

    /**
     * @return Schema
     */
    public function getSchema()
    {
        return $this->schema;
    }

}
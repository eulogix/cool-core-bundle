<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\DataSource;

use Eulogix\Cool\Lib\File\FileRepositoryFactory;
use Eulogix\Cool\Lib\File\FileRepositoryInterface;
use Eulogix\Cool\Lib\File\FileRepositoryPreviewProvider;
use Eulogix\Cool\Lib\File\FileUtil;
use Eulogix\Lib\File\Proxy\FileProxyInterface;
use Eulogix\Lib\File\Proxy\SimpleFileProxy;
use Eulogix\Lib\Error\ErrorReport;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class FileRepositoryDataSource extends BaseDataSource {

    const ROOT_PLACEHOLDER = '_root';

    /**
     * @var FileRepositoryInterface
     */
    private $repo;

    /**
     * @var FileRepositoryPreviewProvider
     */
    private $previewProvider;

    /**
     * @inheritdoc
     */
    public function build($parameters=[]) {

        $this->repo = FileRepositoryFactory::fromId($parameters['repositoryId']);

        $this->repo->setParameters($parameters);

        $this->previewProvider = new FileRepositoryPreviewProvider($this->repo);

        $this->addField('id')
            ->setType(\PropelTypes::VARCHAR)
            ->setIsPrimaryKey(true);

        $this->addField('parId')
            ->setType(\PropelTypes::VARCHAR);

        $this->addField('name')
            ->setType(\PropelTypes::VARCHAR);

        $this->addField('mime')
            ->setType(\PropelTypes::VARCHAR);

        $this->addField('cre')
            ->setType(\PropelTypes::BIGINT);

        $this->addField('mod')
            ->setType(\PropelTypes::BIGINT);

        $this->addField('size')
            ->setType(\PropelTypes::INTEGER);

        $this->addField('ext')
            ->setType(\PropelTypes::VARCHAR);

        return $this;
    }

    /**
     * @param DSRequest $req
     * @return DSResponse
     */
    public function executeFetch(DSRequest $req) {
        $dsresponse = new DSResponse($this);
        $errors = new ErrorReport();

        $data = [];

        if($getOneById = @$req->getParameters()['id']) {
            $f = $this->repo->get($this->cleanPath( $getOneById ));
            if($getOneById === self::ROOT_PLACEHOLDER) {
                $f->setId($getOneById);
                $f->setParentId(null);
            }
            if($f)
                $data = $this->fromFileProxy($f, $this->repo->getFileProperties($getOneById, 'dec_'));
        } else {
            if($parentId = @$req->getParameters()['_parent_id']) {
                $parentId = $this->cleanPath($parentId);
                if($this->repo->getUserPermissions()->canBrowse($parentId)) {
                    $files = $this->repo->getChildrenOf($parentId, false)->fetch();

                    foreach($files as $f) {
                        /**
                         * @var SimpleFileProxy $f
                         */
                        $f->setParentId($parentId === null ? self::ROOT_PLACEHOLDER : $parentId);
                        $data[] = $this->fromFileProxy($f, $this->repo->getFileProperties($f->getId(), 'dec_'));
                    }
                } else $errors->addGeneralError("Forbidden");
            } else if($search = @$req->getParameters()['_search']) {

                $searchDir = @$req->getParameters()['searchDir'];
                $searchDir = $this->cleanPath($searchDir);

                $files = $this->repo->search($searchDir, [
                    FileRepositoryInterface::QUERY_NAME => @$req->getParameters()[ FileRepositoryInterface::QUERY_NAME ],
                    FileRepositoryInterface::QUERY_EXTENDED => @$req->getParameters()[ FileRepositoryInterface::QUERY_EXTENDED ],
                ])->fetch();

                foreach($files as $f) {
                    $data[] = $this->fromFileProxy($f, $this->repo->getFileProperties($f->getId(), 'dec_'));
                }
            }
        }

        if($errors->hasErrors()) {
            $dsresponse->setStatus(false);
            $dsresponse->setErrorReport($errors);
        } else {
            $dsresponse->setStatus(true);
            $dsresponse->setData($data);
        }

        return $dsresponse;
    }

    /**
     * @param FileProxyInterface $f
     * @return array
     */
    private function fromFileProxy($f, $properties=[])
    {
        $r = array_merge([
            'id' => $f->getId(),
            'parId' => $f->getParentId(),
            'name' => $f->getName(),
            'size' => $f->getSize(),
            'cre' => $f->getCreationDate()->format('c'),
            'mod' => $f->getLastModificationDate()->format('c'),
            'mime' => FileUtil::getMIMEType($f->getExtension()),
            'dir'=>$f->isDirectory(),
            'ext' => $f->getExtension()
        ], $properties);

        if(!$f->isDirectory() && ($makeSurePreviewExists = $this->previewProvider->getOrCreateCachedPreviewIcon($f->getId(), 80))) {
            $previewIcon = $this->previewProvider->getUrlOfCachedPreviewIcon($f, 80);
                $r['iconSrc'] = $previewIcon;
        }

        return $r;
    }


    protected function _f_column($colNr)
    {
        // TODO: Implement _f_column() method.
    }

    protected function _f_and()
    {
        // TODO: Implement _f_and() method.
    }

    protected function _f_or()
    {
        // TODO: Implement _f_or() method.
    }

    protected function _f_isEmpty($fieldName)
    {
        // TODO: Implement _f_isEmpty() method.
    }

    protected function _f_contain($fieldName, $arg)
    {
        // TODO: Implement _f_contain() method.
    }

    protected function _f_equal($fieldName, $arg)
    {
        // TODO: Implement _f_equal() method.
    }

    protected function _f_different($fieldName, $arg)
    {
        // TODO: Implement _f_different() method.
    }

    /**
     * @inheritdoc
     */
    public function executeAdd(DSRequest $req)
    {
        $dsresponse = new DSResponse($this);
        $success = true;
        $data = [];

        $values = $req->getValues();
        $parentFolder = $this->cleanPath($values['parId']);

        try {
            if($this->repo->getUserPermissions()->canCreateDirIn($parentFolder)) {
                $newFolder = $this->repo->createFolder($parentFolder, $values['name']);
                $newFolder->setParentId($parentFolder === null ? self::ROOT_PLACEHOLDER : $parentFolder);
                $data = $this->fromFileProxy($newFolder);
            } else {
                $dsresponse->addGeneralError("Forbidden");
                $success = false;
            }
        } catch(\Exception $e) {
            $dsresponse->addGeneralError($e->getMessage());
            $success = false;
        }

        $dsresponse->setStatus($success);
        $dsresponse->setData($data);
        return $dsresponse;
    }

    /**
     * @inheritdoc
     */
    public function executeClientExport(DSRequest $req)
    {
        // TODO: Implement executeClientExport() method.
    }

    /**
     * @inheritdoc
     */
    public function executeCustom(DSRequest $req)
    {
        // TODO: Implement executeCustom() method.
    }

    /**
     * @inheritdoc
     */
    public function executeCount(DSRequest $req)
    {
        // TODO: Implement executeCount() method.
    }

    /**
     * @inheritdoc
     */
    public function executeRemove(DSRequest $req)
    {
        $filePath = $req->getParameters()[ $this->getPrimaryKey() ];

        $dsresponse = new DSResponse($this);

        if($this->repo->getUserPermissions()->canDelete($filePath)) {
            $this->repo->delete($filePath);
            $dsresponse->setStatus(true);
        } else {
            $dsresponse->setStatus(false);
            $errors = new ErrorReport();
            $errors->addGeneralError("Forbidden");
            $dsresponse->setErrorReport($errors);
        }

        return $dsresponse;
    }

    /**
     * @inheritdoc
     */
    public function executeUpdate(DSRequest $req)
    {
        $filePath = $req->getParameters()[ $this->getPrimaryKey() ];

        $dsresponse = new DSResponse($this);
        $success = true;
        $data = [];

        try {
            if ($req->getOldValues()[ 'name' ] != $req->getValues()[ 'name' ]) {
                //file RENAME
                if($this->repo->getUserPermissions()->canRename($filePath)) {
                    $newId = $this->repo->rename($req->getValues()[ 'id' ], $req->getValues()[ 'name' ]);
                    $data = $this->fromFileProxy( $this->repo->get($newId) );
                } else {
                    $dsresponse->addGeneralError("Forbidden");
                    $success = false;
                }
            } elseif ($this->cleanPath( $req->getOldValues()[ 'parId' ] ) != $this->cleanPath( $req->getValues()[ 'parId' ] ) ) {
                //file MOVE
                $targetPath = $this->cleanPath( $req->getValues()[ 'parId' ]);
                if($this->repo->getUserPermissions()->canMove($filePath, $targetPath)) {
                    $newPath = $this->repo->move($filePath, $targetPath);
                    $data = $this->fromFileProxy( $this->repo->get($newPath) );
                } else {
                    $dsresponse->addGeneralError("Forbidden");
                    $success = false;
                }
            }
        } catch(\Exception $e) {
            $dsresponse->addGeneralError($e->getMessage());
            $success = false;
        }

        $dsresponse->setStatus($success);
        $dsresponse->setData($data);
        return $dsresponse;
    }

    /**
     * returns the default file repository instance that retrieves and stores files, for a given fieldname or recordid
     *
     * @param string $recordid
     * @return FileRepositoryInterface
     */
    public function getFileRepository($recordid = null)
    {
        // TODO: Implement getFileRepository() method.
    }

    /**
     * Takes care of translating the paths as supplied by RFE
     * @param string $path
     * @return string
     */
    private function cleanPath($path) {
        return $path == self::ROOT_PLACEHOLDER ? null : $path;
    }
}

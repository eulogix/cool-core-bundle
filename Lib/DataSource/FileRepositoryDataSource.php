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

use Eulogix\Cool\Lib\File\FileProxyInterface;
use Eulogix\Cool\Lib\File\FileRepositoryFactory;
use Eulogix\Cool\Lib\File\FileRepositoryInterface;
use Eulogix\Cool\Lib\File\FileRepositoryPreviewProvider;
use Eulogix\Cool\Lib\File\FileUtil;
use Eulogix\Cool\Lib\File\SimpleFileProxy;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class FileRepositoryDataSource extends BaseDataSource {

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
        $success = false;
        $dsresponse = new DSResponse($this);

        $data = [];

        if($getOneById = @$req->getParameters()['id']) {
            $f = $this->repo->get($getOneById === '_root' ? null : $getOneById);
            if($getOneById === '_root') {
                $f->setId($getOneById);
                $f->setParentId(null);
            }
            if($f)
                $data = $this->fromFileProxy($f, $this->repo->getFileProperties($getOneById, 'dec_'));
        } else {
            if($parentId = @$req->getParameters()['_parent_id']) {

                $parentId = $parentId =='_root' ? null : $parentId;
                $files = $this->repo->getChildrenOf($parentId, false)->fetch();

                foreach($files as $f) {
                    /**
                     * @var SimpleFileProxy $f
                     */
                    $f->setParentId($parentId === null ? '_root' : $parentId);
                    $data[] = $this->fromFileProxy($f, $this->repo->getFileProperties($f->getId(), 'dec_'));
                }

            } else if($search = @$req->getParameters()['_search']) {

                $searchDir = @$req->getParameters()['searchDir'];
                $searchDir = $searchDir == '_root' ? null : $searchDir;

                $files = $this->repo->search($searchDir, [
                    FileRepositoryInterface::QUERY_NAME => @$req->getParameters()[ FileRepositoryInterface::QUERY_NAME ],
                    FileRepositoryInterface::QUERY_EXTENDED => @$req->getParameters()[ FileRepositoryInterface::QUERY_EXTENDED ],
                ])->fetch();

                foreach($files as $f) {
                    $data[] = $this->fromFileProxy($f, $this->repo->getFileProperties($f->getId(), 'dec_'));
                }
            }
        }

        $dsresponse->setData($data);
        $dsresponse->setStatus($success);
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
        // TODO: Implement executeAdd() method.
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
        $success = true;
        $filePath = $req->getParameters()[ $this->getPrimaryKey() ];

        $this->repo->delete($filePath);

        $dsresponse = new DSResponse($this);
        $dsresponse->setStatus($success);
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
            //file MOVE
            if ($req->getOldValues()[ 'parId' ] != $req->getValues()[ 'parId' ]) {
                $newPath = $this->repo->move($filePath, $req->getValues()[ 'parId' ]);
                $data = $this->fromFileProxy( $this->repo->get($newPath) );
            }

            //file RENAME
            if ($req->getOldValues()[ 'name' ] != $req->getValues()[ 'name' ]) {
                $this->repo->rename($req->getValues()[ 'id' ], $req->getValues()[ 'name' ]);
                $data = $this->fromFileProxy( $this->repo->get($filePath) );
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
}

<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Form\Field;

use Eulogix\Cool\Lib\File\FileRepositoryInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class RepoFile extends File {

    /**
     * @var FileRepositoryInterface
     */
    protected $fileRepository;

    /**
     * @var string
     */
    protected $fileId;

    protected $type = self::TYPE_REPOFILE;

    protected function init() {
        $this->getParameters()->set('buttonLabel', "Choose File REPO");
    }

    /**
     * @param FileRepositoryInterface $fileRepository
     * @return $this
     */
    public function setFileRepository($fileRepository)
    {
        $this->fileRepository = $fileRepository;
        return $this;
    }

    /**
     * @return FileRepositoryInterface
     */
    public function getFileRepository()
    {
        return $this->fileRepository;
    }

    /**
     * @param string $fileId
     * @return $this
     */
    public function setFileId($fileId)
    {
        $this->fileId = $fileId;
        return $this;
    }

    /**
     * @return string
     */
    public function getFileId()
    {
        return $this->fileId;
    }
}
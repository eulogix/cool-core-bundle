<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Traits;

use Eulogix\Cool\Lib\File\FileRepositoryInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

trait FileRepositoryHolder {

    /**
     * @var FileRepositoryInterface
     */
    private $fileRepository = null;

    /**
     * @return FileRepositoryInterface
     */
    public function getFileRepository()
    {
        return $this->fileRepository;
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

}
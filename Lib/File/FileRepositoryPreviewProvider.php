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

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class FileRepositoryPreviewProvider {

    /**
     * @var FileRepositoryInterface
     */
    private $repo;

    /**
     * @var string
     */
    private $webRoot,

            $cacheURL = '/cache/frepo';


    public function __construct(FileRepositoryInterface $repo) {
        $this->repo = $repo;
        $this->webRoot = Cool::getInstance()->getContainer()->get('kernel')->getRootDir() . '/../web';
    }

    /**
     * @param FileProxyInterface $file
     * @param int $width
     * @return bool|string
     */
    public function getUrlOfCachedPreviewIcon($file, $width) {

        $filePath = $file->getId();

        $token = $this->tokenize($filePath, $width);
        $cachedResource = $this->cacheURL."/{$token}.jpg";
        $absCachePath = $this->webRoot.$cachedResource;

        if($this->cacheValid($absCachePath, $file->getLastModificationDate()))
            return $cachedResource;

        return false;
    }

    /**
     * @param string $filePath
     * @param int $width
     * @return bool|string
     */
    public function getOrCreateCachedPreviewIcon($filePath, $width) {

        @mkdir($this->webRoot.$this->cacheURL,0777,true);

        $token = $this->tokenize($filePath, $width);
        $cachedResource = $this->cacheURL."/{$token}.jpg";
        $absCachePath = $this->webRoot.$cachedResource;

        $f = $this->repo->get($filePath);
        if(!$f)
            return false;

        if(!$this->cacheValid($absCachePath, $f->getLastModificationDate())) {
            if($thumb = FileUtil::getThumbnail($f, $width)) {
                rename($thumb, $absCachePath);
                return $absCachePath;
            }
        } else return $absCachePath;

        return $this->webRoot.'/cool/res/icons/'.$f->getExtension().'.png';
    }

    /**
     * @param string $filePath
     * @param int $width
     * @return string
     */
    protected function tokenize($filePath, $width)
    {
        return sha1($this->repo->getUid() . '@' . $filePath . '@' . $width);
    }

    /**
     * @param string $absCachePath
     * @param \DateTime $sourceCreationDate
     * @return bool
     */
    protected function cacheValid($absCachePath, $sourceCreationDate) {
        $ret = file_exists($absCachePath) && $sourceCreationDate && @filemtime($absCachePath) > $sourceCreationDate->getTimestamp();
        return $ret;
    }

}
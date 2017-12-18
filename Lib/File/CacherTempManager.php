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

use Eulogix\Lib\Cache\CacherInterface;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Lib\File\Proxy\FileProxyInterface;
use Eulogix\Lib\File\Proxy\SimpleFileProxy;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class CacherTempManager implements TempManagerInterface
{

    const PREFIX_TMP_UPLOAD_MANAGER = "TMPUPMGR";
    const PREFIX_TMP_DOWNLOAD = "TMPDL";

    /**
     * @var CacherInterface
     */
    var $cacher;

    /**
     * @var string
     */
    var $tempFolder;

    /**
     * @var int
     */
    var $purgeEverySeconds = 0;

    public function __construct(CacherInterface $cacher, $tempFolder = null, $purgeEverySeconds = 0) {
        $this->cacher = $cacher;
        $this->tempFolder = $tempFolder ?? Cool::getInstance()->getFactory()->getSettingsManager()->getTempFolder();
        $this->purgeEverySeconds = $purgeEverySeconds;
    }

    /**
     * @inheritdoc
     */
    public function storeFile($uploadedName, $temporaryUploadedFile) {
        $t = tempnam($this->tempFolder, self::PREFIX_TMP_UPLOAD_MANAGER);
        copy($temporaryUploadedFile, $t);
        $id = md5($t);
        $this->cacher->store('TEMP_FILE'.$id, ['tempName'=>$t, 'uploadedName'=>$uploadedName]);

        $this->purge();

        return $id;
    }

    /**
     * @inheritdoc
     */
    public function getLocalFile($id) {
        $t = $this->cacher->fetch('TEMP_FILE'.$id);
        if (isset($t['tempName']) && file_exists($t['tempName'])) {
            return $t['tempName'];
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getFileName($id) {
        $t = $this->cacher->fetch('TEMP_FILE'.$id);
        if (isset($t['uploadedName'])) {
            return $t['uploadedName'];
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getTempKeyFromFileProxy(FileProxyInterface $fp) {
        $t = tempnam($this->tempFolder, self::PREFIX_TMP_DOWNLOAD);
        $fp->toFile($t);
        $id = md5($t);
        $this->cacher->store('TEMP_DL_KEY'.$id, ['tempName'=>$t, 'fileName'=>$fp->getName()]);
        return $id;
    }

    /**
     * @inheritdoc
     */
    public function getFileProxyFromTempKey($key) {
        if($ft = $this->cacher->fetch('TEMP_DL_KEY'.$key)) {
            $f = new SimpleFileProxy();
            $f->setName($ft['fileName'])
              ->setContentFile($ft['tempName']);
            return $f;
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getDownloadUrlFromFileProxy(FileProxyInterface $fp)
    {
        return Cool::getInstance()->getContainer()->get('router')->generate('_downloadTempFile', array('key' => $this->getTempKeyFromFileProxy($fp)));
    }

    /**
     * performs a cleanup of stale files in temp folder
     * with a 10% probability
     */
    private function purge() {
        if($this->purgeEverySeconds > 0 && rand(0,100) > 90)
            foreach (glob($this->tempFolder . "/*") as $fileName) {
                if(preg_match('/\/('.self::PREFIX_TMP_UPLOAD_MANAGER.'|'.self::PREFIX_TMP_DOWNLOAD.')/sim', $fileName)) {
                    $fileAge = time() - filectime($fileName);
                    if ($fileAge > $this->purgeEverySeconds)
                        @unlink($fileName);
                }
            }
    }
}
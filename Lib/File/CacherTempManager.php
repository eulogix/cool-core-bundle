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

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class CacherTempManager implements TempManagerInterface
{
    /**
     * @var CacherInterface
     */
    var $cacher;

    public function __construct(CacherInterface $cacher) {
        $this->cacher = $cacher;
    }

    /**
     * @inheritdoc
     */
    public function storeFile($uploadedName, $temporaryUploadedFile) {
        $t = tempnam(sys_get_temp_dir(),'TMPUPMGR');
        copy($temporaryUploadedFile, $t);
        $id = md5($t);
        $this->cacher->store('TEMP_FILE'.$id, ['tempName'=>$t, 'uploadedName'=>$uploadedName]);
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
        $t = tempnam(sys_get_temp_dir(),'DOWNLOAD');
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
}
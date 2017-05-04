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

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\File\FileCommand;
use Eulogix\Cool\Lib\File\FileProxyInterface;
use Eulogix\Cool\Lib\File\FileRepositoryInterface;
use Eulogix\Cool\Lib\File\SimpleFileProxy;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class File extends Field {
    
    protected $type = self::TYPE_FILE;
    protected $coolDojoWidget = "cool/controls/file";

    /**
     * @var SimpleFileProxy
     */
    protected $storedFile;

    /**
     * @var FileRepositoryInterface
     */
    protected $fileRepository;

    protected function init() {
        $this->setMultiple(false);
        $this->setMaxFiles(1);
    }

    /**
     * @param bool $bool
     * @return $this
     */
    public function setMultiple($bool) {
        $this->getParameters()->set('multiple', $bool);
        return $this;
    }

    /**
     * @param int $int
     * @return $this
     */
    public function setMaxFiles($int) {
        $this->getParameters()->set('maxFiles', $int);
        return $this;
    }

    /**
     * @return SimpleFileProxy[]
     */
    public function getUploadedFiles() {
        $ret = [];
        if( $f = $this->getInValue('uploadedFiles') ) {
            $tempManager = Cool::getInstance()->getFactory()->getFileTempManager();
            foreach($f as $fileName => $fileArray) {
                $FF = new SimpleFileProxy();
                $FF->setName($fileName)
                   ->setContent( $tempManager->getFileContent($fileArray['tempId']) )
                   ->setLastModificationDate( new \DateTime() );
                $ret[$fileName] = $FF;
            }
        }
        return $ret;
    }

    /**
     * @return FileCommand[]
     */
    public function getCommands() {
        $ret = [];
        if( $ops = $this->getInValue('operations') ) {
            foreach($ops as $opType=>$opValue) {
                if($opType == FileCommand::TYPE_REMOVE_STORED) {
                    $ret[] = new FileCommand($opType);
                }
            }
        }
        return $ret;
    }

    /**
     * @return int
     */
    public function countUploadedFiles() {
        return count($this->getUploadedFiles());
    }

    /**
     * @return $this
     */
    public function clearUploadedFiles() {
        $this->setInValue('uploadedFiles', []);
        return $this;
    }

    /**
     * @param int $fileId
     * @param string $fileName the name of the file. used for client representation
     * @param int $fileSize the size of the file in bytes. used for client representation
     */
    public function setViewData($fileId, $fileName, $fileSize)
    {
        $this->setInValue('viewData', [
            'id' => $fileId,
            'name' => $fileName,
            'size' => $fileSize
        ]);
    }

    /**
     * @return array
     */
    public function getViewData()
    {
        return $this->getInValue('viewData');
    }

    /**
     * @param string $fileId
     * @param FileProxyInterface $storedFile
     * @return $this
     */
    public function setStoredFile($fileId, $storedFile)
    {
        $this->setViewData($fileId, $storedFile->getName(), $storedFile->getSize());
        $this->storedFile = $storedFile;
        return $this;
    }

    /**
     * @return FileProxyInterface
     */
    public function getStoredFile()
    {
        if($this->storedFile) {
            return $this->storedFile;
        }

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
     * If a file has been uploaded, the File instance is returned, otherwise, if present, the stored file Id
     * @return mixed|SimpleFileProxy
     */
    public function getValue() {
        return parent::getValue();
    }

    /**
     * @return FileCommand|mixed|null
     */
    public function getPersistableValue() {

        //if a single file has been uploaded, return a File instance populated with the uploaded file
        if($this->countUploadedFiles()==1) {
            $f = $this->getUploadedFiles();
            return array_pop($f);
        }

        //if an operation has been issued, such as removeStoredFile, return a FileOperation instance
        //multiple commands, or single command on multiple files, are not yet implemented
        if( $ops = $this->getInValue('operations') ) {
            foreach($ops as $opType=>$opValue) {
                if($opType == FileCommand::TYPE_REMOVE_STORED) {
                    return new FileCommand($opType);
                }
            }
        }

        if($fileId = @$this->getViewData()['id']) {
            return $fileId;
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function setRawValue($value) {
        //TODO check that
        return $this->setValue($value);
    }

    /**
     * @inheritdoc
     */
    public function setValue($value) {

        if($value instanceof FileProxyInterface){
            $this->setStoredFile(1, $value);
            return $this;
        } else parent::setValue($value);

        return $this;
    }

    /**
     * @return mixed
     */
    protected function getDecodedValue()
    {
        $ret = json_decode($this->getRawValue(), true);
        //ensure that no invalid data is returned, the control is sometimes initialized with scalar data as raw value
        return is_array($ret) ? $ret : [];
    }

    /**
     * @param $arr
     * @return mixed
     */
    protected function setDecodedValue($arr)
    {
        parent::setValue(json_encode($arr));
        return $this;
    }

    /**
     * @param $key
     * @param $value
     */
    protected function setInValue($key, $value) {
        $arr = $this->getDecodedValue();
        $arr[$key] = $value;
        $this->setDecodedValue($arr);
    }

    /**
     * @param $key
     * @return null
     */
    protected function getInValue($key) {
        $arr = $this->getDecodedValue();
        if(isset($arr[$key]))
            return $arr[$key];
        return null;
    }

}
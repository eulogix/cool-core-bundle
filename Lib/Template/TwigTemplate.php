<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Template;

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\File\FileUtil;
use Eulogix\Lib\File\Converter\Html2PdfConverter;
use Eulogix\Lib\File\Proxy\FileProxyInterface;
use Eulogix\Lib\File\Proxy\SimpleFileProxy;
use Eulogix\Lib\File\ZipUtils;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class TwigTemplate extends Template
{
    /**
     * @var string
     */
    protected $tempInput, $wkFolder, $templateName;

    /**
     * @inheritdoc
     */
    public function setTemplateFile($templateFile)
    {
        $this->templateName = $templateFile->getBaseName();
        if($templateFile->getExtension() == 'zip') {
            $this->wkFolder = FileUtil::getTempFolder();
            ZipUtils::unpack($templateFile, $this->wkFolder);
            $this->tempInput = $this->wkFolder.DIRECTORY_SEPARATOR.'template.html.twig';
            if(!file_exists($this->tempInput))
                throw new \Exception("template.html.twig not found in Zip archive");
        } else {
            $this->tempInput = FileUtil::getTempFileName('twig');
            $templateFile->toFile($this->tempInput);
        }
        return parent::setTemplateFile($templateFile);
    }

    /**
     * @param string $format
     * @return FileProxyInterface
     * @throws \Exception
     */
    public function getRenderedOutput($format = null)
    {
        if($this->wkFolder) {
            switch(strtolower($format)) {
                case 'zip' : $outputFile = $this->tempInput; break;
                default : $outputFile = $this->wkFolder.DIRECTORY_SEPARATOR.'output.html';
            }
        } else $outputFile = FileUtil::getTempFileName('html');

        file_put_contents($outputFile, Cool::getInstance()->getFactory()->getTwig()->render(
            $this->tempInput,
            $this->getTemplateVariables()
        ));

        switch(strtolower($format)) {
            case 'pdf' : {
                $c = new Html2PdfConverter();
                $ret = $c->convert($outputFile, 'pdf', $this->getParameters()->all());
                break;
            }
            case 'zip' : {
                if($this->wkFolder) {
                    $ret = ZipUtils::zipFolder($this->wkFolder);
                    break;
                }
            }
            default : $ret = SimpleFileProxy::fromFileSystem($outputFile, true);
        }

        @unlink($outputFile);
        $ret->setName($this->templateName.'.'.$format);
        return $ret;
    }

    /**
     * @inheritdoc
     */
    public function getRenderedTemplateFile() {
        if($this->wkFolder) {
            $originalTpl = $this->getTemplateFile();
            $newTpl = $this->getRenderedOutput('zip');
            $newTpl->setName($originalTpl->getName());
            $newTpl->setProperties($originalTpl->getProperties());
            return $newTpl;
        }
        return null;
    }

    function __destruct() {
        if($this->wkFolder && file_exists($this->wkFolder) && is_dir($this->wkFolder)) {
            exec("rm -rf \"{$this->wkFolder}\"");
        } else @unlink($this->tempInput);
    }
}
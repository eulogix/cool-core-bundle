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

use Eulogix\Cool\Lib\File\FileUtil;
use Eulogix\Lib\File\Proxy\FileProxyInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class RendererFactory
{
    /**
     * @param FileProxyInterface $templateFile
     * @return Template
     */
    public function getRendererFor(FileProxyInterface $templateFile) {
        //TODO cache renderers by template hash
        if(strtolower($templateFile->getExtension()) == 'zip') {
            return $this->getRendererForZipArchive($templateFile);
        } return $this->getRendererForSimpleFile($templateFile);
    }

    /**
     * @param FileProxyInterface $templateFile
     * @return string
     */
    protected function getRendererForZipArchive(FileProxyInterface $templateFile) {
        $ret = null;
        $tempZip = FileUtil::getTempFileName('zip');
        $templateFile->toFile($tempZip);

        if ($zip = zip_open($tempZip)) {
            while (!$ret && $zip_entry = zip_read($zip)) {
                $fileName = zip_entry_name($zip_entry);
                if(preg_match('/^template\.(.+?)$/sim', $fileName, $m))
                    $ret = $this->getRendererForSimpleFile($templateFile, $m[1]);
            }
            zip_close($zip);
        }

        @unlink($tempZip);
        return $ret;
    }

    /**
     * @param FileProxyInterface $templateFile
     * @param string $format
     * @return Template|null
     * @throws \Exception
     */
    protected function getRendererForSimpleFile(FileProxyInterface $templateFile, $format=null)
    {
        $format = strtolower($format ?? $templateFile->getCompleteExtension());
        switch($format) {
            case 'html':
            case 'htm':
            case 'htm.twig':
            case 'html.twig': {
                $ret = new TwigTemplate();
                $ret->setTemplateFile($templateFile);
                return $ret;
            }
        }
        return null;
    }
}
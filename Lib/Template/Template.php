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

use Eulogix\Cool\Lib\Traits\ParametersHolder;
use Eulogix\Lib\File\Proxy\FileProxyInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

abstract class Template
{
    use ParametersHolder;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var FileProxyInterface
     */
    protected $templateFile;

    /**
     * @return FileProxyInterface
     */
    public function getTemplateFile()
    {
        return $this->templateFile;
    }

    /**
     * @param FileProxyInterface $templateFile
     * @return $this
     */
    public function setTemplateFile($templateFile)
    {
        $this->templateFile = $templateFile;
        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return array
     */
    protected function getTemplateVariables()
    {
        return $this->getData();
    }

    /**
     * @param string $format
     * @return FileProxyInterface
     */
    public abstract function getRenderedOutput($format = null);

    /**
     * if implemented, returns a template file with all the variables replaced
     * @return FileProxyInterface|null
     */
    public function getRenderedTemplateFile() {
        return null;
    }
}
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

use Eulogix\Cool\Lib\DataSource\ValueMapInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class XhrPicker extends Field {
    
    protected $type = self::TYPE_XHRPICKER;

    protected $coolDojoWidget = "cool/controls/xhrpicker";

    /**
     * @var string
     */
    protected $storeUrl, $placeHolder;

    /**
     * @var array
     */
    protected $storeParameters = [];

    /**
    * @inheritdoc
    */
    public function getDefinition() {
        $def = array_merge(
            parent::getDefinition(),
        array(
            'placeHolder' => $this->getPlaceHolder(),
            'storeUrl' => $this->getStoreUrl(),
            'storeParameters' => $this->getStoreParameters(),
        ));
        return $def;
    }

    /**
     * @param string $placeHolder
     * @return $this
     */
    public function setPlaceHolder($placeHolder)
    {
        $this->placeHolder = $placeHolder;
        return $this;
    }

    /**
     * @return string
     */
    public function getPlaceHolder()
    {
        return $this->placeHolder;
    }

    /**
     * @param string $storeUrl
     * @return $this
     */
    public function setStoreUrl($storeUrl)
    {
        $this->storeUrl = $storeUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getStoreUrl()
    {
        return $this->storeUrl;
    }

    /**
     * @return array
     */
    public function getStoreParameters()
    {
        return $this->storeParameters;
    }

    /**
     * @param array $storeParameters
     * @return $this
     */
    public function setStoreParameters($storeParameters)
    {
        $this->storeParameters = $storeParameters;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setValueMap(ValueMapInterface $valueMap)
    {
        parent::setValueMap($valueMap);
        $this->setStoreUrl($valueMap->getAjaxEndPoint());
        $this->setStoreParameters($valueMap->getParameters()->all());
        return $this;
    }

}
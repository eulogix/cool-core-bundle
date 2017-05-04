<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Dictionary;

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\DataSource\Bean;
use Eulogix\Cool\Lib\DataSource\SimpleValueMap;
use Eulogix\Cool\Lib\DataSource\ValueMapInterface;
use Eulogix\Cool\Lib\Translation\TranslatorInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class Lookup extends Bean {

    const TYPE_OTLT = 'OTLT';
    const TYPE_TABLE = "table";
    const TYPE_ENUM = "enum";
    const TYPE_VALUEMAP = "valueMap";
    const TYPE_VALUEMAP_SERVICE = "valueMapService";

    const TYPE_FK = "FK";  //not available in lookups instantiated from schema definition

    /**
     * @var string
     */
    private $schemaName,

            $type,
            $domainName,
            $valueMapClass,
            $valueMapService;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param string $schemaName
     * @param TranslatorInterface $translator
     */
    public function __construct($schemaName, TranslatorInterface $translator) {
        $this->schemaName = $schemaName;
        $this->translator = $translator;
    }

    /**
     * @var boolean
     */
    private $multiple = false;

    /**
     * @var string[]
     */
    private $validValues, $valueMapParams;

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $domainName
     */
    public function setDomainName($domainName)
    {
        $this->domainName = $domainName;
    }

    /**
     * @return mixed
     */
    public function getDomainName()
    {
        return $this->domainName;
    }

    /**
     * @param string|string[] $validValues
     * @return $this
     */
    public function setValidValues($validValues)
    {
        if(!is_array($validValues)) {
            $validValues = explode(',',$validValues);
        }
        $this->validValues = $validValues;
        return $this;
    }

    /**
     * @return \string[]
     */
    public function getValidValues()
    {
        return $this->validValues;
    }

    /**
     * @param string $valueMapClass
     * @return $this
     */
    public function setValueMapClass($valueMapClass)
    {
        $this->valueMapClass = $valueMapClass;
        return $this;
    }

    /**
     * @return string
     */
    public function getValueMapClass()
    {
        return $this->valueMapClass;
    }

    /**
     * @return string
     */
    public function getValueMapService()
    {
        return $this->valueMapService;
    }

    /**
     * @param string $valueMapService
     * @return $this
     */
    public function setValueMapService($valueMapService)
    {
        $this->valueMapService = $valueMapService;
        return $this;
    }

    /**
     * @param string|string[] $valueMapParams
     * @return $this
     */
    public function setValueMapParams($valueMapParams)
    {
        if(!is_array($valueMapParams)) {
            $valueMapParams = explode(',',$valueMapParams);
        }
        $this->valueMapParams = $valueMapParams;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getValueMapParams()
    {
        return $this->valueMapParams;
    }

    /**
     * @param boolean $multiple
     * @return $this
     */
    public function setMultiple($multiple)
    {
        $this->multiple = $multiple;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isMultiple()
    {
        return $this->multiple;
    }

    /**
     * @return ValueMapInterface
     */
    public function getValueMap() {
        //TODO: cache this?
        $schema = Cool::getInstance()->getSchema($this->schemaName);

        $ret = null;

        switch($this->getType()) {
            case self::TYPE_OTLT : {
                $mapHash = $schema->getOTLTLookupTable( $this->getDomainName() );
                $ret = new SimpleValueMap($mapHash);
                break;
            }
            case self::TYPE_TABLE : {
                $mapHash = $schema->getValueMapTable( $this->getDomainName() );
                $ret = new SimpleValueMap($mapHash);
                break;
            }
            case self::TYPE_ENUM : {
                $vv = $this->getValidValues();
                $mapHash = [];
                foreach ($vv as $validValue) {
                    $mapHash[] = [
                        'value'=>$validValue,
                        'label'=>$this->translator->trans($validValue)
                    ];
                }
                $ret = new SimpleValueMap($mapHash);
                break;
            }
            case self::TYPE_VALUEMAP_SERVICE: return Cool::getInstance()->getFactory()->getValuemap($this->getValueMapService());
            case self::TYPE_VALUEMAP : { break; }
        }

        if($ret)
            $ret->setAjaxEndPoint($this->getVmapEndpoint());

        return $ret;
    }

    /**
     * @return string
     */
    private function getVmapEndpoint() {
        $vmapParameters = [
            'lookup' => base64_encode(gzcompress(serialize($this)))
        ];
        return Cool::getInstance()->getFactory()->getRouter()->generate('dictionaryLookupVmap', $vmapParameters);
    }

}
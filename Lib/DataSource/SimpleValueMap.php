<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\DataSource;

use Eulogix\Cool\Lib\Translation\TranslatorInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class SimpleValueMap  extends BaseValueMap implements ValueMapInterface
{

    /**
     * @var mixed
     */
    private $mapHash = [];

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param $mapHash
     * @param null|TranslatorInterface $translator
     *
     * @throws \Exception
     */
    public function __construct($mapHash=[], $translator=null) {

        if($translator instanceof TranslatorInterface)
            $this->translator = $translator;

        foreach($mapHash as $key => $value) {
            if(!is_array($value)) {
                $this->addValue($value, is_numeric($key) ? $value : $key);
            } elseif( isset($value['label']) && isset($value['value']) ) {
                $this->addValueHash( $value );
            } elseif( isset($value['value'])) {
                $this->addValue( $value['value'], '[NT] '.$value['value'] );
            } else {
                throw new \Exception("bad option value : ".var_export($value, true));
            }
        }

    }

    /**
     * @param array $mapHash
     * @return SimpleValueMap
     */
    public static function fromHash(array $mapHash) {
        return new self($mapHash);
    }

    /**
     * @inheritdoc
     */
    public function getValuesNumber() {
        return count($this->mapHash);
    }

    /**
     * @param string $value
     * @param string $label
     * @param array $additionalValues
     */
    public function addValue($value, $label=null, $additionalValues=[]) {
        $this->mapHash[] = array_merge($additionalValues, [
            'value' => $value,
            'label' => $this->trans($label ? $label : $value),
        ]);
    }

    /**
     * @param array $hash
     */
    public function addValueHash($hash) {
        $this->mapHash[] = $hash;
    }

    /**
     * @inheritdoc
     */
    public function mapValue($value) {
        if(null===$value)
            return '-';

        $map = $this->getMap($value);
        return $map['label'] ?? "({$value})!";
    }

    /**
     * @param string $value
     * @return boolean
     */
    public function valueExists($value)
    {
        $map = $this->getMap($value);
        return isset($map['label']);
    }

    /**
     * @inheritdoc
     */
    public function getMap($value = '', $searchText = "", $parameters = [], $limit = null) {
        if($value !=='') {
            foreach($this->mapHash as $mapElement) {
                if($mapElement['value']==$value)
                    return $mapElement;
            }
        } elseif($searchText) {
            $ret = [];
            foreach($this->mapHash as $mapElement)
                if(stripos($mapElement['label'], $searchText)!==false)
                    $ret[] = $mapElement;
            return $ret;
        }
        return $this->mapHash;
    }

    /**
     * @inheritdoc
     */
    public function filterByAllowedValues($allowedValues)
    {
        $newHash = [];
        foreach($this->mapHash as $mapElement) {
            if(in_array($mapElement['value'], $allowedValues))
                $newHash[] = $mapElement;
        }
        $this->mapHash = $newHash;
        return $this;
    }

    /**
     * @param string $param
     * @return string
     */
    private function trans($param)
    {
        if($this->translator instanceof TranslatorInterface)
            return $this->translator->trans($param);
        return $param;
    }

}
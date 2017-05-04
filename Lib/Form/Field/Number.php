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

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class Number extends Field {
    
    protected $type = self::TYPE_NUMBER;

    private $useSlider = false;

    /**
     * @return string
     */
    protected function getCoolDojoWidget()
    {
        if($this->getUseSlider())
            return "cool/controls/numberSlider";
        return "cool/controls/number";
    }

    /**
     * @param int|double $from
     * @return $this
     */
    public function setFrom($from) {
        $this->getParameters()->set('from', $from);
        return $this;
    }

    /**
     * @param int|double $to
     * @return $this
     */
    public function setTo($to) {
        $this->getParameters()->set('to', $to);
        return $this;
    }

    /**
     * @param int $dp
     * @return $this
     */
    public function setDecimalPlaces($dp) {
        $this->getParameters()->set('places', $dp);
        return $this;
    }

    /**
     * @param boolean $useSlider
     * @return $this
     */
    public function setUseSlider($useSlider)
    {
        $this->useSlider = $useSlider;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getUseSlider()
    {
        return $this->useSlider;
    }

}
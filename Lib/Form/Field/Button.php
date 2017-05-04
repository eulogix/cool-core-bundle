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

class Button extends Field {
    
    protected $type = self::TYPE_BUTTON;

    protected $coolDojoWidget = "cool/controls/button";

    const PARAM_ONCLICK = 'onClick';
    const PARAM_ICON_LEFT = 'iconSrc';
    const PARAM_ICON_RIGHT = 'iconSrcRight';
    const PARAM_CONFIRM_MESSAGE = 'confirmMessage';
    const PARAM_DISABLED_ON_CLICK = 'disabledOnClick';

    /**
     * @param string $onClick
     * @return $this
     */
    public function setOnClick($onClick)
    {
        $this->getParameters()->set(self::PARAM_ONCLICK, $onClick);
        return $this;
    }

    /**
     * @return string
     */
    public function getOnClick()
    {
        return $this->getParameters()->get(self::PARAM_ONCLICK);
    }

    /**
     * @param boolean $disabledOnClick
     * @return $this
     */
    public function setDisabledOnClick($disabledOnClick)
    {
        $this->getParameters()->set(self::PARAM_DISABLED_ON_CLICK, $disabledOnClick);
        return $this;
    }

    /**
     * @return boolean
     */
    public function getDisabledOnClick()
    {
        return $this->getParameters()->get(self::PARAM_DISABLED_ON_CLICK);
    }

    /**
     * @param string $confirmMessage
     * @return $this
     */
    public function setConfirmMessage($confirmMessage)
    {
        $this->getParameters()->set(self::PARAM_CONFIRM_MESSAGE, $confirmMessage);
        return $this;
    }

    /**
     * @return string
     */
    public function getConfirmMessage()
    {
        return $this->getParameters()->get(self::PARAM_CONFIRM_MESSAGE);
    }

    /**
     * @param $iconSrc
     * @return $this
     */
    public function setLeftIcon($iconSrc) {
        $this->getParameters()->set(self::PARAM_ICON_LEFT, $iconSrc);
        return $this;
    }

    /**
     * @param $iconSrc
     * @return $this
     */
    public function setRightIcon($iconSrc) {
        $this->getParameters()->set(self::PARAM_ICON_RIGHT, $iconSrc);
        return $this;
    }

}
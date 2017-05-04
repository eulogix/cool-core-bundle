<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Widget;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class Action {

    /**
     * a string used to clusterize actions
     * @var string
     */
    private $group;

    /**
     * @var string
     */
    private $label, $onClick, $icon;

    /**
     * @var boolean
     */
    private $readOnly;

    /**
    * @var Menu
    */
    private $menu;

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label) 
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @param string $js
     * @return $this
     */
    public function setOnClick($js) 
    {
        $this->onClick = $js;
        return $this;
    }

    /**
     * @param string $icon
     * @return $this
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @param string $js
     * @param string $confirmMessage
     */
    public function setConfirmedOnClick($js, $confirmMessage=null)
    {
        $realConfirmMessage = $confirmMessage ? $confirmMessage : $this->mapString("Confirm ".$this->label." ?");
        $rjs = "if(confirm('$realConfirmMessage')) { $js }";
        $this->setOnClick($rjs);
    }

    /**
     * @param string $confirmMessage
     */
    public function addConfirmation($confirmMessage=null)
    {
        $realConfirmMessage = $confirmMessage ? $confirmMessage : $this->mapString("Confirm ".$this->label." ?");
        $rjs = "if(confirm('$realConfirmMessage')) { ".$this->onClick." }";
        $this->setOnClick($rjs);
    }

    /**
     * @param boolean $readOnly
     * @return $this
     */
    public function setReadOnly($readOnly)
    {
        $this->readOnly = $readOnly;
        return $this;
    }

    /**
     * @return array
     */
    public function getDefinition() {
         $def = array(
            "label"=>$this->label,
            "onClick"=>$this->onClick,
            "readOnly"=>$this->readOnly,
            "group"=>$this->group,
         );
         if($this->menu) {
             $def['menu'] = $this->menu->getDefinition();
         }
         if($this->icon) {
             $def['icon'] = $this->icon;
         }
            
         return $def;
    }

    /**
    * @param Menu $menu
    * @return Menu
    */
    public function setMenu($menu = null) {
        $this->menu = $menu ? $menu : new Menu();
        return $this->menu;
    }

    /**
     * for translation
     * @param $string
     * @return mixed
     */
    private function mapString($string)
    {
        return $string;
    }

    /**
     * @param string $group
     * @return $this
     */
    public function setGroup($group)
    {
        $this->group = $group;
        return $this;
    }
}
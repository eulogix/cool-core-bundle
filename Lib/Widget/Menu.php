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

class Menu {
    protected $children = [];

    /**
     * @var string
     */
    protected $label, $onClick,  $icon;

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
     * @return array
     */
    public function getDefinition() {
         $children = [];
         foreach($this->children as $c) {
             /** @var Menu $c */
             $children[] = $c->getDefinition();
         }
         $def = array(
            "label"=>$this->label,
            "onClick"=>$this->onClick,
            "children"=>$children,
         );

         if($this->icon)
             $def["icon"] = $this->icon;

         return $def;
    }

    /**
     * @return $this
     */
    public function addChildren() {
        $m = new self();
        $this->children[] = $m;
        return $m;
    }

    /**
     * @return int
     */
    public function countChildren() {
        return count($this->children);
    }

    /**
     * @param string $string
     * @return string
     */
    private function mapString($string)
    {
        return $string;
    }

}
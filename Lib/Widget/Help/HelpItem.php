<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Widget\Help;

use Eulogix\Cool\Lib\Traits\WidgetHolder;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

abstract class HelpItem {

    use WidgetHolder;

    /**
     * @var string
     */
    protected $label;

    /**
     * @return array
     */
    public function getDefinition() {
        return [
            'label' => $this->getLabel()
        ];
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return HelpItem
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }


}
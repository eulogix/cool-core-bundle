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

use Eulogix\Cool\Lib\Form\Field\FieldInterface;
use Eulogix\Cool\Lib\Form\Field\Field;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class TextBox extends Field {
    
    protected $type = self::TYPE_TEXTBOX;

    const IS_PASSWORD = 'isPassword';

    /**
     * @param bool $isPassword
     * @return $this
     */
    public function setIsPassword($isPassword) {
        $this->getParameters()->set(self::IS_PASSWORD, $isPassword ? $isPassword : false);
        return $this;
    }

}
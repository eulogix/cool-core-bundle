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

use Eulogix\Cool\Lib\Translation\TranslatorInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class TranslatableMenu extends Menu {

    /**
     * This translator can be used to translate field names, messages, and so on
     *
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

    /**
     * returns the translator for this widget
     * @returns TranslatorInterface
     */
    public function getTranslator() {
        return $this->translator;
    }

    /**
     * @param $label
     * @param bool $translate
     * @return $this
     */
    public function setLabel($label, $translate = true)
    {
        return parent::setLabel($translate ? $this->mapString($label) : $label);
    }

    /**
     * @return self
     */
    public function addChildren() {
        $m = new self($this->getTranslator());
        $this->children[] = $m;
        return $m;
    }

    private function mapString($string)
    {
        return $this->getTranslator()->trans($string);
    }
}
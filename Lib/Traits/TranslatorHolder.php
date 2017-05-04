<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Traits;

use Eulogix\Cool\Lib\Translation\TranslatorInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

trait TranslatorHolder {

    /**
     * @var TranslatorInterface
     */
    private $translator = null;

    /**
     * @param TranslatorInterface $translator
     * @return $this
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
        return $this;
    }

    /**
     * @return TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->translator;
    }

}
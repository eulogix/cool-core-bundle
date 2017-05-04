<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Form\Event;

use Eulogix\Cool\Lib\Form\FormInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class FormEvent extends Event
{
    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @param FormInterface $form
     */
    public function __construct(FormInterface $form)
    {
        $this->form = $form;
    }

    /**
     * @return formInterface
     */
    public function getForm()
    {
        return $this->form;
    }
}
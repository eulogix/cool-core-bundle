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

use Eulogix\Cool\Lib\Form\FormInterface;
use Eulogix\Cool\Lib\Traits\WidgetHolder;
use Eulogix\Cool\Lib\Widget\WidgetInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class TranslatorHelpProvider implements WidgetHelpProviderInterface
{

    use WidgetHolder;

    const PREFIX_FORM_FIELD = 'FIELD_HELP_';

    public function __construct(WidgetInterface $widget) {
        $this->setWidget($widget);
    }

    /**
     * @param array $parameters
     * @return array
     */
    public function getHelp(array $parameters)
    {
        $t = $this->getWidget()->getTranslator();

        if($this->getWidget() instanceof FormInterface) {
            if(isset($parameters['fieldName']))
                return $t->trans(self::PREFIX_FORM_FIELD.$parameters['fieldName']);
        }

        return '-';
    }
}
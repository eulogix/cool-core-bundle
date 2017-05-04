<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Email;


use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Traits\TranslatorHolder;
use Eulogix\Cool\Lib\Translation\Translator;

class MessageRenderer {

    const HASH_ALERT = 'hashAlert';

    use TranslatorHolder;

    public function __construct($translationDomain) {
        $this->setTranslator( Translator::fromDomain( $translationDomain ) );
    }

    /**
     * @param $items
     * @param string $headerHTML
     * @param array $tplVariables
     * @return string
     */
    public function hashAlert($items, $headerHTML = '', $tplVariables = []) {

        $body = $this->renderTemplate(self::HASH_ALERT, array_merge([
                    'header' => $headerHTML,
                    'items' => $items
                ], $tplVariables));

        return $body;

    }

    /**
     * @param $templatePath
     * @param $variables
     * @return string
     */
    public function renderTemplate($templatePath, $variables) {
        return Cool::getInstance()->getFactory()->getTwig()->render(
            $this->getTemplate( $templatePath ),
            $this->getTemplateVariables($variables)
        );
    }

    /**
     * @param $hash
     * @return array
     */
    private function getTemplateVariables($hash) {
        return array_merge([
                'coolTranslator' => $this->getTranslator(),
                'baseTemplate' => "EulogixCoolCoreBundle:emails:baseEmail.html.twig", //TODO this may be a container parameter
            ], $hash);
    }

    /**
     * @param $templatePath
     * @return string
     */
    private function getTemplate($templatePath) {
        return "EulogixCoolCoreBundle:emails:$templatePath.html.twig";
    }
}
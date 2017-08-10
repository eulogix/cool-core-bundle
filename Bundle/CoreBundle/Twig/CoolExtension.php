<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Twig;

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Translation\TranslatorInterface;
use Eulogix\Cool\Lib\Widget\Factory\SymfonyBundleWidgetFactory;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class CoolExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('t', array($this, 'widgetTranslate'), array('needs_context' => true)),

            new \Twig_SimpleFilter('evaluate', array($this, 'evaluate'), array(
                'needs_environment' => true,
                'needs_context' => true,
                'is_safe' => array(
                    'evaluate' => true
                )
            )),

            new \Twig_SimpleFilter('format_cash', array($this, 'formatCash')),
            new \Twig_SimpleFilter('format_date', array($this, 'formatDate')),
            new \Twig_SimpleFilter('inlineJson', array($this, 'formatInlineJson')),

        );
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('coolWidget', array($this, 'widget'), array('is_safe' => array('all')) )
        ];
    }

    public function widgetTranslate($context, $token, $parameters=[], TranslatorInterface $alternateTranslator = null)
    {
        $translator = $alternateTranslator ?? @$context['coolTranslator'];
        if($token !== null && $translator instanceOf TranslatorInterface) {
            return $translator->trans($token, $parameters);
        }
        return $token;
    }

    /**
     * @param $serverId
     * @param array $widgetParameters the parameters that the widget propagates at every request
     * @param array $parameters arbitrary array of parameters
     * @param array $dojoParameters passed to the constructor of the widget
     * @throws \Exception
     * @return string
     */
    public function widget($serverId, $widgetParameters=[], $parameters=[], $dojoParameters=[])
    {
        $f = new SymfonyBundleWidgetFactory( Cool::getInstance()->getContainer() );
        if($f && ($w = $f->getWidget($serverId, $widgetParameters))) {
                $w->setWidgetFactory($f);
                $w->reBuild();
            if(!$w->isDisabled()) {
                $definition = $w->getDefinition();
                $defResponse = $definition->getResponse();
                $jsonDefinition = json_encode( $defResponse );
                if($jsonDefinition === false)
                    throw new \Exception("JSON encode failure: ".json_last_error_msg());

                $containerId = @$parameters['containerId'] ? @$parameters['containerId'] : 'widget'.mt_rand();
                $containerStyle = @$parameters['containerStyle'];
                return "<div id=\"$containerId\" style=\"{$containerStyle}\"></div>
                    <script>

                        require([
                            'cool/cool',
                            'dojo/domReady!'
                        ], function(cool) {

                            //the small delay is here to ensure that dojo widgets don't get parsed twice when the html output is set at runtime to a contentpane
                            //(which also parses its content, together with scripts). So we wait until it has finished, and then instantiate the cool widgets

                            setTimeout(function() { cool.widgetFactory( '$serverId', ".json_encode($widgetParameters).", function(newWidget) {".
                (@$parameters['onlyContent'] ? 'newWidget.onlyContent = true;' : '').
                (@$parameters['onLoad'] ? $parameters['onLoad'] : '')
                ."newWidget.placeAt('$containerId');
                                    },
                                    null,
                                    $jsonDefinition,
                                    ".json_encode($dojoParameters)."
                            );}, 50);

                        });

                    </script>";
            } else return ''; //widget may be disabled
        }

        return "Cool Twig Extension, widget not found: $serverId";
    }

    /**
     * This function will evaluate $string through the $environment, and return its results.
     *
     * @param \Twig_Environment $environment
     * @param array $context
     * @param string $string
     * @return string
     */
    public function evaluate( \Twig_Environment $environment, $context, $string ) {
        $template = $environment->createTemplate($string);
        return $template->render($context);
    }

    public function getName()
    {
        return 'cool_extension';
    }

    public function formatCash($token, $parameters=[])
    {
        return number_format($token, 2, ',', '.');
    }

    public function formatDate($token, $parameters=[])
    {
        $value = $token instanceof \DateTimeInterface ? $token->getTimestamp() : strtotime($token);
        $formats = [
            'dmy'    => 'dmy',
            'hi'    => 'd/m/Y H:i',
            'his'   =>  'd/m/Y H:i:s'
        ];
        $format = @$parameters['format'] ? $parameters['format'] : 'his';
        return $value ? date($formats[$format], $value) : '';
    }

    public function formatInlineJson($token, $parameters=[])
    {
        return str_replace('"','\\"',$token);
    }
}
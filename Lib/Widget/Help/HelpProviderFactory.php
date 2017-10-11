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

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Help\HelpProviderInterface;
use Eulogix\Cool\Lib\Widget\WidgetInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class HelpProviderFactory implements HelpProviderFactoryInterface
{
    /**
     * @param WidgetInterface $widget
     * @return HelpProviderInterface
     */
    public function getHelperFor(WidgetInterface $widget) {
        if($widget->getWikiHelpPage())
            return $this->getWikimediaHelper($widget);
        return $this->getTranslatorHelper($widget);
    }

    /**
     * @param WidgetInterface $widget
     * @return TranslatorHelpProvider
     */
    protected function getTranslatorHelper(WidgetInterface $widget) {
        return new TranslatorHelpProvider($widget);
    }

    /**
     * @param WidgetInterface $widget
     * @return TranslatorHelpProvider
     */
    protected function getWikimediaHelper(WidgetInterface $widget) {
        return new WikimediaHelpProvider($widget, Cool::getInstance()->getFactory()->getAppHelpWiki());
    }
}
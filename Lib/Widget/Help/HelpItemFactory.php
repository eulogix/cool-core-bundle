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

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class HelpItemFactory {

    /**
     * @param string $page
     * @param string $title
     * @return HelpItem
     */
    public static function WikiPage($page, $title = null) {
        $wiki = Cool::getInstance()->getFactory()->getAppHelpWiki();
        $url = $wiki->getCompleteUrlForPage($page);
        $helpItem =  new SimpleHelpItem();
        $helpItem ->setDisplayMode(SimpleHelpItem::DISPLAY_BROWSER_TAB)
                  ->setContentType(SimpleHelpItem::CONTENT_TYPE_URL)
                  ->setContent($url)
                  ->setLabel($title ?? $page);

        return $helpItem;
    }

}
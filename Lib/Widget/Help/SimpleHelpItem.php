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

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class SimpleHelpItem extends HelpItem {

    const TYPE = 'SIMPLE';

    const CONTENT_TYPE_URL = 'URL';
    const CONTENT_TYPE_HTML = 'HTML';

    const DISPLAY_BROWSER_TAB = 'BROWSER_TAB';
    const DISPLAY_MODAL_POPUP = 'MODAL_POPUP';

    /**
     * @var string
     */
    protected $contentType, $displayMode, $content;

    /**
     * @return array
     */
    public function getDefinition()
    {
        return array_merge(parent::getDefinition(), [
            'type' => self::TYPE,
            'content_type' => $this->getContentType(),
            'content' => $this->getContent(),
            'display_mode' => $this->getDisplayMode()
        ]);
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     * @return SimpleHelpItem
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayMode()
    {
        return $this->displayMode;
    }

    /**
     * @param string $displayMode
     * @return SimpleHelpItem
     */
    public function setDisplayMode($displayMode)
    {
        $this->displayMode = $displayMode;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return SimpleHelpItem
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

}
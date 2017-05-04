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

use Swift_Message;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class MessageFactory {

    /**
     * @var MessageRenderer
     */
    private $renderer;

    /**
     * @var string
     */
    private $mailerFrom;

    public function __construct($mailerFrom, MessageRenderer $renderer) {
        $this->mailerFrom = $mailerFrom;
        $this->renderer = $renderer;
    }

    /**
     * @param array $items hash of key=>value properties summarized as a table in the email
     * @param string $headerHTML optional chunk of HTML that appears before the items table
     * @param array $tplVariables additional variables that can be used in the template
     * @return Swift_Message
     */
    public function hashAlert($items, $headerHTML = '', $tplVariables = []) {
        $body = $this->renderer->hashAlert($items, $headerHTML, $tplVariables);
        $msg = $this->htmlMessage($body);
        return $msg;
    }

    /**
     * base method, returns a message with the default settings
     * @return Swift_Message
     */
    public function message() {
        return \Swift_Message::newInstance()
            ->setFrom( $this->mailerFrom )
            ->setCharset('utf-8');
    }

    /**
     * base method, returns a message with the default settings
     * @param string $htmlBody
     * @param string $textBody
     * @return Swift_Message
     */
    public function htmlMessage($htmlBody, $textBody = null) {
        $msg = $this->message();
        $msg->addPart($htmlBody, 'text/html');
        $textBody = $textBody ? $textBody : 'email in HTML format';
        $msg->setBody( $textBody );
        return $msg;
    }

} 
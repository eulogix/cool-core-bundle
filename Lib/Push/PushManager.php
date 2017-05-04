<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Push;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserNotification;
use Eulogix\Cool\Lib\Cool;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class PushManager {

    const EVENT_JS_COMMAND = 'jsCommand';
    const EVENT_USER_NOTIFICATION = 'userNotification';
    const EVENT_CALL_CLOSED = 'callClosed';

    /**
     * @var string
     */
    private $wampServerBackendUrl;

    public function __construct($wampServerBackendUrl) {
        $this->wampServerBackendUrl = $wampServerBackendUrl;
    }

    /**
     * sends a js command that will get executed on the client of a specified user
     *
     * @param int $userId
     * @param string $jsCommand
     */
    public function pushJSCommand($userId, $jsCommand) {
        $this->pushEvent( $this->getUserChannel($userId), self::EVENT_JS_COMMAND, [
                'js' => $jsCommand
            ]);
    }

    /**
     * @param int $sessionId
     * @param array $properties
     */
    public function pushCallCloseEvent($sessionId, array $properties) {
        $this->pushEvent( $this->getSessionChannel($sessionId), self::EVENT_CALL_CLOSED, $properties);
    }

    /**
     * @param UserNotification $notification
     */
    public function pushUserNotification(UserNotification $notification) {
        $this->pushEvent( $this->getUserChannel($notification->getUserId()), self::EVENT_USER_NOTIFICATION, array_merge(
            [ 'context' => $notification->getContext() ],
            $notification->getNotificationDataArray()
        ));
    }

    /**
     * @param int $userId
     * @return string
     */
    protected function getUserChannel($userId) {
        $userId = $userId ? $userId : Cool::getInstance()->getLoggedUser()->getId();
        return 'user_'.$userId;
    }

    /**
     * @param string $sessionId
     * @return string
     */
    protected function getSessionChannel($sessionId) {
        $sessionId = $sessionId ? $sessionId : Cool::getInstance()->getFactory()->getSession()->getId();
        return 'session_'.$sessionId;
    }

    /**
     * @param string $eventChannel
     * @param string $eventType
     * @param array $eventData
     */
    protected function pushEvent($eventChannel, $eventType, array $eventData) {
        $context = new \ZMQContext();
        $socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'my socket');
        $socket->connect( $this->wampServerBackendUrl );

        $socket->send(json_encode([
            '_eventChannel' => $eventChannel,
            '_eventType'    => $eventType,
            '_eventData'    => $eventData
        ]));

        // Without this line, the script will wait forever after the exit statement
        $socket->setSockOpt(\ZMQ::SOCKOPT_LINGER, 1000);
        //$socket->disconnect($this->wampServerBackendUrl);
    }
} 
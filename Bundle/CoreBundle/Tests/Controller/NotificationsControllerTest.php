<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class NotificationsControllerTest extends WebTestCase
{

    private static function getClient() {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'demo',
        ]);
        return $client;
    }

    public function testSendUserNotification()
    {
        $client = self::getClient();
        $client->request('POST', '/cool/api/notifications/sendUserNotification',
            [
                'userId'  => 1,
                'message'  => 'amessage',
            ]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}

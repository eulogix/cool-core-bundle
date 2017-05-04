<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\om\BaseUserNotification;

class UserNotification extends BaseUserNotification
{

    public static function create($userId, $message, $context = null, $data = []) {
        $n = new UserNotification();

        $n  ->setUserId($userId)
            ->setNotification($message)
            ->setContext($context)
            ->setNotificationData(json_encode($data));

        $n->save();
        return $n;
    }

    public function getNotificationDataArray() {
        $ret = json_decode($this->getNotificationData(), true);
        return $ret ? $ret : [];
    }
}

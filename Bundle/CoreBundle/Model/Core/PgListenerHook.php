<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\om\BasePgListenerHook;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Lib\Database\Postgres\NotificationEvent;
use Eulogix\Lib\Database\Postgres\NotificationHookInterface;

class PgListenerHook extends BasePgListenerHook implements NotificationHookInterface
{
    /**
     * @param NotificationEvent $e
     * @return bool
     */
    public function mustExecute(NotificationEvent $e)
    {
        return preg_match($this->getChannelsRegex(), $e->getChannel());
    }

    /**
     * @param NotificationEvent $e
     */
    public function execute(NotificationEvent $e)
    {
        if($php = $this->getExecPhpCode()) {

            $wkContext = [
                'event' => $e,
                'payload' => json_decode( $e->getPayload(), true ),
                'cool' => Cool::getInstance(),
                'core' => $this->getCoolDatabase()
            ];

            evaluate_in_lambda($php, $wkContext);
        }
    }
}

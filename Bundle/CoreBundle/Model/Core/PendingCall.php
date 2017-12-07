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

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\om\BasePendingCall;
use Eulogix\Cool\Lib\File\FileProxyInterface;
use Eulogix\Lib\File\Proxy\SimpleFileProxy;
use Eulogix\Cool\Lib\Twilio\BasePhoneCall;

class PendingCall extends BasePendingCall
{
    /**
     * @return BasePhoneCall
     */
    public function getPhoneCall() {
        return BasePhoneCall::clientDeserialize( $this->getSerializedCall() );
    }

    /**
     * @param int $wait if set, waits up to $wait seconds before giving up
     * @return FileProxyInterface
     */
    public function getRecordingFile($wait = 30)
    {
        $recordingFile = null;

        if($url = $this->getRecordingUrl()) {
            $elapsed = 0;
            do {
                try {
                    $recordingFile = SimpleFileProxy::fromHTTPRemoteFile($url.'.mp3');
                } catch(\Exception $e) {
                    sleep(1);
                }
            } while(($recordingFile === null) && ($wait && $elapsed < $wait));
        }

        return $recordingFile;
    }
}

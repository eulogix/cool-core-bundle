<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Controller\Api;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserNotification;
use Eulogix\Cool\Lib\Security\CoolUser;
use Eulogix\Cool\Lib\Cool;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\Annotations\RequestParam;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class NotificationsController extends FOSRestController
{
    /**
     * @Route("sendUserNotification")
     * @Method({"POST"})
     *
     * @ApiDoc(
     *   description = "Publishes a new notification",
     *   statusCodes = {
     *     200 = "OK",
     *     400 = "Errors"
     *   }
     * )
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="userId", nullable=false, strict=true, description="The id of the target user")
     * @RequestParam(name="message", nullable=false, strict=true, description="Notification body")
     * @RequestParam(name="context", nullable=true, strict=true, description="An optional context string (could be the name of a multi tenant schema)")
     * @RequestParam(name="jsonData", nullable=true, strict=true, description="optional json object")
     *
     * @Rest\View()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sendUserNotificationAction(ParamFetcher $paramFetcher) {

        $userId = $paramFetcher->get('userId');
        $message = $paramFetcher->get('message');
        $context = $paramFetcher->get('context');
        $jsonData = $paramFetcher->get('jsonData');
        $decodedData = null;

        try {
            if(!CoolUser::fromId($userId))
                $this->returnError(404,'User not found');
            if($jsonData && ($decodedData = json_decode($jsonData)) === null)
                $this->returnError(404,'malformed jsonData argument');

            $pm = Cool::getInstance()->getFactory()->getPushManager();
            $pm->pushUserNotification( UserNotification::create($userId, $message, $context, $decodedData) );

        } catch(\Exception $e) {
            return $this->returnError(500, 'Internal error');
        }

        $view = $this->view(['message' => 'ok' ], 200);
        return $this->handleView($view);
    }

    /**
     * @param int $code
     * @param string $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function returnError($code, $message="")
    {
        if(!$message) {
            switch($code) {
                case 500: $message = "Internal error"; break;
                case 404: $message = "Resource not found"; break;
            }
        }

        $view = $this->view(
            [
                'statusCode' => $code,
                'errorMessage' => $message
            ],
            $code
        );

        return $this->handleView($view);
    }
}
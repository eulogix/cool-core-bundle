<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Symfony\Controller;

use FOS\RestBundle\Controller\FOSRestController;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

abstract class BaseRestController extends FOSRestController
{

    /**
     * @param int $code
     * @param string $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function returnError($code, $message="")
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
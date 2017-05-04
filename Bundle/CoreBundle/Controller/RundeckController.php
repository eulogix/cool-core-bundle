<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Controller;

use Eulogix\Lib\Rundeck\RundeckClient;
use Eulogix\Cool\Lib\Cool;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class RundeckController {

    /**
     * @Route("/getExecution/{executionId}", name="RDKgetExecution", options={"expose"=true})
     */
    public function getExecutionAction($executionId) {
        return new JsonResponse( $this->getClient()->getExecutionWithProgress($executionId) );
    }

    /**
     * @return RundeckClient
     */
    private function getClient() {
        return Cool::getInstance()->getFactory()->getRundeck();
    }
} 
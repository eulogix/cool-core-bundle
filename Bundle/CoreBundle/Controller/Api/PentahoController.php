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

use Eulogix\Cool\Bundle\CoreBundle\Command\Pentaho\RunJobCommand;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Symfony\Controller\BaseRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations as Rest;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\Annotations\RequestParam;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class PentahoController extends BaseRestController
{
    /**
     * @Route("runPDIJob")
     * @Method({"POST"})
     *
     * @ApiDoc(
     *   description = "Runs a PDI Job through the scheduler",
     *   statusCodes = {
     *     200 = "OK",
     *     400 = "Errors"
     *   }
     * )
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="jobName", nullable=false, strict=true, description="The name of the job to run")
     * @RequestParam(name="jobPath", nullable=false, strict=true, description="The path of the job to run")
     * @RequestParam(name="jobParametersJson", nullable=true, strict=true, description="optional json object")
     *
     * @Rest\View()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function runPDIJobAction(ParamFetcher $paramFetcher) {

        $jobName = $paramFetcher->get('jobName');
        $jobPath = $paramFetcher->get('jobPath');
        $jobParametersJson = $paramFetcher->get('jobParametersJson');

        if($jobParametersJson && (json_decode($jobParametersJson) === null))
            $this->returnError(500, 'malformed jobParametersJson argument');

        try {
            $rd = Cool::getInstance()->getFactory()->getRundeck();

            if($jobId = $rd->getJobIdByName($coolJobName = RunJobCommand::NAME)) {
                $ret = $rd->runJob($jobId, [
                    'job' => $jobName,
                    'job_path' => $jobPath,
                    'job_parameters_json' => $jobParametersJson ?? '[]'
                ]);
                $view = $this->view($ret, 200);
                return $this->handleView($view);
            } else $this->returnError(404, "Unable to find Rundeck job id for $coolJobName ");

        } catch(\Exception $e) {
            return $this->returnError(500, $e->getMessage());
        }
    }
}
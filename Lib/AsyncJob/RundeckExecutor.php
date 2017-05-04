<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\AsyncJob;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AsyncJob;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Lib\Rundeck\RundeckClient;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class RundeckExecutor extends BaseExecutor
{
    /**
     * @var RundeckClient;
     */
    private $rdClient;

    public function __construct(AsyncJob $job)
    {
        parent::__construct($job);
        $this->rdClient = Cool::getInstance()->getFactory()->getRundeck();
    }

    public function execute() {
        if($executions = $this->rdClient->runJob( $this->getJobId(), $this->getRDJobParameters() )) {
            $execution = array_pop($executions);
            $this->getJob()->setExecutionId($execution[ 'id' ])->save();
        }
    }

    private function getJobId() {
        return $this->rdClient->getJobIdByName( $this->getJob()->getJobPath() );
    }

    private function getRDJobParameters() {
        return array_merge($this->getJob()->getParametersArray(), [
            'async_job_id' => $this->getJob()->getAsyncJobId(),
            'env' => Cool::getInstance()->getContainer()->get('kernel')->getEnvironment()
        ]);
    }
}
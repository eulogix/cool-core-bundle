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

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\om\BaseAsyncJob;
use Eulogix\Cool\Lib\AsyncJob\BaseExecutor;
use Eulogix\Cool\Lib\AsyncJob\RundeckExecutor;

class AsyncJob extends BaseAsyncJob
{
    const EXECUTOR_RUNDECK = 'rundeck';

    const OUTCOME_SUCCESS = 'success';
    const OUTCOME_FAIL = 'fail';

    /**
     * @param bool $updateRecord
     */
    public function execute($updateRecord = true) {
        $executor = $this->getExecutor();
        $executor->execute($updateRecord);
    }

    /**
     * @param array $parameters
     * @return AsyncJob|void
     */
    public function setParametersArray(array $parameters) {
        return parent::setParameters(json_encode($parameters));
    }

    /**
     * @return array
     */
    public function getParametersArray() {
        $ret = json_decode($this->getParameters(), true);
        return $ret ? $ret : [];
    }
    
    /**
     * @param array $JobOutput
     * @return AsyncJob|void
     */
    public function setJobOutputArray(array $JobOutput) {
        return parent::setJobOutput(json_encode($JobOutput));
    }

    /**
     * @return array
     */
    public function getJobOutputArray() {
        $ret = json_decode($this->getJobOutput(), true);
        return $ret ? $ret : [];
    }

    /**
     * @return BaseExecutor
     */
    private function getExecutor()
    {
        switch($this->getExecutorType()) {
            case self::EXECUTOR_RUNDECK : return new RundeckExecutor($this);
        }
    }

}

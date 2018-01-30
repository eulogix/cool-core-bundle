<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Symfony\Console;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AsyncJob;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\AsyncJobQuery;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\UserNotification;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Push\PushManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class CoolCommand extends ContainerAwareCommand {

    /**
     * @var array
     */
    private $ValidValuesLists, $regexes;

    /**
     * @var string[]
     */
    private $multiValuedArguments = [];

    /**
     * @var AsyncJob
     */
    private $asyncJob;

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->addOption('async_job_id', null, InputOption::VALUE_OPTIONAL, "If set, ties the execution of this command to an async job id");
    }

    /**
     * if set, this command when exported in a scheduler such as Rundeck, will be executed as the specified user
     * @return string
     */
    public function getSchedulerCommandUser() {
        return null;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        if($asyncJobId = $input->getOption('async_job_id')) {
            $job = AsyncJobQuery::create()->findPk($asyncJobId);
            $job->setStartDate(new \DateTime());
            $job->save();
            $this->asyncJob = $job;
        }
    }

    /**
     * used to evaluate the % of execution
     * @param $percentage
     */
    public function outputProgressPercentage($percentage) {

        if($job = $this->getAsyncJob()) {
            $job->setCompletionPercentage($percentage);
            $job->save();
        }
        echo("[PROGRESS: ".floor($percentage)."%]\n");
    }

    /**
     * @param array|null $result
     * @throws \Exception
     * @throws \PropelException
     */
    public function signalSuccess(array $result = null) {
        $this->outputProgressPercentage(100);

        if($job = $this->getAsyncJob()) {
            $job->setOutcome(AsyncJob::OUTCOME_SUCCESS);
            $job->setJobOutputArray($result);
            $job->setCompletionDate(new \DateTime());
            $job->save();

            $this->getPushManager()->pushUserNotification( $this->getCompletedNotification() );
        }
    }

    /**
     * @param array|null $result
     * @throws \Exception
     * @throws \PropelException
     */
    public function signalFailure(array $result = null) {
        $this->outputProgressPercentage(100);

        if($job = $this->getAsyncJob()) {
            $job->setOutcome(AsyncJob::OUTCOME_FAIL);
            $job->setJobOutputArray($result);
            $job->setCompletionDate(new \DateTime());
            $job->save();

            $this->getPushManager()->pushUserNotification( $this->getCompletedNotification() );
        }
    }

    /**
     * @return UserNotification
     */
    protected function getCompletedNotification() {
        if($job = $this->getAsyncJob()) {
            return UserNotification::create($job->getIssuerUserId(), "Job Complete", $job->getContext(), [
                'async_job_id' => $job->getAsyncJobId(),
                'outcome' => $job->getOutcome(),
                'job_class' => get_class($this)
            ]);
        }
    }

    /**
     * @return AsyncJob
     */
    public function getAsyncJob()
    {
        return $this->asyncJob;
    }

    /**
     * @param string $argument
     * @param string[] $valueList
     * @return $this
     * @throws \Exception
     */
    public function setValidValues($argument, $valueList) {
        $this->argumentExists($argument);
        $this->ValidValuesLists[$argument] = $valueList;
        return $this;
    }

    /**
     * @param string $argument
     * @return bool|string[]
     */
    public function getValidValues($argument) {
        return isset($this->ValidValuesLists[$argument]) ? $this->ValidValuesLists[$argument] : false;
    }

    /**
     * @param string $argument
     * @param string $regex
     * @return $this
     */
    public function setRegex($argument, $regex) {
        $this->argumentExists($argument);
        $this->regexes[$argument] = $regex;
        return $this;
    }

    /**
     * @param string $argument
     * @param bool $val
     */
    public function setIsMultiValued($argument, $val=true) {
        $this->argumentExists($argument);
        $this->multiValuedArguments[$argument] = $val ? true : false;
    }

    /**
     * @param string $argument
     * @return bool
     */
    public function isMultiValued($argument) {
        $this->argumentExists($argument);
        return @$this->multiValuedArguments[$argument] ? true : false;
    }

    /**
     * @param string $argument
     * @return bool|string
     */
    public function getRegex($argument) {
        return isset($this->regexes[$argument]) ? $this->regexes[$argument] : false;
    }

    /**
     * @param InputInterface $input
     */
    public function validate(InputInterface $input) {
        $args = $input->getArguments();
        foreach($args as $argument => $argumentValue) {
            $this->validateArgument($argument, $argumentValue);
        }

        $args = $input->getOptions();
        foreach($args as $argument => $argumentValue) {
            $this->validateArgument($argument, $argumentValue);
        }
    }

    /**
     * @param string $argument
     * @param string $argumentValue
     * @return bool
     * @throws \Exception
     */
    public function validateArgument($argument, $argumentValue) {

        if(!$argumentValue)
            return true;

        if($vl = $this->getValidValues($argument)) {
            $values = explode(',',$argumentValue);
            foreach($values as $value)
                if(!in_array($value, $vl)) {
                    throw new \Exception("Bad value: $value for argument/option $argument. Valid values are: [".implode(',',$vl)."]");
                }
        }
        if($rx = $this->getRegex($argument)) {
            if(!preg_match("/$rx/sim", $argumentValue))
                throw new \Exception("Bad value: $argumentValue for argument/option $argument. Valid pattern is: $rx");
        }
        return true;
    }

    /**
     * @param string $argument
     * @return bool
     */
    private function argumentExists($argument) {
        return $this->getDefinition()->hasArgument($argument) || $this->getDefinition()->hasOption($argument);
    }

    /**
     * @return PushManager
     */
    protected function getPushManager()
    {
        return Cool::getInstance()->getFactory()->getPushManager();
    }

} 
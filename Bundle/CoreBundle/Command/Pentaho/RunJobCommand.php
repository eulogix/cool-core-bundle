<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Command\Pentaho;

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Symfony\Console\CoolCommand;
use Eulogix\Lib\Pentaho\ConsolePDIConnector;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class RunJobCommand extends CoolCommand
{
    const NAME = 'cool:pentaho:runjob';

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->addArgument('job', InputArgument::REQUIRED, 'The name of the job to run')
            ->addArgument('job_path', InputArgument::OPTIONAL, '(optional) The path of the job to run')
            ->addArgument('repository_name', InputArgument::OPTIONAL, '(optional) The name of the kettle repository to use')
            ->addOption('user', null, InputOption::VALUE_OPTIONAL, "(optional) the repo username")
            ->addOption('password', null, InputOption::VALUE_OPTIONAL, "(optional) the repo password")
            ->addOption('job_parameters_json', null, InputOption::VALUE_OPTIONAL, "(optional) JSON object used to feed the job options")

            ->setDescription('Wraps the call to kitchen in a command, typically used by Rundeck for async execution of kettle jobs');
    }

    /**
     * if set, this command when exported in a scheduler such as Rundeck, will be executed as the specified user
     * @return string
     */
    public function getSchedulerCommandUser() {
        return $this->getContainer()->getParameter('pdi_command_user');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->validate($input);

        $job = $input->getArgument('job');
        $jobPath = $input->getArgument('job_path');
        $repositoryName = $input->getArgument('repository_name');
        $user = $input->getOption('user');
        $password = $input->getOption('password');
        $jobParamsJSON = $input->getOption('job_parameters_json');

        $pdi = Cool::getInstance()->getFactory()->getPDIConnector();

        if($user)
            $pdi->setRepositoryUser($user);
        if($password)
            $pdi->setRepositoryPassword($password);
        if($repositoryName)
            $pdi->setRepositoryName($repositoryName);

        $jobParams = $jobParamsJSON ? json_decode($jobParamsJSON, true) : [];

        $executionResult = $pdi->runJob($job, $jobPath ?? ConsolePDIConnector::DEFAULT_JOB_PATH, $jobParams);

        $output->write($executionResult->getOutput());

        return $executionResult->isSuccess()? 0 : 1;
    }

}
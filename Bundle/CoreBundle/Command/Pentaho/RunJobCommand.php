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
use Eulogix\Lib\Pentaho\PDIConnector;
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
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('cool:pentaho:runjob')
            ->addArgument('job', InputArgument::REQUIRED, 'The name of the job to run')
            ->addArgument('job_path', InputArgument::OPTIONAL, '(optional) The path of the job to run')
            ->addArgument('repository_name', InputArgument::OPTIONAL, '(optional) The name of the kettle repository to use')
            ->addOption('user', null, InputOption::VALUE_OPTIONAL, "(optional) the repo username")
            ->addOption('password', null, InputOption::VALUE_OPTIONAL, "(optional) the repo password")
            ->addOption('job_parameters_json', null, InputOption::VALUE_OPTIONAL, "(optional) JSON object used to feed the job options")

            ->setDescription('Wraps the call to kitchen in a command, typically used by Rundeck for async execution of kettle jobs');
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

        $c = Cool::getInstance()->getFactory()->getPDIConnector();

        if($user)
            $c->setUser($user);
        if($password)
            $c->setPassword($password);
        if($repositoryName)
            $c->setRepositoryName($repositoryName);

        $jobParams = $jobParamsJSON ? json_decode($jobParamsJSON, true) : [];

        $commandOutput = $c->runJob($job, $jobPath ?? PDIConnector::DEFAULT_JOB_PATH, $jobParams);

        $output->write($commandOutput);
    }

}
<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Command\Rundeck;

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Symfony\Console\CoolCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class ExportJobsCommand extends CoolCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('cool:rundeck:exportjobs')
            ->addArgument('project', InputArgument::REQUIRED, 'The RunDeck project in which to export commands')
            ->addArgument('namespace', InputArgument::OPTIONAL, 'The Namespace in which to search commands')
            ->addOption('simulate', null, InputOption::VALUE_NONE, "if set, nothing is done")
            ->setDescription('Uploads Symfony Command definitions to a Rundeck instance');
    }

    /**
     * this method is called only before an execution is actually issued.
     * If instructions that interact with external systems, or perform database queries, are left in the configure()
     * method, they will be called in many situations in which the system merely scans for commands, slowing down
     * command execution and listing.
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $validValues = [];
        $projects = Cool::getInstance()->getFactory()->getRundeck()->getProjects();
        foreach($projects as $project)
            $validValues[] = $project['name'];
        $this->setValidValues('project', $validValues);
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->validate($input);

        $rd = Cool::getInstance()->getFactory()->getRundeck();
        $utils = Cool::getInstance()->getFactory()->getRundeckSymfonyUtils();
        $simulate = $input->getOption('simulate');

        $defaultCommandUser = $this->getContainer()->getParameter('rundeck_command_user');

        $counter = 0;
        $commands = $this->getApplication()->all($input->getArgument('namespace'));
        foreach($commands as $command) {

            if($command instanceof CoolCommand && ($customUser = $command->getSchedulerCommandUser()))
                $utils->setCommandUser($customUser);
            else $utils->setCommandUser($defaultCommandUser);

            $j = $utils->getRundeckJob( $command );

            $levels = explode(':',$command->getName());
            array_pop($levels);
            $j->setGroup(implode('/',$levels));
            $j->setProject($input->getArgument('project'));
            $j->setMultipleExecutions(true);

            if(!$simulate) {
                $rdOutput = $rd->importJobs($j->getXML(true));
            }
            $output->writeln(sprintf('<comment>%s</comment> OK', $j->getName()));
            $counter++;
        }

        $utils->setCommandUser($defaultCommandUser);

        $output->writeln('');
        $output->writeln(sprintf('<comment>%s</comment> Commands successfully imported', $counter));
        if($simulate)
            $output->writeln(sprintf('<comment>SIMULATION!</comment>'));
    }

}
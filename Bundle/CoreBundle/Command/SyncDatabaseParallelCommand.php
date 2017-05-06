<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\ArrayInput;

use Eulogix\Cool\Lib\Cool;

/**
 * syncing multi tenant schemas in parallel yelds better performance than doing so sequentially, up to a point.
 * The sweet spot seems to be around 8 workers
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class SyncDatabaseParallelCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cool:sync-database-parallel')
            ->setDescription('Sync database structures in parallel using Rundeck')
            ->addOption('connection', null, InputOption::VALUE_REQUIRED, 'the connection name to sync, normally cool_db', 'cool_db')
            ->addOption('schema', null, InputOption::VALUE_REQUIRED, 'the schema (class) to sync')
            ->setHelp(<<<EOF
The <info>%command.name%</info> Automatically migrates database structures for the given connection
<info>php %command.full_name%</info>
EOF
            );
    }
    
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $actualStartDate = new \DateTime();

        $connectionName = $input->getOption('connection');
        $schemaName = $input->getOption('schema');

        $db = Cool::getInstance()->getSchema($schemaName);
        $rd = Cool::getInstance()->getFactory()->getRundeck();
        if($jobId = $rd->getJobIdByName('cool:sync-database')) {
            $executions = [];
            $pendingExecutions = [];

            $schemas = $db->getSiblingSchemas();
            foreach($schemas as $schema) {
                $output->writeln("Launching parallel processing on schema $schema...");

                $pendingExecutions[$schema] = 1;
                $executions[$schema] = $rd->runJob(
                    $jobId,
                    [
                        'connection' => $connectionName,
                        'schema' => $schemaName,
                        'actual_schema' => $schema,
                        'env' => 'prod'
                    ]);
            }

            $totalSeconds = 0;
            do {
                foreach ($pendingExecutions as $schemaKey => $fake) {
                    $execution = $executions[$schemaKey];
                    $exec = array_slice($execution, -1)[0];
                    $updatedExecWhole = $rd->getExecution($exec[ 'id' ]);
                    $updatedExec = array_pop($updatedExecWhole);
                    if ($updatedExec[ 'status' ] != 'running') {
                        $startDate = new \DateTime($updatedExec['date-started']);
                        $endDate = new \DateTime($updatedExec['date-ended']);
                        $seconds = $endDate->getTimestamp() - $startDate->getTimestamp();
                        $totalSeconds += $seconds;
                        $output->writeln($rd->getExecutionOutput($exec[ 'id' ]));
                        $output->writeln("$schemaKey processed in $seconds seconds.");
                        unset( $pendingExecutions[ $schemaKey ] );
                    }
                }
                sleep(1);
            } while(!empty($pendingExecutions));

            $actualEndDate = new \DateTime();
            $actualSeconds = $actualEndDate->getTimestamp() - $actualStartDate->getTimestamp();

            $output->writeln("");
            $output->writeln("Total processing time (cumulative): $totalSeconds seconds.");
            $output->writeln("Total processing time (actual): $actualSeconds seconds.");
            $output->writeln("");

        } else throw new \Exception("Project ".$rd->getProject()." does not contain the cool:sync-database command.");
    }
}

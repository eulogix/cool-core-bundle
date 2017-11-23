<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Command\Database;

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Symfony\Console\CoolCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class CloneSchemaCommand extends CoolCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cool:database:cloneSchema')
            ->setDescription('clones a schema')
            ->addArgument('schema', null, InputArgument::REQUIRED, "The concrete schema name to clone")
            ->addArgument('new_schema', null, InputArgument::REQUIRED, "The target schema")
            ->setHelp(<<<EOF
EOF
            );

    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $schema = $input->getArgument('schema');
        $newSchema = $input->getArgument('new_schema');

        $db = Cool::getInstance()->getCoreSchema();
        $p = $db->getConnectionParameters();

        $host = $p['dsn_info']['host'];
        $port = $p['dsn_info']['port'];
        $database = $p['dsn_info']['dbname'];
        $user = $this->getContainer()->getParameter('cool_postgres_superuser');

        $pgDumpLine = "pg_dump --host=\"{$host}\" --port=\"{$port}\" --username=\"$user\" --dbname=\"$database\"";
        $pgSQLLine = "psql --host=\"{$host}\" --port=\"{$port}\" --username=\"$user\" --dbname=\"$database\"";

        $targetFile = tempnam(Cool::getInstance()->getFactory()->getSettingsManager()->getTempFolder(),'SQL');

        $cmds =[
            "{$pgDumpLine} -n {$schema}  -f \"{$targetFile}_h1\"",
            "sed 's/{$schema}/{$newSchema}/g' \"{$targetFile}_h1\" > \"{$targetFile}\"",
            "{$pgSQLLine} -c 'DROP SCHEMA IF EXISTS {$newSchema} CASCADE'",
            "{$pgSQLLine} -f \"$targetFile\"",
        ];

        foreach($cmds as $cmd) {
            $output->writeln($cmd);
            shell_exec($cmd);
        }

        @unlink($targetFile);
    }
}

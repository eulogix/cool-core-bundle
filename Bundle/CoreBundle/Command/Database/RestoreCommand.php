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

class RestoreCommand extends CoolCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cool:database:restore')
            ->setDescription('restores a schema')
            ->addArgument('input_file', InputArgument::REQUIRED, "The name of the backup file")
            ->addOption('schema', null, InputOption::VALUE_OPTIONAL, "The concrete schema name(s) to restore, comma separated list")
            ->setHelp(<<<EOF
EOF
            );

    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $schemas = explode(',',$input->getOption('schema'));
        $file = $input->getArgument('input_file');
        $databaseName = Cool::getInstance()->getCoreSchema()->getDatabaseName();
        $userName = $this->getContainer()->getParameter('cool_postgres_superuser');

        $cmd = "pg_restore  --dbname=$databaseName";
        foreach($schemas as $schema) {

            Cool::getInstance()->getCoreSchema()->query("DROP SCHEMA IF EXISTS $schema CASCADE;");
            Cool::getInstance()->getCoreSchema()->query("DROP SCHEMA IF EXISTS {$schema}_audit CASCADE;");
            Cool::getInstance()->getCoreSchema()->query("CREATE SCHEMA $schema;");

            $cmd.=" --schema=\"$schema\"";
        }

        $cmd.=" --disable-triggers --username=$userName \"$file\" ";

        shell_exec($cmd);
    }
}

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

class DumpCommand extends CoolCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cool:database:dump')
            ->setDescription('dumps a schema')
            ->addArgument('output_file', InputArgument::REQUIRED, "The name of the output file")
            ->addOption('schema', null, InputOption::VALUE_OPTIONAL, "The concrete schema name(s) to dump, comma separated list")
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
        $file = $input->getArgument('output_file');
        $databaseName = Cool::getInstance()->getCoreSchema()->getDatabaseName();
        $userName = $this->getContainer()->getParameter('cool_postgres_superuser');

        $cmd = "pg_dump";
        foreach($schemas as $schema)
            $cmd.=" --schema=\"$schema\"";

        $cmd.=" --clean --username=$userName --format=custom --file=\"$file\" $databaseName";

        shell_exec($cmd);
    }
}

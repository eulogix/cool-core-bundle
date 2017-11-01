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

class RefreshMaterializedViewCommand extends CoolCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cool:database:refreshMaterializedView')
            ->setDescription('refreshes a materialized view in a schema')
            ->addArgument('schema_name', InputArgument::REQUIRED, "The name of the schema")
            ->addArgument('actual_schema_name', InputArgument::REQUIRED, "The name of the ACTUAL schema")
            ->addArgument('view_name', InputArgument::REQUIRED, "The name of the materialized view to refresh")
            ->setHelp(<<<EOF
EOF
            );

    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $schema = $input->getArgument('schema_name');
        $actualSchema = $input->getArgument('actual_schema_name');
        $view = $input->getArgument('view_name');

        Cool::getInstance()->getSchema($schema)->refreshMaterializedView($view, $actualSchema);
    }
}

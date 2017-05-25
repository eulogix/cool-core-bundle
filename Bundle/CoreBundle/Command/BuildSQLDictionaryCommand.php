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

use Eulogix\Cool\Lib\Builders\Lookups\GlobalLookupFunctionsBuilder;
use Eulogix\Cool\Lib\Symfony\Console\CoolCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class BuildSQLDictionaryCommand extends CoolCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cool:build-sql-dictionary')
            ->addArgument('bundle', InputArgument::REQUIRED, 'The bundle in which the generated SQL files will be saved')
            ->addArgument('schema', InputArgument::REQUIRED, 'The schema in which the SQL files will be saved (in the post_sync folder)')
            ->setDescription('Rebuild SQL dictionaries')
            ->setHelp("");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bundleName = $input->getArgument('bundle');
        $schemaName = $input->getArgument('schema');

        $kernel = $this->getContainer()->get('kernel');
        $bundles = $kernel->getBundles();

        /**
         * @var BundleInterface $bundle
         */
        $bundle = $bundles[ $bundleName ];

        $targetFolder = $bundle->getPath().'/Resources/databases/'.$schemaName.'/sql/post_sync';

        if(!file_exists($targetFolder)) {
            throw new \Exception("Folder $targetFolder does not exist.");
        }

        $glBuilder = new GlobalLookupFunctionsBuilder();
        file_put_contents($targetFolder.'/150_auto_lookup_domain_function.sql', $glBuilder->getLookupDomainFunction());
    }
}

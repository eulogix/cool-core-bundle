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

use Eulogix\Cool\Lib\Symfony\Bundle\BundleUtils;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\ArrayInput;

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Database\Postgres\Differ;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * - computes and applies calculated differences between the master schema and the current one(s)
 * - applies all the additional sql files defined in all the bundles
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class SyncDatabaseCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cool:sync-database')
            ->setDescription('Sync database structures (migrations)')
            ->addOption('connection', null, InputOption::VALUE_REQUIRED, 'the connection name to sync, normally cool_db', 'cool_db')
            ->addOption('schema', null, InputOption::VALUE_REQUIRED, 'the schema (class) to sync')
            ->addOption('actual_schema', null, InputOption::VALUE_OPTIONAL, 'the actual schema to sync')
            ->setHelp(<<<EOF
The <info>%command.name%</info> Automatically migrates database structures for the given connection
<info>php %command.full_name%</info>
EOF
            );
    }
    
    
    protected function dumpDifferErrors( $differ, OutputInterface $output ) {
        $lastErrors = $differ->lastErrors;
        $output->writeln("FAIL");
        $output->writeln("\n* ERRORS:\n");
        $output->writeln(@$lastErrors['errors']);
        $output->writeln("\n* NOTES:\n");
        $output->writeln(@$lastErrors['notes']);
        /*$output->writeln("\n* RAW OUTPUT:\n");
        $output->writeln(@$lastErrors['rawOutput']);*/
    }


    protected function getSqlFilesFromBundle($bundleName, $schemaName) {
        $bundleAdditionalFiles = BundleUtils::getFiles( $bundleName, 'Resources/databases/' . $schemaName . '/sql', '*.sql' );
        $bundleAdditionalPreSyncFiles = BundleUtils::getFiles( $bundleName, 'Resources/databases/' . $schemaName . '/sql/pre_sync', '*.sql' );
        $bundleAdditionalPostSyncFiles = BundleUtils::getFiles( $bundleName, 'Resources/databases/' . $schemaName . '/sql/post_sync', '*.sql' );
        sort($bundleAdditionalFiles, SORT_STRING);
        sort($bundleAdditionalPreSyncFiles, SORT_STRING);
        sort($bundleAdditionalPostSyncFiles, SORT_STRING);

        return [
            'sqlFiles' => $bundleAdditionalFiles,
            'preSyncSql' => $bundleAdditionalPreSyncFiles,
            'postSyncSql' => $bundleAdditionalPostSyncFiles,
            'count' => count($bundleAdditionalFiles) + count($bundleAdditionalPreSyncFiles) + count($bundleAdditionalPostSyncFiles)
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //remember: http://www.postgresql.org/docs/9.1/static/libpq-pgpass.html
        $connectionName = $input->getOption('connection');
        $schemaName = $input->getOption('schema');
        $actualSchemaName = $input->getOption('actual_schema');

        $schema = Cool::getInstance()->getSchema($schemaName);

        $p = $schema->getConnectionParameters();
        $host = $p['dsn_info']['host'];
        $port = $p['dsn_info']['port'];
        $database = $p['dsn_info']['dbname'];
        $appUser = $p['user']; //passed to enable permission creation of audit schemas and in general, of new database objects
        $user = $this->getContainer()->getParameter('cool_postgres_superuser');

        $appRoot = $this->getContainer()->get('kernel')->getRootDir();

        $propelSQLFile = "$appRoot/propel/sql/$connectionName.sql";

        $sqlFiles = [];
        $preSyncSql = [];
        $postSyncSql = [];

        /**
         * fetch all bundles and add the customization sql files for current database, if any
         * these script are executed in bundle order first, then sorted alphabetically
         */
        $bundles = $this->getContainer()->getParameter('kernel.bundles');
        foreach($bundles as $bundleName => $bundleClass) {

            $bundleFiles = $this->getSqlFilesFromBundle($bundleName, $schemaName);

            if($bundleFiles['count'] > 0) {
                $sqlFiles = array_merge($sqlFiles, $bundleFiles['sqlFiles']);
                $preSyncSql = array_merge($preSyncSql, $bundleFiles['preSyncSql']);
                $postSyncSql = array_merge($postSyncSql, $bundleFiles['postSyncSql']);
                $output->writeln("{$bundleFiles['count']} files found in bundle $bundleName");
            }

            if($attachedSchemas = $schema->getAttachedSchemas()) {
                foreach($attachedSchemas as $attachedSchema) {
                    $bundleFiles = $this->getSqlFilesFromBundle($bundleName, $attachedSchema);

                    if($bundleFiles['count'] > 0) {
                        $sqlFiles = array_merge($sqlFiles, $bundleFiles['sqlFiles']);
                        $preSyncSql = array_merge($preSyncSql, $bundleFiles['preSyncSql']);
                        $postSyncSql = array_merge($postSyncSql, $bundleFiles['postSyncSql']);
                        $output->writeln("{$bundleFiles['count']} files found in bundle $bundleName for attached schema $attachedSchema");
                    }
                }
            }
        }

        //then we add the main propel database definition on top of the list
        array_unshift($sqlFiles, $propelSQLFile);

        //fetch complementary schemas
        $complementarySchemas = ['lookups', 'public'];
        $sn = Cool::getInstance()->getAvailableSchemaNames();
        foreach($sn as $availSchema) {
            if(!Cool::getInstance()->getSchema($availSchema)->isMultiTenant()) {
                $complementarySchemas[] = $availSchema;
                $complementarySchemas[] = Cool::getInstance()->getSchema($availSchema)->getCurrentAuditSchemaName();
            }
        }

        $schemas = $schema->getSiblingSchemas();
        foreach($schemas as $siblingSchema)
            if(!$actualSchemaName || $siblingSchema == $actualSchemaName) {
                $output->writeln("Processing schema $siblingSchema...");

                if(true) {
                    $d = new Differ(
                        $host, $port, $database, $user, $appUser, $siblingSchema,
                        $schema->getAuditSchemaNameForSibling($siblingSchema),
                        $complementarySchemas,
                        $schema->isMultiTenant(),
                        $sqlFiles,
                        $preSyncSql,
                        $postSyncSql,
                        $input, $output);

                    $d->setApgDiffJar("$appRoot/cool/bin/apgdiff-2.4/apgdiff-2.4.jar");

                    if($diffScript = $d->getDiffScript()) {
                        if($d->hasErrors) {
                            $output->writeln("Error while generating diff script");
                            $this->dumpDifferErrors($d, $output);
                        } else {
                            $output->writeln("OK");
                            $output->write("Some differences were found, applying sync script...");

                            $ret = $d->applyDiff($diffScript);

                            if($ret) {
                                $output->writeln("OK");
                            } elseif($d->hasErrors) {
                                $output->writeln("Error while applying diff script");
                                $this->dumpDifferErrors($d, $output);
                            }
                        }
                    } elseif($d->hasErrors) {
                        $output->writeln("Error while generating diff script (2)");
                        $this->dumpDifferErrors($d, $output);
                    } else {
                        $output->writeln("No differences found. Skipping sync");
                        //TODO: maybe an option?
                        //$d->applyPostScripts();
                    }
                }
            }

    }
}

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

use Eulogix\Cool\Lib\Builders\DictionaryBuilder;
use Eulogix\Cool\Lib\Symfony\Bundle\BundleUtils;
use Propel\Bundle\PropelBundle\Command\GeneratorAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class BuildDictionaryCommand extends GeneratorAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cool:build-dictionary')
            ->setDescription('Rebuild dictionaries')
            ->addArgument('bundle', InputArgument::REQUIRED, 'The bundle in which the propel schemas reside')
            ->addArgument('operation', InputArgument::REQUIRED, 'operation can be EXTRACT_SETTINGS or BUILD_DICTIONARY')
            ->setHelp(<<<EOF
The <info>%command.name%</info> updates dictionary files according to propel xml schemas
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', "-1");

        if ($dirs = BundleUtils::getCoolProjectDirs($this->bundle)) {
            foreach ($dirs as $projectDir) {

                /**
                 * @var \SplFileInfo $projectDir
                 */
                $builder = new DictionaryBuilder($this->bundle, $projectDir->getFileName(), $projectDir->getRealPath());

                switch($op = $input->getArgument('operation')) {
                    case 'EXTRACT_SETTINGS' : {
                        //1. extract static settings from augmented propel schema, returns the clean schema and stores the extracted settings in the project dir
                        $builder->extractSettingsAndSaveCleanPropelSchema();
                        break;
                    }
                    case 'BUILD_DICTIONARY' : {
                        $builder->build( $this->getContainer()->get('templating') );
                        break;
                    }
                    default: {
                        $output->writeln(sprintf('Bad value <comment>%s</comment> for argument <comment>operation</comment>', $op));
                    }
                }
            }
        } else {
            $output->writeln(sprintf('No <comment>*schemas.xml</comment> files found in bundle <comment>%s</comment>.', $this->bundle->getName()));
        }
    }

}

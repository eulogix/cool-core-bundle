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

use Propel\Bundle\PropelBundle\Command\GeneratorAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Eulogix\Cool\Lib\Builders\DictionaryBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Console\Input\ArrayInput;

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

<info>php %command.full_name%</info>

dsad

<info>php %command.full_name%</info>
EOF
            );
    }

    
    
    protected function executeCommand($command,$output) {
        $command = $this->getApplication()->find($command);
        $arguments = array(
            'command'=>$command,
            //'--force' => true
            ''
        );
        $input = new ArrayInput($arguments);
        
        $returnCode = $command->run($input, $output);

        return $returnCode;
    }
    
    
    /**
     * @param  \SplFileInfo    $file
     * @param  BundleInterface $bundle
     * @return string
     */
    protected function toBundleResRelativePath(\SplFileInfo $file, BundleInterface $bundle)
    {
        $relP = 
        str_replace(DIRECTORY_SEPARATOR, '/',
            str_replace(
                $bundle->getPath(). DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR,
                '',
                 $file->getRealPath()
            )
        );

        return sprintf('@%s/Resources/%s', $bundle->getName(), $relP);
    }

    /**
     * @param \Symfony\Component\HttpKernel\Bundle\BundleInterface
     * @return \Symfony\Component\Finder\Finder
     */
    protected function getCoolProjectDirs(BundleInterface $bundle)
    {
        if (is_dir($dir = $bundle->getPath().'/Resources/databases')) {
            $finder  = new Finder();
            return $finder->directories()->depth(0)->in($dir);

        }    
    }

    /**
     * returns the base src folder e.g /path/to/your/webapp/src
     * @return string
     */
    protected function getBaseSrcFolder() {
        $parts  = explode(DIRECTORY_SEPARATOR, realpath($this->bundle->getPath()));
        $length = count(explode('\\', $this->bundle->getNamespace())) * (-1);
        $baseSrcFolder = implode(DIRECTORY_SEPARATOR, array_slice($parts, 0, $length));
        return $baseSrcFolder;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', "-1");

        if ($dirs = $this->getCoolProjectDirs($this->bundle)) {
            foreach ($dirs as $projectDir) {

                $locatablePjDir = $this->toBundleResRelativePath(new \SplFileInfo($projectDir), $this->bundle);
                $stringPjDir = $this->getContainer()->get('file_locator')->locate($locatablePjDir);

                $b = new DictionaryBuilder($stringPjDir, $locatablePjDir);

                switch($op = $input->getArgument('operation')) {
                    case 'EXTRACT_SETTINGS' : {
                        //1. extract static settings from augmented propel schema, returns the clean schema and stores the extracted settings in the project dir
                        $targetCleanSchema = $this->bundle->getPath().'/Resources/config/'.$projectDir->getFileName().'_schema.xml';
                        $b->extractSettingsFromSchema($targetCleanSchema);
                        break;
                    }
                    case 'BUILD_DICTIONARY' : {
                        $settings = $b->retrieveSettings();
                        $target_folder = str_replace("\\",DIRECTORY_SEPARATOR,$this->getBaseSrcFolder()."\\".$settings['namespace']);
                        $b->build($projectDir->getFileName(), $settings, $target_folder, $this->getContainer()->get('templating') );
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

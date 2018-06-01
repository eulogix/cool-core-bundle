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

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\CodeSnippetQuery;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\CodeSnippetVariableQuery;
use Eulogix\Cool\Lib\Builders\SnippetExtractor;
use Eulogix\Cool\Lib\Symfony\Console\CoolCommand;
use Eulogix\Cool\Lib\Util\ReflectionUtils;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class ImportSnippetsCommand extends CoolCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cool:import-snippets')
            ->setDescription('Imports code snippets from registered bundles')
            ->addOption('bundles', null, InputOption::VALUE_OPTIONAL, 'if set, only the bundles specified in this comma separated list will be processed')
            ->setHelp("");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $kernel = $this->getContainer()->get('kernel');
        $allBundles = $kernel->getBundles();

        $bundles = $input->getOption('bundles');
        $bundles = $bundles ? explode(',',$bundles) : null;

        /** @var BundleInterface $bundle */
        foreach($allBundles as $bundle) {
            $output->writeln("Processing bundle {$bundle->getName()}...");
            if(!$bundles || in_array($bundle->getName(), $bundles)) {
                $snippetsFolder = $bundle->getPath().'/Resources/snippets';
                if(file_exists($snippetsFolder)) {
                    $output->writeln("\n\tImporting from  Bundle <info>{$bundle->getName()}</info>...\n");
                    $this->importFromFolder($snippetsFolder, $output);
                }
            }
        }
    }

    private function importFromFolder($snippetsFolder, OutputInterface $output)
    {
        $classes = ReflectionUtils::getClassesInFolder($snippetsFolder);

        foreach($classes as $FQNClassName) {
            $snippets = SnippetExtractor::getFromClass( $FQNClassName );

            foreach($snippets as $snippet) {

                $snippetLogString = "<info>{$snippet->getNspace()}</info>::<comment>{$snippet->getName()}</comment>";
                if($existingSnippet = CodeSnippetQuery::create()->filterByNspace($snippet->getNspace())->filterByName($snippet->getName())->findOne()) {
                    if(!$existingSnippet->getLockUpdatesFlag()) {
                        $output->writeln("<info>*</info> Snippet {$snippetLogString} Already exists, will be updated</info>");

                        $existingSnippet->setReturnType( $snippet->getReturnType() )
                            ->setDescription( $snippet->getDescription() )
                            ->setLongDescription( $snippet->getLongDescription() )
                            ->setLanguage( $snippet->getLanguage() )
                            ->setCategory( $snippet->getCategory() )
                            ->setType( $snippet->getType() )
                            ->setSnippet( $snippet->getSnippet() );

                        foreach($snippet->getCodeSnippetVariables() as $variable) {
                            if($existingVariable = CodeSnippetVariableQuery::create()->filterByCodeSnippetId($existingSnippet->getCodeSnippetId())->filterByName($variable->getName())->findOne()) {
                                $output->writeln("    <comment>*</comment> Updating existing Variable <comment>{$existingVariable->getName()}</comment>");
                                $existingVariable->setDescription( $variable->getDescription() );
                                $existingVariable->save();
                            } else {
                                $output->writeln("    <info>+</info> Inserting new variable <info>{$variable->getName()}</info>");
                                $variable->setCodeSnippet($existingSnippet);
                            }
                        }

                        $existingSnippet->save();
                    } else {
                        $output->writeln("<error>!</error> Snippet {$snippetLogString} Already exists, but is marked as <error>LOCKED</error>. Skipping");
                    }
                } else {
                    $output->writeln("<info>+</info> Inserting new snippet {$snippetLogString}");
                    $snippet->save();
                }
            }
        }
    }

}

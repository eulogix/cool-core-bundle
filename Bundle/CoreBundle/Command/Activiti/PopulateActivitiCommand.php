<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Command\Activiti;

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Symfony\Console\CoolCommand;
use Eulogix\Lib\Activiti\om\ProcessDefinition;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Finder\Finder;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class PopulateActivitiCommand extends CoolCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('cool:activiti:populate')
            ->setDescription('populates (and optionally initializes) an activiti instance')
            ->addArgument('dir', InputArgument::REQUIRED, 'the directory (or bundle location) that contains the bpmn files')
            ->addOption('category', null, InputOption::VALUE_OPTIONAL, 'optional category string which will be prepended to the process categories')
            ->addOption('nopreview', null, InputOption::VALUE_OPTIONAL, 'if not set, the command will output a preview');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->validate($input);

        $perform = $input->getOption('nopreview') == 1;
        $location = $input->getArgument('dir');
        $category = $input->getOption('category');
        $tenant = $this->getContainer()->getParameter('activiti_tenant_id');
        $baseCategory = $tenant.($category ? '/'.$category : '');

        $output->writeln("\nBase tenant for all imports is: <comment>{$tenant}</comment>\n");

        $ac = Cool::getInstance()->getFactory()->getActiviti();
        $output->writeln("Activiti engine stats:");
        print_r($ac->getEngineProperties());

        $locator = Cool::getInstance()->getFactory()->getFileLocator();
        $finder = new Finder();

        $iterator = $finder ->files()
                            ->name('*.bpmn')
                            ->in($locator->locate($location));

        /** @var $file \SplFileInfo */
        foreach ($iterator as $file)
        {
            $output->write(sprintf("Importing <comment>{$file->getBasename()}</comment>..."));
            if($perform) {
                $deployment = $ac->createNewDeployment( $file->getRealPath(), $tenant );
                $output->writeln("OK with id:<comment>{$deployment['id']}</comment>");

                //grab new process definitions
                $processDefinitions = $ac->getListOfProcessDefinitions(['deploymentId'=>$deployment['id']]);
                for($i=0; $i<$processDefinitions->getTotal(); $i++) {
                    $definition = new ProcessDefinition( $processDefinitions->getRow($i), $ac);

                    $output->writeln("  Deployment contains process with key::<comment>{$definition->getKey()}</comment>");
                    $ac->updateCategoryForProcessDefinition($definition->getId(), $newCat = $baseCategory.'*'.$definition->getCategory());

                }

            } else $output->writeln("PREVIEW");
        }

        $output->writeln(sprintf('<comment>DONE!</comment>'));
    }

}
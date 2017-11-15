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

use Eulogix\Cool\Lib\Builders\SnippetExtractor;
use Eulogix\Cool\Lib\Symfony\Console\CoolCommand;
use Eulogix\Cool\Lib\Util\ReflectionUtils;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
            ->addArgument('folder', InputArgument::REQUIRED, 'The folder which contains the snippet classes to import')
            ->setDescription('Imports code snippets')
            ->setHelp("");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $folder = $input->getArgument('folder');

        if(!file_exists($folder)) {
            throw new \Exception("Folder $folder does not exist.");
        }

        $classes = ReflectionUtils::getClassesInFolder($folder);

        foreach($classes as $FQNClassName) {
            $snippets = SnippetExtractor::getFromClass( $FQNClassName );
        }

    }


}

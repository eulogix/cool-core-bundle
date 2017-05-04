<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Command\Translation;

use Eulogix\Cool\Lib\Cool;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class DumpCommand extends ContainerAwareCommand
{
    private $target;

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('cool:translation:dump')
            ->setDefinition(array( new InputOption( 'target', null, InputOption::VALUE_OPTIONAL, 'Override the target directory to dump JS translation files in.' )))
            ->setDescription('Dumps all JS translation files to the filesystem');
    }

    /**
     * {@inheritDoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->target = $input->getOption('target') ? :
            realpath(sprintf('%s/../web/js', $this->getContainer()->getParameter('kernel.root_dir'))).'/cool_translations.js';

    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!is_dir($dir = dirname($this->target))) {
            $output->writeln('<info>[dir+]</info>  ' . $dir);
            if (false === @mkdir($dir, 0777, true)) {
                throw new \RuntimeException('Unable to create directory ' . $dir);
            }
        }

        $output->writeln(sprintf(
            'Installing translation files in <comment>%s</comment> directory',
            $this->target
        ));

        $this->dumpPublicTranslations($this->target);
    }

    /**
     * TODO: move to a dumper class if needed
     * @param $target string
     */
    private function dumpPublicTranslations($target)
    {
        $buffer = "(function (Translator) {\n";

        $data = Cool::getInstance()->getCoreSchema()->fetchArray("SELECT domain_name, locale, token, value FROM translation WHERE expose_flag=TRUE");
        foreach($data as $t) {
            $token = json_encode($t['token']);
            $value = json_encode($t['value']);
            $domain = json_encode($t['domain_name']);
            $buffer.="      Translator.add({$token}, {$value}, {$domain}, '{$t['locale']}');\n";
        }

        $buffer.="})(Translator);";

        file_put_contents($target, $buffer);
    }
}

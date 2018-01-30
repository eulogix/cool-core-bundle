<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Command\DataSource;

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\DataSource\DSRequest;
use Eulogix\Cool\Lib\DataSource\Renderer\RendererInterface;
use Eulogix\Cool\Lib\Symfony\Console\CoolCommand;
use Eulogix\Lib\Progress\Event\ProgressEvent;
use Eulogix\Lib\Progress\ProgressTracker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class RenderCommand extends CoolCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cool:dataSource:render')
            ->setDescription('Asynchronously renders datasource rows. Used by listers for large resultsets')
            ->addArgument('input_key', InputArgument::REQUIRED, "Shared cacher input key")
            ->setHelp(<<<EOF
EOF
            );
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $command = $this;

        $cacher = Cool::getInstance()->getFactory()->getSharedCacher();
        $input = $cacher->fetch($input->getArgument('input_key'));

        $outputFile = $input['outputFile'];
        $raw = $input['raw'];

        $listerColumnsDefinitions = unserialize($input['listerColumnsDefinitions']);

        /**
         * @var DSRequest $DSRequest
         */
        $DSRequest = unserialize($input['DSRequest']);

        /**
         * @var RendererInterface $Renderer
         */
        $Renderer = unserialize($input['Renderer']);

        $Renderer->getProgressTracker()->getDispatcher()->addListener( ProgressTracker::EVENT_PROGRESS,
            function(ProgressEvent $e) use ($command) {
                $command->outputProgressPercentage($e->getProgressPercentage());
            }
        );

        $data = $Renderer->render($DSRequest, $raw, $listerColumnsDefinitions);
        file_put_contents($outputFile, $data);
    }

}

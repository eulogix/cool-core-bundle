<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Command\Test;

use Eulogix\Cool\Lib\Symfony\Console\CoolCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Used to test asynchoronous jobs + notifications through Rundeck
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class WaitCommand extends CoolCommand
{
    /**
     * @var int
     */
    protected $seconds;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cool:test:wait')
            ->setDescription('Waits for <arg1> seconds, then prints a random number. Used to test AsyncJobs')
            ->addArgument('seconds', InputArgument::OPTIONAL, "The number of seconds to wait", 5)
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

        $this->seconds = $input->getArgument('seconds');
        for($i = 0; $i < $this->seconds; $i++) {
            sleep(1);
            $this->outputProgressPercentage(round($i * 100 / $this->seconds));
        }

        $this->signalSuccess([
            'some_output' => rand(0,10000)
        ]);
    }

    protected function getCompletedNotification() {
        if($p = parent::getCompletedNotification()) {
            $p  ->setNotification($this->seconds . " seconds")
                ->setTitle("I waited")
                ->save();
            return $p;
        }
    }

}

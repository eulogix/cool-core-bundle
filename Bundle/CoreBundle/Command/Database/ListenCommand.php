<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Command\Database;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\PgListenerHookQuery;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Symfony\Console\CoolCommand;
use Eulogix\Lib\Database\Postgres\NotificationEvent;
use Eulogix\Lib\Database\Postgres\NotificationListener;
use PDO;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class ListenCommand extends CoolCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cool:database:listen')
            ->setDescription('listens to notifications channels and calls appropriate hooks')
            ->setHelp("");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $channels = $this->getNotifierChannels();

        $output->writeln( "Initializing ".count($channels)." channels...");

        $connection = Cool::getInstance()->getSchema('core')->getConnection();

        $listener = new NotificationListener($connection);
        $listener->registerChannel($channels);

        $this->addCoreHooks($listener);

        $listener->getDispatcher()->addListener( NotificationListener::EVENT_LISTENING_STARTED,
            function(Event $e) use ($output) {
                $output->writeln("Done. Listening...");
            }
        );

        $listener->getDispatcher()->addListener( NotificationListener::EVENT_ERROR,
            function(GenericEvent $e) use ($output) {
                $output->writeln("ERROR: {$e->getSubject()}");
            }
        );

        $listener->getDispatcher()->addListener( NotificationListener::EVENT_NOTIFICATION_RECEIVED,
            function(NotificationEvent $e) use ($output) {
                $output->writeln( "PID: {$e->getPid()}, CHANNEL: {$e->getChannel()}, PAYLOAD: {$e->getPayload()}");
            }
        );

        $listener->listen();
    }

    /**
     * @return string[]
     */
    protected function getNotifierChannels() {
        $ret = [];
        $cool = Cool::getInstance();
        $schemaNames = $cool->getAvailableSchemaNames();
        foreach($schemaNames as $schemaName) {
            $schema = $cool->getSchema($schemaName);
            $schemaChannels = $schema->getNotificationChannels();
            $ret = array_merge($ret, $schemaChannels);
        }
        return $ret;
    }

    /**
     * @param NotificationListener $listener
     */
    protected function addCoreHooks(NotificationListener $listener) {
        $dbHooks = PgListenerHookQuery::create()->find();
        foreach($dbHooks as $hook)
            $listener->addHook($hook);
    }
}

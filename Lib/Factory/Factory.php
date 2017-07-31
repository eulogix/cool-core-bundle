<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Factory;

use Eulogix\Cool\Lib\Activiti\WorkFlowEngine;
use Eulogix\Cool\Lib\App\Settings\Manager;
use Eulogix\Cool\Lib\DataSource\DataSourceManager;
use Eulogix\Cool\Lib\Email\MessageFactory;
use Eulogix\Cool\Lib\Email\MessageRenderer;
use Eulogix\Cool\Lib\File\TempManagerInterface;
use Eulogix\Cool\Lib\Reminder\RemindersManager;
use Eulogix\Cool\Lib\Security\GroupManager;
use Eulogix\Cool\Lib\Security\UserManager;
use Eulogix\Cool\Lib\Session\CoolSession;
use Eulogix\Lib\Activiti\ActivitiClient;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Database\Schema;
use Eulogix\Cool\Lib\DataSource\ValueMapInterface;
use Eulogix\Cool\Lib\File\SchemaFileStorage;
use Eulogix\Lib\Cache\CacherInterface;
use Eulogix\Lib\Java\Bridge;
use Eulogix\Cool\Lib\Push\PushManager;
use Eulogix\Lib\Pentaho\PDIConnector;
use Eulogix\Lib\Rundeck\RundeckClient;
use Eulogix\Lib\Rundeck\SymfonyUtils;
use Eulogix\Cool\Lib\Translation\LocaleManager;
use Eulogix\Cool\Lib\Translation\TranslatorInterface;
use Eulogix\Cool\Lib\Widget\Factory\WidgetFactoryInterface;
use Eulogix\Lib\Validation\BeanValidator;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * this class provides convenience methods for accessing objects from the Symfony Dic and other places,
 * passing around their interfaces to ease coding in IDEs
 *
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class Factory {

    /**
     * @return Manager
     */
    public static function getSettingsManager() {
        return Cool::getInstance()->getContainer()->get('cool.settings.manager');
    }

    /**
    * @return UserManager
    */
    public static function getUserManager() {
        return Cool::getInstance()->getContainer()->get('cool.user.manager');
    }

    /**
    * @return GroupManager
    */
    public static function getGroupManager() {
        return Cool::getInstance()->getContainer()->get('cool.group.manager');
    }
    
    /**
    * @return CoolSession
    */
    public static function getSession() {
        return Cool::getInstance()->getContainer()->get('session');
    }

    /**
    * @return \SessionHandlerInterface
    */
    public static function getSessionHandler() {
        return Cool::getInstance()->getContainer()->get('session.handler');
    }

    /**
    * @return BeanValidator
    */
    public static function getNewBeanValidator() {
        return Cool::getInstance()->getContainer()->get('cool.beanvalidator');
    }

    /**
    * @return CacherInterface
    */
    public static function getCacher() {
        return Cool::getInstance()->getContainer()->get('cool.cacher');
    }

    /**
    * this instance of cacher must be shared between all the WEB heads and CLI (db, memcached, redis...)
    * unlike the regular cacher, which could well be different for each worker/web head (APCU)
    * @return CacherInterface
    */
    public static function getSharedCacher() {
        return Cool::getInstance()->getContainer()->get('cool.dbcacher');
    }

    /**
     * @return CacherInterface
     */
    public static function getKVStore() {
        return Cool::getInstance()->getContainer()->get('cool.kvstore');
    }

    /**
    * @return TempManagerInterface
    */
    public static function getFileTempManager() {
        return Cool::getInstance()->getContainer()->get('cool.file.temp_manager');
    }

    /**
    * @return DataSourceManager
    */
    public static function getDataSourceManager() {
        return Cool::getInstance()->getContainer()->get('cool.ds.manager');
    }

    /**
    * @return RemindersManager
    */
    public static function getRemindersManager() {
        //lazy loads all the providers
        return Cool::getInstance()->getContainer()->get('cool.reminders.manager')->initialize();
    }

    /**
     * @return RouterInterface
     */
    public static function getRouter() {
        return Cool::getInstance()->getContainer()->get('router');
    }

    /**
     * @return RundeckClient
     */
    public static function getRundeck() {
        return Cool::getInstance()->getContainer()->get('rundeck.client');
    }

    /**
     * @return SymfonyUtils
     */
    public static function getRundeckSymfonyUtils() {
        return Cool::getInstance()->getContainer()->get('rundeck.symfony_utils');
    }

    /**
     * @return ActivitiClient
     */
    public static function getActiviti() {
        return Cool::getInstance()->getContainer()->get('activiti.client');
    }

    /**
     * @return WorkFlowEngine
     */
    public static function getWorkflowEngine() {
        return Cool::getInstance()->getContainer()->get('cool.workflowEngine');
    }

    /**
     * @return PDIConnector
     */
    public static function getPDIConnector() {
        return Cool::getInstance()->getContainer()->get('pentaho.connector');
    }

    /**
     * @return Bridge
     */
    public static function getJavaBridge() {
        return Cool::getInstance()->getContainer()->get('java.bridge');
    }

    /**
     * @return FileLocatorInterface
     */
    public static function getFileLocator() {
        return Cool::getInstance()->getContainer()->get('file_locator');
    }

    /**
     * @return EventDispatcherInterface
     */
    public static function getEventDispatcher() {
        return Cool::getInstance()->getContainer()->get('event_dispatcher');
    }

    /**
     * @return \Swift_Mailer
     */
    public static function getMailer() {
        return Cool::getInstance()->getContainer()->get('mailer');
    }

    /**
     * @return \Twig_Environment
     */
    public static function getTwig() {
        return Cool::getInstance()->getContainer()->get('twig');
    }

    /**
     * @return MessageFactory
     */
    public static function getEmailFactory() {
        return Cool::getInstance()->getContainer()->get('cool.email.message_factory');
    }

    /**
     * @return MessageRenderer
     */
    public static function getMessageRenderer() {
        return Cool::getInstance()->getContainer()->get('cool.email.message_renderer');
    }

    /**
     * @return LocaleManager
     */
    public static function getLocaleManager() {
        return Cool::getInstance()->getContainer()->get('cool.locale.manager');
    }

    /**
     * @return ValueMapInterface
     */
    public static function getValuemap($serviceId)
    {
        return Cool::getInstance()->getContainer()->get('vmaps.'.$serviceId);
    }

    /**
     * @param Schema $schema
     * @return SchemaFileStorage
     */
    public function getSchemaFileStorage(Schema $schema)
    {
        return new SchemaFileStorage($schema, Cool::getInstance()->getContainer()->getParameter('cool_fs_storage_path'));
    }

    /**
     * @return PushManager
     */
    public function getPushManager()
    {
        return Cool::getInstance()->getContainer()->get('cool.push.manager');
    }

    /**
     * @return TranslatorInterface
     */
    public function getGlobalTranslator() {
        return Cool::getInstance()->getContainer()->get('cool.translator.global');
    }

    /**
     * @return WidgetFactoryInterface
     */
    public function getWidgetFactory() {
        return Cool::getInstance()->getContainer()->get('cool.widget.factory');
    }
}
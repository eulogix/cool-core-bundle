<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Widget;

use Eulogix\Cool\Lib\File\FileProxyInterface;
use Eulogix\Lib\Error\ErrorReport;
use Eulogix\Cool\Lib\Widget\Configurator\WidgetConfiguratorInterface;
use Eulogix\Cool\Lib\Widget\Factory\WidgetFactoryInterface;
use Eulogix\Cool\Lib\Translation\TranslatorInterface;

use Eulogix\Cool\Lib\DataSource\DataSourceInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

interface WidgetInterface {

    const PARAM_DEBUG_MODE    = '_debug';
    const PARAM_DATASOURCE_INSTANT = "instant";
    const PARAM_READONLY     =    'readOnly';

    const ATTRIBUTE_READONLY     =    'readOnly';
    const ATTRIBUTE_ONLY_CONTENT     =    'onlyContent';
    const ATTRIBUTE_DISABLED     =    'disabled';

    //fired just before the definition is returned. allows you to make some last final adjustments
    const EVENT_DEFINITION_REQUESTED = "event_definition_requested";

    /**
    * @return Definition
    *     
    */
    public function getDefinition();

    /**
     * @return array
     */
    public function getClientParameters();

    /**
     * builds the widget
     * @return $this
     */
    public function build();

    /**
     * performs additional configuration (by using the configurator)
     */
    public function configure();

    /**
     * resets the widget to blank state
     * @return $this
     */
    public function clear();

    /**
     * convenience method that clears, builds and configures the widget
     * @return $this
     */
    public function reBuild();

    /**
    * calls a class method, for example: actionName='submit' -> $this->onSubmit()
    * @param string $actionName
    * @return null|mixed if null is returned, the definition of the widget is passed to the client, otherwise the json'd return value
    */
    public function callAction($actionName);
    
    /**
    * tells wether the widget has received an action call or not (a submit)
    * @return boolean
    */
    public function actionCalled();

    /**
     * instructs the client to execute a js command upon receiving the widget definition
     * @param string $jsBody
     * @param null|int $delayMsec
     * @return mixed
     */
    public function addCommandJs($jsBody, $delayMsec=null);

    /**
     * @param string $name
     * @param SlotInterface $slot
     * @param string $group
     * @return SlotInterface
     */
    public function setSlot($name, SlotInterface $slot, $group=null);
    
    /**
    * returns the title that is displayed in the client
    * @return string
    */
    public function getTitle();
    
    /**
    * returns a string that contains the type of the widget (lister, form ...)
    * @return string
    */
    public function getType();

    /**
    * specifies the dojo widget that will be instantiated by the client
    * @return string
    */
    public static function getClientWidget();
    
    /**
    * sets the widget factory
    * @param WidgetFactoryInterface $factory
    */
    public function setWidgetFactory($factory);

    /**
     * gets the widget factory
     * @return WidgetFactoryInterface|null
     */
    public function getWidgetFactory();

    /**
    * sets the debug mode (for widget inspection)
    * 
    * @param boolean $debugMode
    * @return $this
    */
    public function setDebugMode( $debugMode );
    
    /**
    * @return boolean
    */
    public function getDebugMode();
    
    /**
    * returns a system-wide unique identifier that ties the widget to customizations done in the database
    * @return string
    */
    public function getId();

    /**
    * returns a unique identifier, unique for each instance of the widget, that identifies its representation on the client
    * this is typically a dijit id (dijit.byId()...)
    * @return string
    */
    public function getClientId();

    /**
     * sets the id
     * @param string $id
     * @return $this
     */
    public function setId($id);
    
    /**
    * returns a hash that represent the "variation", or "state" of the widget, for each of its levels
    * 
    * @return array
    */
    public function getVariation();
    
    /**
    * returns an array of categories that drive the possible configuration of the widget
    * for example, if you have a widget that depends on some user attribute named "userKind", you may return array("userKind"=>array("admins","default")).
    * if you widget depends on both "userKind" and "systemLocale", you may return array("userKind"=>array("admins","default"), "systemLocale"=>array("it","en"))
    * return null if the widget is not supposed to vary / be editable
    * 
    * @return array
    */
    public function getVariationLevels();
    
    /**
    * returns the currently active variant for a given level. This function can look into system state, widget state, or whatever else.
    * Suppose that the current system locale is "english", and you want to select the variation level "en" for this widget on the level "systemLocale":
    * return $sys->getLocale()=="english"? "en" : "it";
    * 
    * @param string $level
    * @return string
    */
    public function getActiveLevelVariant($level);

    /**
     * tells whether the widget is configurable with the editor.
     * @return boolean
     */
    public function isConfigurable();

    /**
    * returns the configurator for this widget
    * @returns WidgetConfiguratorInterface
    */
    public function getConfigurator();
    
    /**
    * returns the translator domains for this widget
    * @returns string[]
    */
    public function getTranslatorDomains();

    /**
    * returns the translator for this widget
    * @returns TranslatorInterface
    */
    public function getTranslator();
    
    /**
    * sets the template engine that will process the layout
    * @param  \Twig_Environment $twig
    * @return $this
    */
    public function setTwig(\Twig_Environment $twig);
    
    /**
    * gets the dataSource
    * @return DataSourceInterface|null
    */
    public function getDataSource();
    
    /**
    * sets the dataSource for the widget
    * @param $dataSource DataSourceInterface
    */
    public function setDataSource($dataSource);

    /**
     * @return ParameterBag
     */
    public function getParameters();

    /**
     * @return ParameterBag
     */
    public function getRequest();

    /**
     ** @return ParameterBag
     */
    public function getAttributes();

    /**
     ** @return ParameterBag
     */
    public function getServerAttributes();

    /**
     * forces the widget, and its dependencies, to be completely redrawn on the client
     * @return $this
     */
    public function forceRedraw();

    /**
     * adds a message, {1}...{n} may be used as placeholders for text variables
     *
     * @param mixed $messageType
     * @param mixed $messageText,...
     * @returns Message
     */
    public function addMessage($messageType, $messageText);

    /**
     * @param string $messageText,...
     * @return Message
     */
    public function addMessageInfo($messageText);

    /**
     * @param string $messageText,...
     * @return Message
     */
    public function addMessageWarning($messageText);

    /**
     * @param string $messageText,...
     * @return Message
     */
    public function addMessageError($messageText);

    /**
     * adds an Action
     *
     * @param $actionName
     * @returns Action
     */
    public function addAction($actionName);

    /**
     * removes an action
     *
     * @param $actionName
     * @returns self
     */
    public function removeAction($actionName);

    /**
     * @return boolean
     */
    public function getReadOnly();

    /**
     * @param bool $readOnly
     * @return $this
     */
    public function setReadOnly($readOnly);

    /**
     * does not even render the widget
     * @return boolean
     */
    public function isDisabled();

    /**
     * @param bool $disabled
     * @return $this
     */
    public function setDisabled($disabled);

    /**
     * @param ErrorReport $errorReport
     * @return mixed
     */
    public function mergeErrorReport(ErrorReport $errorReport);

    /**
     * sends a file to the client
     * @param FileProxyInterface $f
     * @return $this
     */
    public function downloadFile(FileProxyInterface $f);
}
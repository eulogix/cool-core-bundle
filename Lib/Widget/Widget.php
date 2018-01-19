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

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\RuleCode;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\WidgetRule;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\WidgetRuleQuery;
use Eulogix\Cool\Lib\Audit\AuditSchema;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\DataSource\Classes\Audit\DSFieldAuditTrailDataSource;
use Eulogix\Cool\Lib\DataSource\CoolCrudDataSource;
use Eulogix\Cool\Lib\DataSource\DataSourceInterface;
use Eulogix\Cool\Lib\DataSource\DSRequest;
use Eulogix\Cool\Lib\Enums\UserSettings;
use Eulogix\Cool\Lib\Security\CoolUser;
use Eulogix\Cool\Lib\Traits\DataSourceHolder;
use Eulogix\Cool\Lib\Traits\EventHolder;
use Eulogix\Cool\Lib\Translation\Translator;
use Eulogix\Cool\Lib\Translation\TranslatorInterface;
use Eulogix\Cool\Lib\Widget\Event\WidgetEvent;
use Eulogix\Cool\Lib\Widget\Factory\WidgetFactoryInterface;
use Eulogix\Cool\Lib\Widget\Help\HelpItem;
use Eulogix\Cool\Lib\Widget\Help\WidgetHelpProviderInterface;
use Eulogix\Lib\Error\ErrorReport;
use Eulogix\Lib\File\Proxy\FileProxyInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

abstract class Widget implements WidgetInterface {

    use EventHolder, DataSourceHolder;
    
    protected $type = "widget";
    
    /**
     * the serverId (class) of the widget
     * @var string
     */
    protected $id = null;

    /**
     * the id of the dijit associated widget
     * @var string
     */
    private $clientId = null;

    /**
     * @var array
     */
    protected $messages = [];

    /**
     * @var Action[]
     */
    protected $actions = [];
    
    protected $slots = [];

    /**
     * @var HelpItem[]
     */
    protected $helpItems = [];

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
    * optional widget factory, can be used to recursively instance and define related slotted widgets
    * 
    * @var WidgetFactoryInterface
    */
    protected $widgetFactory;
    
    /**
    * This translator can be used to translate field names, messages, and so on
    * 
    * @var TranslatorInterface
    */
    protected $translator;
    
    /**
    * @var \Twig_Environment
    */
    protected $twig;
    
    /**
    * this bag contains attributes that are only consumed by server side code, typically twig template processing
    * they do not get sent to the client
    * 
    * @var ParameterBag
    */
    public $serverAttributes;

    /**
    * attributes bag, this bag contains values that are isolated in the widget: they do not get propagated during requests.
    * the content of this bag is however sent with the json definition of the widget and is thus available to the client side
    *
    * @var ParameterBag
    */
    public $attributes;
    
    /**
    * parameters bag, this bag contains parameters that get propagated during requests, and they get appended to the query string (GET)
    * 
    * @var ParameterBag
    */
    public $parameters;
    
    /**
    * request bag, this bag is populated whenever an action is called (for instance, form submission)
    * this bag does not get embedded in the definition, as it is only used server side to process a widget submission
    * 
    * @var ParameterBag
    */
    public $request;
    
    /**
    * this list of commands gets executed by the client
    * @var mixed
    */
    protected $commands = [];

    /**
     * @var WidgetHelpProviderInterface
     */
    protected $helper;

    /**
     * initialize the widget with an initial set of parameters.
     * and optionally, a request
     *
     * @param array $parameters
     */
    public function __construct($parameters = [])
    {
        $this->attributes = new ParameterBag();
        $this->serverAttributes = new ParameterBag();
        $this->request = new ParameterBag();
        $this->parameters = new ParameterBag();
        $this->parameters->replace($parameters);
        $this->setUpDispatcher();

        //avoids a new client id on every callAction
        if($cid = @$parameters['_client_id'])
            $this->clientId = $cid;
    }

    /**
     * @param DataSourceInterface $dataSource
     * @return $this
     */
    public function setDataSource($dataSource)
    {
        if($instant = $this->parameters->get(self::PARAM_DATASOURCE_INSTANT)) {
            $dataSource->setInstant(new \DateTime($instant));
        }

        $this->dataSource = $dataSource;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function build() {
        $this->attributes->set("title", $this->getTitle());
        $this->attributes->set("id", $this->getId());
        $this->attributes->set("_client_id", $this->getClientId());
        $this->attributes->set("_configurable", $this->isConfigurable());

        $this->setDebugMode( Cool::getInstance()->getFactory()->getSession()->getDebugMode() );
        $this->attributes->set("_translation_domains",$this->getTranslator()->getDomains());

        //checks if the widget is somehow disabled
        if($u = Cool::getInstance()->getFactory()->getUserManager()->getLoggedUser()) {
            if($u->getSetting(UserSettings::USER_SETTING_WIDGET_DISABLE, $this->getId()))
                $this->setDisabled(true);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function reBuild() {
        $this->clear();
        $this->build();
        $this->configure();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function getClientParameters() {
        return array(
            'widget' => $this->getClientWidget(),
        );
    }

    /**
     * @inheritdoc
     */
    public function getTitle() {
        return $this->getTranslator()->trans( 'TITLE_'.$this->getId() );
    }

    /**
     * @inheritdoc
     */
    public function getClientId() {
        return $this->clientId ? $this->clientId : $this->clientId = uniqid('widget');
    }

    /**
    * @inheritdoc
    */
    public function getId() {
        return $this->id ? $this->id : get_class($this);
    }

    /**
    * @inheritdoc
    */
    public function setId($id) {
        $this->id  = $id;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function clear() {
        $this->messages = []; 
        $this->commands = []; 
        $this->slots = []; 
        $this->actions = [];
        $this->setUpDispatcher();
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function setUpDispatcher() {
        $this->dispatcher = new EventDispatcher();
    }
    
    /**
    * @inheritdoc
    */
    public function callAction($actionName) {
        $this->getAttributes()->set(self::ATTRIBUTE_LAST_CALLED_ACTION, $actionName);

        $method = 'on'.ucfirst($actionName);
        if(method_exists($this, $method)) {
           $ret = $this->$method();
           if($ret !== null)
               return $ret;
        }
    }
    
    /**
    * @inheritdoc
    */
    public function getLastCalledAction() {
        return $this->getAttributes()->get(self::ATTRIBUTE_LAST_CALLED_ACTION);
    }
    
    /**
    * @return Definition | null
    */
    public function getDefinition() {

        $this->processRules(WidgetRule::EVALUATION_TYPE_BEFORE_DEFINITION);

        $this->dispatcher->dispatch(self::EVENT_DEFINITION_REQUESTED, new WidgetEvent($this));

        $d = new Definition($this->parameters->get('_hashes'));
        
        $d->setBlock('clientParameters', $this->getClientParameters());
         
        if($this->parameters->count()>0) {
            $d->setBlock('parameters', $this->parameters->all());
        }
        
        $this->getAttributes()->set(self::ATTRIBUTE_ID, $this->getId());

        $d->setBlock('attributes', $this->attributes->all());

        $helpItems = [];
        foreach ($this->getHelpItems() as $helpItem) {
            $helpItems[] = $helpItem->getDefinition();
        }
        $d->setBlock('helpItems', $helpItems);

        if(!$this->isDisabled()) {
            $msgs = [];
            foreach ($this->messages as $cat => $arr) {
                foreach ($arr as $a) {
                    /**@var Message $a */
                    $msgs[ $cat ][] = $a->getDefinition();
                }
            }
            if (!empty( $msgs )) {
                $d->setBlock('messages', $msgs);
            }

            $actions = [];
            foreach ($this->actions as $actionName => $a) {
                /**@var Action $a */
                $actions[ $actionName ] = $a->getDefinition();
            }
            $d->setBlock('actions', $actions);

            if (!empty( $this->commands )) {
                $d->setBlock('commands', $this->commands);
            }

            if ($this->hasEvents()) {
                $d->setBlock('events', $this->getEvents());
            }

            $slots = [];
            foreach ($this->slots as $group => $arr) {
                foreach ($arr as $slotName => $slot) {
                    /**@var SlotInterface $slot * */
                    $slots[ $group ][ $slotName ] = $slot->getDefinition();
                }
            }

            $d->setBlock('slots', $slots);
        }

        return $d;
    }

    /**
     * @inheritdoc
     */
    public function setSlot($name, SlotInterface $slot, $group=null) {
        $slot->setWidgetFactory($this->getWidgetFactory());
        $this->slots[$group?$group:"_base"][$name] = $slot;
        return $slot;
    }

    /**
     * @param array $arr
     */
    protected function addCommand($arr) {
        $this->commands[] = $arr;        
    }

    /**
     * @inheritdoc
     */
    public function addCommandJs($jsBody, $delayMsec=null) {
        if($delayMsec) {
            $jsBody = "setTimeout( function() { $jsBody }, $delayMsec );";
        }
        $this->addCommand( array(
            'type'=>'js',
            'body'=>$jsBody
        ));    
    }

    /**
     * @inheritdoc
     */
    public function addMessage($messageType, $messageText) {
        if($m = new Message($messageType)) {
            $args = [];
            for($i=1;$i<func_num_args();$i++) {
                $args[]=func_get_arg($i);
            } 
            call_user_func_array(array($m, "setText"), $args);
            $this->messages[$messageType][] = $m;
            return $m;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function addMessageInfo($messageText) {
        $args = func_get_args();
        array_unshift($args, Message::TYPE_INFO);
        return call_user_func_array([$this, 'addMessage'], $args);
    }

    /**
     * @inheritdoc
     */
    public function addMessageWarning($messageText) {
        $args = func_get_args();
        array_unshift($args, Message::TYPE_WARNING);
        return call_user_func_array([$this, 'addMessage'], $args);
    }

    /**
     * @inheritdoc
     */
    public function addMessageError($messageText) {
        $args = func_get_args();
        array_unshift($args, Message::TYPE_ERROR);
        return call_user_func_array([$this, 'addMessage'], $args);
    }

    /**
     * @inheritdoc
     */
    public function addAction($actionName) {
        if($a = new Action()) {
            $a->setLabel( $this->getTranslator()->trans($actionName) )
              ->setReadOnly( $this->getReadOnly() );

            $this->actions[$actionName] = $a;
            return $a;
        }
        return false;
    }

    /**
     * @param string $actionName
     * @returns Action
     */
    public function addCallActionAction($actionName) {
        return $this->addAction($actionName)->setOnClick("widget.callAction('$actionName');");
    }

    /**
     * @inheritdoc
     */
    public function removeAction($actionName) {
        unset($this->actions[$actionName]);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setWidgetFactory($factory) {
        $this->widgetFactory = $factory;    
    }

    /**
     * @inheritdoc
     */
    public function getWidgetFactory() {
        return $this->widgetFactory;  
    }

    /**
     * @inheritdoc
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     * @inheritdoc
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * @inheritdoc
     */
    public function getAttributes() {
        return $this->attributes;
    }

    /**
     * @inheritdoc
     */
    public function getServerAttributes() {
        return $this->serverAttributes;
    }

    /**
     * @inheritdoc
     */
    public function setCurrentVariation($variation) {
        $this->getAttributes()->set(self::ATTRIBUTE_CURRENT_VARIATION, $variation);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCurrentVariation() {
        return $this->getAttributes()->get(self::ATTRIBUTE_CURRENT_VARIATION);
    }

    /**
     * @inheritdoc
     */
    public function isConfigurable() {
        return Cool::getInstance()->getLoggedUser()->hasRole( CoolUser::ROLE_ADMIN );
    }

    /**
    * @inheritdoc
    */
    public function getConfigurator() {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function configure() {
        $this->processRules(WidgetRule::EVALUATION_TYPE_ON_LOAD);
        if(($c = $this->getConfigurator()) && $c->configurationExists()) {
            $c->applyConfiguration();
        }
        $this->setHelpProvider( Cool::getInstance()->getFactory()->getWidgetHelpProviderFactory()->getHelperFor($this) );
    }

    /**
     * processes all the rules recursively and returns a complete hash of outcomes
     *
     * @param string $evaluationType
     */
    public function processRules($evaluationType = null) {

        $query = WidgetRuleQuery::create()->filterByWidgetId( $this->getId() )->filterByEnabledFlag(true)->filterByParentWidgetRuleId(null);

        if($evaluationType)
            $query = $query->filterByEvaluation([$evaluationType, WidgetRule::EVALUATION_TYPE_ALWAYS]);

        $widgetRules = $query->find();

        $ruleContext = $this->getRuleContext();

        $returnHash = [];

        /** @var WidgetRule $widgetRule */
        foreach($widgetRules as $widgetRule)
            $this->processRuleRecursive($widgetRule, $ruleContext, $evaluationType, $returnHash);

        $rules = $this->getAttributes()->get('rules') ?? [];
        $this->getAttributes()->set('rules', array_merge($rules, $returnHash));
    }

    /**
     * processes one rule and descends into its sub rules
     *
     * @param WidgetRule $widgetRule
     * @param array $ruleContext
     * @param string $evaluationType
     * @param array $returnHash
     */
    protected function processRuleRecursive(WidgetRule $widgetRule, array $ruleContext, $evaluationType = null, array &$returnHash = []) {

        $mainRuleOutcome = $this->processRule($widgetRule, $ruleContext);
        $returnHash[ $widgetRule->getRule()->getName() ] = $mainRuleOutcome;

        if($mainRuleOutcome['valid']) {
            $query = WidgetRuleQuery::create()
                ->filterByParentWidgetRuleId( $widgetRule->getWidgetRuleId() )
                ->filterByEnabledFlag(true);

            if($evaluationType)
                $query = $query->filterByEvaluation([$evaluationType, WidgetRule::EVALUATION_TYPE_ALWAYS]);

            $subWidgetRules = $query->find();

            /** @var WidgetRule $widgetRule */
            foreach($subWidgetRules as $subWidgetRule)
                $this->processRuleRecursive($subWidgetRule, $ruleContext, $evaluationType, $returnHash);
        }
    }

    /**
     * atomic function that processes a widget rule and returns the outcome
     *
     * @param WidgetRule $widgetRule
     * @param array $ruleContext
     * @return array
     */
    protected function processRule(WidgetRule $widgetRule, array $ruleContext) {

        $rule = $widgetRule->getRule();
        try {
            $ruleValid = $rule->assert( $ruleContext );
            $rule->execCodes( $ruleValid ? RuleCode::TYPE_EXEC_IF_TRUE : RuleCode::TYPE_EXEC_IF_FALSE, $ruleContext);

            return [
                'valid' => $ruleValid,
                'report' => $rule->getLastExecutionReport()
            ];
        } catch(\Error $e) {
            $this->addMessageError("Rule ".$rule->getName()." could not be processed: ".$e->getMessage());
        } catch(\Throwable $e) {
            $this->addMessageError("Rule ".$rule->getName()." could not be processed: ".$e->getMessage());
        } catch(\Exception $e) {
            $this->addMessageError("Rule ".$rule->getName()." could not be processed: ".$e->getMessage());
        }

        return [
            'valid' => false,
            'report'=> []
        ];
    }

    /**
     * @inheritdoc
    */
    public function getTranslatorDomains() {
        return [$this->getId()];
    }

    /**
     * @inheritdoc
     */
    public function getTranslator() {
        if(!$this->translator) {
            $this->translator = Translator::fromDomain( $this->getTranslatorDomains() );
        }
        return $this->translator;
    }
        
    /**
    * @inheritdoc
    */
    public function setTwig(\Twig_Environment $twig) {
        $this->twig = $twig;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setDebugMode( $debugMode ) {
        $this->attributes->set(self::PARAM_DEBUG_MODE, $debugMode);
        return $this;
    }
    
    /**
    * @return boolean
    */
    public function getDebugMode() {
        return $this->attributes->get(self::PARAM_DEBUG_MODE);
    }
    
    /**
     * @inheritdoc
     */
    public function forceRedraw() {
        $this->parameters->set('_hashes',serialize([]));
    }

    /**
     * @param boolean $readOnly
     * @return $this
     */
    public function setReadOnly($readOnly)
    {
        $this->getAttributes()->set(self::ATTRIBUTE_READONLY, $readOnly);
        return $this;
    }

    /**
     * @return boolean
     */
    public function getReadOnly()
    {
        return $this->getAttributes()->get(self::ATTRIBUTE_READONLY);
    }

    /**
     * @param bool $disabled
     * @return $this
     */
    public function setDisabled($disabled)
    {
        $this->getAttributes()->set(self::ATTRIBUTE_DISABLED, $disabled);
        return $this;
    }

    /**
     * @return boolean
     */
    public function isDisabled()
    {
        return $this->getAttributes()->get(self::ATTRIBUTE_DISABLED);
    }

    /**
     * @inheritdoc
     */
    public function mergeErrorReport(ErrorReport $errorReport)
    {
        $errors = $errorReport->getErrors();
        foreach ($errors as $fieldName => $error) {
            $this->addMessage(Message::TYPE_ERROR, $error);
        }

        $generalErrors = $errorReport->getGeneralErrors();
        foreach ($generalErrors as $error) {
            $this->addMessage(Message::TYPE_ERROR, $error);
        }
    }

    /**
     * @inheritdoc
     */
    public function downloadFile(FileProxyInterface $f) {
        $url = Cool::getInstance()->getFactory()->getFileTempManager()->getDownloadUrlFromFileProxy($f);
        $this->addCommandJs("document.location='$url'");
    }

    public function onGetFieldAuditTrail() {
        $ds = $this->getDataSource();
        $buf = "Audit trail not available";

        if($ds && $ds instanceof CoolCrudDataSource) {
            $p = $this->getParameters()->all();
            if(isset($p[DataSourceInterface::RECORD_IDENTIFIER]) && method_exists($this, 'getRecordIdForDSR')) {
                $p[DataSourceInterface::RECORD_IDENTIFIER] = $this->getRecordIdForDSR();
            }
            $trailDS = new DSFieldAuditTrailDataSource($ds, $this->request->get('fieldName'), $p);
            $trailDS->build();

            $dsr = new DSRequest();
            $dsr->setOperationType($dsr::OPERATION_TYPE_FETCH)
                ->setSortBy([AuditSchema::FIELD_VALIDITY_FROM=>'D'])
                ->setStartRow(0)
                ->setEndRow(10)
                ->setIncludeDecodings(true);

            $resp = $trailDS->execute($dsr);
            if($rows = $resp->getRows()) {
                $buf = "<table>";
                foreach($rows as $row) {
                    $rd = $row['_decodifications'];
                    $buf.="<tr>
                                <td style='padding-right: 5px'>{$rd[DSFieldAuditTrailDataSource::AUDITED_FIELD]}</td>
                                <td style='padding-right: 5px'>{$rd[AuditSchema::FIELD_VALIDITY_FROM]}</td>
                                <td>{$rd[AuditSchema::FIELD_COOL_USER_ID]}</td>
                          </tr>";
                }
                $buf .= "</table>";
            } else $buf = "-";
        }

        return [
            'trail' => $buf
        ];
    }

    /**
     * @return array
     */
    public function getRuleContext() {
        return [
            'widget' => $this,
            'parameters' => $this->getParameters()->all(),
            'attributes' => $this->getAttributes()->all(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function setHelpProvider(WidgetHelpProviderInterface $helper) {
        $this->helper = $helper;
    }

    /**
     * @inheritdoc
     */
    public function getHelpProvider() {
        return $this->helper;
    }

    public function onGetContextualHelpFor() {
        $helpContent = '';

        if($helper = $this->getHelpProvider()) {
            $helpContent = $helper->getHelp($this->request->all());
        }

        return [
            'helpContent' => $helpContent
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDefaultWikiHelpPage()
    {
        return implode(':', [ '{{ locale }}', '{{"WIDGETS"|t}}', '{{ title }}' ]);
    }

    /**
     * @inheritdoc
     */
    public function getWikiHelpPage() {
        return $this->getAttributes()->get(self::ATTRIBUTE_WIKI_HELP_PAGE);
    }

    /**
     * @inheritdoc
     */
    public function setWikiHelpPage($page) {
        $this->getAttributes()->set(self::ATTRIBUTE_WIKI_HELP_PAGE, $page);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDispatcher() {
        return $this->dispatcher;
    }

    /**
     * @inheritdoc
     */
    public function addHelpItem(HelpItem $help) {
        $help->setWidget($this);
        $this->helpItems[] = $help;
    }

    /**
     * @inheritdoc
     */
    public function getHelpItems() {
        return $this->helpItems;
    }
}
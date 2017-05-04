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

use Eulogix\Cool\Lib\Widget\Factory\WidgetFactoryInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class WidgetSlot implements SlotInterface {

    /**
     * @var string
     */
    protected $serverId = '';

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var array
     */
    protected $dojoParameters = [];

    /**
     * @var string
     */
    protected $type = 'widget';

    /**
     * @var bool
     */
    protected $preFetch = false;

    /**
     * @var WidgetInterface
     */
    protected $widget;

    /**
    * optional widget factory, can be used to recursively instance and define related slotted widgets
    * 
    * @var WidgetFactoryInterface
    */
    protected $widgetFactory;

    /**
     * @param string $serverId
     * @param array $parameters
     * @param array $dojoParameters
     */
    public function __construct($serverId, $parameters, $dojoParameters=[])
    {
        $this->serverId = $serverId;
        $this->parameters = $this->cleanParameters( $parameters );
        $this->dojoParameters = $dojoParameters;
    }

    /**
     * @inheritdoc
     */
    public function getDefinition() {
         $def = array(
            "type"=>$this->type,
            "serverId"=>$this->serverId,
            "parameters"=>$this->getParameters(),
            "dojoParameters"=>$this->getDojoParameters(),
         );

        if($this->isPreFetch() && ($w = $this->getWidget()))
             $def['widgetDefinition'] = $w->getDefinition()->getResponse();

         return $def;
    }

    /**
     * @inheritdoc
     */
    public function isPreFetch()
    {
        return $this->preFetch;
    }

    /**
     * @inheritdoc
     */
    public function setPreFetch($preFetch)
    {
        $this->preFetch = $preFetch;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setWidgetFactory($factory) {
        $this->widgetFactory = $factory;    
    }

    /**
     * returns the widget that populates the slot
     * @return WidgetInterface|null
     */
    public function getWidget() {
        if($this->widget)
            return $this->widget;
        if($this->widgetFactory && ($w = $this->widgetFactory->getWidget($this->serverId, $this->parameters))) {
            $w->setWidgetFactory($this->widgetFactory);
            $w->reBuild();
            return $this->widget = $w;
        }
    }

    /**
     * @return array
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     * @return array
     */
    public function getDojoParameters() {
        return $this->dojoParameters;
    }

    /**
     * when instantiating descendant widgets, some parameters have to be removed in order to maintain the definition of the descendants coherent
     * between requests. If, for instance, the _hashes parameter was passed, every descendant widget would have it in the "parameters" section
     * of the definition, thus negating the advantage of optimized message exchanging between client and server.
     *
     * @param array $parameters
     * @return array
     */
    protected function cleanParameters( $parameters ) {
        $ret = [];
        foreach($parameters as  $key=>$value) {
            if(!in_array($key, ['_hashes', '_storeOp', '_client_id'])) {
                $ret[$key] = $value;
            }
        }
        return $ret;
    }

}
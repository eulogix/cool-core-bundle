<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Widget\Factory;

use Eulogix\Cool\Lib\Widget\WidgetInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class SymfonyBundleWidgetFactory extends BaseWidgetFactory implements WidgetFactoryInterface {
    
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * initialize the factory with the required parameters
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * tries to retrieve the Widget class by looking in the CWidget/{path} path in the specified bundle
     * bundle name and Widget name can be specified with or without the "Bundle" and "Widget" extension
     * these two are equivalent
     * 1. EulogixCoolCoreBundle/TestWidget
     * 2. EulogixCoolCore/Test
     *
     * @param mixed $serverId
     * @param array $parameters
     * @return WidgetInterface|bool
     */
    protected function getWidgetByBundleAndPath($serverId, $parameters=null) {
        if(!preg_match('%([^/]+?)(Bundle|)/(.+?)$%im',$serverId,$m))
            return false;
        $bundleName = $m[1]; $file = $m[3];
        $bundles = $this->container->getParameter('kernel.bundles');
        if($bundleNameSpace = @$bundles[$bundleName."Bundle"]) {
            $nameSpace = str_replace($bundleName."Bundle","CWidget\\".str_replace("/","\\",$file), $bundleNameSpace);
            if(class_exists($nameSpace)) {
                return new $nameSpace( $parameters );
            }          
        }
        return false;
    }

    /**
     * wrapper for the above functions, tries to retrieve the Widget class using $path first as a bundle relative path, then as a namespace
     * the retrieved object is returned only if it actually is a subclass of Widget
     *
     * @param mixed $serverId
     * @param array $parameters
     * @return WidgetInterface
     */
    public function getWidget($serverId, $parameters=null) {
        $WidgetInstance = parent::getWidget($serverId, $parameters);
        if(!$WidgetInstance) {
            $WidgetInstance = $this->getWidgetByBundleAndPath($serverId, $parameters);
        }
        if( $WidgetInstance ) {
            if(method_exists($WidgetInstance, 'setContainer')) {
                $WidgetInstance->setContainer( $this->container );
            }
            if(method_exists($WidgetInstance, 'setTwig')) {
                $WidgetInstance->setTwig( $this->container->get('twig') );
            }

            //propagate the factory
            $WidgetInstance->setWidgetFactory($this);
            return $WidgetInstance;
        } 
        return false;
    }

}
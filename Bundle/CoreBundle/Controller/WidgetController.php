<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Controller;

use Eulogix\Cool\Lib\Cool;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Eulogix\Cool\Lib\Widget\Factory\SymfonyBundleWidgetFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class WidgetController extends Controller
{
    /**
     * @Route("/getDefinition/{serverId}", name="_widget_get_definition", requirements={"serverId" = ".+"}, options={"expose"=true})
     */
    public function getDefinitionAction($serverId)
    {
       Cool::getInstance()->freeSession();

       if($WidgetInstance = $this->getWidgetInstance($serverId)) {
           $WidgetInstance->reBuild();
           return new JsonResponse( $WidgetInstance->getDefinition()->getResponse() );
       }                                                             
              
       return $this->getErrorResponse("Widget not found: $serverId");
    }

    /**
     * @Route("/callAction/{actionName}/{serverId}", name="_widget_call_action", requirements={"actionName" = "[a-zA-Z0-9]+", "serverId" = ".+"}, options={"expose"=true})
     */
    public function callActionAction($actionName, $serverId)
    {       
       if($WidgetInstance = $this->getWidgetInstance($serverId)) {

           //pass the whole POST bag to the widget
           $WidgetInstance->request = $this->get('request')->request;

           //build it later, as the build process may be influenced by some parameters passed in the request
           $WidgetInstance->reBuild();
           //then call the action
           $ret = $WidgetInstance->callAction($actionName);

           if($ret === null)
               return new JsonResponse($WidgetInstance->getDefinition()->getResponse());

           if(is_string($ret) || is_scalar($ret))
               return new Response($ret);

           return new JsonResponse( $ret );
       }
       
       return $this->getErrorResponse("Widget not found: $serverId");
    }                                                                                     
    
    public function getWidgetInstance($serverId) {
        $f = new SymfonyBundleWidgetFactory($this->container);
        if($WidgetInstance = $f->getWidget( $serverId, $this->get('request')->query->all() )) {
            return $WidgetInstance;
        }
        return false;
    }
    
    public function getErrorResponse($message) {
         $response = new Response();
         $response->setStatusCode(500);
         $response->setContent($message);
         return $response;
    }

}

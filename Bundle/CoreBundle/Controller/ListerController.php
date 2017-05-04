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
use Eulogix\Cool\Lib\Dojo\ListerStore;
use Eulogix\Cool\Lib\Dojo\StoreInterface;
use Eulogix\Cool\Lib\Dojo\XhrStoreRequest;
use Eulogix\Cool\Lib\Widget\Message;
use Eulogix\Cool\Lib\Lister\ListerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class ListerController extends WidgetController
{
    /**
     * @var ListerInterface
     */
    private $lister;

    /**
     * @param string $serverId
     * @throws \Exception
     * @return StoreInterface
     */
    private function getStore($serverId) {
        if($WidgetInstance = $this->getWidgetInstance($serverId)) {
            $WidgetInstance->reBuild();
            $this->lister = $WidgetInstance;
            return new ListerStore( $WidgetInstance );
        } else throw new \Exception("no Widget");
    }

    /**
     * This action gets all the requests made from the lister store in the lister 
     * 
     * @Route("/store/{serverId}", name="_lister_store", requirements={"serverId" = ".+"}, options={"expose"=true})
     */
    public function storeAction($serverId) {

        Cool::getInstance()->freeSession();

        $dojoRequest = XhrStoreRequest::fromSymfonyRequest($this->get('request'));
        $dojoStore = $this->getStore($serverId);
        $dojoResponse = $dojoStore->execute($dojoRequest);
        $responseData = $dojoResponse->getResponseData();

        $response = new JsonResponse();

        if($dojoResponse->getStartRow()!==null && $dojoResponse->getTotalRows()!==null) {
            $retEnd = $dojoResponse->getStartRow() + count($dojoResponse->getData());
            $response->headers->set("Content-Range", $dojoResponse->getStartRow()."-{$retEnd}/".$dojoResponse->getTotalRows());
        }

        //lister specific stuff
        if($dojoRequest->getOperation() == $dojoRequest::OPERATION_TYPE_PUT) {
            switch($dojoResponse->getStatus()) {
                case $dojoResponse::STATUS_TRANSACTION_SUCCESS : {
                    $this->lister->addMessage(Message::TYPE_INFO,"SAVED");
                    break;
                }
                case $dojoResponse::STATUS_TRANSACTION_FAILED :
                case $dojoResponse::STATUS_VALIDATION_ERROR : {
                    $this->lister->addMessage(Message::TYPE_ERROR, "DATASOURCE_ERROR");

                    $generalErrors = $dojoResponse->getErrorReport()->getGeneralErrors();
                    foreach($generalErrors as $error) {
                        $this->lister->addMessage(Message::TYPE_ERROR, $error);
                        $responseData = array_merge(
                            [
                                '_widgetDefinition'=>$this->lister->getDefinition()->getResponse(),
                            ],$responseData);
                    }
                    break;
                }
            }
        }

        $response->setData($responseData);

        return $response;
    }

}

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
use Eulogix\Cool\Lib\Dojo\DSstore;
use Eulogix\Cool\Lib\Dojo\StoreInterface;
use Eulogix\Cool\Lib\Dojo\XhrStoreRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class DSStoreController extends Controller
{

    /**
     * @param string $dataSourceId
     * @return StoreInterface
     */
    private function getStore($dataSourceId) {
        $dataSource = Cool::getInstance()->getFactory()->getDataSourceManager()->getDataSource($dataSourceId);
        $dataSource->build($this->get('request')->query->all());
        return new DSstore($dataSource);
    }

    /**
     * This action gets all the requests made from the dojo store
     * 
     * @Route("/store/{dataSourceId}", name="dataSourceDojoStore", requirements={"dataSourceId" = ".+"}, options={"expose"=true})
     */
    public function storeAction($dataSourceId) {
        $dojoRequest = XhrStoreRequest::fromSymfonyRequest($this->get('request'));
        $dojoStore = $this->getStore($dataSourceId);
        $dojoResponse = $dojoStore->execute($dojoRequest);
        $responseData = $dojoResponse->getResponseData();

        $response = new JsonResponse();

        if($dojoResponse->getStartRow()!==null && $dojoResponse->getTotalRows()!==null) {
            $retEnd = $dojoResponse->getStartRow() + count($dojoResponse->getData());
            $response->headers->set("Content-Range", $dojoResponse->getStartRow()."-{$retEnd}/".$dojoResponse->getTotalRows());
        }

        $response->setData($responseData);

        return $response;
    }

}

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

use Eulogix\Cool\Lib\File\FileRepositoryFactory;
use Eulogix\Cool\Lib\File\FileRepositoryPreviewProvider;
use Eulogix\Cool\Lib\File\FileUtil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class FileRepositoryController extends Controller
{
    /**
     * @Route("/getPreviewImage/{width}/{repositoryId}", name="frepoGetPreviewImage", options={"expose"=true})
     */
    public function getPreviewImageAction($width, $repositoryId)
    {
        $repo = FileRepositoryFactory::fromId($repositoryId);
        $repo->setParameters($this->get('request')->query->all());

        $previewProvider = new FileRepositoryPreviewProvider($repo);

        $filePath = $this->get('request')->query->get('filePath');

        $thumb = $previewProvider->getOrCreateCachedPreviewIcon($filePath, $width);

        $response = new Response(file_get_contents($thumb), 200);

        $response->headers->set('Content-Type', 'image/jpg');

        return $response;
    }

    /**
     * @Route("/download/{repositoryId}", name="frepoDownload", options={"expose"=true})
     */
    public function downloadAction($repositoryId)
    {
        $repo = FileRepositoryFactory::fromId($repositoryId);
        $repo->setParameters($this->get('request')->query->all());

        $filePath = $this->get('request')->query->get('filePath');
        $fileProxy = $repo->get($filePath);

        $response = new Response($fileProxy->getContent(), 200);
        $response->headers->set('Content-Type', FileUtil::getMIMEType($fileProxy->getExtension()));
        $response->headers->set('Content-Disposition', "attachment; filename=\"".$fileProxy->getName()."\"");

        return $response;
    }

    /**
     * @Route("/serve/{repositoryId}", name="frepoServe", options={"expose"=true})
     */
    public function serveAction($repositoryId)
    {
        $repo = FileRepositoryFactory::fromId($repositoryId);
        $repo->setParameters($this->get('request')->query->all());

        $filePath = $this->get('request')->query->get('filePath');
        $fileProxy = $repo->get($filePath);

        $response = new Response($fileProxy->getContent(), 200);
        $response->headers->set('Content-Type', FileUtil::getMIMEType($fileProxy->getExtension()));

        return $response;
    }

    /**
     * @Route("{repositoryId}/getAvailableFileProperties", name="frepoGetAvailableFileProperties", options={"expose"=true})
     */
    public function getAvailablePropertiesAction($repositoryId)
    {
        $repo = FileRepositoryFactory::fromId($repositoryId);
        $repo->setParameters($this->get('request')->query->all());

        $filePath = $this->get('request')->query->get('filePath');
        $properties = $repo->getAvailableFileProperties($filePath == "_root" ? null : $filePath);
        $data = [];
        foreach($properties as $property) {
            $data[] = $property->toArray();
        }
        $response = new JsonResponse($data, 200);
        return $response;
    }

    /**
     * Helper, called by RFE to suggest file search string based on what the user types
     * @Route("{repositoryId}/queryStringSuggestion", name="frepoQueryStringSuggestion", options={"expose"=true})
     */
    public function getQueryStringSuggestion($repositoryId)
    {
        $repo = FileRepositoryFactory::fromId($repositoryId);
        $repo->setParameters($this->get('request')->query->all());

        $filePath = $this->get('request')->query->get('filePath');
        $userQuery = $this->get('request')->query->get('query');
        $cursorPosition = $this->get('request')->query->get('selectionEnd');

        $firstHalf = substr($userQuery,0,$cursorPosition+1);
        $secondHalf = substr($userQuery,$cursorPosition+1,strlen($userQuery)-$cursorPosition);

        $data = [];

        preg_match('%^([^/]*)(.*?)$%im', $firstHalf, $m);
        $queryBegin = trim($m[1]);
        $variablesSpec = $m[2];

        $variableExpressions = explode('/',$variablesSpec);
        $hasLastExpression = count($variableExpressions)>1;
        $lastExpression = array_pop($variableExpressions);

        foreach($variableExpressions as &$ve)
            if($v = trim($ve))
                $ve = "/{$v}";

        $fullQueryBegin = $queryBegin.implode(" ", $variableExpressions);

        if($hasLastExpression && preg_match('/^([^=]*)=*(.*)$/im', $lastExpression, $m)) {
            $properties = $repo->getAvailableFileProperties($filePath == "_root" ? null : $filePath, true);
            $variableName = $m[1];
            $variableValue = $m[2];
            $hasEqual = strpos($lastExpression, '=');

            $dedup = [];
            foreach($properties as $property) {
                if(!$variableName || (strpos($property->getName(), $variableName) !== false)) {
                    if($hasEqual && ($vm = $property->getValueMap())) {
                        foreach($vm->getMap() as $mapValue) {
                            if( !isset($dedup[$property->getName()][$mapValue['value']]) && (!$variableValue || strpos($mapValue['value'], $variableValue) !== false))
                                $data[] = ['query' => $fullQueryBegin.' /'.$property->getName().'='.$mapValue['value'].$secondHalf];
                            $dedup[$property->getName()][$mapValue['value']] = 1;
                        }
                    } else {
                        if(!isset($dedup[$property->getName()]))
                            $data[] = ['query' => $fullQueryBegin.' /'.$property->getName().'='.$secondHalf];
                        $dedup[$property->getName()] = 1;
                    }
                }
            }
        }

        $response = new JsonResponse($data, 200);
        return $response;
    }

}
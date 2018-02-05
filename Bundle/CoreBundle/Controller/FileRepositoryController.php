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
use Eulogix\Cool\Lib\DataSource\FileRepositoryDataSource;
use Eulogix\Cool\Lib\File\FileRepositoryFactory;
use Eulogix\Cool\Lib\File\FileRepositoryInterface;
use Eulogix\Cool\Lib\File\FileRepositoryPreviewProvider;
use Eulogix\Cool\Lib\File\FileUtil;
use Eulogix\Lib\File\Proxy\SimpleFileProxy;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

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

        if($repo->getUserPermissions()->canDownloadFile($filePath)) {
            $fileProxy = $repo->get($filePath);

            $tempFile = tempnam( Cool::getInstance()->getFactory()->getSettingsManager()->getTempFolder(), 'DOWNLOAD');
            $fileProxy->toFile($tempFile);
            $response = new BinaryFileResponse($tempFile, 200);
            $response->headers->set('Content-Type', FileUtil::getMIMEType($fileProxy->getExtension()));
            $response->headers->set('Content-Disposition', "attachment; filename=\"".$fileProxy->getName()."\"");
            $response->deleteFileAfterSend(true);
            return $response;
        } else return $this->getForbiddenResponse();
    }

    /**
     * downloads a file using the storage directly
     * @Route("/downloadDirect/{schema}/{actualSchema}/{fileId}", name="frepoDownloadDirect", options={"expose"=true})
     */
    public function downloadDirectAction($schema, $actualSchema, $fileId)
    {
        $schema = Cool::getInstance()->getSchema($schema);
        $schema->setCurrentSchema($actualSchema);
        $storage = Cool::getInstance()->getFactory()->getSchemaFileStorage($schema);

        $fileProxy = $storage->getById($fileId);
        $tempFile = tempnam( Cool::getInstance()->getFactory()->getSettingsManager()->getTempFolder(), 'DOWNLOAD');
        $fileProxy->toFile($tempFile);
        $response = new BinaryFileResponse($tempFile, 200);
        $response->headers->set('Content-Type', FileUtil::getMIMEType($fileProxy->getExtension()));
        $response->headers->set('Content-Disposition', "attachment; filename=\"".$fileProxy->getName()."\"");
        $response->deleteFileAfterSend(true);
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
        if($repo->getUserPermissions()->canDownloadFile($filePath)) {
            $fileProxy = $repo->get($filePath);
            $tempFile = tempnam( Cool::getInstance()->getFactory()->getSettingsManager()->getTempFolder(), 'DOWNLOAD');
            $fileProxy->toFile($tempFile);
            $response = new BinaryFileResponse($tempFile, 200);
            $response->headers->set('Content-Type', FileUtil::getMIMEType($fileProxy->getExtension()));
            $response->headers->set('Content-Disposition', "filename=\"".$fileProxy->getName()."\"");
            $response->deleteFileAfterSend(true);
            return $response;
        } else return $this->getForbiddenResponse();
    }

    /**
     * serves a file using the storage directly
     * @Route("/serveDirect/{schema}/{actualSchema}/{fileId}", name="frepoServeDirect", options={"expose"=true})
     */
    public function serveDirectAction($schema, $actualSchema, $fileId)
    {
        $schema = Cool::getInstance()->getSchema($schema);
        $schema->setCurrentSchema($actualSchema);
        $storage = Cool::getInstance()->getFactory()->getSchemaFileStorage($schema);

        $fileProxy = $storage->getById($fileId);
        $tempFile = tempnam( Cool::getInstance()->getFactory()->getSettingsManager()->getTempFolder(), 'DOWNLOAD');
        $fileProxy->toFile($tempFile);
        $response = new BinaryFileResponse($tempFile, 200);
        $response->headers->set('Content-Type', FileUtil::getMIMEType($fileProxy->getExtension()));
        $response->headers->set('Content-Disposition', "filename=\"".$fileProxy->getName()."\"");
        $response->deleteFileAfterSend(true);
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
        $filePath = $filePath == FileRepositoryDataSource::ROOT_PLACEHOLDER ? null : $filePath;

        $properties = $repo->getAvailableFileProperties($filePath);
        $data = [];
        foreach($properties as $property) {
            $data[] = $property->toArray();
        }
        $response = new JsonResponse($data, 200);
        return $response;
    }

    /**
     * @Route("{repositoryId}/getContextualMenuFor", name="frepoGetContextualMenuFor", options={"expose"=true})
     */
    public function getContextualMenuForAction($repositoryId)
    {
        $repo = FileRepositoryFactory::fromId($repositoryId);
        $repo->setParameters($this->get('request')->query->all());

        $filePath = $this->get('request')->query->get('filePath');
        $filePath = $filePath == FileRepositoryDataSource::ROOT_PLACEHOLDER ? null : $filePath;

        $contextMenu = $repo->getContextualMenuFor($filePath);
        $data = $contextMenu ? $contextMenu->getDefinition() : [];
        $response = new JsonResponse($data, 200);
        return $response;
    }

    /**
     * @Route("{repositoryId}/getPermissions", name="frepoGetPermissions", options={"expose"=true})
     */
    public function getPermissionsAction($repositoryId)
    {
        $repo = FileRepositoryFactory::fromId($repositoryId);
        $repo->setParameters($this->get('request')->query->all());
        $filePath = $this->get('request')->query->get('filePath');
        $filePath = $filePath == FileRepositoryDataSource::ROOT_PLACEHOLDER ? null : $filePath;

        $permissions = $repo->getUserPermissions()->getAllFor($filePath);

        $response = new JsonResponse($permissions, 200);
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
            $properties = $repo->getAvailableFileProperties($filePath == FileRepositoryDataSource::ROOT_PLACEHOLDER ? null : $filePath, true);
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

    /**
     * handles file uploads from within CKeditor
     * @Route("{repositoryId}/CKUpload", name="frepoCKUpload", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function CKUploadAction($repositoryId)
    {
        $data = [
            "uploaded" => 0,
            "error" => [
                "message" => "no files uploaded"
            ]
        ];

        try {
            $repo = FileRepositoryFactory::fromId($repositoryId);
            $filePath = $this->get('request')->query->get('filePath');
            if(!$repo->exists($filePath))
                $repo->createFolder($filePath);

            /**
             * @var UploadedFile $uploadedFile
             */
            foreach($this->get('request')->files->getIterator() as $uploadedFile) {

                $proxy = new SimpleFileProxy();
                $proxy->setName($uploadedFile->getClientOriginalName())
                    ->setContentFile($uploadedFile->getPathname());
                $storedFile = $repo->storeFileAt($proxy, $filePath, FileRepositoryInterface::COLLISION_STRATEGY_RENAME);

                $data = [
                    "uploaded" => 1,
                    "fileName" => $storedFile->getName(),
                    "url" => urldecode(Cool::getInstance()->getContainer()->get('router')->generate('frepoServe', [
                        'repositoryId' => $repositoryId,
                        'filePath' => $filePath.DIRECTORY_SEPARATOR.$storedFile->getName()
                    ]))
                ];
            }
        } catch(\Exception $e) {
            $data = [
                "uploaded" => 0,
                "error" => [
                    "message" => $e->getMessage()
                ]
            ];
        }

        $response = new JsonResponse($data, 200);
        return $response;

    }

    /**
     * @return Response
     */
    private function getForbiddenResponse()
    {
        $response = new Response();
        $response->setContent("Content forbidden");
        $response->setStatusCode('403');
        return $response;
    }

    /**
     * @Route("/{repositoryId}/browser", name="frepoBrowser", options={"expose"=true})
     * @Template(engine="twig")
     */
    public function fileRepositoryBrowserAction($repositoryId)
    {
        $templateVars = $this->get('request')->query->all();
        $templateVars['repositoryId'] = $repositoryId;
        return $this->render('EulogixCoolCoreBundle:File:fileRepositoryBrowser.html.twig', $templateVars);
    }
}

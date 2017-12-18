<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Controller\Api;

use Eulogix\Cool\Lib\File\CoolTableFileRepository;
use Eulogix\Cool\Lib\File\FileRepositoryFactory;
use Eulogix\Lib\File\Proxy\SimpleFileProxy;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Symfony\Controller\BaseRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\Annotations\RequestParam;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class RestFileRepositoryController extends BaseRestController
{
    /**
     * Creates a folder
     *
     * statusCodes
     *      200 = "Returned when successful",
     *      400 = "Returned when the form has errors"
     *
     * @Route("createFolder")
     * @Method({"POST"})
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="repositoryId", nullable=false, strict=true, description="the id of the repository (service)")
     * @RequestParam(name="repositoryParameters", nullable=true, strict=true, description="json repository initialization parameters")
     * @RequestParam(name="folderPath", nullable=false, strict=true, description="path in which the new folder will be created")
     * @RequestParam(name="folderName", nullable=false, strict=true, description="folder name")
     *
     * @Rest\View()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createFolderAction(ParamFetcher $paramFetcher)
    {
        $repositoryId = $paramFetcher->get('repositoryId');
        $repositoryParameters = json_decode( $paramFetcher->get('repositoryParameters'), true) ?? [];
        $folderPath = $paramFetcher->get('folderPath');
        $folderName = $paramFetcher->get('folderName');

        $message = 'ok';

        try {

            $repo = FileRepositoryFactory::fromId($repositoryId);
            $repo->setParameters($repositoryParameters);
            $repo->createFolder($folderPath, $folderName);

        } catch(\Exception $e) {
            switch($e->getCode()) {
                case -1 : {
                    $proxy = new SimpleFileProxy();
                    $proxy->setId(-1);
                    $message = $e->getMessage();
                    break;
                }
                default: return $this->returnError(500);
            }
        }

        $data = array_merge(['message' => $message ]);

        $view = $this->view($data, 200);
        return $this->handleView($view);
    }

    /**
     * Uploads a new file to the specified file repository
     *
     * statusCodes
     *      200 = "Returned when successful",
     *      400 = "Returned when the form has errors"
     *
     * @Route("upload")
     * @Method({"POST"})
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="repositoryId", nullable=false, strict=true, description="the id of the repository (service)")
     * @RequestParam(name="repositoryParameters", nullable=true, strict=true, description="json repository initialization parameters")
     * @RequestParam(name="filePath", nullable=false, strict=true, description="directory in which to upload the file")
     * @RequestParam(name="fileName", nullable=false, strict=true, description="file Name")
     * @RequestParam(name="fileContent", nullable=false, strict=true, description="BASE64 encoded file content")
     * @RequestParam(name="collisionStrategy", nullable=false, strict=true, requirements="(overwrite|skip|append)", default="overwrite", description="overwrite flag")
     *
     * @Rest\View()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function uploadFileAction(ParamFetcher $paramFetcher)
    {
        $repositoryId = $paramFetcher->get('repositoryId');
        $repositoryParameters = json_decode( $paramFetcher->get('repositoryParameters'), true) ?? [];
        $filePath = $paramFetcher->get('filePath');
        $fileName = $paramFetcher->get('fileName');
        $collisionStrategy = $paramFetcher->get('collisionStrategy');

        $message = 'ok';

        try {

            $repo = FileRepositoryFactory::fromId($repositoryId);
            $repo->setParameters($repositoryParameters);

            if(!$repo->exists($filePath))
                return $this->returnError(404, "Directory $filePath does not exist");

            $fp = new SimpleFileProxy();
            $fp->setName( $fileName );
            $t = tempnam(Cool::getInstance()->getFactory()->getSettingsManager()->getTempFolder(),'RESTUPLOAD');
            file_put_contents($t, base64_decode($paramFetcher->get('fileContent')));
            $fp->setContentFile($t);
            $proxy = $repo->storeFileAt($fp, $filePath, $collisionStrategy);
            $fp->clear();
        } catch(\Exception $e) {
            switch($e->getCode()) {
                case -1 : {
                    $proxy = new SimpleFileProxy();
                    $proxy->setId(-1);
                    $message = $e->getMessage();
                    break;
                }
                default: return $this->returnError(500);
            }
        }

        $data = array_merge(['message' => $message ], $proxy->getArray());

        $view = $this->view($data, 200);
        return $this->handleView($view);
    }

    /**
     * deletes a file or folder
     *
     * @Route("delete")
     * @Method({"POST"})
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="repositoryId", nullable=false, strict=true, description="the id of the repository (service)")
     * @RequestParam(name="repositoryParameters", nullable=true, strict=true, description="json repository initialization parameters")
     * @RequestParam(name="filePath", nullable=false, strict=true, description="file or folder to delete")
     *
     * @Rest\View()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(ParamFetcher $paramFetcher)
    {
        $repositoryId = $paramFetcher->get('repositoryId');
        $repositoryParameters = json_decode( $paramFetcher->get('repositoryParameters'), true) ?? [];
        $filePath = $paramFetcher->get('filePath');

        try {

            $repo = FileRepositoryFactory::fromId($repositoryId);
            $repo->setParameters($repositoryParameters);

            if(!$repo->exists($filePath))
                return $this->returnError(404, "File or folder $filePath does not exist");

            $repo->delete($filePath);

        } catch(\Exception $e) {
            return $this->returnError(500, $e->getMessage());
        }

        $data = [];

        $view = $this->view($data, 200);
        return $this->handleView($view);
    }

    /**
     * performs a search and returns files or handles
     *
     * @Route("search")
     * @Method({"POST"})
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="repositoryId", nullable=false, strict=true, description="the id of the repository (service)")
     * @RequestParam(name="repositoryParameters", nullable=true, strict=true, description="json repository initialization parameters")
     * @RequestParam(name="path", nullable=false, strict=true, description="root for the search")
     * @RequestParam(name="name", nullable=true, strict=true, description="file name")
     * @RequestParam(name="page", nullable=true, strict=true, requirements="[0-9]+", default="0", description="the page to fetch")
     * @RequestParam(name="fetchContent", nullable=true, strict=true, requirements="(true|false)", default="false", description="wether to fetch content or not")
     *
     * @Rest\View()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchFilesAction(ParamFetcher $paramFetcher)
    {
        $repositoryId = $paramFetcher->get('repositoryId');
        $repositoryParameters = json_decode( $paramFetcher->get('repositoryParameters'), true) ?? [];
        $path = $paramFetcher->get('path');

        $name = $paramFetcher->get('name');
        $fetchContent = $paramFetcher->get('fetchContent') == 'true';

        /* TODO
        $page = 0+$paramFetcher->get('page');

        try {
            $schema = Cool::getInstance()->getSchema($schemaName);
            $storage = Cool::getInstance()->getFactory()->getSchemaFileStorage($schema);

            $filesSearcher = $storage->query($table, $pk, $cat, $name, $fetchContent);
            $files = $filesSearcher->getPage($page);

            if($fetchContent)
                foreach($files as &$f)
                    $f['content'] = base64_encode($f['content']);

            $data = [
                'total_count' => $filesSearcher->getTotalSize(),
                'pages_count' => $filesSearcher->countPages(),
                'page' => $page,
                'page_size' => $filesSearcher->getPageSize(),
                'files' => $files
            ];

        } catch(\Exception $e) {
            return $this->returnError(500, $e->getMessage());
        }

        $view = $this->view($data, 200);
        return $this->handleView($view);
        */
        return $this->returnError(500, "TODO");
    }

    /**
     * @Route("version")
     * @Method({"GET"})
     * @Rest\View()
     */
    public function getVersionAction()
    {
        $data = [
            'version' => '0.1',
        ];
        $view = $this->view($data, 200);
        return $this->handleView($view);
    }
}
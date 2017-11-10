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
use Eulogix\Cool\Lib\File\SimpleFileProxy;
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

class RestFilesController extends BaseRestController
{
    /**
     * Uploads a new file to the specified record
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
     * @RequestParam(name="schemaName", nullable=false, strict=true, description="schema")
     * @RequestParam(name="actualSchema", nullable=false, strict=true, description="real physical schema name")
     * @RequestParam(name="table", nullable=false, strict=true)
     * @RequestParam(name="pk", nullable=false, strict=true)
     * @RequestParam(name="fileName", nullable=false, strict=true, description="file Name")
     * @RequestParam(name="fileContent", nullable=false, strict=true, description="BASE64 encoded file content")
     * @RequestParam(name="collisionStrategy", nullable=false, strict=true, requirements="(overwrite|skip|append)", default="overwrite", description="overwrite flag")
     * @RequestParam(name="category", nullable=true, strict=true, description="optional category")
     *
     * @Rest\View()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function uploadFileAction(ParamFetcher $paramFetcher)
    {

        $schemaName = $paramFetcher->get('schemaName');
        $actualSchema = $paramFetcher->get('actualSchema');
        $table = $paramFetcher->get('table');
        $pk = $paramFetcher->get('pk');
        $collisionStrategy = $paramFetcher->get('collisionStrategy');

        $message = 'ok';

        if($cp = $this->checkSchema($schemaName, $actualSchema)) return $cp;

        try {
            $schema = Cool::getInstance()->getSchema($schemaName);
            if(!$obj = $schema->getPropelObject($table, $pk))
                return $this->returnError(404);

            $fp = new SimpleFileProxy();
            $fp->setName( $paramFetcher->get('fileName') );
            $t = tempnam(sys_get_temp_dir(),'RESTUPLOAD');
            file_put_contents($t, base64_decode($paramFetcher->get('fileContent')));
            $fp->setContentFile($t);
            $cat = $paramFetcher->get('category');
            $proxy = $obj->getFileRepository()->storeFileAt($fp, $cat ? 'cat_'.$cat : null, $collisionStrategy);
            @unlink($t);
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
     * sets properties for a given file
     *
     * @Route("setProperties")
     * @Method({"POST"})
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="schemaName", nullable=false, strict=true, description="schema")
     * @RequestParam(name="actualSchema", nullable=false, strict=true, description="real physical schema name")
     * @RequestParam(name="fileId", nullable=false, strict=true)
     * @RequestParam(name="fileProperties", nullable=false, strict=true, description="file properties")
     * @RequestParam(name="merge", nullable=true, strict=true, description="if set, merges the provided properties with the existing ones")
     *
     * @Rest\View()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function setFilePropertiesAction(ParamFetcher $paramFetcher)
    {

        $schemaName = $paramFetcher->get('schemaName');
        $actualSchema = $paramFetcher->get('actualSchema');
        $fileId = $paramFetcher->get('fileId');
        $merge = $paramFetcher->get('merge') != null;
        $fileProperties = json_decode( $paramFetcher->get('fileProperties'), true);
        $fileProperties = $fileProperties ? $fileProperties : [];

        if($cp = $this->checkSchema($schemaName, $actualSchema)) return $cp;

        try {
            $schema = Cool::getInstance()->getSchema($schemaName);
            $repo = CoolTableFileRepository::fromSchema($schema);

            if(!$repo->exists($fileId))
                return $this->returnError(404);

            $repo->setFileProperties($fileId, $fileProperties, $merge);

        } catch(\Exception $e) {
            return $this->returnError(500);
        }

        $data = [];

        $view = $this->view($data, 200);
        return $this->handleView($view);
    }

    /**
     * deletes a file
     *
     * @Route("delete")
     * @Method({"POST"})
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="schemaName", nullable=false, strict=true, description="schema")
     * @RequestParam(name="actualSchema", nullable=false, strict=true, description="real physical schema name")
     * @RequestParam(name="fileId", nullable=false, strict=true)
     *
     * @Rest\View()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteFileAction(ParamFetcher $paramFetcher)
    {

        $schemaName = $paramFetcher->get('schemaName');
        $actualSchema = $paramFetcher->get('actualSchema');
        $fileId = $paramFetcher->get('fileId');

        if($cp = $this->checkSchema($schemaName, $actualSchema)) return $cp;

        try {
            $schema = Cool::getInstance()->getSchema($schemaName);
            $repo = CoolTableFileRepository::fromSchema($schema);

            if(!$repo->exists($fileId))
                return $this->returnError(404);

            $repo->delete($fileId);

        } catch(\Exception $e) {
            return $this->returnError(500, $e->getMessage());
        }

        $data = [];

        $view = $this->view($data, 200);
        return $this->handleView($view);
    }

    /**
     * retrieves all the files in a given folder
     *
     * @Route("getRecordFiles")
     * @Method({"POST"})
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="schemaName", nullable=false, strict=true, description="schema")
     * @RequestParam(name="actualSchema", nullable=false, strict=true, description="real physical schema name")
     * @RequestParam(name="table", nullable=false, strict=true)
     * @RequestParam(name="pk", nullable=false, strict=true)
     * @RequestParam(name="category", nullable=true, strict=true, description="optional category")
     * @RequestParam(name="recursive", nullable=true, strict=true, description="recursive flag")
     *
     * @Rest\View()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getRecordFilesAction(ParamFetcher $paramFetcher)
    {
        $schemaName = $paramFetcher->get('schemaName');
        $actualSchema = $paramFetcher->get('actualSchema');
        $table = $paramFetcher->get('table');
        $pk = $paramFetcher->get('pk');
        $cat = $paramFetcher->get('category');
        $recursive = $paramFetcher->get('recursive');

        if($cp = $this->checkSchema($schemaName, $actualSchema)) return $cp;

        try {
            $schema = Cool::getInstance()->getSchema($schemaName);
            if(!$obj = $schema->getPropelObject($table, $pk))
                return $this->returnError(404);

            $data = [];
            $files = $obj->getFileRepository()->getChildrenOf($cat ? $cat : '', $recursive);

            /**
             * @var SimpleFileProxy $f
             */
            foreach($files->getIterator() as $f) {
                $data[] = $f->getArray();
            }

        } catch(\Exception $e) {
            return $this->returnError(500);
        }

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
     * @RequestParam(name="schemaName", nullable=false, strict=true, description="schema")
     * @RequestParam(name="actualSchema", nullable=false, strict=true, description="real physical schema name")
     * @RequestParam(name="table", nullable=true, strict=true)
     * @RequestParam(name="pk", nullable=true, strict=true)
     * @RequestParam(name="category", nullable=true, strict=true, description="optional category")
     * @RequestParam(name="name", nullable=true, strict=true, description="file name")
     * @RequestParam(name="page", nullable=true, strict=true, requirements="[0-9]+", default="0", description="the page to fetch")
     * @RequestParam(name="fetchContent", nullable=true, strict=true, requirements="(true|false)", default="false", description="wether to fetch content or not")
     *
     * @Rest\View()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchFilesAction(ParamFetcher $paramFetcher)
    {
        $schemaName = $paramFetcher->get('schemaName');
        $actualSchema = $paramFetcher->get('actualSchema');
        $table = $paramFetcher->get('table');
        $pk = $paramFetcher->get('pk');
        $cat = $paramFetcher->get('category');
        $name = $paramFetcher->get('name');
        $fetchContent = $paramFetcher->get('fetchContent') == 'true';

        $page = 0+$paramFetcher->get('page');

        if($cp = $this->checkSchema($schemaName, $actualSchema)) return $cp;

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
    }

    /**
     * gets properties for a given file
     *
     * @Route("getProperties")
     * @Method({"POST"})
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="schemaName", nullable=false, strict=true, description="schema")
     * @RequestParam(name="actualSchema", nullable=false, strict=true, description="real physical schema name")
     * @RequestParam(name="fileId", nullable=false, strict=true)
     *
     * @Rest\View()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getFilePropertiesAction(ParamFetcher $paramFetcher)
    {

        $schemaName = $paramFetcher->get('schemaName');
        $actualSchema = $paramFetcher->get('actualSchema');
        $fileId = $paramFetcher->get('fileId');

        if($cp = $this->checkSchema($schemaName, $actualSchema)) return $cp;

        try {
            $schema = Cool::getInstance()->getSchema($schemaName);
            $repo = CoolTableFileRepository::fromSchema($schema);

            if(!$repo->exists($fileId))
                return $this->returnError(404);

            $data = $repo->getFileProperties($fileId);

        } catch(\Exception $e) {
            return $this->returnError(500, $e->getMessage());
        }

        $view = $this->view($data, 200);
        return $this->handleView($view);
    }

    /**
     * gets available properties for a given table/category
     *
     * @Route("getAvailableProperties")
     * @Method({"POST"})
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="schemaName", nullable=false, strict=true, description="schema")
     * @RequestParam(name="actualSchema", nullable=true, strict=true, description="real physical schema name")
     * @RequestParam(name="table", nullable=true, strict=true)
     * @RequestParam(name="category", nullable=true, strict=true)
     *
     * @Rest\View()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAvailablePropertiesAction(ParamFetcher $paramFetcher)
    {

        $schemaName = $paramFetcher->get('schemaName');
        $actualSchema = $paramFetcher->get('actualSchema');
        $table = $paramFetcher->get('table');
        $category = $paramFetcher->get('category');

        if($cp = $this->checkSchema($schemaName, $actualSchema ? $actualSchema : $schemaName)) return $cp;

        try {
            $properties = Cool::getInstance()->getCoreSchema()->getAvailableFileProperties($schemaName, $actualSchema, $table, $category);
            $data = [];
            foreach($properties as $property) {
                $data[] = $property->toArray();
            }

        } catch(\Exception $e) {
            return $this->returnError(500, $e->getMessage());
        }

        $view = $this->view($data, 200);
        return $this->handleView($view);
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

    private function checkSchema($schema, $actualSchema) {
        try {
            Cool::getInstance()->getSchema($schema)->setCurrentSchema($actualSchema);
            Cool::getInstance()->initSchemas();
        } catch(\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
        return false;
    }
}
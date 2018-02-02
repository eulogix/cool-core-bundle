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
use Eulogix\Cool\Lib\DataSource\CoolValueMap;
use Eulogix\Cool\Lib\Dictionary\Lookup;
use Eulogix\Cool\Lib\File\FileUtil;
use Eulogix\Cool\Lib\Translation\Translator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class AjaxController extends Controller
{

    /**
     * receives files from the client, stores them in a temporary location and returns the details to the client so that
     * it can build the outer request (usually a form)
     *
     * @Route("/uploadFiles", name="_coolUploader", options={"expose"=true})
     */
    public function uploadFiles()
    {
        $tempManager = Cool::getInstance()->getFactory()->getFileTempManager();
        $ret = [];


        if(isset($_FILES['uploadedfile'])) {
            //single HTML5 upload, may come from a form field
            $uploadedFileName = $_FILES['uploadedfile']['name'];
            $fileId = $tempManager->storeFile($uploadedFileName, $_FILES['uploadedfile']['tmp_name']);
            //housekeeping
            @unlink($_FILES['uploadedfile']['tmp_name']);
            $ret[$uploadedFileName] = $fileId;
        }elseif(isset($_FILES['uploadedfiles'])) {
            //multiple HTML5 upload, may come from a form field
            $fileCount = count($_FILES['uploadedfiles']['name']);
            for($i=0; $i<$fileCount; $i++) {
                $uploadedFileName = $_FILES['uploadedfiles']['name'][$i];
                $fileId = $tempManager->storeFile($uploadedFileName, $_FILES['uploadedfiles']['tmp_name'][$i]);
                //housekeeping
                @unlink($_FILES['uploadedfiles']['tmp_name'][$i]);
                $ret[$uploadedFileName] = $fileId;
            }
        }

        return new JsonResponse( $ret );
    }

    /**
     * @Route("/downloadTempFile/{key}", name="_downloadTempFile")
     */
    public function downloadTempFile($key)
    {
        $fileProxy = Cool::getInstance()->getFactory()->getFileTempManager()->getFileProxyFromTempKey($key);
        $tempFile = tempnam( Cool::getInstance()->getFactory()->getSettingsManager()->getTempFolder() ,'DOWNLOAD');
        $fileProxy->toFile($tempFile);
        $response = new BinaryFileResponse($tempFile, 200);

        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileProxy->getName()
        );

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', FileUtil::getMIMEType($fileProxy->getExtension()));
        $response->deleteFileAfterSend(true);
        return $response;
    }

    /**
     * @Route("/serveTempFile/{key}", name="_serveTempFile")
     */
    public function serveTempFile($key)
    {
        $fileProxy = Cool::getInstance()->getFactory()->getFileTempManager()->getFileProxyFromTempKey($key);
        $tempFile = tempnam( Cool::getInstance()->getFactory()->getSettingsManager()->getTempFolder() ,'DOWNLOAD');
        $fileProxy->toFile($tempFile);
        $response = new BinaryFileResponse($tempFile, 200);

        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $fileProxy->getName()
        );

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', FileUtil::getMIMEType($fileProxy->getExtension()));
        $response->deleteFileAfterSend(true);
        return $response;
    }

    /**
     * @Route("/vmap/column/{schema}/{table}/{column}", name="columnVmap", options={"expose"=true}, defaults={"value" = null})
     */
    public function columnVmapAction($schema, $table, $column, Request $request)
    {
        Cool::getInstance()->getFactory()->getSessionHandler()->close();

        if($request->request->has('actualschema'))
            Cool::getInstance()->getSchema($schema)->setCurrentSchema($request->request->get('actualschema'));

        $data = [];

        if( $vmap = CoolValueMap::getValueMapFor($schema, $table, $column, $recordId=null) ) {

            $vmap->getParameters()->add( $request->query->all() );

            $requestValues = array_merge(
                $request->query->all(),
                $request->request->all()
            );
            $value = @$requestValues['value'];

            $data = $value=='[{NIL}]'?
                ['value'=>'[{NIL}]', 'label'=>'-']
                :$vmap->getMap($value, str_replace('*', '', @$requestValues['label']), @$requestValues, 30);
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/vmap/service/{serviceId}", name="serviceVmap", options={"expose"=true}, defaults={"value" = null})
     */
    public function serviceVmapAction($serviceId, Request $request)
    {
        Cool::getInstance()->getFactory()->getSessionHandler()->close();

        $data = [];

        if( $vmap = Cool::getInstance()->getContainer()->get($serviceId) ) {

            $vmap->getParameters()->add( $request->query->all() );

            $requestValues = array_merge(
                $request->query->all(),
                $request->request->all()
            );
            $value = @$requestValues['value'];

            $data = $value=='[{NIL}]'?
                ['value'=>'[{NIL}]', 'label'=>'-']
                :$vmap->getMap($value, str_replace('*','',@$requestValues['label']), $requestValues, 30);
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/vmap/dictionaryLookup", name="dictionaryLookupVmap", options={"expose"=true})
     */
    public function dictionaryLookupVmapAction(Request $request)
    {
        Cool::getInstance()->getFactory()->getSessionHandler()->close();

        $data = [];

        /**
         * @var Lookup $lookup
         */
        $lookup = unserialize(gzuncompress(base64_decode($request->get('lookup'))));

        if( $vmap = $lookup->getValueMap() ) {

            $vmap->getParameters()->add( $request->query->all() );

            $requestValues = array_merge(
                $request->query->all(),
                $request->request->all()
            );
            $value = @$requestValues['value'];

            $data = $value=='[{NIL}]'?
                ['value'=>'[{NIL}]', 'label'=>'-']
                :$vmap->getMap($value, str_replace('*','',@$requestValues['label']), $requestValues, 30);
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/getTranslation/{locale}", name="getTranslation", options={"expose"=true})
     */
    public function getTranslationAction($locale)
    {
        $translator = new Translator();
        $translator->setDomains( $this->get('request')->query->get('domain') );
        $translator->setLocale( $locale );
        $translator->setExpose(true);

        return new JsonResponse($translator->trans($this->get('request')->query->get('id')));
    }

    /**
     * @Route("/cacheData", name="_cacheData", options={"expose"=true})
     */
    public function cacheDataAction()
    {
        $data = $this->get('request')->request->get('data');
        $tempKey = md5(serialize($data));
        Cool::getInstance()->getFactory()->getSharedCacher()->store($tempKey, $data);
        return new JsonResponse($tempKey);
    }

}

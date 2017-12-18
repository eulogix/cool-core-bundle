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

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\File\FileRepositoryFactory;
use Eulogix\Cool\Lib\Symfony\Controller\BaseRestController;
use Eulogix\Lib\File\Proxy\SimpleFileProxy;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\Controller\Annotations\RequestParam;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class TemplatesController extends BaseRestController
{
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

    /**
     * Creates a folder
     *
     * statusCodes
     *      200 = "Returned when successful",
     *      400 = "Returned when the form has errors"
     *
     * @Route("render")
     * @Method({"POST"})
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="repositoryId", nullable=false, strict=true, description="the id of the repository (service)")
     * @RequestParam(name="repositoryParameters", nullable=true, strict=true, description="json repository initialization parameters")
     * @RequestParam(name="templatePath", nullable=false, strict=true, description="template full path")
     * @RequestParam(name="templateData", nullable=false, strict=true, description="JSON data to be rendered by the template")
     * @RequestParam(name="outputFormat", nullable=false, strict=true, description="file format of the desired output")
     * @RequestParam(name="rendererParameters", nullable=false, strict=true, description="JSON parameters which will be fed to the renderer")
     *
     * @Rest\View()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderTemplateAction(ParamFetcher $paramFetcher)
    {
        $repositoryId = $paramFetcher->get('repositoryId');
        $repositoryParameters = json_decode( $paramFetcher->get('repositoryParameters'), true) ?? [];
        $templatePath = $paramFetcher->get('templatePath');
        $templateData = json_decode( $paramFetcher->get('templateData'), true) ?? [];
        $outputFormat = $paramFetcher->get('outputFormat');
        $rendererParameters = json_decode( $paramFetcher->get('rendererParameters'), true) ?? [];

        $message = 'ok';
        $retData = [];

        try {

            $repo = FileRepositoryFactory::fromId($repositoryId);
            $repo->setParameters($repositoryParameters);

            $template = $repo->get($templatePath);
            if($renderer = Cool::getInstance()->getFactory()->getTemplateRendererFactory()->getRendererFor($template)) {
                $renderer->getParameters()->replace($rendererParameters);
                $renderer->setData($templateData);
                $output = $renderer->getRenderedOutput($outputFormat);
                $retData = $output->getArray();
                $retData['content'] = base64_encode($output->getContent());
            } else throw new \Exception("no valid renderers found for template $templatePath");

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

        $data = array_merge(['message' => $message ], $retData);

        $view = $this->view($data, 200);
        return $this->handleView($view);
    }
}
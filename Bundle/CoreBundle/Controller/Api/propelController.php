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
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class PropelController extends FOSRestController {

    /**
     * @Route("{schemaName}/{actualSchema}/{table}/{pk}")
     * @Method({"GET"})
     * @Rest\View()
     */
    public function getObjectAction($schemaName, $actualSchema, $table, $pk, Request $request)
    {
        if($cp = $this->checkSchema($schemaName, $actualSchema)) return $cp;

        try {
            $schema = Cool::getInstance()->getSchema($schemaName);
            if(!$obj = $schema->getPropelObject($table, $pk))
                return $this->returnError(404);
        } catch(\Exception $e) {
            return $this->returnError(500);
        }

        $view = $this->view($obj->toArray(\BasePeer::TYPE_FIELDNAME), 200);
        return $this->handleView($view);
    }

    /**
     * @Route("{schemaName}/{actualSchema}/{table}/{pk}")
     * @Method({"POST"})
     * @Rest\View()
     */
    public function updateObjectAction($schemaName, $actualSchema, $table, $pk, Request $request)
    {
        if($cp = $this->checkSchema($schemaName, $actualSchema)) return $cp;

        try {
            $schema = Cool::getInstance()->getSchema($schemaName);
            if(!$obj = $schema->getPropelObject($table, $pk))
                return $this->returnError(404);
        } catch(\Exception $e) {
            return $this->returnError(500);
        }

        try {
            $obj->extendedFromArray($request->get("object"));
            $obj->save();
        } catch(\Exception $e) {
            return $this->returnError(403, $e->getMessage()." Bad parameters: missing $table JSON hash?");
        }

        $view = $this->view($obj->toArray(\BasePeer::TYPE_FIELDNAME), 200);
        return $this->handleView($view);
    }

    /**
     * @Route("{schemaName}/{actualSchema}/{table}")
     * @Method({"POST"})
     * @Rest\View()
     */
    public function createObjectAction($schemaName, $actualSchema, $table, Request $request)
    {
        if($cp = $this->checkSchema($schemaName, $actualSchema)) return $cp;

        try {
            $schema = Cool::getInstance()->getSchema($schemaName);
            if(!$obj = $schema->getPropelObject($table))
                return $this->returnError(404);
        } catch(\Exception $e) {
            return $this->returnError(500);
        }

        try {
            $obj->extendedFromArray($request->get("object"));
            $obj->save();
        } catch(\Exception $e) {
            return $this->returnError(403, $e->getMessage()." Bad parameters: missing 'object' JSON hash?");
        }

        $view = $this->view($obj->toArray(\BasePeer::TYPE_FIELDNAME), 200);
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

    /**
     * @param int $code
     * @param string $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function returnError($code, $message="")
    {
        if(!$message) {
            switch($code) {
                case 500: $message = "Internal error"; break;
                case 404: $message = "Resource not found"; break;
            }
        }

        $view = $this->view(
            [
                'statusCode' => $code,
                'errorMessage' => $message
            ],
            $code
        );

        return $this->handleView($view);
    }
} 
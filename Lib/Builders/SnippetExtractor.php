<?php
/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Builders;

use Doctrine\Common\Annotations\AnnotationReader;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\CodeSnippet;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\CodeSnippetVariable;
use Eulogix\Cool\Lib\Annotation\SnippetMeta;
use Eulogix\Cool\Lib\Util\ReflectionUtils;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class SnippetExtractor
{

    /**
     * @param $FQNClassName
     * @return CodeSnippet[];
     */
    public static function getFromClass($FQNClassName) {

        $ret = [];

        $snippetMetaReader = new AnnotationReader();
        $rClass = new \ReflectionClass($FQNClassName);
        foreach($rClass->getMethods() as $rMethod) {
            $snippetMetaAnnotation = $snippetMetaReader->getMethodAnnotation($rMethod, 'Eulogix\\Cool\\Lib\\Annotation\\SnippetMeta');
            if($snippetMetaAnnotation) {
                $rParameterNames = [];
                /**
                 * @var SnippetMeta $snippetMetaAnnotation
                 */
                $snippet = new CodeSnippet();
                $snippet->setName($rMethod->getName())
                        ->setNspace($rClass->getNamespaceName())
                        ->setCategory( $snippetMetaAnnotation->category )
                        ->setDescription( $snippetMetaAnnotation->description )
                        ->setLongDescription( $snippetMetaAnnotation->longDescription )
                        ->setLanguage( CodeSnippet::LANGUAGE_PHP )
                        ->setReturnType( $snippetMetaAnnotation->getReturnType($rMethod) );

                foreach( $rMethod->getParameters() as $rParameter) {
                    $rParameterNames[] = '$'.$rParameter->getName();
                    if(!$snippetMetaAnnotation->shouldIgnoreParameter($rParameter->getName())) {
                        $variable = new CodeSnippetVariable();
                        $variable->setName($rParameter->getName());
                        $variable->setCodeSnippet($snippet);
                        if( $parameterInfo = $snippetMetaAnnotation->getParameterInfo($rParameter) ) {
                            $variable->setDescription($parameterInfo['description']);
                        }
                    }
                }

                $parametersString = implode(', ',$rParameterNames);
                $directInvocationStatement = "\\$FQNClassName::{$rMethod->getName()}({$parametersString})";

                if($snippetMetaAnnotation->directInvocation) {
                    $methodBody = $directInvocationStatement;
                    $snippet->setType( CodeSnippet::TYPE_EXPRESSION );
                } else {
                    $methodBody = ReflectionUtils::getMethodBody($rMethod);
                    if(preg_match('/\s*return\s+(.+?);\s*$/sim', $methodBody, $m)) {
                        $snippet->setType( CodeSnippet::TYPE_EXPRESSION);
                        $methodBody = $m[1];
                    } else $snippet->setType( CodeSnippet::TYPE_FUNCTION_BODY);
                }

                $snippet->setSnippet("/* automatically generated from\n$directInvocationStatement\n*/\n\n$methodBody");

                $ret[] = $snippet;
            }
        }

        return $ret;
    }
}
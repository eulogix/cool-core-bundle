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
                        ->setNspace($rClass->getName())
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

                $methodBody = ReflectionUtils::getMethodBody($rMethod);

                if($snippetMetaAnnotation->directInvocation) {
                    $snippetBody = $directInvocationStatement;
                    $snippet->setType( CodeSnippet::TYPE_EXPRESSION );
                } else {
                    $snippetBody = $methodBody;
                    if(preg_match('/\s*return\s+(.+?);\s*$/sim', $snippetBody, $m)) {
                        $snippet->setType( CodeSnippet::TYPE_EXPRESSION);
                        $snippetBody = $m[1];
                    } else $snippet->setType( CodeSnippet::TYPE_FUNCTION_BODY);
                }

                $commentedBody = preg_replace('/^(.+?)$/sim', '// $1', $methodBody);
                $snippet->setSnippet("$snippetBody\n\n\n// automatically generated from\n\n//\t$directInvocationStatement\n\n// Original code:\n\n$commentedBody");

                $ret[] = $snippet;
            }
        }

        return $ret;
    }
}
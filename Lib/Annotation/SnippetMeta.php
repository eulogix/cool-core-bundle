<?php
/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Annotation;

use Doctrine\Common\Annotations\Annotation;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\CodeSnippet;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

/**
 * @Annotation
 * @Target("METHOD")
 */
final class SnippetMeta extends Annotation
{
    public $category;

    public $contextIgnore = [];

    public $description;

    public $longDescription;

    /**
     * if true, the body of the snippet will be a static call to the class method
     * if false, it will be copied over as raw PHP
     * @var bool
     */
    public $directInvocation = false;

    /**
     * @return string[]
     */
    public function getIgnoredParameters() {
        return is_array($this->contextIgnore) ? $this->contextIgnore : [$this->contextIgnore];
    }

    /**
     * @param string $parameterName
     * @return bool
     */
    public function shouldIgnoreParameter($parameterName) {
        return in_array($parameterName, $this->getIgnoredParameters());
    }

    /**
     * @param \ReflectionParameter $parameter
     * @return string
     */
    public function getParameterInfo(\ReflectionParameter $parameter)
    {
        $parameterName = $parameter->name;
        // Get the content of the @param annotation
        $method = $parameter->getDeclaringFunction();
        if (preg_match($rx = "/@param\\s+([^\\s]+)\\s+\\\${$parameterName}\\s?(.*?)$/sim", $method->getDocComment(), $matches)) {
            return [
                'type' => $matches[1],
                'description' => $matches[2]
            ];
        } else {
            return null;
        }
    }

    /**
     * @param \ReflectionMethod $method
     * @return string
     */
    public function getReturnType(\ReflectionMethod $method)
    {
        if (preg_match($rx = "/@return\\s+([^\\s]+)$/sim", $method->getDocComment(), $matches)) {
            switch(strtolower($matches[1])) {
                case 'bool':
                case 'boolean':
                    return CodeSnippet::RETURN_TYPE_BOOLEAN;
                case 'array' :
                    return CodeSnippet::RETURN_TYPE_ARRAY;
                case 'string':
                    return CodeSnippet::RETURN_TYPE_STRING;
                case 'object':
                    return CodeSnippet::RETURN_TYPE_OBJECT;
                case 'int':
                case 'double':
                    return CodeSnippet::RETURN_TYPE_NUMBER;
            }

       }
       return CodeSnippet::RETURN_TYPE_NONE;
    }

}
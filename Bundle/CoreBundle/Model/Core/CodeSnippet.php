<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\om\BaseCodeSnippet;

class CodeSnippet extends BaseCodeSnippet
{
    const LANGUAGE_PHP = 'PHP';

    const TYPE_EXPRESSION = 'EXPRESSION';
    const TYPE_FUNCTION_BODY = 'FUNCTION_BODY';

    const RETURN_TYPE_BOOLEAN = 'BOOLEAN';
    const RETURN_TYPE_STRING = 'STRING';
    const RETURN_TYPE_NUMBER = 'NUMBER';
    const RETURN_TYPE_ARRAY = 'ARRAY';
    const RETURN_TYPE_OBJECT = 'OBJECT';
    const RETURN_TYPE_NONE = 'NONE';

    /**
     * @inheritdoc
     */
    public function getHumanDescription() {
        return implode(" - ", [ $this->getDecodedField('category'), $this->getDescription() ]);
    }

    /**
     * evaluates the template as an expression
     * @param array $context
     * @return mixed
     * @throws \Exception
     */
    public function evaluate(array $context=[]) {

        try {
            $wkContext = $context;
            $vars = $this->getCodeSnippetVariables();

            //default to NULL for missing vars
            foreach ($vars as $var) {
                if (!in_array($var->getName(), array_keys($context))) {
                    $wkContext[ $var->getName() ] = null;
                }
            }

            return evaluate_in_lambda($this->getSnippet(), $wkContext, $this->getType() == self::TYPE_EXPRESSION);
        } catch(\Error $e) {
            throw new \Exception('Snippet '.$this->getHumanDescription().' produced an ERROR: '.$e->getMessage());
        } catch(\Throwable $e) {
            throw new \Exception('Snippet '.$this->getHumanDescription().' threw an Exception : '.$e->getMessage(), 0, $e);
        }
    }

}

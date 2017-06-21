<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\om\BaseCodeSnippet;

class CodeSnippet extends BaseCodeSnippet
{
    const LANGUAGE_PHP = 'PHP';

    const TYPE_EXPRESSION = 'EXPRESSION';
    const TYPE_FUNCTION_BODY = 'FUNCTION_BODY';

    /**
     * @inheritdoc
     */
    public function getHumanDescription() {
        return implode(" - ", [ $this->getCategory(), $this->getName() ]);
    }

    /**
     * evaluates the template as an expression
     * @param array $context
     * @return mixed
     * @throws \Exception
     */
    public function evaluate(array $context=[]) {

        $vars = $this->getCodeSnippetVariables();
        foreach($vars as $var)
            if(!in_array($var->getName(), array_keys($context)))
                throw new \Exception("Can't evaluate snippet. Context variable ".$var->getName()." missing.");

        return evaluate_in_lambda($this->getSnippet(), $context, $this->getType() == self::TYPE_EXPRESSION);
    }

}

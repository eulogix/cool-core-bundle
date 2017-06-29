<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\om\BaseRuleCode;

class RuleCode extends BaseRuleCode
{
    const TYPE_VARIABLE = "VARIABLE";
    const TYPE_EXEC_IF_TRUE = "EXEC_IF_TRUE";
    const TYPE_EXEC_IF_FALSE = "EXEC_IF_FALSE";

    /**
     * @param array $context
     * @return mixed
     * @throws \Exception
     */
    public function evaluate(array $context=[]) {
        if($rawCode = $this->getRawCode())
            return evaluate_in_lambda($rawCode, $context);
        if($sn = $this->getCodeSnippet()) {
            return $sn->evaluate( array_merge($this->getSubstitutedSnippetVariables($context), $context) );
        }
    }

    /**
     * substitutes $var with $context['var'] in each of the snippet variables
     * @param array $context
     * @return array
     */
    public function getSubstitutedSnippetVariables(array $context) {
        $vars = $this->getSnippetVariablesAsArray();

        foreach($vars as $varName => &$var) {

            if(preg_match('/^\$([a-z0-9_]+)$/sim', $var, $matches)) {

                /**
                 * check if the variable provided is exactly a single variable reference, like "$var"
                 * in this case, we try to assign an existing variable from the context
                 * passing its PHP reference directly
                 */

                $varName = $matches[1];
                if(isset($context[$varName]))
                    $var = $context[$varName];
            } else {

                /**
                 * if the variable provided contains references to more than one variable, like "$var ($var2)"
                 * we assume that this is a template and we perform a basic string replacement
                 */

                preg_match_all('/\$(\w+)/sim', $var, $matches, PREG_SET_ORDER);
                if($matches) {
                    foreach($matches as $match) {
                        $variablePlaceHolder = $match[0];
                        $varName = $match[1];
                        $var = str_replace($variablePlaceHolder, @$context[$varName], $var);
                    }
                }
            }
        }

        return $vars;
    }

    /**
     * @return array
     */
    public function getSnippetVariablesAsArray() {
        return json_decode($this->getCodeSnippetVariables(), true) ?? [];
    }
}

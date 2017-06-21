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
        if($sn = $this->getCodeSnippet())
            return $sn->evaluate(array_merge($this->getSnippetVariablesAsArray(), $context));
    }

    /**
     * @return array
     */
    public function getSnippetVariablesAsArray() {
        return json_decode($this->getCodeSnippetVariables(), true) ?? [];
    }
}

<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\om\BaseRuleCode;
use Symfony\Component\Stopwatch\Stopwatch;

class RuleCode extends BaseRuleCode
{
    const TYPE_VARIABLE = "VARIABLE";
    const TYPE_EXEC_IF_TRUE = "EXEC_IF_TRUE";
    const TYPE_EXEC_IF_FALSE = "EXEC_IF_FALSE";

    const REPORT_RETURN_VALUE = 'return_value';
    const REPORT_EXECUTION_TIME = 'execution_time';
    const REPORT_MEMORY_USAGE = 'memory_usage';

    /**
     * @var array
     */
    private $lastExecutionReport = [];

    /**
     * @param array $context
     * @return mixed
     * @throws \Exception
     */
    public function evaluate(array $context=[]) {

        try {
            $this->resetLastExecutionReport();

            $ret = null;

            $stopwatch = new Stopwatch();
            $stopwatch->start('eval');

            if($rawCode = $this->getRawCode())
                $ret = evaluate_in_lambda($rawCode, $context);
            if($sn = $this->getCodeSnippet()) {
                $ret = $sn->evaluate( array_merge($this->getSubstitutedSnippetVariables($context), $context) );
            }

            $event = $stopwatch->stop('eval');

            $this->lastExecutionReport[self::REPORT_RETURN_VALUE] = $ret;
            $this->lastExecutionReport[self::REPORT_EXECUTION_TIME] = $event->getDuration();
            $this->lastExecutionReport[self::REPORT_MEMORY_USAGE] = $event->getMemory();

            //TODO fill properly
            $this->lastExecutionReport['errors'] = [];

            return $ret;
        } catch(\Throwable $e) {
            throw new \Exception("Error in Rule code ".$this->getRuleCodeId().': '.$e->getMessage(), 0, $e);
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
                        $var = str_replace($variablePlaceHolder, $context[$varName] ?? null, $var);
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

    /**
     * @return array
     */
    public function getLastExecutionReport()
    {
        return $this->lastExecutionReport;
    }

    private function resetLastExecutionReport() {
        $this->lastExecutionReport = [];
    }

}

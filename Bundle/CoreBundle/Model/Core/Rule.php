<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\om\BaseRule;
use Eulogix\Lib\Graph\Graph;
use Hoa\Ruler\Context;
use Hoa\Ruler\Ruler;
use Symfony\Component\Stopwatch\Stopwatch;

class Rule extends BaseRule
{
    const EXPRESSION_TYPE_HOA = 'HOA';
    const EXPRESSION_TYPE_PHP = 'PHP';

    const REPORT_CODE_EVALUATION_ORDER = 'evaluation_order';
    const REPORT_CODE_TYPE = 'code_type';

    const REPORT_EXECUTION_TIME = 'execution_time';
    const REPORT_MEMORY_USAGE = 'memory_usage';

    private $lastExecutionReport = [];

    private $varExecutionOrder = 1;

    /**
     * @var StopWatch
     */
    private $timer;

    /**
     * @param array $contextArray
     * @return mixed
     */
    public function assert(array $contextArray=[]) {

        $this->resetLastExecutionReport();

        $wkContext = $this->getEvaluatedVariables($contextArray);

        switch($this->getExpressionType()) {
            case self::EXPRESSION_TYPE_HOA: {
                $ruler = new Ruler();
                $context = new Context($wkContext);
                $ret = $ruler->assert($this->getExpression(), $context);
                break;
            }
            case self::EXPRESSION_TYPE_PHP: {
                $ret = evaluate_in_lambda($this->getExpression(), $wkContext, !preg_match('/return[ \t]+.+?;$/sim', $this->getExpression()) );
                break;
            }
            default: $ret = null;
        }

        $this->stopTiming();

        return $ret;
    }

    /**
     * @param array $contextArray
     * @return array
     * @throws \Exception
     * @throws \PropelException
     */
    private function getEvaluatedVariables(array $contextArray) {
        $evaluatedVariables = $contextArray;
        $order = $this->getCodeExecutionOrder( $this->getCodeVariables() );
        foreach($order as $varName) {
            $var = RuleCodeQuery::create()->filterByRule($this)->filterByType( RuleCode::TYPE_VARIABLE )->findOneByName( $varName );
            $evaluatedVariables[ $var->getName() ] = $var->evaluate($evaluatedVariables);
            $this->logExecution($var);
        }
        return $evaluatedVariables;
    }

    /**
     * @param string $type
     * @param $context
     * @return $this
     */
    public function execCodes($type, $context) {

        $this->resetLastExecutionReport();

        $wkContext = $context;

        $codes = $this->getCodes( [RuleCode::TYPE_VARIABLE, $type] );
        $order = $this->getCodeExecutionOrder( $codes );

        foreach($order as $varName) {
            $var = RuleCodeQuery::create()->filterByRule($this)->findOneByName( $varName );
            $wkContext[ $var->getName() ] = $var->evaluate($wkContext);
            $this->logExecution($var);
        }

        $this->stopTiming();

        return $this;
    }

    /**
     * @param string[] $types
     * @return RuleCode[]
     * @throws \PropelException
     */
    private function getCodes( array $types )
    {
        return RuleCodeQuery::create()->filterByRule($this)->filterByEnabledFlag(true)->filterByType( $types )->find();
    }

    /**
     * @return RuleCode[]
     */
    private function getCodeVariables() {
        return $this->getCodes( [RuleCode::TYPE_VARIABLE] );
    }


    /**
     * @param RuleCode[] $ruleCodes
     * @return \string[]
     * @throws \Exception
     */
    private function getCodeExecutionOrder($ruleCodes) {

        // Create a graph with one vertex per variable
        $g = new Graph();
        foreach($ruleCodes as $code) {
            $definedVars = $code->getSnippetVariablesAsArray();
            $g->addVertex( $code->getName(), $definedVars );
        }

        // now, if one variable vv of a vertex v depends on another variable vv1, we suppose that vv1 is the name of a vertex
        // and we add an edge from v to vv1
        foreach($g->getVertices() as $vertexId => $vertex) {
            if(is_array($vertex['data']))
                foreach($vertex['data'] as $varName => $varExpression) {
                    preg_match_all('/\$(\w+)/sim', $varExpression, $matches, PREG_SET_ORDER);
                    if($matches) {
                        foreach($matches as $match) {
                            $varName = $match[1];
                            $g->addEdge(null, $varName, $vertexId);
                        }
                    }
                }
        }

        //now, if there are loops in the graph we have to raise an exception as the variable values can not be resolved
        $loops = $g->getStronglyConnectedComponents();
        if(count($loops) > 0) {
            foreach($loops as $loop)
                throw new \Exception("Variables ".implode(',', $loop)." define an unresolvable loop.");
        }

        $g->TopologicalVertexSort();

        return $g->getVertexIds();
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
        $this->varExecutionOrder = 1;
        $this->timer = new Stopwatch();
        $this->timer->start('eval');
    }

    private function stopTiming() {
        $event = $this->timer->stop('eval');

        $this->lastExecutionReport[self::REPORT_EXECUTION_TIME] = $event->getDuration();
        $this->lastExecutionReport[self::REPORT_MEMORY_USAGE] = $event->getMemory();
    }

    /**
     * @param RuleCode $ruleCode
     */
    private function logExecution($ruleCode) {
        $this->lastExecutionReport['codes'][ $ruleCode->getName() ] = array_merge( $ruleCode->getLastExecutionReport(), [
            self::REPORT_CODE_EVALUATION_ORDER => $this->varExecutionOrder++,
            self::REPORT_CODE_TYPE => $ruleCode->getType()
        ]);
    }

}

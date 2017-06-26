<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\om\BaseRule;
use Eulogix\Lib\Graph\Graph;
use Hoa\Ruler\Context;
use Hoa\Ruler\Ruler;

class Rule extends BaseRule
{
    const EXPRESSION_TYPE_HOA = 'HOA';
    const EXPRESSION_TYPE_PHP = 'PHP';

    /**
     * @param array $contextArray
     * @return mixed
     */
    public function assert(array $contextArray=[]) {
        $ruler = new Ruler();
        $finalContext = $this->getEvaluatedVariables($contextArray);
        $context = new Context($finalContext);
        return $ruler->assert($this->getExpression(), $context);

    }

    /**
     * @param array $contextArray
     * @return array
     * @throws \Exception
     * @throws \PropelException
     */
    private function getEvaluatedVariables(array $contextArray) {
        $evaluatedVariables = $contextArray;
        $order = $this->getVariableExecutionOrder();
        foreach($order as $varName) {
            $var = RuleCodeQuery::create()->filterByRule($this)->filterByType( RuleCode::TYPE_VARIABLE )->findOneByName( $varName );
            $evaluatedVariables[ $var->getName() ] = $var->evaluate($evaluatedVariables);
        }
        return $evaluatedVariables;
    }

    /**
     * @param string $type
     * @param $context
     * @return array
     */
    public function execCodes($type, $context) {
        $wkContext = $this->getEvaluatedVariables($context);

        $report = [];
        $codes = $this->getCodes( $type );
        foreach($codes as $code) {
            $report[ $code->getName() ] = [
                'return_value' => $code->evaluate($wkContext)
            ];
        }

        return $report;
    }

    /**
     * @param string $type
     * @return RuleCode[]
     * @throws \PropelException
     */
    private function getCodes( $type = null)
    {
        return RuleCodeQuery::create()->filterByRule($this)->filterByType( $type )->find();
    }


    /**
     * @return string[]
     * @throws \Exception
     */
    private function getVariableExecutionOrder() {
        $vars = $this->getCodes( RuleCode::TYPE_VARIABLE );

        // Create a graph with one vertex per variable
        $g = new Graph();
        foreach($vars as $var) {
            $definedVars = $var->getSnippetVariablesAsArray();
            $g->addVertex( $var->getName(), $definedVars );
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

}

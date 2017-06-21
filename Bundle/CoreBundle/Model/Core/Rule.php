<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\om\BaseRule;
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
        $context = new Context($contextArray);

        $vars = $this->getCodes( RuleCode::TYPE_VARIABLE );
        foreach($vars as $var)
            $context[ $var->getName() ] = $var->evaluate($contextArray);

        return $ruler->assert($this->getExpression(), $context);

    }

    /**
     * @param string $type
     * @param $context
     * @return array
     */
    public function execCodes($type, $context) {
        $report = [];
        $codes = $this->getCodes( $type );
        foreach($codes as $code) {
            $report[ $code->getName() ] = [
                'return_value' => $code->evaluate($context)
            ];
        }
        return $report;
    }

    /**
     * @param string $type
     * @return RuleCode[]
     * @throws \PropelException
     */
    private function getCodes( $type )
    {
        return RuleCodeQuery::create()->filterByRule($this)->filterByType( $type )->find();
    }

}

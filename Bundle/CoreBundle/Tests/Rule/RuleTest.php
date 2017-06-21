<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Tests\Rule;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\CodeSnippet;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\CodeSnippetVariable;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\Rule;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\RuleCode;
use Eulogix\Cool\Bundle\CoreBundle\Tests\Cases\baseTestCase;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class NotificationsControllerTest extends baseTestCase
{
    public function testExpressionSnippet()
    {
        $sn = new CodeSnippet();
        $sn ->setCategory('c1')
            ->setLanguage( CodeSnippet::LANGUAGE_PHP )
            ->setType( CodeSnippet::TYPE_EXPRESSION )
            ->setDescription('a test snippet that evaluates an expression')
            ->setName('test_1')
            ->setSnippet("\$var1 + \$var2 + (\$thirdNumber ?? 0)")
            ->save();

        $var1 = new CodeSnippetVariable();
        $var1->setCodeSnippet($sn)
            ->setName('var1')
            ->setDescription('first number')
            ->save();

        $var2 = new CodeSnippetVariable();
        $var2->setCodeSnippet($sn)
            ->setName('var2')
            ->setDescription('second number')
            ->save();

        $this->assertEquals( 7, $sn->evaluate(["var1"=>1, "var2"=>2, 'thirdNumber' => 4]));

        return $sn;
    }

    public function testFunctionSnippet()
    {
        $sn = new CodeSnippet();
        $sn ->setCategory('c1')
            ->setLanguage( CodeSnippet::LANGUAGE_PHP )
            ->setType( CodeSnippet::TYPE_FUNCTION_BODY )
            ->setDescription('a test snippet that evaluates as a function')
            ->setName('test_2')
            ->setSnippet("\$temp = \$var1 + \$var2; return \$temp + (\$thirdNumber ?? 0);")
            ->save();

        $var1 = new CodeSnippetVariable();
        $var1->setCodeSnippet($sn)
            ->setName('var1')
            ->setDescription('first number')
            ->save();

        $var2 = new CodeSnippetVariable();
        $var2->setCodeSnippet($sn)
            ->setName('var2')
            ->setDescription('second number')
            ->save();

        $this->assertEquals( 10, $sn->evaluate(["var1"=>5, "var2"=>2, 'thirdNumber' => 3]));

        return $sn;
    }

    /**
     * @depends testExpressionSnippet
     * @depends testFunctionSnippet
     * @param CodeSnippet $sn
     * @param CodeSnippet $sn2
     * @throws \Exception
     * @throws \PropelException
     */
    public function testRule($sn, $sn2) {
        $r = new Rule();
        $r ->setName("a test rule".microtime())
           ->setCategory("c1")
           ->setExpressionType( Rule::EXPRESSION_TYPE_HOA )
           ->setExpression("sum(sn1,sn2) = 10")
           ->save();

        $rv = new RuleCode();
        $rv ->setType(RuleCode::TYPE_VARIABLE)
            ->setName("sn1")
            ->setRule($r)
            ->setCodeSnippet($sn)
            ->setCodeSnippetVariables(json_encode(["var1"=>1, "var2"=>2]))
            ->save();

        $rv2 = new RuleCode();
        $rv2->setType(RuleCode::TYPE_VARIABLE)
            ->setName("sn2")
            ->setRule($r)
            ->setCodeSnippet($sn2)
            ->setCodeSnippetVariables(json_encode(["var1"=>5, "var2"=>2]))
            ->save();

        $this->assertTrue( $r->assert() );
    }
}

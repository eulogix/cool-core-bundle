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

        //we test that no exception is raised
        $var3 = new CodeSnippetVariable();
        $var3->setCodeSnippet($sn)
            ->setName('var3_optional')
            ->setDescription('an optional var')
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
     * @return Rule
     * @throws \Exception
     * @throws \PropelException
     */
    public function testRule($sn, $sn2) {
        $r = new Rule();
        $r ->setName("a test rule".microtime())
           ->setCategory("c1")
           ->setExpressionType( Rule::EXPRESSION_TYPE_HOA )
           ->setExpression("sum(sn1,sn2) = 20")
           ->save();

        // sn1 = 11
        $rv = new RuleCode();
        $rv ->setType(RuleCode::TYPE_VARIABLE)
            ->setName("sn1")
            ->setRule($r)
            ->setCodeSnippet($sn)
            ->setCodeSnippetVariables(json_encode(["var1"=>'$sn2', "var2"=>2]))
            ->save();

        // sn2 = 9
        $rv2 = new RuleCode();
        $rv2->setType(RuleCode::TYPE_VARIABLE)
            ->setName("sn2")
            ->setRule($r)
            ->setCodeSnippet($sn2)
            ->setCodeSnippetVariables(json_encode(["var1"=>'$sn4', "var2"=>2]))
            ->save();

        // sn4 = 13
        $rv3 = new RuleCode();
        $rv3 ->setType(RuleCode::TYPE_VARIABLE)
            ->setName("sn3")
            ->setRule($r)
            ->setCodeSnippet($sn)
            ->setCodeSnippetVariables(json_encode(["var1"=>'$sn1', "var2"=>2]))
            ->save();

        // sn4 = 7
        $rv4 = new RuleCode();
        $rv4 ->setType(RuleCode::TYPE_VARIABLE)
            ->setName("sn4")
            ->setRule($r)
            ->setCodeSnippet($sn)
            ->setCodeSnippetVariables(json_encode(["var1"=>'$sn5', "var2"=>'$sn1']))
            ->save();

        // sn5 = 3
        $rv5 = new RuleCode();
        $rv5 ->setType(RuleCode::TYPE_VARIABLE)
            ->setName("sn5")
            ->setRule($r)
            ->setCodeSnippet($sn)
            ->setCodeSnippetVariables(json_encode(["var1"=>1, "var2"=>2]))
            ->save();

        //test the unresolvable loop
        try {
            $this->assertTrue( $r->assert() );
        } catch (\Exception $e) { $emess = $e->getMessage(); }

        $this->assertEquals("Variables sn1,sn2,sn4 define an unresolvable loop.", $emess);

        //fix the rule and assert it via HOA
        $rv4->setCodeSnippetVariables(json_encode(["var1"=>'$sn5', "var2"=>4]))->save();
        $this->assertTrue( $r->assert() );

        //test PHP evaluation
        $r->setExpressionType(Rule::EXPRESSION_TYPE_PHP)
            ->setExpression("\$avar = \$sn1 + \$sn2; return \$avar == 20;")
            ->save();

        $this->assertTrue( $r->assert() );

        return $r;
    }


    /**
     * @depends testRule
     * @param Rule $r
     * @throws \Exception
     * @throws \PropelException
     */
    public function testExecCodes($r) {

        $sn = new CodeSnippet();
        $sn ->setCategory('c1')
            ->setLanguage( CodeSnippet::LANGUAGE_PHP )
            ->setType( CodeSnippet::TYPE_FUNCTION_BODY )
            ->setDescription('a test snippet that returns a value')
            ->setName('test_ret')
            ->setSnippet("return \$value;")
            ->save();

        $var1 = new CodeSnippetVariable();
        $var1->setCodeSnippet($sn)
            ->setName('value')
            ->setDescription('the value to return')
            ->save();

        $rv = new RuleCode();
        $rv ->setType(RuleCode::TYPE_EXEC_IF_TRUE)
            ->setName("exec2")
            ->setRule($r)
            ->setCodeSnippet($sn)
            ->setCodeSnippetVariables(json_encode(["value"=>'This code should be executed last. Return value of previous code should be 2 : $exec1_ret returned']))
            ->save();

        $rv = new RuleCode();
        $rv ->setType(RuleCode::TYPE_EXEC_IF_TRUE)
            ->setName("exec1_ret")
            ->setRule($r)
            ->setCodeSnippet($sn)
            ->setCodeSnippetVariables(json_encode(["value"=>2]))
            ->save();

        $report = $r->execCodes(RuleCode::TYPE_EXEC_IF_TRUE, []);
        $this->assertEquals(['exec1_ret','exec2'], array_keys($report));

    }
}

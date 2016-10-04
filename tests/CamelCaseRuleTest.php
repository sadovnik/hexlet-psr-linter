<?php

namespace PsrLinter\Tests;

use PHPUnit\Framework\TestCase;

use PsrLinter\Rules\CamelCaseRule;
use PsrLinter\Messages\WarningMessage;
use PsrLinter\RuleResults\OkRuleResult;
use PsrLinter\RuleResults\WarningRuleResult;

use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\Array_;

use function PsrLinter\Rules\isCamelCase;
use function PsrLinter\Rules\convertToCamelCase;

class CamelCaseRuleTest extends TestCase
{
    /**
     * @dataProvider providePassingNodes
     */
    public function testPassingNodes($node)
    {
        $checker = new CamelCaseRule();
        $result = $checker->verify($node);
        $this->assertInstanceOf(OkRuleResult::class, $result);
    }

    public function providePassingNodes()
    {
        return [
            [ new Function_('doSomeStuff') ],
            [ new ClassMethod('doSomeStuff') ],
            [ new ClassMethod('__construct') ]
        ];
    }

    /**
     * @dataProvider provideNodesWithErrors
     */
    public function testErrors($node, $expectedError)
    {
        $checker = new CamelCaseRule();
        $result = $checker->verify($node);
        $this->assertInstanceOf(WarningRuleResult::class, $result);
        $this->assertEquals($expectedError, $result);
    }

    public function provideNodesWithErrors()
    {
        return [
            [
                new Function_('do_some_stuff', [], [ 'startLine' => 1 ]),
                new WarningRuleResult(
                    'Wrong function name: do_some_stuff',
                    'Function names must be declared in camelCase.',
                    CamelCaseRule::class,
                    new Function_('do_some_stuff', [], [ 'startLine' => 1 ])
                )
            ],
            [
                new ClassMethod('do_some_stuff', [], [ 'startLine' => 1 ]),
                new WarningRuleResult(
                    'Wrong method name: do_some_stuff',
                    'Method names must be declared in camelCase.',
                    CamelCaseRule::class,
                    new ClassMethod('do_some_stuff', [], [ 'startLine' => 1 ])
                )
            ]
        ];
    }
}

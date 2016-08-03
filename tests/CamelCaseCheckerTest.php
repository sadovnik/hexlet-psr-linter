<?php

namespace PsrLinter\Tests;

use PHPUnit\Framework\TestCase;

use PsrLinter\Checkers\CamelCaseChecker;

use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\Array_;

class CamelCaseCheckerTest extends TestCase
{
    /**
     * @dataProvider providePassingNodes
     */
    public function testPassingNodes($node)
    {
        $checker = new CamelCaseChecker;
        $checker->check($node);
        $actualErrors = $checker->getErrors();
        $this->assertEmpty($actualErrors);
    }

    public function providePassingNodes()
    {
        return [
            [ new Function_('doSomeStuff') ],
            [ new ClassMethod('doSomeStuff') ],
            [ new Assign(new Variable('doSomeStuff'), new Closure()) ],
            [ new Array_() ] // random node
        ];
    }

    /**
     * @dataProvider provideNodesWithErrors
     */
    public function testErrors($node, $expectedErrors)
    {
        $checker = new CamelCaseChecker;
        $checker->check($node);
        $actualErrors = $checker->getErrors();
        $this->assertEquals([ $expectedErrors ], $actualErrors);
    }

    public function provideNodesWithErrors()
    {
        // node array, errors array
        return [
            [
                new Function_('do_some_stuff', [], [ 'startLine' => 1 ]),
                [
                    'line' => 1,
                    'title' => 'Wrong function name.',
                    'description' => 'Function names must be declared in camelCase.'
                ]
            ],
            [
                new ClassMethod('do_some_stuff', [], [ 'startLine' => 1 ]),
                [
                    'line' => 1,
                    'title' => 'Wrong method name.',
                    'description' => 'Method names must be declared in camelCase.'
                ]
            ],
            [
                new Assign(new Variable('do_some_stuff'), new Closure(), [ 'startLine' => 1 ]),
                [
                    'line' => 1,
                    'title' => 'Wrong callable name.',
                    'description' => 'Callable names must be declared in camelCase.'
                ]
            ]
        ];
    }
}

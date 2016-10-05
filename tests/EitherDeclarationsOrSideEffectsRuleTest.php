<?php

namespace PsrLinter\Tests;

use PHPUnit\Framework\TestCase;

use PsrLinter\Rules\EitherDeclarationsOrSideEffectsRule;

use PsrLinter\RuleResults\OkRuleResult;
use PsrLinter\RuleResults\ErrorRuleResult;

use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use PhpParser\Node\Scalar;

use function PsrLinter\Rules\isCamelCase;
use function PsrLinter\Rules\convertToCamelCase;

class EitherDeclarationsOrSideEffectsRuleTest extends TestCase
{
    /**
     * @dataProvider provideTestOkData
     */
    public function testOk(...$nodes)
    {
        $rule = new EitherDeclarationsOrSideEffectsRule;
        foreach ($nodes as $node) {
            $rule->verify($node);
        }
        $result = $rule->conclude();
        $this->assertInstanceOf(OkRuleResult::class, $result);
    }

    public function provideTestOkData()
    {
        return [
            // empty file
            [],
            [ new Stmt\Function_([]) ],
            [ new Expr\Assign(new Expr\Variable('foo'), new Scalar\String_('bar')) ],
            [ new Stmt\Echo_([ new Scalar\String_('hello there') ]) ],

            [ new Expr\Assign(new Expr\Variable('foo'), new Scalar\String_('bar')),
              new Stmt\Class_('Foo') ],

            [ new Expr\Assign(new Expr\Variable('foo'), new Scalar\String_('bar')),
              new Stmt\Function_('foo') ],

            [ new Expr\Assign(new Expr\Variable('foo'), new Scalar\String_('bar')),
              new Stmt\Trait_('FooTrait') ],

            [ new Expr\Assign(new Expr\Variable('foo'), new Scalar\String_('bar')),
              new Expr\Closure ],
        ];
    }

    /**
     * @dataProvider provideTestErrorData
     */
    public function testError(...$nodes)
    {
        $rule = new EitherDeclarationsOrSideEffectsRule;
        foreach ($nodes as $node) {
            $rule->verify($node);
        }
        $result = $rule->conclude();
        $this->assertInstanceOf(ErrorRuleResult::class, $result);
        $this->assertEquals('Side effects and declarations are mixed.', $result->getTitle());
    }

    public function provideTestErrorData()
    {
        return [
            [ new Stmt\Function_([]),
              new Expr\Eval_(new Expr\Exit_) ],

            [ new Stmt\Class_('Foo'),
              new Expr\Assign(new Expr\Variable('foo'), new Scalar\String_('bar')) ],

            [ new Stmt\Trait_('FooTrait'),
              new Expr\Assign(new Expr\Variable('foo'), new Scalar\String_('bar')) ],

            [ new Stmt\Echo_([ new Scalar\String_('hello there') ]),
              new Stmt\Function_([]) ],
        ];
    }
}

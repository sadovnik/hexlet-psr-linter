<?php

namespace PsrLinter\Tests;

use PHPUnit\Framework\TestCase;

use PsrLinter\Rules\RuleCollection;
use PsrLinter\Rules\CamelCaseRule;
use PsrLinter\Rules\EitherDeclarationsOrSideEffectsRule;
use PhpParser\Node\Stmt\ClassMethod;

class RuleCollectionTest extends TestCase
{
    public function testGetAssociatedRules()
    {
        $collection = new RuleCollection([ new CamelCaseRule ]);
        $actual = $collection->getAssociatedRules(new ClassMethod([]));
        $expected = [ new CamelCaseRule ];
        $this->assertEquals($expected, $actual);
    }

    public function testGetFilewideRules()
    {
        $collection = new RuleCollection([
            new CamelCaseRule,
            new EitherDeclarationsOrSideEffectsRule
        ]);
        $rules = $collection->getAssociatedRules(new ClassMethod([]));
        $expected = [ new CamelCaseRule ];
        $this->assertEquals($expected, $rules);
    }
}

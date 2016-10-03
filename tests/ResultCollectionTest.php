<?php

namespace PsrLinter\Tests;

use PHPUnit\Framework\TestCase;
use PsrLinter\RuleResults\ResultCollection;
use PsrLinter\RuleResults\OkRuleResult;
use PsrLinter\RuleResults\FixedRuleResult;
use PsrLinter\RuleResults\WarningRuleResult;
use PsrLinter\RuleResults\ErrorRuleResult;

class ResultCollectionTest extends TestCase
{
    public $collection;

    public function setUp()
    {
        $this->collection = new ResultCollection;
    }

    public function testAdd()
    {
        $ruleResult = new OkRuleResult(null);
        $this->collection->add($ruleResult);
        $this->assertEquals(
            [ $ruleResult ],
            $this->collection->getAll()
        );
    }

    public function testIsEmpty()
    {
        $this->assertTrue($this->collection->isEmpty());
        $this->collection->add(new OkRuleResult(null));
        $this->assertFalse($this->collection->isEmpty());
    }

    public function testHasErrorsWithEmptyCollection()
    {
        $this->assertFalse($this->collection->hasErrors());
    }

    /**
     * @dataProvider provideHasErrorsData
     */
    public function testHasErrorsWithRuleResult()
    {
        $this->collection->add(new OkRuleResult(null));
        $this->assertFalse($this->collection->hasErrors());
    }

    public function provideHasErrorsData()
    {
        return [
            [ new OkRuleResult(null), false ],
            [ new FixedRuleResult(null, null, null, null), false ],
            [ new WarningRuleResult(null, null, null, null), true ],
            [ new ErrorRuleResult(null, null, null, null), true ]
        ];
    }

    public function testTraverse()
    {
        $this->collection->add(new OkRuleResult(null));
        $this->collection->add(new OkRuleResult(null));
        $timesIterated = 0;
        $this->collection->traverse(function ($rule) use (&$timesIterated) {
            $timesIterated++;
        });
        $this->assertEquals($timesIterated, 2);
    }
}

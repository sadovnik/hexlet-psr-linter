<?php

namespace PsrLinter\Tests;

use PsrLinter\Linter;
use PsrLinter\CliApp;
use PsrLinter\Rules\RuleCollection;
use PsrLinter\RuleResults\WarningRuleResult;
use PhpParser\Node\Stmt\Function_;

class LinterTest extends BaseTestCase
{
    protected $linter;

    public function setUp()
    {
        $rules = new RuleCollection(CliApp::getCoreRules());
        $this->linter = new Linter($rules);
    }

    public function testSuccess()
    {
        $code = self::getFixture('camel-case-ok');
        $errorCollection = $this->linter->lint($code);
        $this->assertTrue($errorCollection->isEmpty());
    }

    public function testFail()
    {
        $code = self::getFixture('camel-case-fail');
        $errors = $this->linter->lint($code);
        $count = 0;
        $errors->traverse(function ($error) use (&$count) {
            $this->assertInstanceOf(WarningRuleResult::class, $error);
            $this->assertEquals('Wrong function name: make_some_stuff', $error->getTitle());
            $this->assertEquals('Function names must be declared in camelCase.', $error->getDescription());
            $this->assertEquals(3, $error->getNode()->getLine());
            $count++;
        });
        $this->assertEquals($count, 1);
    }
}

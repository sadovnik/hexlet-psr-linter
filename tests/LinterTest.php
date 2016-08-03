<?php

namespace PsrLinter\Tests;

use PsrLinter\Linter;

class LinterTest extends BaseTestCase
{
    protected $linter;

    public function setUp()
    {
        $this->linter = new Linter;
    }

    public function testSuccess()
    {
        $code = self::getFixture('linter-test-success');
        $this->assertEmpty($this->linter->lint($code));
    }

    public function testFail()
    {
        $code = self::getFixture('linter-test-fail');
        $expectedErrors = [
            [
                'line' => 3,
                'title' => 'Wrong function name.',
                'description' => 'Function names must be declared as camelCase.'
            ]
        ];
        $errors = $this->linter->lint($code);
        $this->assertEquals($expectedErrors, $errors);
    }
}

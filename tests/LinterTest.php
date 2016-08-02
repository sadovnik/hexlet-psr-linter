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
        $this->assertTrue($this->linter->lint($code));
    }

    public function testFail()
    {
        $code = self::getFixture('linter-test-fail');
        $expectedErrors = [
            [ 3, 'Wrong function name.', 'Function names must be declared as camelCase.' ]
        ];
        $errors = $this->linter->lint($code);
        $this->assertEquals($expectedErrors, $errors);
    }
}

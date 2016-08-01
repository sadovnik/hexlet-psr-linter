<?php

namespace PsrLinter\Tests;

use PsrLinter\Linter;
use PHPUnit\Framework\TestCase;

class LinterTest extends TestCase
{
    protected $linter;

    public function setUp()
    {
        $this->linter = Linter::factory();
    }

    public function testSuccess()
    {
        $code = '<?php $foo = "bar";';
        $this->assertTrue($this->linter->lint($code));
    }

    public function testFail()
    {
        $code = '<?php $foo "bar";';
        $this->assertFalse($this->linter->lint($code));
    }
}


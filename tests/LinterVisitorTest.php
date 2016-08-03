<?php

namespace PsrLinter\Tests;

use PhpParser;
use PsrLinter\LinterVisitor;
use PHPUnit\Framework\TestCase;

class LinterVisitorTest extends TestCase
{
    public function testNewlyCreatedLinterVisitorHasNoErrors()
    {
        $linterVisitor = new LinterVisitor;
        $this->assertEquals([], $linterVisitor->getErrors());
    }
}

<?php

namespace PsrLinter\Tests;

use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    protected static function getFixture(string $fixture)
    {
        return file_get_contents(__DIR__ . "/fixtures/$fixture.php.test");
    }
}

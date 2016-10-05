<?php

namespace PsrLinter\Tests;

use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    protected static function getFixture(string $fixture)
    {
        return file_get_contents(self::getFixturePath($fixture));
    }

    protected static function getFixturePath(string $fixture)
    {
        return self::getFixtureDirectoryPath() . "$fixture.test.php";
    }

    protected static function getFixtureDirectoryPath()
    {
        return __DIR__ . "/fixtures/";
    }
}

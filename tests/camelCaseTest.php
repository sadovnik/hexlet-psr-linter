<?php

namespace PsrLinter\Tests;

use PHPUnit\Framework\TestCase;

use function PsrLinter\Rules\isCamelCase;
use function PsrLinter\Rules\convertToCamelCase;

class camelCaseTest extends TestCase
{
    /**
     * @dataProvider provideConvertToCamelCaseData
     */
    public function testConvertToCamelCase($input, $expected)
    {
        $actual = convertToCamelCase($input);
        $this->assertEquals($expected, $actual);
    }

    public function provideConvertToCamelCaseData()
    {
        return [
            [ 'hello_world', 'helloWorld' ],
            [ 'hello_world_', 'helloWorld' ],
            [ 'hello_world__', 'helloWorld' ],
            [ '_some_private_method', 'somePrivateMethod' ],
            [ '__some_private_method', 'somePrivateMethod' ],
            [ 'make_something__important', 'makeSomethingImportant' ]
        ];
    }

    /**
     * @dataProvider provideIsCamelCaseData
     */
    public function testIsCamelCase($input, $expected)
    {
        $actual = isCamelCase($input);
        $this->assertEquals($expected, $actual);
    }

    public function provideIsCamelCaseData()
    {
        return [
            [ 'hello_world', false ],
            [ 'hello_world_', false ],
            [ 'hello_world__', false ],
            [ '_some_private_method', false ],
            [ '__some_private_method', false ],
            [ 'make_something__important', false ],
            [ 'helloWorld', true ],
            [ 'findAllUsers', true ],
        ];
    }
}

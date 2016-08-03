<?php

namespace PsrLinter\Tests;

use PHPUnit\Framework\TestCase;
use PsrLinter\Cli\ReportGenerator;

class ReportGeneratorTest extends TestCase
{
    /**
     * @dataProvider provideErrorData
     */
    public function testGenerate($input, $expected)
    {
        $this->assertEquals($expected, ReportGenerator::generate($input));
    }

    public function provideErrorData()
    {
        return [
            [
                [
                    [
                        'line' => 1,
                        'title' => 'Wrong function name.',
                        'description' => 'Function names must be declared in camelCase.'
                    ]
                ],
                'Line #1: Wrong function name. Function names must be declared in camelCase.' . PHP_EOL
            ],
            [
                [
                    [
                        'line' => 10,
                        'title' => 'Wrong callable name.',
                        'description' => 'Callable names must be declared in camelCase.'
                    ],
                    [
                        'line' => 23,
                        'title' => 'Wrong method name.',
                        'description' => 'Method names must be declared in camelCase.'
                    ]
                ],
                'Line #10: Wrong callable name. Callable names must be declared in camelCase.' . PHP_EOL .
                'Line #23: Wrong method name. Method names must be declared in camelCase.' . PHP_EOL
            ],
        ];
    }
}

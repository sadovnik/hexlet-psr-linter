<?php

namespace PsrLinter\Tests;

use PsrLinter\CliApp;
use League\CLImate\CLImate;
use League\CLImate\Util\Output;
use Vfs\FileSystem;
use Vfs\Node\File;

class CliAppTest extends BaseTestCase
{
    public $app;
    public $buffer;

    public function setUp()
    {
        $this->app = $this->getMockBuilder(CliApp::class)
            ->setMethods(['getCli'])
            ->getMock();

        $output = new Output;
        $output->defaultTo('buffer');
        $this->buffer = $output->get('buffer');

        $cli = new CLImate();
        $cli->setOutput($output);

        $this->app
            ->method('getCli')
            ->will($this->returnValue($cli));
    }

    public function testLintFileNormalFlow()
    {
        $exitCode = $this->app->run([
            '<path>' => self::getFixturePath('camel-case-ok'),
            '--fix' => false,
            '--debug' => false
        ]);

        $this->assertEquals($exitCode, 0);
        $this->assertContains('Code is valid!', $this->buffer->get());
    }

    public function testLintFileErrorFlow()
    {
        $exitCode = $this->app->run([
            '<path>' => self::getFixturePath('camel-case-fail'),
            '--fix' => false,
            '--debug' => false
        ]);
        $this->assertEquals($exitCode, 1);
        $this->assertContains('Found some errors.', $this->buffer->get());
    }

    public function testLintDirectoryErrorFlow()
    {
        $exitCode = $this->app->run([
            '<path>' => self::getFixtureDirectoryPath(),
            '--fix' => false,
            '--debug' => false
        ]);
        $this->assertEquals($exitCode, 1);
        $this->assertContains('Found some errors.', $this->buffer->get());
    }

    public function testFixFlow()
    {
        $fs = FileSystem::factory();
        $fs->mount();
        $fs->get('/')->add('some.php', new File(self::getFixture('camel-case-fail')));
        $exitCode = $this->app->run([
            '<path>' => 'vfs://some.php',
            '--fix' => true,
            '--debug' => false
        ]);
        $this->assertEquals($exitCode, 0);
        $this->assertContains('fixed', $this->buffer->get());
        $this->assertContains('Code is valid!', $this->buffer->get());
    }
}

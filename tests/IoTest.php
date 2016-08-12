<?php

namespace PsrLinter\Tests;

use PHPUnit\Framework\TestCase;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;
use org\bovigo\vfs\vfsStreamDirectory;

use PsrLinter\Cli\Io;
use PsrLinter\Cli\IoException;

class IoTest extends TestCase
{
    protected $root;

    public function setUp()
    {
        $this->root = vfsStream::setUp();
    }

    public function testReadNormal()
    {
        $fileContent = '<?php echo "Hello, world!";';
        $file = (new vfsStreamFile('hello_world.php'))->withContent($fileContent);
        $this->root->addChild($file);
        $result = Io::read($file->url());
    }

    public function testReadNotFoundError()
    {
        try {
            $result = Io::read($this->root->url() . '/non-existing.php.file');
            $this->fail();
        } catch (IoException $e) {
            $this->assertContains('File not found', $e->getMessage());
        }
    }

    public function testReadNotAFile()
    {
        try {
            $result = Io::read($this->root->url());
            $this->fail();
        } catch (IoException $e) {
            $this->assertContains('is not a file', $e->getMessage());
        }
    }

    public function testReadPermissionDeniedError()
    {
        $file = new vfsStreamFile('some.php', 0000);
        $this->root->addChild($file);
        try {
            $result = Io::read($file->url());
            $this->fail();
        } catch (IoException $e) {
            $this->assertContains('Permission denied', $e->getMessage());
        }
    }

    public function testIsDirNormal()
    {
        $directory = new vfsStreamDirectory('project');
        $file = new vfsStreamFile('some.php');
        $this->root->addChild($directory);
        $this->root->addChild($file);
        $this->assertTrue(Io::isDir($directory->url()));
        $this->assertFalse(Io::isDir($file->url()));
    }

    public function testIsDirPermissionDenied()
    {
        $rootDirectory = new vfsStreamDirectory('project');
        $this->root->addChild($rootDirectory);
        $childDirectory = new vfsStreamDirectory('src', 0000);
        $rootDirectory->addChild($childDirectory);
        try {
            $result = Io::isDir($childDirectory->url());
            $this->fail();
        } catch (IoException $e) {
            $this->assertContains('Permission denied', $e->getMessage());
        }
    }

    public function testIsDirNotFound()
    {
        $directory = new vfsStreamDirectory('project');
        $this->root->addChild($directory);
        try {
            $result = Io::isDir($directory->url() . '/non-existing-directory/');
            $this->fail();
        } catch (IoException $e) {
            $this->assertContains('File or directory not found:', $e->getMessage());
        }
    }
}

<?php

namespace Converter\Tests;

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
        $file = new vfsStreamFile('some.json', 0000);
        $this->root->addChild($file);
        try {
            $result = Io::read($file->url());
            $this->fail();
        } catch (IoException $e) {
            $this->assertContains('Permission denied', $e->getMessage());
        }
    }
}

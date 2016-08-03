<?php

namespace PsrLinter\Tests;

use PHPUnit\Framework\TestCase;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;

class BinaryTest extends TestCase
{
    const BIN_PATH = __DIR__ . '/../bin/psr-linter';

    private static function getTempPathByHandle($handle)
    {
        return stream_get_meta_data($handle)['uri'];
    }

    public function testNormalFlow()
    {
        $fileContent = '<?php echo "Hello, world!";';
        $tempFile = tmpfile();
        fwrite($tempFile, $fileContent);
        $command = self::BIN_PATH . ' ' . self::getTempPathByHandle($tempFile);

        exec($command, $output, $exitCode);
        $this->assertEquals(0, $exitCode);
        $this->assertContains('Code is valid!', $output[0]);
    }

    public function testParseError()
    {
        $fileContent = '<?php echo';
        $tempFile = tmpfile();
        fwrite($tempFile, $fileContent);
        $command = self::BIN_PATH . ' ' . self::getTempPathByHandle($tempFile);

        exec($command, $output, $exitCode);
        $this->assertEquals(1, $exitCode);
        $this->assertContains('Unable to parse the sourcecode.', $output[0]);
    }

    public function testWithoutArguments()
    {
        $command = self::BIN_PATH;
        exec($command, $output, $exitCode);
        $this->assertEquals(1, $exitCode);
        $this->assertContains('You must specify path to a php file.', $output[0]);
    }
}

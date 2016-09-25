<?php

namespace PsrLinter\Cli;

use PsrLinter\Linter;
use PsrLinter\Cli\ReportGenerator;
use PhpParser\Error;
use Fs\Fs;

class App
{
    private $args;

    public function __construct($args)
    {
        $this->args = $args;
    }

    public function run()
    {
        $path = $this->args['<path>'];
        try {
            if (Fs::isDir($path)) {
                $this->lintDirectory($path);
            } else {
                $this->lintFile($path);
            }
        } catch (Error $e) {
            $message  = 'Unable to parse the sourcecode.' . PHP_EOL;
            $message .= '    ' . $e->getMessage() . PHP_EOL;
            $code = 1;
            $this->exit($message, $code);
        }
    }

    private function lintFile($file)
    {
        $code = Fs::read($file);

        $linter = new Linter;
        $errors = $linter->lint($code);

        if (empty($errors)) {
            $this->validationSuccess();
        }

        $report = ReportGenerator::generate($errors);
        $this->validationFailed($report);
    }

    private function lintDirectory($directory)
    {
        $directoryIterator = new \RecursiveDirectoryIterator($directory);
        $iteratorIterator = new \RecursiveIteratorIterator($directoryIterator);
        $regexIterator = new \RegexIterator($iteratorIterator, '/\.php$/');

        $fileErrors = [];

        foreach ($regexIterator as $file) {
            $linter = new Linter;
            $path = $file->getPathname();
            $code = Fs::read($path);
            try {
                $errors = $linter->lint($code);
            } catch (Error $e) {
                throw new Error($path . ': ' . $e->getMessage());
            }
            if (!empty($errors)) {
                $fileErrors[$path] = $errors;
            }
        }

        if (empty($fileErrors)) {
            $this->validationSuccess();
        }

        $report = array_reduce(
            array_keys($fileErrors),
            function ($acc, $file) use ($fileErrors) {
                $report = ReportGenerator::generate($fileErrors[$file]);

                $newAcc  = $acc . PHP_EOL;
                $newAcc .= $file . PHP_EOL;
                $newAcc .= '    ' . $report;

                return $newAcc;
            },
            ''
        );
        $this->validationFailed($report);
    }

    private function validationSuccess()
    {
        $this->exit('Code is valid!' . PHP_EOL, 0);
    }

    private function validationFailed(string $report)
    {
        $message = '🐛  Code is not valid. Errors: ';
        $this->exit($message . PHP_EOL . $report, 1);
    }

    private function exit($message, $code)
    {
        echo $message;
        exit($code);
    }
}
